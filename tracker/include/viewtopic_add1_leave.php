<?php
/**
*
* @package ppkBB3cker
* @version $Id: viewtopic_add1_leave.php 1.000 2009-02-07 11:20:00 PPK $
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

$sort_opt[$opt]=array('username', 'uploaded', 'downloaded', 'to_go', 'startdat', 'last_action', 'completedat', 'ratio');
if(!$is_admod)
{
	$sort_opt[$opt]=array('username', 'uploaded', 'downloaded', 'to_go', 'ratio');
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
else
{
	$sql_addon="s.$opt_param $opt_sort";
}
*/

$sql='SELECT s.*, u.username, u.user_id, u.user_colour FROM '. TRACKER_SNATCHED_TABLE .' s LEFT JOIN '.TRACKER_PEERS_TABLE.' p ON (p.userid=s.userid AND p.torrent=s.torrent) LEFT JOIN '. USERS_TABLE ." u ON (s.userid=u.user_id) WHERE s.torrent='{$torrent_id}' AND ISNULL(p.torrent) AND s.finished!='0'".($mua_limit ? " LIMIT {$mua_limit}" : '');// ORDER BY $sql_addon";
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

	$assign_vars[$i3]['TORRENT_COMPLETED'] = my_float_val(($userlist['to_go']!==null ? (100 * (1 - ($userlist['to_go'] / $torrents[$userlist['torrent']]['size']))) : 0), 2);

	$assign_vars[$i3]['TORRENT_START'] =  $userlist['startdat'] ? $user->format_date($userlist['startdat'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SSTART'] =  $userlist['startdat'];

	$assign_vars[$i3]['TORRENT_LAST'] =  $userlist['last_action'] ? $user->format_date($userlist['last_action'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SLAST'] =  $userlist['last_action'];

	$assign_vars[$i3]['TORRENT_END'] =  $userlist['completedat'] ? $user->format_date($userlist['completedat'], 'Y-m-d H:i:s') : '';
	$assign_vars[$i3]['TORRENT_SEND'] =  $userlist['completedat'];

	$assign_vars[$i3]['TORRENT_RATIO'] = get_ratio_alias(get_ratio($userlist['uploaded'], $userlist['downloaded']));
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
