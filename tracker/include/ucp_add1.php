<?php
/**
*
* @package ppkBB3cker
* @version $Id: ucp_add1.php 1.000 2009-05-11 14:36:00 PPK $
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

$torrent_info_curr='';

$torrent_info_curr_explain=$assign_vars=$torrent_info=$sort_opt=$postrow_headers=$postrow_header=array();
$i3=0;
$user_id=$user->data['user_id'];
$is_admod=$auth->acl_gets('a_', 'm_') || $auth->acl_getf_global('m_') ? 1 : 0;
$dt=time();

$torrent_opt=$opt=request_var('opt', '');
$opt_sort=request_var('opts', '');
$opt_param=request_var('optp', '');
$opt_sort=='ASC' ? '' : $opt_sort='DESC';

$mua_url=$ucp_url=append_sid("ucp.$phpEx", "i=$id&amp;mode=torrents");

$torrents_info=array('torrent', 'finished', 'seed', 'leech', 'tothanks', 'fromthanks', 'history', 'leave', '', 'downloads');
foreach($torrents_info as $iv)
{
	if($iv)
	{
		if((in_array($iv, array('tothanks', 'fromthanks')) && !$config['ppkbb_thanks_enable']))
		{
			in_array($torrent_opt, array('tothanks', 'fromthanks')) && !$config['ppkbb_thanks_enable'] ? $torrent_opt='' : '';
			continue;
		}
		$torrent_info[$iv]='<a href="'.$ucp_url.($torrent_opt!=$iv ? '&amp;opt='.$iv.'#opt' : '').'">'.$user->lang['TORRENT_INFO_HEADER_'.strtoupper($iv)].'</a>';
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
$mua_add1inc=$phpbb_root_path.'tracker/include/';
$mua='ucp';
$ex_fid_ary=array_keys($auth->acl_getf('!f_read', true));
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

$torrent_info_curr_explain[$opt]=true;
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
	'S_IS_ADMOD' => $is_admod ? true : false,
	'S_FROM_UCP' => true,

	'TORRENT_INFO_OPT' => sizeof($torrent_info) ? implode(' : ', $torrent_info) : '',
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
