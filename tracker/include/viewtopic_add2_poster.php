<?php
/**
*
* @package ppkBB3cker
* @version $Id: viewtopic_add2_poster.php 1.000 2009-02-07 12:27:00 PPK $
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

$poster_addon='';
foreach($torrents_attach['poster'] as $poster_data)
{
	if(!$poster_data['i_external'])
	{
		if($poster_data['thumbnail'] && $config['ppkbb_torr_blocks'][2]==1)
		{
			$poster_addon='&amp;t=1';
		}
		$poster_data['filesize'] = get_formatted_filesize($poster_data['filesize']);
		$poster_basename=utf8_basename($poster_data['real_filename']);
		$poster_wh=tracker_get_thumb_size($poster_data['i_width'], $poster_data['i_height'], $config['ppkbb_torrblock_width'][8], $config['ppkbb_torrblock_width'][9], ($config['ppkbb_torrblock_width'][12] ? true : false));
		$template->assign_block_vars((isset($posting_page) && $posting_page ? '' : 'postrow.').'torrent_poster_fields', array(
			'POSTER_LINK' => append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . (int) $poster_data['attach_id'], true, ($poster_data['is_orphan']) ? $user->session_id : false).'&amp;ext=.'.$poster_data['extension'],
			'POSTER_SRC' => append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . $poster_data['attach_id'] . $poster_addon),
			'POSTER_FILESIZE' => $poster_data['filesize'],
			'POSTER_WH_WIDTH' => $poster_wh[0] ? $poster_wh[0] : false,
			'POSTER_WH_HEIGHT' => $poster_wh[1] ? $poster_wh[1] : false,
			'POSTER_SHORTNAME' => $config['ppkbb_torrblock_width'][2] && utf8_strlen($poster_basename)>$config['ppkbb_torrblock_width'][2] ? utf8_substr($poster_basename, 0, $config['ppkbb_torrblock_width'][2]).'...' : $poster_basename,
			'POSTER_FILENAME' => $poster_basename,
			'POSTER_COMMENT' => $poster_data['attach_comment'] ? $poster_data['attach_comment'] : '',
			'POSTER_DOWNLOADED' =>$poster_data['download_count'],
			'POSTER_FORUM' => 1,
			)
		);
	}
	else
	{
		$poster_wh=tracker_get_thumb_size($poster_data['i_width'], $poster_data['i_height'], $config['ppkbb_torrblock_width'][8], $config['ppkbb_torrblock_width'][9], ($config['ppkbb_torrblock_width'][12] ? true : false));
		$template->assign_block_vars((isset($posting_page) && $posting_page ? '' : 'postrow.').'torrent_poster_fields', array(
			'POSTER_LINK' => $poster_data['real_filename'],
			'POSTER_SRC' => $poster_data['real_filename'],
			'POSTER_WH_WIDTH' => $poster_wh[0] ? $poster_wh[0] : false,
			'POSTER_WH_HEIGHT' => $poster_wh[1] ? $poster_wh[1] : false,
			'POSTER_FORUM' => 0,
			)
		);
	}
}
?>
