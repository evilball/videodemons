<?php
/**
*
* @package ppkBB3cker
* @version $Id: viewtopic_add2_screenshot.php 1.000 2009-02-07 12:28:00 PPK $
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

$screenshot_addon='';
foreach($torrents_attach['screenshot'] as $screenshot_data)
{
	if(!$screenshot_data['i_external'])
	{
		if($screenshot_data['thumbnail'] && $config['ppkbb_torr_blocks'][3]==1)
		{
			$screenshot_addon='&amp;t=1';
		}
		$screenshot_data['filesize'] = get_formatted_filesize($screenshot_data['filesize']);
		$screenshot_basename=utf8_basename($screenshot_data['real_filename']);
		$screenshot_wh=tracker_get_thumb_size($screenshot_data['i_width'], $screenshot_data['i_height'], $config['ppkbb_torrblock_width'][10], $config['ppkbb_torrblock_width'][11], ($config['ppkbb_torrblock_width'][12] ? true : false));
		$template->assign_block_vars((isset($posting_page) && $posting_page ? '' : 'postrow.').'torrent_screenshot_fields', array(
			'SCREENSHOT_LINK' => append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . (int) $screenshot_data['attach_id'], true, ($screenshot_data['is_orphan']) ? $user->session_id : false).'&amp;ext=.'.$screenshot_data['extension'],
			'SCREENSHOT_SRC' => append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . $screenshot_data['attach_id'] . $screenshot_addon),
			'SCREENSHOT_FILESIZE' => $screenshot_data['filesize'],
			'SCREENSHOT_WH_WIDTH' => $screenshot_wh[0] ? $screenshot_wh[0] : false,
			'SCREENSHOT_WH_HEIGHT' => $screenshot_wh[1] ? $screenshot_wh[1] : false,
			'SCREENSHOT_SHORTNAME' => $config['ppkbb_torrblock_width'][2] && utf8_strlen($screenshot_basename)>$config['ppkbb_torrblock_width'][2] ? utf8_substr($screenshot_basename, 0, $config['ppkbb_torrblock_width'][2]).'...' : $screenshot_basename,
			'SCREENSHOT_FILENAME'	=> $screenshot_basename,
			'SCREENSHOT_COMMENT'	=> $screenshot_data['attach_comment'] ? $screenshot_data['attach_comment'] : '',
			'SCREENSHOT_DOWNLOADED' =>$screenshot_data['download_count'],
			'SCREENSHOT_FORUM' => 1,
			)
		);
	}
	else
	{
		$screenshot_wh=tracker_get_thumb_size($screenshot_data['i_width'], $screenshot_data['i_height'], $config['ppkbb_torrblock_width'][10], $config['ppkbb_torrblock_width'][11], ($config['ppkbb_torrblock_width'][12] ? true : false));
		$template->assign_block_vars((isset($posting_page) && $posting_page ? '' : 'postrow.').'torrent_screenshot_fields', array(
			'SCREENSHOT_LINK' => $screenshot_data['real_filename'],
			'SCREENSHOT_SRC' => $screenshot_data['real_filename'],
			'SCREENSHOT_WH_WIDTH' => $screenshot_wh[0] ? $screenshot_wh[0] : false,
			'SCREENSHOT_WH_HEIGHT' => $screenshot_wh[1] ? $screenshot_wh[1] : false,
			'SCREENSHOT_FORUM' => 0,
			)
		);
	}
}
?>
