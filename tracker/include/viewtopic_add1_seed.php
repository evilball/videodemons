<?php
/**
*
* @package ppkBB3cker
* @version $Id: viewtopic_add1_seed.php 1.000 2009-02-07 11:27:00 PPK $
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

$sort_opt[$opt]=array('username', 'uploaded', 'upspeed', 'downloaded', 'downspeed', 'ratio', 'startdat', 'to_go', 'connectable', 'ip', 'port', 'agent');
if(!$is_admod)
{
	$sort_opt[$opt]=array('username', 'uploaded', 'upspeed', 'downloaded', 'downspeed', 'ratio', 'to_go');
}
/*if(!in_array($opt_param, $sort_opt[$opt]))
{
	$opt_param=$sort_opt[$opt][0];
}


if($opt_param=='ratio')
{
	$sql_addon="p.uploaded/p.downloaded $opt_sort";
}
else if($opt_param=='username')
{
	$sql_addon="u.$opt_param $opt_sort";
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
	$sql_addon="p.$opt_param $opt_sort";
}
*/

$sql='SELECT p.*, INET_NTOA(p.ip) ip, u.username, u.user_id, u.user_colour, s.prev_action, s.uploadoffset, s.downloadoffset FROM '. TRACKER_PEERS_TABLE ." p LEFT JOIN ". USERS_TABLE ." u ON (p.userid=u.user_id) LEFT JOIN ".TRACKER_SNATCHED_TABLE." s ON (s.torrent=p.torrent AND s.userid=p.userid) WHERE p.torrent='{$torrent_id}' AND p.seeder='1'".($mua_limit ? " LIMIT {$mua_limit}" : '');// ORDER BY $sql_addon";
$result=$db->sql_query($sql);
while($userlist=$db->sql_fetchrow($result))
{
	if($userlist['guests'])
	{
		$userlist['user_id']=1;
		$userlist['username']=$user->lang['GUEST'];
	}
	if(($userlist['userid']!=$user->data['user_id'] && !$is_admod) || !$user->data['is_registered'])
	{
		$userlist['ip']='';
		$userlist['port']='';
	}
	$i3+=1;
	$secs = max(10, ($userlist['last_action']) - $userlist['prev_action']);

	$assign_vars[$i3]['TORRENT_USER'] = empty($userlist['username']) ? (empty($userlist['username']) ? $user->lang['USER_DELETED'] : '<img src="'.$phpbb_root_path.'images/tracker/overview.png" align="absmiddle" alt="" />') : str_replace('../', './', get_username_string('full', $userlist['user_id'], $userlist['username'], $userlist['user_colour'], $userlist['username']));
	$assign_vars[$i3]['TORRENT_CUSER'] = $userlist['username'];

	$assign_vars[$i3]['TORRENT_UP'] = get_formatted_filesize($userlist['uploaded']);
	$assign_vars[$i3]['TORRENT_BUP'] = $userlist['uploaded'];

	$assign_vars[$i3]['TORRENT_UPSP'] = get_formatted_filesize($userlist['uploadoffset'] / $secs, 1, false, 1);
	$assign_vars[$i3]['TORRENT_BUPSP'] = $userlist['uploadoffset'] / $secs;

	$assign_vars[$i3]['TORRENT_DOWN'] = get_formatted_filesize($userlist['downloaded']);
	$assign_vars[$i3]['TORRENT_BDOWN'] = $userlist['downloaded'];

	$assign_vars[$i3]['TORRENT_DOWNSP'] = get_formatted_filesize($userlist['downloadoffset'] / $secs, 1, false,  1);
	$assign_vars[$i3]['TORRENT_BDOWNSP'] = $userlist['downloadoffset'] / $secs;

	$assign_vars[$i3]['TORRENT_RATIO'] = get_ratio_alias(get_ratio($userlist['uploaded'], $userlist['downloaded']));

	$assign_vars[$i3]['TORRENT_START'] = $td->spelldelta($userlist['startdat'], $dt);
	$assign_vars[$i3]['TORRENT_SSTART'] = $dt-$userlist['startdat'];

	$assign_vars[$i3]['TORRENT_COMPLETED'] = my_float_val(($userlist['to_go']!==null ? (100 * (1 - ($userlist['to_go'] / $torrents[$userlist['torrent']]['size']))) : 0), 2);

	$assign_vars[$i3]['TORRENT_UCONN'] = $config['ppkbb_tcignore_connectable']==1 ? $user->lang['TORRENT_UNKNOWN_CONNECTABLE'] : ($userlist['connectable']=='1' ? $user->lang['YES'] : $user->lang['NO']);

	$assign_vars[$i3]['TORRENT_IP'] = htmlspecialchars($userlist['ip']);
	$assign_vars[$i3]['TORRENT_NIP'] = ip2long($userlist['ip']);

	$assign_vars[$i3]['TORRENT_PORT'] = htmlspecialchars($userlist['port']);

	$assign_vars[$i3]['TORRENT_AGENT'] = htmlspecialchars($userlist['agent']);
}
$db->sql_freeresult($result);
if($config['ppkbb_tcenable_rannounces'][0])
{
	$template->assign_vars(array(
		'S_REMSEEDS' => isset($torrents[$torrent_id]['rem_seeders']) ? sprintf($user->lang['TRACKER_REMSEEDS'], $torrents[$torrent_id]['rem_seeders']) : false,
		)
	);
}
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
//$torrent_info[$opt]='<b>'.$torrent_info[$opt].'</b> ('.($i3).')';
?>
