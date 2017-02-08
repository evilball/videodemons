<?php
/**
*
* @package ppkBB3cker
* @version $Id: mcp_topic_add2.php 1.000 2014-04-22 11:04:48 PPK $
* @copyright (c) 2014 PPK
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

if($torrent_id)
{
	$viewtopic_add2inc="{$phpbb_root_path}tracker/include/";

	if(isset($torrents_attach['torrent']) && sizeof($torrents_attach['torrent'])/* && $torrent_guest*/)
	{
		include($viewtopic_add2inc.'viewtopic_add2_torrent.'.$phpEx);
	}

	if(isset($torrents_attach['poster']) && sizeof($torrents_attach['poster']))
	{
		include($viewtopic_add2inc.'viewtopic_add2_poster.'.$phpEx);
	}

	if(isset($torrents_attach['screenshot']) && sizeof($torrents_attach['screenshot']))
	{
		include($viewtopic_add2inc.'viewtopic_add2_screenshot.'.$phpEx);
	}
}

?>
