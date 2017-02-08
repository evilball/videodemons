<?php
/**
*
* @package ppkBB3cker
* @version $Id: viewtopic_add2_torrent.php 1.000 2009-02-07 12:26:00 PPK $
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

foreach($torrents_attach['torrent'] as $torrent_data)
{
	if(isset($posting_page) && $posting_page)
	{
		$torrent_link=$is_candowntorr=$is_canskiprequpload=$is_canskipreqratio=$is_canusefree=$is_canusebonus=$is_canskiprcheck=1;
		$torrent_id=$torrent_data['attach_id'];

		isset($torrents[$torrent_data['attach_id']]['size']) ? '' : $torrents[$torrent_data['attach_id']]['size']=0;
		isset($torrents[$torrent_data['attach_id']]['req_upload']) ? '' : $torrents[$torrent_data['attach_id']]['req_upload']=0;
		isset($torrents[$torrent_data['attach_id']]['req_ratio']) ? '' : $torrents[$torrent_data['attach_id']]['req_ratio']=0;
		isset($torrents[$torrent_data['attach_id']]['free']) ? '' : $torrents[$torrent_data['attach_id']]['free']=0;
		isset($torrents[$torrent_data['attach_id']]['forb']) ? '' : $torrents[$torrent_data['attach_id']]['forb']=0;
		isset($torrents[$torrent_data['attach_id']]['thanks']) ? '' : $torrents[$torrent_data['attach_id']]['thanks']=0;
		isset($torrents[$torrent_data['attach_id']]['seeders']) ? '' : $torrents[$torrent_data['attach_id']]['seeders']=0;
		isset($torrents[$torrent_data['attach_id']]['leechers']) ? '' : $torrents[$torrent_data['attach_id']]['leechers']=0;
	}
	$req_upload=$req_upload_src_img=$req_upload_text='';
	if(!$is_canskiprequpload && $torrents[$torrent_data['attach_id']]['req_upload'])
	{
		if($user->data['user_uploaded'] < ($torrents[$torrent_data['attach_id']]['req_upload']))
		{
			$req_upload_src_img=$phpbb_root_path . 'images/tracker/info_requpload_big.png';
			$req_upload_text=sprintf($user->lang['TORRENT_REQ_UPLOAD_1'], get_formatted_filesize($torrents[$torrent_data['attach_id']]['req_upload']), get_formatted_filesize($user->data['user_uploaded']));
			$req_upload=true;
			$torrent_link=0;
		}
		else
		{
			$req_upload_src_img=$phpbb_root_path . 'images/tracker/button_ok_requpload_big.png';
			$req_upload_text= sprintf($user->lang['TORRENT_REQ_UPLOAD_0'], $torrents[$torrent_data['attach_id']]['req_upload'], get_formatted_filesize($user->data['user_uploaded']));
			$req_upload=true;
		}
	}

	$req_ratio=$req_ratio_src_img=$req_ratio_text='';
	if(!$is_canskipreqratio && $torrents[$torrent_data['attach_id']]['req_ratio']!=0.000)
	{
		if($user_ratio!='None.' && ($user_ratio < $torrents[$torrent_data['attach_id']]['req_ratio'] || $user_ratio=='Leech.' || $user_ratio=='Inf.'))
		{
			$req_ratio_src_img=$phpbb_root_path . 'images/tracker/info_reqratio_big.png';
			$req_ratio_text=sprintf($user->lang['TORRENT_REQ_RATIO_1'], $torrents[$torrent_data['attach_id']]['req_ratio'], get_ratio_alias($user_ratio));
			$req_ratio=true;
			$torrent_link=0;
		}
		else
		{
			$req_ratio_src_img=$phpbb_root_path . 'images/tracker/button_ok_reqratio_big.png';
			$req_ratio_text=sprintf($user->lang['TORRENT_REQ_RATIO_0'], $torrents[$torrent_data['attach_id']]['req_ratio'], get_ratio_alias($user_ratio));
			$req_ratio=true;
		}
	}

	$torrent_data['filesize'] = get_formatted_filesize($torrent_data['filesize']);
	$torrent_basename=utf8_basename(urldecode($torrent_data['real_filename']));
	$config['ppkbb_tcbonus_fsize'][1] = get_formatted_filesize(!$config['ppkbb_tcbonus_fsize'][1] ? $torrents[$torrent_data['attach_id']]['size'] : $config['ppkbb_tcbonus_fsize'][1]);

	$freetorr_percent=$is_canusefree ? $torrents[$torrent_data['attach_id']]['free'] : 0;

	$torrent_src_link=append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . (int) $torrent_data['attach_id'], true, ($torrent_data['is_orphan']) ? $user->session_id : false);
	$torrent_link ? $torrent_link=$torrent_src_link : $torrent_link='';

		$magnet_src_link='';
	if($torrent_link && (($config['ppkbb_torrent_magnetlink'][3] && $user->data['is_registered']) || (!$user->data['is_registered'] && $config['ppkbb_torrent_gmagnetlink'][3] && $config['ppkbb_tcguests_enabled'][0])))
	{
		$magnet_src_link="{$torrent_src_link}&amp;magnet=1";
	}
	$torrent_shortname=$config['ppkbb_torrblock_width'][2] && utf8_strlen($torrent_basename)>$config['ppkbb_torrblock_width'][2] ? utf8_substr($torrent_basename, 0, $config['ppkbb_torrblock_width'][2]).'...' : $torrent_basename;

	$template->assign_block_vars((isset($posting_page) && $posting_page ? '' : 'postrow.').'torrent_fields', array(
		'TORRENT_LINK' => $torrent_link ? true : false,

		'TORRENT_DOWNLOAD_SRC_IMG' => $phpbb_root_path . 'images/tracker/filesave_big.png',
		'TORRENT_FILENAME' => $torrent_basename,
		'TORRENT_SRC_LINK' => $torrent_link ? $torrent_src_link : '',

		'TORRENT_MAGNET_LINK' => $magnet_src_link ? $magnet_src_link : false,

		'TORRENT_MAGNET_SRC_IMG'	=> $phpbb_root_path.'images/tracker/filesaveas_big.png',

		'TORRENT_FILESIZE' => $torrent_data['filesize'],
		'TORRENT_SHORTNAME' => $torrent_shortname,
		'TORRENT_COMMENT' => $torrent_data['attach_comment'] ? $torrent_data['attach_comment'] : '',
		'TORRENT_DOWNLOADED' => $torrent_data['download_count'],

		'TORRENT_FREE' => $is_candowntorr && $is_canusefree && $torrents[$torrent_data['attach_id']]['free'] && $freetorr_percent ? true : false,
		'TORRENT_FREE_SRC_IMG' => $is_candowntorr ? $phpbb_root_path . 'images/tracker/bookmark_big.png' : '',
		'TORRENT_FREE_TEXT' => $is_candowntorr && $is_canusefree && $torrents[$torrent_data['attach_id']]['free'] && $freetorr_percent ? sprintf($user->lang['FORM_TORRENT_FREE'], $torrents[$torrent_data['attach_id']]['free'], '%') : '',

		'TORRENT_REQ_UPLOAD' => $req_upload ? true : false,
		'TORRENT_REQ_UPLOAD_SRC_IMG' => $req_upload_src_img,
		'TORRENT_REQ_UPLOAD_TEXT' => $req_upload_text,

		'TORRENT_REQ_RATIO' => $req_ratio ? true : false,
		'TORRENT_REQ_RATIO_SRC_IMG' => $req_ratio_src_img,
		'TORRENT_REQ_RATIO_TEXT' => $req_ratio_text,

		'TORRENT_THANKS' => $torrents[$torrent_data['attach_id']]['thanks'],
		'TORRENT_BONUS' => $is_candowntorr && $is_canusebonus && $torrents[$torrent_data['attach_id']]['size'] > $config['ppkbb_tcbonus_fsize'][0] && $config['ppkbb_tcbonus_value'][0] > 0 ? true : false,
		'TORRENT_BONUS_SRC_IMG' => $phpbb_root_path . 'images/tracker/add_big.png',
		'TORRENT_BONUS_TEXT' => $is_candowntorr && $is_canusebonus && $torrents[$torrent_data['attach_id']]['size'] > $config['ppkbb_tcbonus_fsize'][0] && $config['ppkbb_tcbonus_value'][0] > 0 ? sprintf($user->lang['TORRENT_BONUS'], $config['ppkbb_tcbonus_fsize'][1], $config['ppkbb_tcbonus_value'][3]) : '',

		'TORRENT_FORB' => $is_candowntorr && $torrents[$torrent_data['attach_id']]['forb'] > 0 ? true : false,
		'TORRENT_FORB_SRC_IMG' => $phpbb_root_path . 'images/tracker/'.($torrents[$torrent_data['attach_id']]['forb']==1 ? '' : 'half').'encrypted_big.png',
		'TORRENT_FORB_TEXT' => $is_candowntorr && $torrents[$torrent_data['attach_id']]['forb'] > 0 ? sprintf($user->lang['FORM_TORRENT_FORB'], $torrent_statuses['TRACKER_FORB_REASON'][$torrents[$torrent_data['attach_id']]['forb']]) : '',

		'TORRENT_WAIT' => $is_candowntorr && !$is_canskiprcheck && ($t_wait>=0 || $t_wait2 >= 0) ? true : false,
		'TORRENT_WAIT_SRC_IMG' => $phpbb_root_path . 'images/tracker/xclock_big.png',
		'TORRENT_WAIT_TEXT' => $is_candowntorr && !$is_canskiprcheck && ($t_wait>=0 || $t_wait2 >= 0) ? (($t_wait > 0 || $t_wait2 > 0) ? sprintf($user->lang['TORRENT_WAIT'], ($t_wait > $t_wait2 ? $t_wait : $t_wait2)) : $user->lang['TORRENT_WAIT_NEVER']) : '',
		)
	);
}
?>
