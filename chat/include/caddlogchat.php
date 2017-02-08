<?php
/**
*
* @package ppkBB3cker
* @version $Id: caddlogchat.php 1.000 2010-03-16 11:46:00 PPK $
* @copyright (c) 2010 PPK
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

define('LOG_TABLE', $table_prefix . 'log');

//From /includes/functions.php
function chat_add_log($log_type, $user_id, $forum_id, $reportee_id, $log_operation, $log_data)
{
	global $dt, $c;

	$log_data=$log_data ? mysql_real_escape_string(serialize($log_data), $c) : '';

	$sql="INSERT INTO ".LOG_TABLE." (log_type, user_id, forum_id, reportee_id, log_ip, log_time, log_operation, log_data) VALUES('{$log_type}', '{$user_id}', '{$forum_id}', '{$reportee_id}', '".mysql_real_escape_string($_SERVER['REMOTE_ADDR'], $c)."', '{$dt}', '{$log_operation}', '{$log_data}')";
	$result=my_sql_query($sql);
}
?>
