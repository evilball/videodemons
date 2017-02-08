<?php
/**
*
* @package ppkBB3cker
* @version $Id: posting_add2.php 1.000 2009-05-14 15:51:00 PPK $
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

if($is_cansetfree || $is_cansetreqratioupload)
{
	$template->assign_vars(array(
		'TORRENT_FREE_VAL' => $torrent_free,
		'TORRENT_REQRATIO_VAL' => $torrent_reqratio,
		'TORRENT_REQUPLOAD_VAL' => $preview || $submit ? get_size_value(request_var('torrent_requploadv', 'b'), $torrent_requpload) : $torrent_requpload,
		'TORRENT_REQUPLOAD_VAL2' => $preview || $submit ? get_formatted_filesize(get_size_value(request_var('torrent_requploadv', 'b'), $torrent_requpload)) : get_formatted_filesize($torrent_requpload),
		)
	);
}
$template->assign_vars(array(
	'TRACKER_TORRENTS_COUNT' => isset($torrents_attach['torrent']) && sizeof($torrents_attach['torrent']) > 1 ? true : false,
	'TRACKER_POSTERS_COUNT' => isset($torrents_attach['poster']) && sizeof($torrents_attach['poster']) > 1 ? true : false,
	'TRACKER_SCREENSHOTS_COUNT' => isset($torrents_attach['screenshot']) && sizeof($torrents_attach['screenshot']) > 1 ? true : false,
	)
);
?>
