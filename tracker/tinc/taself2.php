<?php
/**
*
* @package ppkBB3cker
* @version $Id: taself2.php 1.000 2009-02-13 12:02:00 PPK $
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

$connectable=0;
if($config['ppkbb_tcignore_connectable']!=1)
{
	$sockres = @fsockopen($ip, $port, $errno, $errstr, 5);
	if ($sockres)
	{
		$connectable=1;
		@fclose($sockres);
	}
}

if($user['finished']==NULL)
{
	my_sql_query("INSERT INTO ". TRACKER_SNATCHED_TABLE ." (torrent, to_go, userid, startdat, last_action, uploadoffset, downloadoffset, uploaded, downloaded".(sizeof($snatch_add) ? ', '.implode(', ', $snatch_add['k']) : '').") VALUES ('{$torrentid}', '{$left}', '{$userid}', '{$dt}', '{$dt}', '{$upthis}', '{$downthis}', '{$upthis}', '{$downthis}'".(sizeof($snatch_add) ? ", '".implode("', '", $snatch_add['v'])."'" : '').")");
}
else
{
	my_sql_query("UPDATE ". TRACKER_SNATCHED_TABLE ." SET last_action='{$dt}', prev_action='{$attachment['last_action']}'".(sizeof($updatesnatch) ? ', '.implode(', ', $updatesnatch) : '')." WHERE torrent='{$torrentid}' AND userid='{$userid}'".($config['ppkbb_tcguests_enabled'][0] ? (IS_GUESTS ? " AND guests!='0'" : " AND guests='0'") : ''));
}

$result = my_sql_query("UPDATE ". TRACKER_PEERS_TABLE ." tp SET ip=INET_ATON('{$ip}'), port='{$port}', last_action='{$dt}', seeder='{$seeder}', agent='{$agent}'".(sizeof($updatepeers) ? ', '.implode(', ', $updatepeers) : '')." WHERE {$selfwhere}");

if(mysql_affected_rows($c) && $attachment['seeder']!=$seeder)
{
	if($seeder)
	{
		$updateset[]="seeders=seeders+1";
		$updateset[]="leechers='".my_int_val($user['leechers']-1)."'";
	}
	else
	{
		$updateset[]="leechers=leechers+1";
		$updateset[]="seeders='".my_int_val($user['seeders']-1)."'";
	}
}

?>
