<?php
/**
*
* @package ppkBB3cker
* @version $Id: viewtopic_add1_filelist.php 1.000 2009-02-07 11:29:00 PPK $
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

$sort_opt[$opt]=array('filename', 'size');
/*if(!in_array($opt_param, $sort_opt[$opt]))
{
	$opt_param=$sort_opt[$opt][0];
}


$sql_addon="$opt_param $opt_sort";
*/

$sql='SELECT * FROM '. TRACKER_FILES_TABLE ." WHERE id='{$torrent_id}'".($mua_limit ? " LIMIT {$mua_limit}" : '');// ORDER BY $sql_addon";
$result=$db->sql_query($sql);
while($filelist=$db->sql_fetchrow($result))
{
	$i3+=1;

	$assign_vars[$i3]['TORRENT_FILE'] = htmlspecialchars($filelist['filename']);

	$assign_vars[$i3]['TORRENT_SIZE'] = get_formatted_filesize($filelist['size']);
	$assign_vars[$i3]['TORRENT_BSIZE'] = $filelist['size'];
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
