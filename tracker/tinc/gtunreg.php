<?php
/**
*
* @package ppkBB3cker
* @version $Id: tmaxip.php 1.000 2012-01-08 18:32:58 PPK $
* @copyright (c) 2011 PPK
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

empty($config['ppkbb_tcunregtorr_sessid']) ? $config['ppkbb_tcunregtorr_sessid']=$session_id=md5(@$_SERVER['SERVER_ADDR']) : '';
if($session_id!=$config['ppkbb_tcunregtorr_sessid'])
{
	err('Invalid passkey');
}
$unregtorr=$user['poster_id']=1;
$sql="INSERT INTO ".ATTACHMENTS_TABLE." (is_orphan, poster_id, filetime, extension, mimetype) VALUES('0', '{$user['poster_id']}', '{$dt}', 'torrent', 'application/x-bittorrent')";
$result=my_sql_query($sql);
$user['id']=$a_id=$torrentid=mysql_insert_id($c);
if(!$userid)
{
	$result=my_sql_query("INSERT INTO ".TRACKER_GUESTS_TABLE." (user_passkey, user_ip, user_time, user_last_time, session_id, unreg) VALUES('{$passkey}', INET_ATON('{$ip}'), '{$dt}', '{$dt}', '{$config['ppkbb_tcunregtorr_sessid']}', '1')");
	$user['user_id']=$userid=mysql_insert_id($c);
}
$sql="INSERT INTO ".TRACKER_TORRENTS_TABLE." (id, poster_id, info_hash, added, ip, unreg) VALUES('{$a_id}', '{$user['poster_id']}', '{$info_hash}', '{$dt}', '{$ip}', '1')";
my_sql_query($sql);
$session_id=$config['ppkbb_tcunregtorr_sessid'];
$user['seeders']=$user['leechers']=$user['times_completed']=$user['size']=$user['forb']=$user['finished']=0;
$user['added']=$dt;

?>
