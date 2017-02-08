<?php
/**
*
* @package ppkBB3cker
* @version $Id: viewtopic_add1_finished.php 1.000 2009-02-07 11:28:00 PPK $
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

$sort_opt[$opt]=array('username', 'uploaded', 'downloaded', 'seeder', 'ratio', 'downtime');
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
else if($opt_param=='username')
{
	$sql_addon="u.$opt_param $opt_sort";
}
else if($opt_param=='downtime')
{
	$sql_addon="s.completedat-s.startdat $opt_sort";
}
else
{
	$sql_addon="s.$opt_param $opt_sort";
}
*/

$sql='SELECT s.*, u.username, u.user_id, u.user_colour, p.seeder FROM '. TRACKER_SNATCHED_TABLE ." s LEFT JOIN ". USERS_TABLE ." u ON (s.userid=u.user_id) LEFT JOIN ".TRACKER_PEERS_TABLE." p ON (s.torrent=p.torrent AND s.userid=p.userid) WHERE s.torrent='{$torrent_id}' AND s.finished!='0'".($mua_limit ? " LIMIT {$mua_limit}" : '');// ORDER BY $sql_addon";
$result=$db->sql_query($sql);
while($userlist=$db->sql_fetchrow($result))
{
	if($userlist['guests'])
	{
		$userlist['user_id']=1;
		$userlist['username']=$user->lang['GUEST'];
	}
	$i3+=1;

	$assign_vars[$i3]['TORRENT_USER'] = empty($userlist['username']) ? (empty($userlist['username']) ? $user->lang['USER_DELETED'] : '<img src="'.$phpbb_root_path.'images/tracker/overview.png" align="absmiddle" alt="" />') : str_replace('../', './', get_username_string('full', $userlist['user_id'], $userlist['username'], $userlist['user_colour'], $userlist['username']));

	$assign_vars[$i3]['TORRENT_CUSER'] = $userlist['username'];

	$assign_vars[$i3]['TORRENT_UP'] = get_formatted_filesize($userlist['uploaded']);
	$assign_vars[$i3]['TORRENT_BUP'] = $userlist['uploaded'];

	$assign_vars[$i3]['TORRENT_DOWN'] = get_formatted_filesize($userlist['downloaded']);
	$assign_vars[$i3]['TORRENT_BDOWN'] = $userlist['downloaded'];

	$assign_vars[$i3]['TORRENT_NOWSEED'] = $userlist['seeder'] ? $user->lang['TORRENT_INFO_HEADER_NOWSEED_YES'] : $user->lang['TORRENT_INFO_HEADER_NOWSEED_NO'];

	$assign_vars[$i3]['TORRENT_RATIO'] = get_ratio_alias(get_ratio($userlist['uploaded'], $userlist['downloaded']));

	$assign_vars[$i3]['TORRENT_DOWNTIME'] = $td->spelldelta($userlist['startdat'], $userlist['completedat']);
	$assign_vars[$i3]['TORRENT_SDOWNTIME'] = $userlist['completedat']-$userlist['startdat'];
}
$db->sql_freeresult($result);

if($config['ppkbb_tcenable_rannounces'][0])
{
	$template->assign_vars(array(
		'S_REMCOMPLETED' => isset($torrents[$torrent_id]['rem_times_completed']) ? sprintf($user->lang['TRACKER_REMCOMPLETED'], $torrents[$torrent_id]['rem_times_completed']) : false,
		)
	);
}
$assigned_vars=sizeof($assign_vars);
//$opt_sort=$opt_sort=='DESC' ? 'ASC' : 'DESC';
foreach($sort_opt[$opt] as $k => $v)
{
	$v=strtoupper($v);
	$postrow_headers[]=isset($user->lang['TORRENT_INFO_HEADER_'.$v]) ? $user->lang['TORRENT_INFO_HEADER_'.$v] : 'TORRENT_INFO_HEADER_'.$v;
	$postrow_header[]='<a href="javascript:;" onclick="fnShowHide('.$k.');">'.(isset($user->lang['TORRENT_INFO_HEADER_'.$v]) ? $user->lang['TORRENT_INFO_HEADER_'.$v] : 'TORRENT_INFO_HEADER_'.$v).'</a>';
}
$torrent_info_curr=$opt;
//$torrent_info[$opt]='<b>'.$torrent_info[$opt].'</b> ('.($i3).')';
?>
