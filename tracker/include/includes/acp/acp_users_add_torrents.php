<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_users_add_torrents.php 1.000 2009-05-20 11:36:00 PPK $
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

$is_admod=$auth->acl_gets('a_', 'm_') || $auth->acl_getf_global('m_') ? 1 : 0;

$torrent_info_curr='';
$torrent_info_curr_explain=$assign_vars=$torrent_info=$sort_opt=$postrow_headers=$postrow_header=array();
$i3=0;

$torrent_opt=$opt=request_var('opt', '');
$torrent_opt_sort=request_var('opts', '');
$torrent_opt_param=request_var('optp', '');
$torrent_opt_sort=='ASC' ? '' : $torrent_opt_sort='DESC';

$acp_users_url=append_sid("{$phpbb_admin_path}index.$phpEx", 'i=users&amp;mode=torrents&amp;u='.$user_id);

$torrents_info=array('torrent', 'finished', 'seed', 'leech', 'tothanks', 'fromthanks', 'history', 'leave', 'downloads');
foreach($torrents_info as $iv)
{
	$torrent_info[$iv]='<a href="'.$acp_users_url.($torrent_opt!=$iv ? '&amp;opt='.$iv.'#opt' : '').'">'.$user->lang['TORRENT_INFO_HEADER_'.strtoupper($iv)].'</a>';
}

$del=request_var('del', array(0=>''));
if(sizeof($del))
{
	$del=array_map('my_int_val', array_keys($del));
	$sql='';
	switch($torrent_opt)
	{
		case 'torrent':
			$sql='DELETE FROM '.TRACKER_TORRENTS_TABLE." WHERE id IN('".implode("', '", $del)."') AND poster_id='{$user_id}'";
		break;

		case 'finished':
		case 'history':
		case 'leave':
			$sql='DELETE FROM '.TRACKER_SNATCHED_TABLE." WHERE id IN('".implode("', '", $del)."') AND userid='{$user_id}'";
		break;

		case 'seed':
		case 'leech':
			$sql='DELETE FROM '.TRACKER_PEERS_TABLE." WHERE id IN('".implode("', '", $del)."') AND userid='{$user_id}'";
		break;

		case 'tothanks':
		case 'fromthanks':
			$sql='DELETE FROM '.TRACKER_THANKS_TABLE." WHERE id IN('".implode("', '", $del)."') AND ".($torrent_opt=='fromthanks' ? 'user_id' : 'to_user')."='{$user_id}'";
		break;

		case 'downloads':
			$sql='DELETE FROM '.TRACKER_DOWNLOADS_TABLE." WHERE id IN('".implode("', '", $del)."') AND downloader_id='{$user_id}'";
		break;
	}
	if($sql)
	{
		$result=$db->sql_query($sql);
		if($db->sql_affectedrows($result))
		{
			trigger_error($user->lang['DEL_USER_TORRENTS_SUCCESS'] . adm_back_link($this->u_action . '&amp;u=' . $user_id));
		}
	}
}
if(!class_exists('timedelta'))
{
$user->add_lang('mods/posts_merging');
require($phpbb_root_path . 'includes/time_delta.'.$phpEx);
$td = new timedelta();
}
!$config['ppkbb_mua_countlist'] ? !$config['ppkbb_mua_countlist']=array(5, 10, 25, 50, 75, 100, 250, 500, 750, 1000, 5000, 10000) : $config['ppkbb_mua_countlist']=my_split_config($config['ppkbb_mua_countlist'], 0, 'my_int_val');
sort($config['ppkbb_mua_countlist']);
$mua_limit=$config['ppkbb_mua_countlist'][sizeof($config['ppkbb_mua_countlist'])-1];
$ex_fid_ary=array();
$mua_add1inc=$phpbb_root_path.'tracker/include/';
$mua='acp';

switch($torrent_opt)
{
	case 'leave':
		include($mua_add1inc.'mua_add1_leave.'.$phpEx);
		break;

	case 'history':
		include($mua_add1inc.'mua_add1_history.'.$phpEx);
		break;

	case 'finished':
		include($mua_add1inc.'mua_add1_finished.'.$phpEx);
		break;

	case 'seed':
		include($mua_add1inc.'mua_add1_seed.'.$phpEx);
		break;

	case 'leech':
		include($mua_add1inc.'mua_add1_leech.'.$phpEx);
		break;

	case 'tothanks':
		include($mua_add1inc.'mua_add1_tothanks.'.$phpEx);
		break;

	case 'fromthanks':
		include($mua_add1inc.'mua_add1_fromthanks.'.$phpEx);
		break;

	case 'torrent':
		include($mua_add1inc.'mua_add1_torrent.'.$phpEx);
		break;

	case 'downloads':
		include($mua_add1inc.'mua_add1_downloads.'.$phpEx);
		break;

	default:
		$torrent_opt='';
		break;

}

$user->add_lang('mods/acp/ppkbb3cker_torrents');
$postrow_headers[]=$user->lang['DELETE'];
$postrow_header[]='<a href="javascript:;" onclick="fnShowHide('.(sizeof($postrow_header)).');">'.$user->lang['DELETE'].'</a>';
$torrent_info_curr_explain[$torrent_opt]=$user->lang['TORRENT_INFO_HEADER_'.strtoupper($torrent_opt)];

$template->assign_vars(array(
	'S_HAS_TORRENT_EXPLAIN'	=> $torrent_info_curr ? true : false,
	'S_HAS_TORRENT_EXPLAIN_TORRENT'	=> @$torrent_info_curr_explain['torrent'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_FINISHED'	=> @$torrent_info_curr_explain['finished'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_FROMTHANKS'=> @$torrent_info_curr_explain['fromthanks'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_TOTHANKS'	=> @$torrent_info_curr_explain['tothanks'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_HISTORY'	=> @$torrent_info_curr_explain['history'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_LEAVE'	=> @$torrent_info_curr_explain['leave'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_SEED'	=> @$torrent_info_curr_explain['seed'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_LEECH'	=> @$torrent_info_curr_explain['leech'] ? true : false,
	'S_HAS_TORRENT_EXPLAIN_DOWNLOADS'	=> @$torrent_info_curr_explain['downloads'] ? true : false,
	'TORRENT_INFO_OPT' => sizeof($torrent_info) ? implode(' : ', $torrent_info) : '',
	'S_IS_ADMOD' => $is_admod ? true : false,

	'S_TORRENTS' => true,
	'S_HIDDEN_FELDS' => '<input type="hidden" name="opt" value="'.$opt.'" />',
	'S_DEL_USER_TORRENTS_WARN' => isset($user->lang['DEL_USER_TORRENTS_WARN_'.strtoupper($opt)]) ? $user->lang['DEL_USER_TORRENTS_WARN_'.strtoupper($opt)] : '',

	'S_MUA_COUNTLIST_DEFAULT' => $config['ppkbb_mua_countlist'][0],
	'S_MUA_COUNTLIST' => implode(', ', $config['ppkbb_mua_countlist']),
));


if($assign_vars)
{
	foreach($assign_vars as $k2 => $v2)
	{
		$template->assign_block_vars($torrent_info_curr.'_option', $v2);
	}
}

if($postrow_headers)
{
	foreach($postrow_headers as $k2 => $v2)
	{
		$template->assign_block_vars('headers', array('VALUE' => $v2));
	}
}

if(sizeof($postrow_header))
{
	$template->assign_var('S_TORRENT_FOOTER', implode(' : ', $postrow_header));

}
?>
