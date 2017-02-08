<?php
/**
*
* @package ppkBB3cker
* @version $Id: posting_add1.php 1.000 2009-05-14 15:49:00 PPK $
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

if(isset($torrents_attach['torrent']) && sizeof($torrents_attach['torrent']))
{
	$user->add_lang('viewtopic');
	$torrents_id=$torrents=array();
	foreach ($torrents_attach['torrent'] as $torrent_data)
	{
		$torrents_id[]=$torrent_data['attach_id'];
	}
	if(sizeof($torrents_id))
	{
		$sql="SELECT info_hash, size, id, free, forb, req_upload, req_ratio, thanks FROM ".TRACKER_TORRENTS_TABLE." WHERE id IN('".implode("', '", $torrents_id)."')";
		$result=$db->sql_query($sql);
		while($row=$db->sql_fetchrow($result))
		{
			$row['free']=$torrent_free;
			$row['req_ratio']=$torrent_reqratio;
			$row['req_upload']=$torrent_requpload;
			$torrents[$row['id']]=$row;
		}
		$db->sql_freeresult($result);
	}
	include("{$phpbb_root_path}tracker/include/viewtopic_add2_torrent.{$phpEx}");
}

if(isset($torrents_attach['poster']) && sizeof($torrents_attach['poster']))
{
	include("{$phpbb_root_path}tracker/include/viewtopic_add2_poster.{$phpEx}");
}

if(isset($torrents_attach['screenshot']) && sizeof($torrents_attach['screenshot']))
{
	include("{$phpbb_root_path}tracker/include/viewtopic_add2_screenshot.{$phpEx}");
}
?>
