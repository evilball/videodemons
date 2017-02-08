<?php
/**
*
* @package ppkBB3cker
* @version $Id: mua_add1_downloads.php 1.000 2013-01-16 12:49:50 PPK $
* @copyright (c) 2013 PPK
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
$sort_opt[$opt]=array('username', 'topic_title', 'uploaded', 'downloaded', 'to_go', 'startdat', 'last_action', 'completedat', 'ratio', 'dl_time');
if(!$is_admod && $mua!='ucp')
{
	$sort_opt[$opt]=array('username', 'topic_title', 'uploaded', 'downloaded', 'to_go', 'ratio', 'dl_time');
}
/*if(!in_array($opt_param, $sort_opt[$opt]))
{
	$opt_param=$sort_opt[$opt][0];
}


if($opt_param=='ratio')
{
	$sql_addon="s.uploaded/s.downloaded $opt_sort";
}
else if($opt_param=='dl_time')
{
	$sql_addon="t.$opt_param $opt_sort";
}
else
{
	$sql_addon="$opt_param $opt_sort";
}
*/

$sql="SELECT d.id dl_id, d.dl_time, u.username, u.user_id, u.user_colour, tt.forum_id, tt.topic_id, tt.topic_title, tt.topic_first_post_id, s.*, ttt.size, ttt.unreg FROM ".TRACKER_DOWNLOADS_TABLE." d LEFT JOIN ".ATTACHMENTS_TABLE." a ON (d.attach_id=a.attach_id) LEFT JOIN ".TOPICS_TABLE." tt ON (d.post_msg_id=tt.topic_first_post_id) LEFT JOIN ".TRACKER_SNATCHED_TABLE." s ON (d.downloader_id=s.userid AND d.attach_id=s.torrent) LEFT JOIN ".TRACKER_TORRENTS_TABLE." ttt ON (d.attach_id=ttt.id) LEFT JOIN ".USERS_TABLE." u ON (tt.topic_poster=u.user_id) WHERE d.downloader_id='{$mua_user_id}'".(sizeof($ex_fid_ary) ? " AND ttt.forum_id NOT IN('".implode("', '", $ex_fid_ary)."')" : '').($mua_limit ? " LIMIT {$mua_limit}" : '');//." GROUP BY d.post_msg_id";// ORDER BY $sql_addon";
$result=$db->sql_query($sql);
while($userlist=$db->sql_fetchrow($result))
{
	if($userlist['guests'])
	{
		$userlist['user_id']=1;
		$userlist['username']=$user->lang['GUEST'];
	}
	$i3+=1;

	$assign_vars[$i3]['TORRENT_USER'] = !empty($userlist['username']) ? str_replace('../', './', get_username_string('full', $userlist['user_id'], $userlist['username'], $userlist['user_colour'], $userlist['username'])) : $user->lang['USER_DELETED'];
	$assign_vars[$i3]['TORRENT_CUSER'] = $userlist['username'];

	$assign_vars[$i3]['TORRENT_URL'] = $userlist['topic_title'] ? append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f={$userlist['forum_id']}&amp;t={$userlist['topic_id']}") : '';

	$assign_vars[$i3]['TORRENT_NAME'] = $userlist['topic_title'] ? censor_text($userlist['topic_title']) : ($userlist['unreg'] ? $user->lang['TORRENT_UNREGISTERED'] : $user->lang['TORRENT_DELETED']);
	$assign_vars[$i3]['TORRENT_CNAME'] = $userlist['topic_title'];

	$assign_vars[$i3]['TORRENT_UP'] = get_formatted_filesize($userlist['uploaded']);
	$assign_vars[$i3]['TORRENT_BUP'] = $userlist['uploaded'];

	$assign_vars[$i3]['TORRENT_DOWN'] = get_formatted_filesize($userlist['downloaded']);
	$assign_vars[$i3]['TORRENT_BDOWN'] = $userlist['downloaded'];

	$assign_vars[$i3]['TORRENT_COMPLETED'] = $userlist['unreg'] || !$userlist['size'] ? $user->lang['TORRENT_UNKNOWN_SIZE'] : my_float_val(($userlist['to_go']!==null ? (100 * (1 - ($userlist['to_go'] / $userlist['size']))) : 0), 2);

	$assign_vars[$i3]['TORRENT_START'] =  $userlist['startdat'] ? $user->format_date($userlist['startdat'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SSTART'] =  $userlist['startdat'];

	$assign_vars[$i3]['TORRENT_LAST'] =  $userlist['last_action'] ? $user->format_date($userlist['last_action'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SLAST'] =  $userlist['last_action'];

	$assign_vars[$i3]['TORRENT_END'] =  $userlist['completedat'] ? $user->format_date($userlist['completedat'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SEND'] =  $userlist['completedat'];

	$assign_vars[$i3]['TORRENT_RATIO'] = get_ratio_alias(get_ratio($userlist['uploaded'], $userlist['downloaded']));

	$assign_vars[$i3]['TORRENT_DOWNLOADED'] =  $userlist['dl_time'] ? $user->format_date($userlist['dl_time'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SDOWNLOADED'] =  $userlist['dl_time'];
	$assign_vars[$i3]['ID'] =  $userlist['dl_id'];
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
