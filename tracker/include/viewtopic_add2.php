<?php
/**
*
* @package ppkBB3cker
* @version $Id: viewtopic_add2.php 1.000 2008-11-14 17:23:00 PPK $
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

if($torrent_id)
{

	$viewtopic_add2inc="{$phpbb_root_path}tracker/include/";

	if($assign_vars)
	{
		foreach($assign_vars as $k2 => $v2)
		{
			$template->assign_block_vars('postrow.'.$torrent_info_curr.'_option', $v2);
		}
	}

	if($postrow_headers)
	{
		foreach($postrow_headers as $k2 => $v2)
		{
			$template->assign_block_vars('postrow.headers', array('VALUE' => $v2));
		}
	}

	if(sizeof($postrow_header))
	{
		$template->assign_var('S_TORRENT_FOOTER', implode(' : ', $postrow_header));
	}

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
