<?php
/**
*
* @package ppkBB3cker
* @version $Id: tunreg.php 1.000 2012-01-08 18:31:34 PPK $
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

$unregtorr=1;
$user['poster_id']=$userid;
$sql="INSERT INTO ".ATTACHMENTS_TABLE." (is_orphan, poster_id, filetime, extension, mimetype) VALUES('0', '{$userid}', '{$dt}', 'torrent', 'application/x-bittorrent')";
$result=my_sql_query($sql);
$user['id']=$a_id=$torrentid=mysql_insert_id($c);
$sql="INSERT INTO ".TRACKER_TORRENTS_TABLE." (id, poster_id, info_hash, added, ip, unreg) VALUES('{$a_id}', '{$userid}', '{$info_hash}', '{$dt}', '{$ip}', '1')";
my_sql_query($sql);
$user['seeders']=$user['leechers']=$user['times_completed']=$user['size']=$user['forb']=$user['free']=$user['upload']=$user['req_ratio']=$user['req_upload']=$user['finished']=0;
$user['added']=$dt;

?>
