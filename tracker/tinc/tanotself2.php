<?php
/**
*
* @package ppkBB3cker
* @version $Id: tanotself2.php 1.000 2009-02-13 12:03:00 PPK $
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
	if($sockres)
	{
		$connectable=1;
		@fclose($sockres);
	}
}

if($user['finished']==NULL)
{
	my_sql_query("INSERT INTO ". TRACKER_SNATCHED_TABLE ." (torrent, to_go, userid, startdat, last_action, uploadoffset, downloadoffset, uploaded, downloaded".(sizeof($snatch_add) ? ', '.implode(', ', $snatch_add['k']) : '').") VALUES ('{$torrentid}', '{$left}', '{$userid}', '{$dt}', '{$dt}', '{$upthis}', '{$downthis}', '{$upthis}', '{$downthis}'".(sizeof($snatch_add) ? ", '".implode("', '", $snatch_add['v'])."'" : '').")");
}

$result=my_sql_query("INSERT INTO ". TRACKER_PEERS_TABLE ." (connectable, torrent, peer_id, ip, port, uploaded, downloaded, to_go, startdat, last_action, seeder, userid, rights, guests, agent) VALUES ('{$connectable}', '{$torrentid}', '{$peer_id}', INET_ATON('{$ip}'), '{$port}', '{$uploaded}', '{$downloaded}', '{$left}', '{$dt}', '{$dt}', '{$seeder}', '{$userid}', '{$s_rights}', '".IS_GUESTS."', '{$agent}')");
if(mysql_affected_rows($c))
{
	if($seeder)
	{
		$updateset[]="seeders=seeders+1";
	}
	else
	{
		$updateset[]="leechers=leechers+1";
	}
}

?>
