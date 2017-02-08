<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_acheat.php 1.000 2009-12-23 13:04:00 PPK $
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

$acheat_title='ACP_TRACKER_ACHEAT';

$user->add_lang('mods/acp/ppkbb3cker_acheat');

$view_acheats_torr=request_var('view_acheats_torr', 0);
$view_acheats_user=request_var('view_acheats_user', 0);
$view_acheats=!$view_acheats_torr && !$view_acheats_user ? 1 : 0;


if($view_acheats_torr)
{
	$valid_sort=array('s_diff', 'uploaded', 'downloaded', 't.times_completed');
	$valid_sort2=array('t_ratio', 'u_ratio', 'u_rratio', 's.userid', 's.uploaded', 's.downloaded', 'u.username', 'u.user_bonus', 'u.user_uploaded', 'u.user_downloaded', 'u.user_shadow_downloaded');

	$diff_ml=request_var('diff_ml', 0);
	$uploaded_ml=request_var('uploaded_ml', 0);
	$downloaded_ml=request_var('downloaded_ml', 0);

	$upspeed=request_var('upspeed', 0);
	$downspeed=request_var('downspeed', 0);
	$upv=request_var('upv', 'b');
	$downv=request_var('downv', 'b');
	$uploaded=get_size_value($upv, request_var('uploaded', 0));
	$downloaded=get_size_value($downv, request_var('downloaded', 0));
	$seeders=request_var('seeders', 0);
	$leechers=request_var('leechers', 0);
	$diff=request_var('diff', '')!='' ? my_float_val(request_var('diff', '')) : '';
	$start=request_var('start', 0);
	$end=request_var('end', 0);
	$incuname=request_var('incuname', 0);
	$inctorrent=request_var('inctorrent', 0);
	$sort=request_var('sort', '');
	$order=request_var('order', 0);
	$userid=request_var('userid', '');
	$torrentid=request_var('torrentid', '');
	$end ? '' : $end=50;
	$diff ? '' : $diff=1;

	$ubonus_ml=request_var('ubonus_ml', 0);
	$uratio_ml=request_var('uratio_ml', 0);
	$urratio_ml=request_var('urratio_ml', 0);
	$tratio_ml=request_var('tratio_ml', 0);
	$uuploaded_ml=request_var('uuploaded_ml', 0);
	$udownloaded_ml=request_var('udownloaded_ml', 0);
	$urdownloaded_ml=request_var('urdownloaded_ml', 0);
	$completed_ml=request_var('completed_ml', 0);

	$sort2=request_var('sort2', '');
	$order2=request_var('order2', 0);
	$umax=request_var('umax', 0);
	$udatefrom=request_var('udatefrom', 0);
	$udateto=request_var('udateto', 0);
	$ubonus=request_var('ubonus', '')!='' ? my_float_val(request_var('ubonus', '')) : '';
	$uratio=request_var('uratio', '')!='' ? my_float_val(request_var('uratio', '')) : '';
	$urratio=request_var('urratio', '')!='' ? my_float_val(request_var('urratio', '')) : '';
	$tratio=request_var('tratio', '')!='' ? my_float_val(request_var('tratio', '')) : '';
	$uupv=request_var('uupv', 'b');
	$udownv=request_var('udownv', 'b');
	$uuploaded=get_size_value($uupv, request_var('uuploaded', 0));
	$udownloaded=get_size_value($udownv, request_var('udownloaded', 0));
	$urdownv=request_var('urdownv', 'b');
	$urdownloaded=get_size_value($urdownv, request_var('urdownloaded', 0));
	$completed=request_var('completed', 0);
	$ustats='';

	if(request_var('submit_acheats_torr', ''))
	{
		$sql_where=$sql_where2=array();
		$sql_from=$sql_select=$sql_limit=$sql_sort=$sql_sort2=$sql_limit2='';

		if($inctorrent)
		{
			$sql_from.=" LEFT JOIN ".ATTACHMENTS_TABLE." a ON (s.torrent=a.attach_id) LEFT JOIN ".POSTS_TABLE." p ON (a.post_msg_id=p.post_id)";
			$sql_select.=', p.post_subject, p.post_id';
			$valid_sort[]='p.post_subject';
			$valid_sort[]='s.torrent';
		}
		if($end && $start < $end)
		{
			$sql_limit.=" LIMIT $start, $end";
		}

		$torrentid ? $torrentids=my_split_config($torrentid, 0, 'my_int_val', ',') : $torrentids=array();
		$torrentids ? $sql_where[]="s.torrent IN('".implode("', '", $torrentids)."')" : '';

		$downloaded ? $sql_where[]="downloaded ".($downloaded_ml ? '<' : '>')." '{$downloaded}'" : '';
		$uploaded ? $sql_where[]="uploaded ".($uploaded_ml ? '<' : '>')." '{$uploaded}'" : '';
		$diff ? $sql_where[]="s_diff ".($diff_ml ? '<' : '>')." '{$diff}'" : '';

		$sort && in_array($sort, $valid_sort) ? $sql_sort.=" ORDER BY {$sort}".($order ? ' ASC' : ' DESC') : '';

		$sql="SELECT s.torrent, t.times_completed, SUM(uploaded) uploaded, SUM(downloaded) downloaded, SUM(uploaded)/SUM(downloaded) s_diff{$sql_select} FROM ".TRACKER_SNATCHED_TABLE." s LEFT JOIN ".TRACKER_TORRENTS_TABLE." t ON (s.torrent=t.id) {$sql_from}".($config['ppkbb_tcguests_enabled'][0] ? " WHERE s.guests='0'" : '')." GROUP BY s.torrent".($sql_where ? ' HAVING '.implode(' AND ', $sql_where) : '')."{$sql_sort}{$sql_limit}";
		$result=$db->sql_query($sql);
		$torrents=$assign_vars=$u_data=$users=$t_data=array();
		while($row=$db->sql_fetchrow($result))
		{
			if($incuname)
			{
				$torrents[$row['torrent']]=$row['torrent'];
			}
			if($row['s_diff'] > 10)
			{
				$row['s_diff']='<span style="color:#FF0000;">'.$row['s_diff'].'</span>';
			}
			else if($row['s_diff'] > 5)
			{
				$row['s_diff']='<span style="color:#FF4545;">'.$row['s_diff'].'</span>';
			}
			else if($row['s_diff'] > 1)
			{
				$row['s_diff']='<span style="color:#FF8585;">'.$row['s_diff'].'</span>';
			}
			$assign_vars[$row['torrent']]=array(
				'SUBJECT'	=> @$row['post_id'] ? '<a href="'.append_sid($phpbb_root_path.'viewtopic.'.$phpEx.'?p='.$row['post_id']).'">'.$row['post_subject'].'</a>' : ($inctorrent ? $user->lang['ACHEAT_TORRENT_DELETED'] : ''),
				'USERNAME'	=> @$row['user_id'] ? get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']) : '',
				'UPLOADED' => get_formatted_filesize($row['uploaded']),
				'DOWNLOADED' => get_formatted_filesize($row['downloaded']),
				'DIFF' => $row['s_diff'],
				'COMPL' => intval($row['times_completed']),
			);
			$t_data[$row['torrent']]['up']=$row['uploaded'];
			$t_data[$row['torrent']]['down']=$row['downloaded'];
		}
		$db->sql_freeresult($result);
		if($incuname && $torrents)
		{
			$userid ? $userids=my_split_config($userid, 0, 'my_int_val', ',') : $userids=array();
			$userids ? $sql_where2[]="s.userid IN('".implode("', '", $userids)."')" : '';
			$uratio ? $sql_where2[]="(u.user_uploaded/u.user_downloaded)+u.user_bonus ".($uratio_ml ? '<' : '>')." '{$uratio}'" : '';
			$urratio ? $sql_where2[]="u.user_uploaded/u.user_shadow_downloaded ".($urratio_ml ? '<' : '>')." '{$urratio}'" : '';
			$ubonus ? $sql_where2[]="u.user_bonus ".($ubonus_ml ? '<' : '>')." '{$ubonus}'" : '';
			$tratio ? $sql_where2[]="s.uploaded/s.downloaded ".($tratio_ml ? '<' : '>')." '{$tratio}'" : '';
			$uuploaded ? $sql_where2[]="u.user_uploaded ".($uuploaded_ml ? '<' : '>')." '{$uuploaded}'" : '';
			$udownloaded ? $sql_where2[]="u.user_downloaded ".($udownloaded_ml ? '<' : '>')." '{$udownloaded}'" : '';
			$urdownloaded ? $sql_where2[]="u.user_shadow_downloaded ".($urdownloaded_ml ? '<' : '>')." '{$urdownloaded}'" : '';

			if($udatefrom)
			{
				$chk_udatefrom=my_split_config($udatefrom, 3, 'my_int_val', '-');
				$chk_udatefrom=checkdate($chk_udatefrom[1], $chk_udatefrom[2], $chk_udatefrom[0]) ? mktime(0, 0, 0, $chk_udatefrom[1], $chk_udatefrom[2], $chk_udatefrom[0]) : '';
				$chk_udatefrom ? $sql_where2[]="u.user_regdate > '{$chk_udatefrom}'" : '';
			}
			if($udateto)
			{
				$chk_udateto=my_split_config($udateto, 3, 'my_int_val', '-');
				$chk_udateto=checkdate($chk_udateto[1], $chk_udateto[2], $chk_udateto[0]) ? mktime(0, 0, 0, $chk_udateto[1], $chk_udateto[2], $chk_udateto[0]) : '';
				$chk_udateto ? $sql_where2[]="u.user_regdate < '{$chk_udateto}'" : '';
			}

			$sort2 && in_array($sort2, $valid_sort2) ? $sql_sort2.=" ORDER BY {$sort2}".($order2 ? ' ASC' : ' DESC') : '';
			$config['ppkbb_tcguests_enabled'][0] ? $sql_where2[]="s.guests='0'" : '';

			$sql="SELECT s.torrent, s.userid, s.uploaded, s.downloaded, s.uploaded/s.downloaded t_ratio, (u.user_uploaded/u.user_downloaded)+u.user_bonus u_ratio, u.user_uploaded/u.user_shadow_downloaded u_rratio, u.user_id, u.username, u.user_colour, u.user_downloaded, u.user_uploaded, u.user_shadow_downloaded, u.user_bonus, u.username, u.user_colour FROM ".TRACKER_SNATCHED_TABLE." s LEFT JOIN ".USERS_TABLE." u ON (s.userid=u.user_id) WHERE s.torrent IN('".implode("', '", $torrents)."') AND (s.uploaded OR s.downloaded)".($sql_where2 ? ' AND '.implode(" AND ", $sql_where2) : '')."{$sql_sort2}";//echo $sql;
			$result=$db->sql_query($sql);
			$e_user=array();
			while($row=$db->sql_fetchrow($result))
			{
				if(!isset($assign_vars[$row['torrent']]['USERS']))
				{
					$assign_vars[$row['torrent']]['USERS']='';
				}
				$users[$row['userid']]['user_colour']=$row['user_colour'];
				$users[$row['userid']]['username']=$row['username'];

				@$u_data[$row['userid']]+=1;
				if($umax && @$e_user[$row['torrent']] >= $umax)
				{

				}
				else
				{
					if($row['user_id'])
					{
						$ct_ratio=get_ratio($row['uploaded'], $row['downloaded']);
						if(!in_array($ct_ratio, array('Inf.', 'Leech.', 'Seed.', 'None.')))
						{
							if($ct_ratio > 10)
							{
								$ct_ratio='<span style="color:#FF0000;">'.$ct_ratio.'</span>';
							}
							else if($ct_ratio > 5)
							{
								$ct_ratio='<span style="color:#FF4545;">'.$ct_ratio.'</span>';
							}
							else if($ct_ratio > 1)
							{
								$ct_ratio='<span style="color:#FF8585;">'.$ct_ratio.'</span>';
							}
						}
						if(@$t_data[$row['torrent']]['down'])
						{
							$u_pdown=my_float_val(100*$row['downloaded']/$t_data[$row['torrent']]['down']);
							/*if($u_pdown > 90)
							{
								$u_pdown='<span style="color:#FF0000;">'.$u_pdown.'</span>';
							}
							else if($u_pdown > 60)
							{
								$u_pdown='<span style="color:#FF4545;">'.$u_pdown.'</span>';
							}
							else if($u_pdown > 30)
							{
								$u_pdown='<span style="color:#FF8585;">'.$u_pdown.'</span>';
							}*/
						}
						if(@$t_data[$row['torrent']]['up'])
						{
							$u_pup=my_float_val(100*$row['uploaded']/$t_data[$row['torrent']]['up']);
							if($u_pup > 90)
							{
								$u_pup='<span style="color:#FF0000;">'.$u_pup.'</span>';
							}
							else if($u_pup > 60)
							{
								$u_pup='<span style="color:#FF4545;">'.$u_pup.'</span>';
							}
							else if($u_pup > 30)
							{
								$u_pup='<span style="color:#FF8585;">'.$u_pup.'</span>';
							}
						}
						$assign_vars[$row['torrent']]['USERS'].='
						<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td>'.get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']).'</td>
							<td>UBA: <b>'.$row['user_bonus'].'</td>';
						$assign_vars[$row['torrent']]['USERS'].='
							<td width="15%">TR: <b>'.$ct_ratio.'</b></td>
							<td width="15%">TUP: <b>'.get_formatted_filesize($row['uploaded']).'</b></td>
							<td width="15%">TDL: <b>'.get_formatted_filesize($row['downloaded']).'</b></td>';
						$assign_vars[$row['torrent']]['USERS'].='
							<td width="15%">UDL: <b>'.get_formatted_filesize($row['user_downloaded']).'</b></td>
							<td width="15%">UUP: <b>'.get_formatted_filesize($row['user_uploaded']).'</b></td>
						</tr>
						<tr>
							<td width="15%">URR: <b>'.get_ratio($row['user_uploaded'], $row['user_shadow_downloaded']).'</b></td>';
						$assign_vars[$row['torrent']]['USERS'].='
							<td>&nbsp;</td>
							<td>UR: <b>'.get_ratio($row['user_uploaded'], $row['user_downloaded'], $row['user_bonus']).'</b></td>
							<td>PRUP: <b>'.(@$t_data[$row['torrent']]['up'] ? $u_pup.'%' : '0%').'</b></td>';
						$assign_vars[$row['torrent']]['USERS'].='
							<td>PRDL: <b>'.(@$t_data[$row['torrent']]['down'] ? $u_pdown.'%' : '0%').'</b></td>
							<td>URDL: <b>'.get_formatted_filesize($row['user_shadow_downloaded']).'</b></td>
							<td>&nbsp;
							</td>
						</tr>
						</table>';
						@$e_user[$row['torrent']]+=1;
					}
				}
			}
			$db->sql_freeresult($result);
		}
		if($assign_vars)
		{
			foreach($assign_vars as $v)
			{
				$template->assign_block_vars('acheats', $v);
			}
		}//var_dump($users);
		if($u_data && $assign_vars)
		{
			$t_count=sizeof($assign_vars);
			foreach($u_data as $uk => $uv)
			{
				$u_percent=my_int_val($uv*100/$t_count);
				$ustats.='<div style="float:left;width:'.$u_percent.'px;height:12px;background-color:#FF0000;"></div>&nbsp;PRUT: <b>'.$u_percent.'%</b>&nbsp;'.(@$users[$uk] ? get_username_string('full', $uk, $users[$uk]['username'], $users[$uk]['user_colour']) : '').'<br />';
			}
		}
		$template->assign_vars(array(
			'S_VIEW_ACHEATS_TORR'	=> true,
			'S_HIDDEN_FIELDS'=>'<input type="hidden" name="view_acheats_torr" value="1" >',
			)
		);
	}

	$acheat_sort='<select name="sort">';
	foreach($valid_sort as $s)
	{
		$acheat_sort.='<option value="'.$s.'"'.($s==$sort ? ' selected="selected"' : '').'>'.@$user->lang['ACHEAT_SORTS_TORR'][$s].'</option>';
	}
	$acheat_sort.='</select>';

	$acheat_sort2='<select name="sort2">';
	foreach($valid_sort2 as $s)
	{
		$acheat_sort2.='<option value="'.$s.'"'.($s==$sort2 ? ' selected="selected"' : '').'>'.$user->lang['ACHEAT_SORTS_TORR'][$s].'</option>';
	}
	$acheat_sort2.='</select>';

	$template->assign_vars(array(
		'S_ACHEAT_INC'	=> true,
		'S_VIEW_ACHEATS_TORR' => true,
		'ACHEAT_SORT' => $acheat_sort,
		'ACHEAT_SORT2' => $acheat_sort2,
		'SUBJECT_USERNAME' => $incuname || $inctorrent ? true : false,
		'TORRENTID' => $torrentid,
		'USERID' => $userid,
		'UPSPEED' => $upspeed,
		'DOWNSPEED' => $downspeed,
		'UPLOADED' => $uploaded,
		'DOWNLOADED' => $downloaded,
		'UPLOADEDV' => get_formatted_filesize($uploaded),
		'DOWNLOADEDV' => get_formatted_filesize($downloaded),
		'UPV' => select_size_value(),
		'DOWNV' => select_size_value(),
		'SEEDERS' => $seeders,
		'LEECHERS' => $leechers,
		//'LEFT' => $left,
		//'EVENT' => $event,
		'START' => $start,
		'END' => $end,
		'DIFF' => $diff,
		'ORDER' => build_sort_form($order),
		'INCUNAME' => $incuname ? ' checked="checked"' : '',
		'INCTORRENT' => $inctorrent ? ' checked="checked"' : '',
		'UPLOADED_ML' => build_ml_form($uploaded_ml),
		'DOWNLOADED_ML' => build_ml_form($downloaded_ml),
		'DIFF_ML' => build_ml_form($diff_ml),
		'S_HIDDEN_FIELDS'=>'<input type="hidden" name="view_acheats_torr" value="1" >',

		'ORDER2' => build_sort_form($order2),
		'UMAX' => $umax ? $umax : 10,
		'URATIO' => $uratio,
		'URRATIO' => $urratio,
		'UBONUS' => $ubonus,
		'TRATIO' => $tratio,
		'UDATEFROM' => $udatefrom,
		'UDATETO' => $udateto,
		'COMPLETED' => $completed,

		'UUPLOADED_ML' => build_ml_form($uuploaded_ml),
		'UDOWNLOADED_ML' => build_ml_form($udownloaded_ml),
		'UUPLOADED' => $uuploaded,
		'UDOWNLOADED' => $udownloaded,
		'UUPLOADEDV' => get_formatted_filesize($uuploaded),
		'UDOWNLOADEDV' => get_formatted_filesize($udownloaded),
		'UUPV' => select_size_value(),
		'UDOWNV' => select_size_value(),

		'URDOWNLOADED_ML' => build_ml_form($urdownloaded_ml),
		'URDOWNLOADED' => $urdownloaded,
		'URDOWNLOADEDV' => get_formatted_filesize($urdownloaded),
		'URDOWNV' => select_size_value(),

		'URATIO_ML' => build_ml_form($uratio_ml),
		'URRATIO_ML' => build_ml_form($urratio_ml),
		'UBONUS_ML' => build_ml_form($ubonus_ml),
		'TRATIO_ML' => build_ml_form($tratio_ml),
		'COMPLETED_ML' => build_ml_form($completed_ml),

		'ACHEAT_USTATS' => $ustats,
		)
	);
}
else if($view_acheats_user)
{
	$valid_sort=array('u_ratio', 'u_rratio', 'u.username', 'u.user_bonus', 'u.user_uploaded', 'u.user_downloaded', 'u.user_shadow_downloaded');
	$valid_sort2=array('t_ratio', 't.times_completed', 'torrent', 'uploaded', 'downloaded');

	$ubonus_ml=request_var('ubonus_ml', 0);
	$uratio_ml=request_var('uratio_ml', 0);
	$urratio_ml=request_var('urratio_ml', 0);
	$uuploaded_ml=request_var('uuploaded_ml', 0);
	$udownloaded_ml=request_var('udownloaded_ml', 0);
	$urdownloaded_ml=request_var('urdownloaded_ml', 0);
	$udatefrom=request_var('udatefrom', '');
	$udateto=request_var('udateto', '');
	$start=request_var('start', 0);
	$end=request_var('end', 0);
	$userid=request_var('userid', '');
	$sort=request_var('sort', '');
	$order=request_var('order', 0);

	$tratio_ml=request_var('tratio_ml', 0);
	$diff_ml=request_var('diff_ml', 0);
	$uploaded_ml=request_var('uploaded_ml', 0);
	$downloaded_ml=request_var('downloaded_ml', 0);
	$upspeed=request_var('upspeed', 0);
	$downspeed=request_var('downspeed', 0);
	$upv=request_var('upv', 'b');
	$downv=request_var('downv', 'b');
	$uploaded=get_size_value($upv, request_var('uploaded', 0));
	$downloaded=get_size_value($downv, request_var('downloaded', 0));
	$seeders=request_var('seeders', 0);
	$leechers=request_var('leechers', 0);
	$diff=request_var('diff', '')!='' ? my_float_val(request_var('diff', '')) : '';

	$incuname=request_var('incuname', 0);
	$inctorrent=request_var('inctorrent', 0);

	$torrentid=request_var('torrentid', '');
	$end ? '' : $end=50;
	$diff ? '' : $diff=1;

	$completed_ml=request_var('completed_ml', 0);

	$sort2=request_var('sort2', '');
	$order2=request_var('order2', 0);
	$tmax=request_var('tmax', 0);
	$ubonus=request_var('ubonus', '')!='' ? my_float_val(request_var('ubonus', '')) : '';
	$uratio=request_var('uratio', '')!='' ? my_float_val(request_var('uratio', '')) : '';
	$urratio=request_var('urratio', '')!='' ? my_float_val(request_var('urratio', '')) : '';
	$tratio=request_var('tratio', '')!='' ? my_float_val(request_var('tratio', '')) : '';

	$uupv=request_var('uupv', 'b');
	$udownv=request_var('udownv', 'b');
	$uuploaded=get_size_value($uupv, request_var('uuploaded', 0));
	$udownloaded=get_size_value($udownv, request_var('udownloaded', 0));
	$urdownv=request_var('urdownv', 'b');
	$urdownloaded=get_size_value($urdownv, request_var('urdownloaded', 0));
	$completed=request_var('completed', 0);
	$ucompleted=request_var('ucompleted', '');
	$ustats='';

	if(request_var('submit_acheats_user', ''))
	{
		$sql_where=$sql_where2=array();
		$sql_from=$sql_select=$sql_limit=$sql_sort=$sql_sort2=$sql_limit2='';
		$torrents=$assign_vars=$u_data=$users=$t_data=array();

		if($end && $start < $end)
		{
			$sql_limit.=" LIMIT $start, $end";
		}

		$userid ? $userids=my_split_config($userid, 0, 'my_int_val', ',') : $userids=array();
		$userids ? $sql_where[]="u.user_id IN('".implode("', '", $userids)."')" : '';

		$uratio ? $sql_where[]="(u.user_uploaded/u.user_downloaded)+u.user_bonus ".($uratio_ml ? '<' : '>')." '{$uratio}'" : '';
		$urratio ? $sql_where[]="u.user_uploaded/u.user_shadow_downloaded ".($urratio_ml ? '<' : '>')." '{$urratio}'" : '';
		$ubonus ? $sql_where[]="u.user_bonus ".($ubonus_ml ? '<' : '>')." '{$ubonus}'" : '';
		$uuploaded ? $sql_where[]="u.user_uploaded ".($uuploaded_ml ? '<' : '>')." '{$uuploaded}'" : '';
		$udownloaded ? $sql_where[]="u.user_downloaded ".($udownloaded_ml ? '<' : '>')." '{$udownloaded}'" : '';
		$urdownloaded ? $sql_where[]="u.user_shadow_downloaded ".($urdownloaded_ml ? '<' : '>')." '{$urdownloaded}'" : '';

		if($udatefrom)
		{
			$chk_udatefrom=my_split_config($udatefrom, 3, 'my_int_val', '-');
			$chk_udatefrom=checkdate($chk_udatefrom[1], $chk_udatefrom[2], $chk_udatefrom[0]) ? mktime(0, 0, 0, $chk_udatefrom[1], $chk_udatefrom[2], $chk_udatefrom[0]) : '';
			$chk_udatefrom ? $sql_where[]="u.user_regdate > '{$chk_udatefrom}'" : '';
		}
		if($udateto)
		{
			$chk_udateto=my_split_config($udateto, 3, 'my_int_val', '-');
			$chk_udateto=checkdate($chk_udateto[1], $chk_udateto[2], $chk_udateto[0]) ? mktime(0, 0, 0, $chk_udateto[1], $chk_udateto[2], $chk_udateto[0]) : '';
			$chk_udateto ? $sql_where[]="u.user_regdate < '{$chk_udateto}'" : '';
		}

		$sort && in_array($sort, $valid_sort) ? $sql_sort.=" ORDER BY {$sort}".($order ? ' ASC' : ' DESC') : '';
		$sql_where[]="u.user_type IN('".USER_NORMAL."')";//, '".USER_FOUNDER."'

		$sql="SELECT (u.user_uploaded/u.user_downloaded)+u.user_bonus u_ratio, u.user_uploaded/u.user_shadow_downloaded u_rratio, u.user_id, u.username, u.user_colour, u.user_downloaded, u.user_uploaded, u.user_shadow_downloaded, u.user_bonus, u.username, u.user_colour FROM ".USERS_TABLE." u ".($sql_where ? 'WHERE '.implode(" AND ", $sql_where) : '')."{$sql_sort}{$sql_limit}";//echo $sql;
		$result=$db->sql_query($sql);
		$e_torrent=array();
		while($row=$db->sql_fetchrow($result))
		{
			if($inctorrent)
			{
				$users[$row['user_id']]=$row['user_id'];
				$u_data[$row['user_id']]['up']=$row['user_uploaded'];
				$u_data[$row['user_id']]['down']=$row['user_downloaded'];
			}
			$assign_vars[$row['user_id']]=array(
				'USERNAME'	=> @$row['user_id'] ? get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']) : '',
				'UUPLOADED' => get_formatted_filesize($row['user_uploaded']),
				'UDOWNLOADED' => get_formatted_filesize($row['user_downloaded']),
				'URDOWNLOADED' => get_formatted_filesize($row['user_shadow_downloaded']),
				'URATIO' => get_ratio($row['user_uploaded'], $row['user_downloaded'], $row['user_bonus']),
				'URRATIO' => get_ratio($row['user_uploaded'], $row['user_shadow_downloaded']),
				'UBONUS1' => my_float_val($row['user_bonus']),
			);

		}
		$db->sql_freeresult($result);

		if($inctorrent && $users)
		{
			$tratio ? $sql_where2[]="s.uploaded/s.downloaded ".($tratio_ml ? '<' : '>')." '{$tratio}'" : '';
			$torrentid ? $torrentids=my_split_config($torrentid, 0, 'my_int_val', ',') : $torrentids=array();
			$torrentids ? $sql_where2[]="s.torrent IN('".implode("', '", $torrentids)."')" : '';

			$downloaded ? $sql_where2[]="s.downloaded ".($downloaded_ml ? '<' : '>')." '{$downloaded}'" : '';
			$uploaded ? $sql_where2[]="s.uploaded ".($uploaded_ml ? '<' : '>')." '{$uploaded}'" : '';
			$completed ? $sql_where2[]="t.times_completed ".($completed_ml ? '<' : '>')." '{$completed}'" : '';
			$ucompleted ? $sql_where2[]=($ucompleted=='yes' ? "s.finished!='0'" : "s.finished='0'") : '';
			$config['ppkbb_tcguests_enabled'][0] ? $sql_where2[]="s.guests='0'" : '';

			$sort2 && in_array($sort2, $valid_sort2) ? $sql_sort2.=" ORDER BY $sort2".($order2 ? ' ASC' : ' DESC') : '';

			$sql="SELECT p.post_id, p.post_subject, s.userid, s.torrent, s.finished, t.times_completed, s.uploaded, s.downloaded, s.uploaded/s.downloaded t_ratio FROM ".TRACKER_SNATCHED_TABLE." s LEFT JOIN ".TRACKER_TORRENTS_TABLE." t ON (s.torrent=t.id) LEFT JOIN ".POSTS_TABLE." p ON (t.post_msg_id=p.post_id) WHERE s.userid IN('".implode("', '", $users)."') ".($sql_where2 ? ' AND '.implode(' AND ', $sql_where2) : '')."{$sql_sort2}";
			$result=$db->sql_query($sql);

			while($row=$db->sql_fetchrow($result))
			{
				if(!isset($assign_vars[$row['userid']]['TORRENTS']))
				{
					$assign_vars[$row['userid']]['TORRENTS']='';
				}

				@$t_data[$row['userid']]+=1;
				if($tmax && @$e_torrent[$row['userid']] >= $tmax)
				{

				}
				else
				{
					//if($row['post_id'])
					//{
						$ct_ratio=get_ratio($row['uploaded'], $row['downloaded']);
						if(!in_array($ct_ratio, array('Inf.', 'Leech.', 'Seed.', 'None.')))
						{
							if($ct_ratio > 10)
							{
								$ct_ratio='<span style="color:#FF0000;">'.$ct_ratio.'</span>';
							}
							else if($ct_ratio > 5)
							{
								$ct_ratio='<span style="color:#FF4545;">'.$ct_ratio.'</span>';
							}
							else if($ct_ratio > 1)
							{
								$ct_ratio='<span style="color:#FF8585;">'.$ct_ratio.'</span>';
							}
						}
						if($row['downloaded'])
						{
							$t_pdown=$row['downloaded'] < $u_data[$row['userid']]['down'] ? my_float_val(100*$row['downloaded']/$u_data[$row['userid']]['down']) : 100;
							if($t_pdown > 90)
							{
								$t_pdown='<span style="color:#FF0000;">'.$t_pdown.'</span>';
							}
							else if($t_pdown > 60)
							{
								$t_pdown='<span style="color:#FF4545;">'.$t_pdown.'</span>';
							}
							else if($t_pdown > 30)
							{
								$t_pdown='<span style="color:#FF8585;">'.$t_pdown.'</span>';
							}
						}
						if($row['uploaded'])
						{
							$t_pup=$row['uploaded'] < $u_data[$row['userid']]['up'] ? my_float_val(100*$row['uploaded']/$u_data[$row['userid']]['up']) : 100;
							if($t_pup > 90)
							{
								$t_pup='<span style="color:#FF0000;">'.$t_pup.'</span>';
							}
							else if($t_pup > 60)
							{
								$t_pup='<span style="color:#FF4545;">'.$t_pup.'</span>';
							}
							else if($t_pup > 30)
							{
								$t_pup='<span style="color:#FF8585;">'.$t_pup.'</span>';
							}
						}
						$assign_vars[$row['userid']]['TORRENTS'].='
						<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td colspan="5">'.($row['post_id'] ? '<a href="'.append_sid($phpbb_root_path.'viewtopic.'.$phpEx.'?p='.$row['post_id']).'">'.$row['post_subject'].'</a>' : $user->lang['ACHEAT_TORRENT_DELETED']).'</td>
						</tr>';
						$assign_vars[$row['userid']]['TORRENTS'].='
						<tr>
							<td width="15%"><b>TD/TUD:</b> '.$row['finished'].'/'.my_int_val($row['times_completed']).'</td>
							<td width="15%"><b>TR:</b> '.$ct_ratio.'</td>
							<td width="25%"><b>TUP:</b> '.get_formatted_filesize($row['uploaded']).' / <b>TDL:</b> '.get_formatted_filesize($row['downloaded']).'</td>
							<td width="15%"><b>TPRUP:</b> '.($row['uploaded'] ? $t_pup.'%' : '0%').'</td>
							<td width="15%"><b>TPRDL:</b> '.($row['downloaded'] ? $t_pdown.'%' : '0%').'</td>
						</tr>
						</table>';

						@$e_torrent[$row['userid']]+=1;
					//}
				}
			}
			$db->sql_freeresult($result);

		}
		if($assign_vars)
		{
			foreach($assign_vars as $v)
			{
				$template->assign_block_vars('acheats', $v);
			}
		}
		/*if($u_data && $assign_vars)
		{
			$t_count=sizeof($assign_vars);
			foreach($u_data as $uk => $uv)
			{
				$u_percent=my_int_val($uv*100/$t_count);
				$ustats.='<div style="float:left;width:'.$u_percent.'px;height:12px;background-color:#FF0000;"></div>&nbsp;PRUT: <b>'.$u_percent.'%</b>&nbsp;'.(@$users[$uk] ? get_username_string('full', $uk, $users[$uk]['username'], $users[$uk]['user_colour']) : '').'<br />';
			}
		}*/
		$template->assign_vars(array(
			'S_VIEW_ACHEATS_USER'	=> true,
			'S_HIDDEN_FIELDS'=>'<input type="hidden" name="view_acheats_user" value="1" >',
			)
		);
	}

	$acheat_sort='<select name="sort">';
	foreach($valid_sort as $s)
	{
		$acheat_sort.='<option value="'.$s.'"'.($s==$sort ? ' selected="selected"' : '').'>'.@$user->lang['ACHEAT_SORTS_USER'][$s].'</option>';
	}
	$acheat_sort.='</select>';

	$acheat_sort2='<select name="sort2">';
	foreach($valid_sort2 as $s)
	{
		$acheat_sort2.='<option value="'.$s.'"'.($s==$sort2 ? ' selected="selected"' : '').'>'.$user->lang['ACHEAT_SORTS_USER'][$s].'</option>';
	}
	$acheat_sort2.='</select>';
	$s_ucompleted='<select name="ucompleted"><option value=""'.(!$ucompleted ? ' selected="selected"' : '').'></option><option value="yes"'.($ucompleted=='yes' ? ' selected="selected"' : '').'>'.$user->lang['YES'].'</option><option value="no"'.($ucompleted=='no' ? ' selected="selected"' : '').'>'.$user->lang['NO'].'</option></select>';

	$template->assign_vars(array(
		'UCOMPLETED' => $s_ucompleted,
		'S_ACHEAT_INC'	=> true,
		'S_VIEW_ACHEATS_USER' => true,
		'ACHEAT_SORT' => $acheat_sort,
		'ACHEAT_SORT2' => $acheat_sort2,
		'SUBJECT_USERNAME' => $incuname || $inctorrent ? true : false,
		'TORRENTID' => $torrentid,
		'USERID' => $userid,
		'UPSPEED' => $upspeed,
		'DOWNSPEED' => $downspeed,
		'UPLOADED' => $uploaded,
		'DOWNLOADED' => $downloaded,
		'UPLOADEDV' => get_formatted_filesize($uploaded),
		'DOWNLOADEDV' => get_formatted_filesize($downloaded),
		'UPV' => select_size_value(),
		'DOWNV' => select_size_value(),
		'SEEDERS' => $seeders,
		'LEECHERS' => $leechers,
		//'LEFT' => $left,
		//'EVENT' => $event,
		'START' => $start,
		'END' => $end,
		'DIFF' => $diff,
		'ORDER' => build_sort_form($order),
		'INCUNAME' => $incuname ? ' checked="checked"' : '',
		'INCTORRENT' => $inctorrent ? ' checked="checked"' : '',
		'UPLOADED_ML' => build_ml_form($uploaded_ml),
		'DOWNLOADED_ML' => build_ml_form($downloaded_ml),
		'DIFF_ML' => build_ml_form($diff_ml),
		'S_HIDDEN_FIELDS'=>'<input type="hidden" name="view_acheats_user" value="1" >',

		'ORDER2' => build_sort_form($order2),
		'TMAX' => $tmax ? $tmax : 10,
		'URATIO' => $uratio,
		'URRATIO' => $urratio,
		'UBONUS' => $ubonus,
		'TRATIO' => $tratio,
		'UDATEFROM' => $udatefrom,
		'UDATETO' => $udateto,
		'COMPLETED' => $completed,

		'UUPLOADED_ML' => build_ml_form($uuploaded_ml),
		'UDOWNLOADED_ML' => build_ml_form($udownloaded_ml),
		'UUPLOADED' => $uuploaded,
		'UDOWNLOADED' => $udownloaded,
		'UUPLOADEDV' => get_formatted_filesize($uuploaded),
		'UDOWNLOADEDV' => get_formatted_filesize($udownloaded),
		'UUPV' => select_size_value(),
		'UDOWNV' => select_size_value(),

		'URDOWNLOADED_ML' => build_ml_form($urdownloaded_ml),
		'URDOWNLOADED' => $urdownloaded,
		'URDOWNLOADEDV' => get_formatted_filesize($urdownloaded),
		'URDOWNV' => select_size_value(),

		'URATIO_ML' => build_ml_form($uratio_ml),
		'URRATIO_ML' => build_ml_form($urratio_ml),
		'UBONUS_ML' => build_ml_form($ubonus_ml),
		'TRATIO_ML' => build_ml_form($tratio_ml),
		'COMPLETED_ML' => build_ml_form($completed_ml),

		'ACHEAT_USTATS' => $ustats,
		)
	);
}
else if($view_acheats)
{
	$acheat_title='ACP_TRACKER_ACHEAT';

	$template->assign_vars(array(
		'S_SET_ACHEATS'	=> true,
		)
	);
}

$template->assign_vars(array(
	'S_ACHEAT_INC' => true,
	'S_TRACKER_NOBUTT' => true,

	'S_VIEW_ACHEATS' => true,
	'VIEW_ACHEAT2' => append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=acheat&view_acheats_torr=1'),
	'VIEW_ACHEAT3' => append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=acheat&view_acheats_user=1'),
	)
);

$display_vars = array(
	'title'	=> $acheat_title,
	'vars'	=> array(
		'legend1'				=> 'ACP_TRACKER_ACHEAT_SETTINGS',
	)
);

//##############################################################################
function build_ml_form($curr)
{
	global $user;

	$form='';

	if($user->lang['ACHEAT_ML_VAL'])
	{
		foreach($user->lang['ACHEAT_ML_VAL'] as $k => $v)
		{
			$form.='<option value="'.$k.'"'.($curr==$k ? ' selected="selected"' : '').'>'.$v.'</option>';

		}
	}

	return $form;
}

function build_sort_form($curr=0)
{
	global $user;

	$form='';

	if($user->lang['ACHEAT_SORT_VAL'])
	{
		foreach($user->lang['ACHEAT_SORT_VAL'] as $k => $v)
		{
			$form.='<option value="'.$k.'"'.($curr==$k ? ' selected="selected"' : '').'>'.$v.'</option>';

		}
	}

	return $form;
}
?>
