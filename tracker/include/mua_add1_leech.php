<?php
/**
*
* @package ppkBB3cker
* @version $Id: mua_add1_leech.php 1.000 2010-10-29 14:16:00 PPK $
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
$sort_opt[$opt]=array('topic_title', 'uploaded', 'upspeed', 'downloaded', 'downspeed', 'ratio', 'startdat', 'to_go', 'connectable', 'ip', 'port', 'agent');
if(!$is_admod && $mua!='ucp')
{
	$sort_opt[$opt]=array('topic_title', 'uploaded', 'upspeed', 'downloaded', 'downspeed', 'ratio', 'to_go');
}
/*if(!in_array($opt_param, $sort_opt[$opt]))
{
	$opt_param=$sort_opt[$opt][0];
}


if($opt_param=='ratio')
{
	$sql_addon="p.uploaded/p.downloaded $opt_sort";
}
else if($opt_param=='upspeed')
{
	$sql_addon="s.uploadoffset/(s.last_action-s.prev_action) $opt_sort";
}
else if($opt_param=='downspeed')
{
	$sql_addon="s.downloadoffset/(s.last_action-s.prev_action) $opt_sort";
}
else
{
	$sql_addon="$opt_param $opt_sort";
}
*/

$sql='SELECT p.*, INET_NTOA(p.ip) ip, t.forum_id, t.topic_id, t.topic_title, t.topic_first_post_id, tt.size, tt.unreg, s.prev_action, s.uploadoffset, s.downloadoffset FROM '. TRACKER_PEERS_TABLE .' p LEFT JOIN '.ATTACHMENTS_TABLE." a ON (p.torrent=a.attach_id) LEFT JOIN ".TOPICS_TABLE." t ON (a.topic_id=t.topic_id) LEFT JOIN ".TRACKER_TORRENTS_TABLE." tt ON (p.torrent=tt.id) LEFT JOIN ".TRACKER_SNATCHED_TABLE." s ON (s.torrent=p.torrent AND s.userid=p.userid) WHERE p.userid='{$mua_user_id}' AND p.guests='0' AND p.seeder='0'".(sizeof($ex_fid_ary) ? " AND tt.forum_id NOT IN('".implode("', '", $ex_fid_ary)."')" : '').($mua_limit ? " LIMIT {$mua_limit}" : '');// ORDER BY $sql_addon";
$result=$db->sql_query($sql);
while($userlist=$db->sql_fetchrow($result))
{
	if(($userlist['userid']!=$user->data['user_id'] && !$is_admod) || !$user->data['is_registered'])
	{
		$userlist['ip']='';
		$userlist['port']='';
	}
	$i3+=1;
	$secs = max(10, ($userlist['last_action']) - $userlist['prev_action']);

	$assign_vars[$i3]['TORRENT_URL'] = $userlist['topic_title'] ? append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f={$userlist['forum_id']}&amp;t={$userlist['topic_id']}") : '';

	$assign_vars[$i3]['TORRENT_NAME'] = $userlist['topic_title'] ? censor_text($userlist['topic_title']) : ($userlist['unreg'] ? $user->lang['TORRENT_UNREGISTERED'] : $user->lang['TORRENT_DELETED']);
	$assign_vars[$i3]['TORRENT_CNAME'] = $userlist['topic_title'];

	$assign_vars[$i3]['TORRENT_UP'] = get_formatted_filesize($userlist['uploaded']);
	$assign_vars[$i3]['TORRENT_BUP'] = $userlist['uploaded'];

	$assign_vars[$i3]['TORRENT_UPSP'] = get_formatted_filesize($userlist['uploadoffset'] / $secs, 1, false, 1);
	$assign_vars[$i3]['TORRENT_BUPSP'] = $userlist['uploadoffset'] / $secs;

	$assign_vars[$i3]['TORRENT_DOWN'] = get_formatted_filesize($userlist['downloaded']);
	$assign_vars[$i3]['TORRENT_BDOWN'] = $userlist['downloaded'];

	$assign_vars[$i3]['TORRENT_DOWNSP'] = get_formatted_filesize($userlist['downloadoffset'] / $secs, 1, false, 1);
	$assign_vars[$i3]['TORRENT_BDOWNSP'] = $userlist['downloadoffset'] / $secs;

	$assign_vars[$i3]['TORRENT_RATIO'] = get_ratio_alias(get_ratio($userlist['uploaded'], $userlist['downloaded']));

	$assign_vars[$i3]['TORRENT_START'] = $td->spelldelta($userlist['startdat'], $dt);
	$assign_vars[$i3]['TORRENT_SSTART'] = $dt-$userlist['startdat'];

	$assign_vars[$i3]['TORRENT_COMPLETED'] = $userlist['unreg'] || !$userlist['size'] ? $user->lang['TORRENT_UNKNOWN_SIZE'] : my_float_val(($userlist['to_go']!==null ? (100 * (1 - ($userlist['to_go'] / $userlist['size']))) : 0), 2);

	$assign_vars[$i3]['TORRENT_UCONN'] = $config['ppkbb_tcignore_connectable']==1 ? $user->lang['TORRENT_UNKNOWN_CONNECTABLE'] : ($userlist['connectable']=='1' ? $user->lang['YES'] : $user->lang['NO']);

	$assign_vars[$i3]['TORRENT_IP'] = htmlspecialchars($userlist['ip']);
	$assign_vars[$i3]['TORRENT_NIP'] = ip2long($userlist['ip']);

	$assign_vars[$i3]['TORRENT_PORT'] = htmlspecialchars($userlist['port']);

	$assign_vars[$i3]['TORRENT_AGENT'] = htmlspecialchars($userlist['agent']);
	$assign_vars[$i3]['ID'] =  $userlist['id'];
}
$db->sql_freeresult($result);

$assigned_vars=sizeof($assign_vars);
//$opt_sort=$opt_sort=='DESC' ? 'ASC' : 'DESC';
foreach($sort_opt[$opt] as $k => $v)
{
	$v=='startdat' ? $v='connect' : '';
	$v=strtoupper($v);
	$postrow_headers[]=isset($user->lang['TORRENT_INFO_HEADER_'.$v]) ? $user->lang['TORRENT_INFO_HEADER_'.$v] : 'TORRENT_INFO_HEADER_'.$v;
	$postrow_header[]='<a href="javascript:;" onclick="fnShowHide('.$k.');">'.(isset($user->lang['TORRENT_INFO_HEADER_'.$v]) ? $user->lang['TORRENT_INFO_HEADER_'.$v] : 'TORRENT_INFO_HEADER_'.$v).'</a>';
}
$torrent_info_curr=$opt;
//$torrent_info[$opt]='<b>'.$torrent_info[$opt].'</b> ('.$i3.')';
?>
