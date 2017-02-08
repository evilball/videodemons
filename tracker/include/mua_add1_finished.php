<?php
/**
*
* @package ppkBB3cker
* @version $Id: mua_add1_finished.php 1.000 2010-10-29 14:13:00 PPK $
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
$sort_opt[$opt]=array('topic_title', 'uploaded', 'downloaded', 'ratio', 'seeder', 'downtime');
/*if(!in_array($opt_param, $sort_opt[$opt]))
{
	$opt_param=$sort_opt[$opt][0];
}

if($opt_param=='ratio')
{
	$sql_addon="s.uploaded/s.downloaded $opt_sort";
}
else if($opt_param=='seeder')
{
	$sql_addon="p.$opt_param $opt_sort";
}
else if($opt_param=='downtime')
{
	$sql_addon="s.completedat-s.startdat $opt_sort";
}
else
{
	$sql_addon="$opt_param $opt_sort";
}
*/

$sql='SELECT s.*, t.topic_title, t.topic_first_post_id, p.seeder, tt.unreg, tt.forum_id, tt.topic_id FROM '. TRACKER_SNATCHED_TABLE ." s LEFT JOIN ".ATTACHMENTS_TABLE." a ON (s.torrent=a.attach_id) LEFT JOIN ".TOPICS_TABLE." t ON (a.topic_id=t.topic_id) LEFT JOIN ".TRACKER_TORRENTS_TABLE." tt ON (s.torrent=tt.id) LEFT JOIN ".TRACKER_PEERS_TABLE." p ON (s.torrent=p.torrent AND s.userid=p.userid) WHERE s.userid='{$mua_user_id}' AND s.guests='0' AND s.finished!='0'".(sizeof($ex_fid_ary) ? " AND tt.forum_id NOT IN('".implode("', '", $ex_fid_ary)."')" : '').($mua_limit ? " LIMIT {$mua_limit}" : '');// ORDER BY $sql_addon";
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
	$assign_vars[$i3]['TORRENT_NOWSEED'] = $userlist['seeder'] ? $user->lang['TORRENT_INFO_HEADER_NOWSEED_YES'] : $user->lang['TORRENT_INFO_HEADER_NOWSEED_NO'];
	$assign_vars[$i3]['TORRENT_DOWNTIME'] = $td->spelldelta($userlist['startdat'], $userlist['completedat']);
	$assign_vars[$i3]['TORRENT_SDOWNTIME'] = $userlist['completedat']-$userlist['startdat'];
	$assign_vars[$i3]['ID'] =  $userlist['id'];
}
$db->sql_freeresult($result);
$assigned_vars=sizeof($assign_vars);
//$opt_sort=$opt_sort=='DESC' ? 'ASC' : 'DESC';
foreach($sort_opt[$opt] as $k => $v)
{
	$v=='seeder' && $mua=='ucp' ? $v='seeder2' : '';
	$v=strtoupper($v);
	$postrow_headers[]=isset($user->lang['TORRENT_INFO_HEADER_'.$v]) ? $user->lang['TORRENT_INFO_HEADER_'.$v] : 'TORRENT_INFO_HEADER_'.$v;
	$postrow_header[]='<a href="javascript:;" onclick="fnShowHide('.$k.');">'.(isset($user->lang['TORRENT_INFO_HEADER_'.$v]) ? $user->lang['TORRENT_INFO_HEADER_'.$v] : 'TORRENT_INFO_HEADER_'.$v).'</a>';
}
$torrent_info_curr=$opt;
//$torrent_info[$opt]='<b>'.$torrent_info[$opt].'</b> ('.$i3.')';
?>
