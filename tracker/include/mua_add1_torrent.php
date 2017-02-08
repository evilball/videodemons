<?php
/**
*
* @package ppkBB3cker
* @version $Id: mua_add1_torrent.php 1.000 2010-10-29 14:16:00 PPK $
* @copyright (c) 2010 PPK
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

$mua=='memberlist' ? $mua_user_id=$user_id : $mua_user_id=$user->data['user_id'];
$sort_opt[$opt]=array('topic_title', 'uploaded', 'downloaded', 'ratio', 'to_go', 'last_action', 'startdat', 'completedat');
if(!$is_admod && $mua!='ucp')
{
	$sort_opt[$opt]=array('topic_title', 'uploaded', 'downloaded', 'ratio', 'to_go');
}
/*if(!in_array($opt_param, $sort_opt[$opt]))
{
	$opt_param=$sort_opt[$opt][0];
}


if($opt_param=='ratio')
{
	$sql_addon="s.uploaded/s.downloaded $opt_sort";
}
else
{
	$sql_addon="$opt_param $opt_sort";
}
*/

$sql="SELECT tt.id torrent_id, tt.size, tt.unreg, t.forum_id, t.topic_id, t.topic_title, t.topic_first_post_id, s.* FROM ".TRACKER_TORRENTS_TABLE." tt LEFT JOIN ".TOPICS_TABLE." t ON (tt.poster_id=t.topic_poster AND tt.post_msg_id=t.topic_first_post_id) LEFT JOIN ".TRACKER_SNATCHED_TABLE." s ON (tt.id=s.torrent AND s.userid=tt.poster_id) WHERE tt.poster_id='{$mua_user_id}'".(sizeof($ex_fid_ary) ? " AND tt.forum_id NOT IN('".implode("', '", $ex_fid_ary)."')" : '').($mua_limit ? " LIMIT {$mua_limit}" : '');// ORDER BY $sql_addon";
$result=$db->sql_query($sql);
while($userlist=$db->sql_fetchrow($result))
{
	$i3+=1;

	$assign_vars[$i3]['TORRENT_URL'] = $userlist['topic_title'] ? append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f={$userlist['forum_id']}&amp;t={$userlist['topic_id']}") : '';

	$assign_vars[$i3]['TORRENT_NAME'] = $userlist['topic_title'] ? censor_text($userlist['topic_title']) : ($userlist['unreg'] ? $user->lang['TORRENT_UNREGISTERED'] : $user->lang['TORRENT_DELETED']);
	$assign_vars[$i3]['TORRENT_CNAME'] = $userlist['topic_title'];

	$assign_vars[$i3]['TORRENT_UP'] = get_formatted_filesize($userlist['uploaded']);
	$assign_vars[$i3]['TORRENT_BUP'] = $userlist['uploaded'];

	$assign_vars[$i3]['TORRENT_DOWN'] = get_formatted_filesize($userlist['downloaded']);
	$assign_vars[$i3]['TORRENT_BDOWN'] = $userlist['downloaded'];

	$assign_vars[$i3]['TORRENT_RATIO'] = get_ratio_alias(get_ratio($userlist['uploaded'], $userlist['downloaded']));

	$assign_vars[$i3]['TORRENT_COMPLETED'] = $userlist['unreg'] || !$userlist['size'] ? $user->lang['TORRENT_UNKNOWN_SIZE'] : my_float_val(($userlist['to_go']!==null ? (100 * (1 - ($userlist['to_go'] / $userlist['size']))) : 0), 2);

	$assign_vars[$i3]['TORRENT_LAST'] =  $userlist['last_action'] ? $user->format_date($userlist['last_action'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SLAST'] =  $userlist['last_action'];

	$assign_vars[$i3]['TORRENT_START'] =  $userlist['startdat'] ? $user->format_date($userlist['startdat'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SSTART'] =  $userlist['startdat'];

	$assign_vars[$i3]['TORRENT_END'] =  $userlist['completedat'] ? $user->format_date($userlist['completedat'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SEND'] =  $userlist['completedat'];
	$assign_vars[$i3]['ID'] =  $userlist['torrent_id'];
}
$db->sql_freeresult($result);

$assigned_vars=sizeof($assign_vars);
//$opt_sort=$opt_sort=='DESC' ? 'ASC' : 'DESC';
foreach($sort_opt[$opt] as $k => $v)
{
	$v=strtoupper($v);
	$postrow_headers[]=isset($user->lang['TORRENT_INFO_HEADER_'.$v]) ? $user->lang['TORRENT_INFO_HEADER_'.$v] : 'TORRENT_INFO_HEADER_'.$v;
	$postrow_header[]='<a href="javascript:;" onclick="fnShowHide('.$k.');">'.(isset($user->lang['TORRENT_INFO_HEADER_'.$v]) ? $user->lang['TORRENT_INFO_HEADER_'.$v] : 'TORRENT_INFO_HEADER_'.$v).'</a>';
}
$torrent_info_curr=$opt;
//$torrent_info[$opt]='<b>'.$torrent_info[$torrent_info_curr].'</b> ('.$i3.')';
?>
