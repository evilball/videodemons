<?php
/**
*
* @package dbal
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* Database Abstraction Layer
* @package dbal
*/
class dbal
{
	var $db_connect_id;
	var $query_result;
	var $return_on_error = false;
	var $transaction = false;
	var $sql_time = 0;
	var $num_queries = array();
	var $open_queries = array();

	var $curtime = 0;
	var $query_hold = '';
	var $html_hold = '';
	var $sql_report = '';

	var $persistency = false;
	var $user = '';
	var $server = '';
	var $dbname = '';

	// Set to true if error triggered
	var $sql_error_triggered = false;

	// Holding the last sql query on sql error
	var $sql_error_sql = '';
	// Holding the error information - only populated if sql_error_triggered is set
	var $sql_error_returned = array();

	// Holding transaction count
	var $transactions = 0;

	// Supports multi inserts?
	var $multi_insert = false;

	/**
	* Current sql layer
	*/
	var $sql_layer = '';

	/**
	* Wildcards for matching any (%) or exactly one (_) character within LIKE expressions
	*/
	var $any_char;
	var $one_char;

	/**
	* Exact version of the DBAL, directly queried
	*/
	var $sql_server_version = false;

	/**
	* Constructor
	*/
	function dbal()
	{
		$this->num_queries = array(
			'normal'		=> 0,
		);

		// Fill default sql layer based on the class being called.
		// This can be changed by the specified layer itself later if needed.
		$this->sql_layer = substr(get_class($this), 5);

		// Do not change this please! This variable is used to easy the use of it - and is hardcoded.
		$this->any_char = chr(0) . '%';
		$this->one_char = chr(0) . '_';
	}

	/**
	* Return number of sql queries and cached sql queries used
	*/
	function sql_num_queries()
	{
		return $this->num_queries['normal'];
	}

	/**
	* Add to query count
	*/
	function sql_add_num_queries()
	{
		$this->num_queries['normal']+=1;
	}

	/**
	* DBAL garbage collection, close sql connection
	*/
	function sql_close()
	{
		if (!$this->db_connect_id)
		{
			return false;
		}

		if ($this->transaction)
		{
			do
			{
				$this->sql_transaction('commit');
			}
			while ($this->transaction);
		}

		foreach ($this->open_queries as $query_id)
		{
			$this->sql_freeresult($query_id);
		}

		// Connection closed correctly. Set db_connect_id to false to prevent errors
		if ($result = $this->_sql_close())
		{
			$this->db_connect_id = false;
		}

		return $result;
	}

	/**
	* Seek to given row number
	* rownum is zero-based
	*/
	function sql_rowseek($rownum, &$query_id)
	{
		global $cache;

		if ($query_id === false)
		{
			$query_id = $this->query_result;
		}

		if (isset($cache->sql_rowset[$query_id]))
		{
			return $cache->sql_rowseek($rownum, $query_id);
		}

		if ($query_id === false)
		{
			return false;
		}

		$this->sql_freeresult($query_id);
		$query_id = $this->sql_query($this->last_query_text);

		if ($query_id === false)
		{
			return false;
		}

		// We do not fetch the row for rownum == 0 because then the next resultset would be the second row
		for ($i = 0; $i < $rownum; $i++)
		{
			if (!$this->sql_fetchrow($query_id))
			{
				return false;
			}
		}

		return true;
	}

	/**
	* Fetch field
	* if rownum is false, the current row is used, else it is pointing to the row (zero-based)
	*/
	function sql_fetchfield($field, $rownum = false, $query_id = false)
	{
		if ($query_id === false)
		{
			$query_id = $this->query_result;
		}

		if ($query_id !== false)
		{
			if ($rownum !== false)
			{
				$this->sql_rowseek($rownum, $query_id);
			}

			$row = $this->sql_fetchrow($query_id);
			return (isset($row[$field])) ? $row[$field] : false;
		}

		return false;
	}

	/**
	* Correctly adjust LIKE expression for special characters
	* Some DBMS are handling them in a different way
	*
	* @param string $expression The expression to use. Every wildcard is escaped, except $this->any_char and $this->one_char
	* @return string LIKE expression including the keyword!
	*/
	function sql_like_expression($expression)
	{
		$expression = utf8_str_replace(array('_', '%'), array("\_", "\%"), $expression);
		$expression = utf8_str_replace(array(chr(0) . "\_", chr(0) . "\%"), array('_', '%'), $expression);

		return $this->_sql_like_expression('LIKE \'' . $this->sql_escape($expression) . '\'');
	}

	/**
	* SQL Transaction
	* @access private
	*/
	function sql_transaction($status = 'begin')
	{
		switch ($status)
		{
			case 'begin':
				// If we are within a transaction we will not open another one, but enclose the current one to not loose data (prevening auto commit)
				if ($this->transaction)
				{
					$this->transactions++;
					return true;
				}

				$result = $this->_sql_transaction('begin');

				if (!$result)
				{
					$this->sql_error();
				}

				$this->transaction = true;
			break;

			case 'commit':
				// If there was a previously opened transaction we do not commit yet... but count back the number of inner transactions
				if ($this->transaction && $this->transactions)
				{
					$this->transactions--;
					return true;
				}

				// Check if there is a transaction (no transaction can happen if there was an error, with a combined rollback and error returning enabled)
				// This implies we have transaction always set for autocommit db's
				if (!$this->transaction)
				{
					return false;
				}

				$result = $this->_sql_transaction('commit');

				if (!$result)
				{
					$this->sql_error();
				}

				$this->transaction = false;
				$this->transactions = 0;
			break;

			case 'rollback':
				$result = $this->_sql_transaction('rollback');
				$this->transaction = false;
				$this->transactions = 0;
			break;

			default:
				$result = $this->_sql_transaction($status);
			break;
		}

		return $result;
	}

	/**
	* display sql error page
	*/
	function sql_error($sql = '')
	{
		// Set var to retrieve errored status
		$this->sql_error_triggered = true;
		$this->sql_error_sql = $sql;

		$this->sql_error_returned = $this->_sql_error();

		if (!$this->return_on_error)
		{
			$message = 'SQL ERROR [ ' . $this->sql_layer . ' ]<br /><br />' . $this->sql_error_returned['message'] . ' [' . $this->sql_error_returned['code'] . ']';

			//$message .= ($sql) ? '<br /><br />SQL<br /><br />' . htmlspecialchars($sql) : '';

			if ($this->transaction)
			{
				$this->sql_transaction('rollback');
			}

			err($message);
		}

		if ($this->transaction)
		{
			$this->sql_transaction('rollback');
		}

		return $this->sql_error_returned;
	}

}

/**
* This variable holds the class name to use later
*/
$sql_db = (!empty($dbms)) ? 'dbal_' . basename($dbms) : 'dbal';

?>
