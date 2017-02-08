<?php
/**
*
* @package ppkBB3cker
* @version $Id: tban.php 1.000 2009-01-31 13:59:00 PPK $
* @copyright (c) 2009 PPK
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

define('BANLIST_TABLE',	$table_prefix . 'banlist');

if(check_ban($user['user_id']))
{
	err('This user or ip banned');
}

//From includes/session.php
function check_ban($user_id)
{
	$banned = false;
	$where_sql = array();
	$user_ips=$_SERVER['REMOTE_ADDR'];
	$user_email=false;

	$sql = 'SELECT ban_ip, ban_userid, ban_email, ban_exclude, ban_give_reason, ban_end
		FROM ' . BANLIST_TABLE . '
		WHERE ';

	// Determine which entries to check, only return those
	if ($user_email === false)
	{
		$where_sql[] = "ban_email = ''";
	}

	if ($user_ips === false)
	{
		$where_sql[] = "(ban_ip = '' OR ban_exclude = 1)";
	}

	if ($user_id === false)
	{
		$where_sql[] = '(ban_userid = 0 OR ban_exclude = 1)';
	}
	else
	{
		$_sql = '(ban_userid = ' . $user_id;

		if ($user_email !== false)
		{
			$_sql .= " OR ban_email <> ''";
		}

		if ($user_ips !== false)
		{
			$_sql .= " OR ban_ip <> ''";
		}

		$_sql .= ')';

		$where_sql[] = $_sql;
	}

	$sql .= (sizeof($where_sql)) ? implode(' AND ', $where_sql) : '';
	$result = my_sql_query($sql);

	$ban_triggered_by = 'user';
	while ($row = mysql_fetch_array($result))
	{
		if ($row['ban_end'] && $row['ban_end'] < time())
		{
			continue;
		}

		$ip_banned = false;
		if (!empty($row['ban_ip']))
		{
			if (!is_array($user_ips))
			{
				$ip_banned = preg_match('#^' . str_replace('\*', '.*?', preg_quote($row['ban_ip'], '#')) . '$#i', $user_ips);
			}
			else
			{
				foreach ($user_ips as $user_ip)
				{
					if (preg_match('#^' . str_replace('\*', '.*?', preg_quote($row['ban_ip'], '#')) . '$#i', $user_ip))
					{
						$ip_banned = true;
						break;
					}
				}
			}
		}

		if ((!empty($row['ban_userid']) && intval($row['ban_userid']) == $user_id) ||
			$ip_banned ||
			(!empty($row['ban_email']) && preg_match('#^' . str_replace('\*', '.*?', preg_quote($row['ban_email'], '#')) . '$#i', $user_email)))
		{
			if (!empty($row['ban_exclude']))
			{
				$banned = false;
				break;
			}
			else
			{
				$banned = true;
				$ban_row = $row;

				if (!empty($row['ban_userid']) && intval($row['ban_userid']) == $user_id)
				{
					$ban_triggered_by = 'user';
				}
				else if ($ip_banned)
				{
					$ban_triggered_by = 'ip';
				}
				else
				{
					$ban_triggered_by = 'email';
				}

				// Don't break. Check if there is an exclude rule for this user
			}
		}
	}
	mysql_free_result($result);

	return $banned;
}

?>
