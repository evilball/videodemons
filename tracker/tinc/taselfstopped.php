<?php
/**
*
* @package ppkBB3cker
* @version $Id: taselfstopped.php 1.000 2009-02-13 11:57:00 PPK $
* @copyright (c) 2008 PPK
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

if($user['finished']==NULL)
{
	my_sql_query("INSERT INTO ". TRACKER_SNATCHED_TABLE ." (torrent, to_go, userid, startdat, last_action, uploadoffset, downloadoffset, uploaded, downloaded, guests) VALUES('{$torrentid}', '{$left}', '{$userid}', '{$dt}', '{$dt}', '{$upthis}', '{$downthis}', '{$upthis}', '{$downthis}', '".IS_GUESTS."')");
}
else
{
	sizeof($updatesnatch) ? $result=my_sql_query("UPDATE ". TRACKER_SNATCHED_TABLE ." SET ".implode(', ', $updatesnatch)." WHERE torrent='{$torrentid}' AND userid='{$userid}'".($config['ppkbb_tcguests_enabled'][0] ? (IS_GUESTS ? " AND guests!='0'" : " AND guests='0'") : '')) : '';
}

$result=my_sql_query("DELETE FROM ". TRACKER_PEERS_TABLE ." WHERE torrent='{$torrentid}' AND userid='{$userid}'");
if(mysql_affected_rows($c))
{
	if($seeder)
	{
		$updateset[]="seeders='".my_int_val($user['seeders']-1)."'";
	}
	else
	{
		$updateset[]="leechers='".my_int_val($user['leechers']-1)."'";
	}
}
?>
