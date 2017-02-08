<?php
/**
*
* @package ppkBB3cker
* @version $Id: viewtopic_add1_download.php 1.000 2013-01-16 10:47:41 PPK $
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

$sort_opt[$opt]=array('username', 'uploaded', 'downloaded', 'to_go', 'startdat', 'last_action', 'completedat', 'ratio', 'dl_time');
if(!$is_admod)
{
	$sort_opt[$opt]=array('username', 'uploaded', 'downloaded', 'to_go', 'ratio', 'dl_time');
}
/*if(!in_array($opt_param, $sort_opt[$opt]))
{
	$opt_param=$sort_opt[$opt][0];
}


if($opt_param=='ratio')
{
	$sql_addon="s.uploaded/s.downloaded $opt_sort";
}
else if($opt_param=='username')
{
	$sql_addon="u.$opt_param $opt_sort";
}
else if($opt_param=='dl_time')
{
	$sql_addon="t.$opt_param $opt_sort";
}
else
{
	$sql_addon="s.$opt_param $opt_sort";
}
*/

$sql="SELECT d.dl_time, d.downloader_id, d.guests guests2, u.username, u.user_id, u.user_colour, tt.topic_title, tt.topic_first_post_id, s.* FROM ".TRACKER_DOWNLOADS_TABLE." d LEFT JOIN ".USERS_TABLE." u ON (d.downloader_id=u.user_id) LEFT JOIN ".ATTACHMENTS_TABLE." a ON (d.attach_id=a.attach_id) LEFT JOIN ".TOPICS_TABLE." tt ON (a.topic_id=tt.topic_id) LEFT JOIN ".TRACKER_SNATCHED_TABLE." s ON (d.downloader_id=s.userid AND d.attach_id=s.torrent) WHERE d.post_msg_id='{$post_id}'".($mua_limit ? " LIMIT {$mua_limit}" : '');// GROUP BY downloader_id";// ORDER BY $sql_addon";
$result=$db->sql_query($sql);
while($userlist=$db->sql_fetchrow($result))
{
	if($userlist['guests2'])
	{
		$userlist['user_id']=1;
		$userlist['username']=$user->lang['GUEST'];
	}
	$i3+=1;
	if(empty($userlist['username']) && $userlist['user_id']!=1)
	{
		$torrent_user=$user->lang['USER_DELETED'];
	}
	else
	{

			$torrent_user=($userlist['user_id']!=1 ? str_replace('../', './', get_username_string('full', $userlist['user_id'], $userlist['username'], $userlist['user_colour'], $userlist['username'])) : $userlist['username']);

	}
	$assign_vars[$i3]['TORRENT_USER'] = $torrent_user;
	$assign_vars[$i3]['TORRENT_CUSER'] = $userlist['username'];

	$assign_vars[$i3]['TORRENT_UP'] = get_formatted_filesize($userlist['uploaded']);
	$assign_vars[$i3]['TORRENT_BUP'] = $userlist['uploaded'];

	$assign_vars[$i3]['TORRENT_DOWN'] = get_formatted_filesize($userlist['downloaded']);
	$assign_vars[$i3]['TORRENT_BDOWN'] = $userlist['downloaded'];

	$assign_vars[$i3]['TORRENT_COMPLETED'] = my_float_val(($userlist['to_go']!==null ? (100 * (1 - ($userlist['to_go'] / $torrents[$torrent_id]['size']))) : 0), 2);

	$assign_vars[$i3]['TORRENT_START'] =  $userlist['startdat'] ? $user->format_date($userlist['startdat'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SSTART'] =  $userlist['startdat'];

	$assign_vars[$i3]['TORRENT_LAST'] =  $userlist['last_action'] ? $user->format_date($userlist['last_action'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SLAST'] =  $userlist['last_action'];

	$assign_vars[$i3]['TORRENT_END'] =  $userlist['completedat'] ? $user->format_date($userlist['completedat'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SEND'] =  $userlist['completedat'];

	$assign_vars[$i3]['TORRENT_RATIO'] = get_ratio_alias(get_ratio($userlist['uploaded'], $userlist['downloaded']));

	$assign_vars[$i3]['TORRENT_DOWNLOADED'] =  $userlist['dl_time'] ? $user->format_date($userlist['dl_time'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SDOWNLOADED'] =  $userlist['dl_time'];


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
//$torrent_info[$opt]='<b>'.$torrent_info[$opt].'</b> ('.$i3.')';
?>
