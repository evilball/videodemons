<?php
/**
*
* @package ppkBB3cker
* @version $Id: viewtopic_add1_remote.php 1.000 2010-05-07 12:33:00 PPK $
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

$sort_opt[$opt]=array('rtrack_url', 'next_announce', 'a_message', 'a_interval', 'err_count', 'seeders', 'leechers', 'times_completed', 'peers');
if(!$is_admod)
{
	$sort_opt[$opt]=array('rtrack_url', 'next_announce', 'seeders', 'leechers', 'times_completed', 'peers');
}
/*if(!in_array($opt_param, $sort_opt[$torrent_opt]))
{
	$opt_param=$sort_opt[$torrent_opt][0];
}


if($opt_param=='rtrack_url')
{
	$sql_addon="rt.rtrack_url $opt_sort";
}
else if($opt_param=='peers')
{
	$sql_addon="ra.seeders+ra.leechers $opt_sort";
}
else
{
	$sql_addon="ra.$opt_param $opt_sort";
}
*/

if($torrents[$torrent_id]['forb'] < 1)
{
	$forb_rtracks=get_forb_rtrack();
	$sql='SELECT rt.rtrack_url, ra.next_announce, ra.a_message, ra.s_message, ra.a_interval, ra.err_count, ra.seeders, ra.leechers, ra.seeders+ra.leechers peers, ra.times_completed, ra.locked FROM '.TRACKER_RTRACK_TABLE.' rt LEFT JOIN '.TRACKER_RANNOUNCES_TABLE." ra ON (rt.id=ra.tracker AND ra.torrent='{$torrent_id}') WHERE rt.rtrack_enabled='1' AND ((rt.zone_id='0' AND rt.rtrack_remote='1' AND rt.torrent='0') OR rt.torrent='{$torrent_id}')".($mua_limit ? " LIMIT {$mua_limit}" : '');// ORDER BY $sql_addon";
	$result=$db->sql_query($sql);
	while($userlist=$db->sql_fetchrow($result))
	{
		$rtrack_forb=0;
		if(sizeof($forb_rtracks))
		{
			foreach($forb_rtracks as $f)
			{
				if(in_array($f['rtrack_forb'], array(1, 3)))
				{
					if($f['forb_type']=='s' && strstr($userlist['rtrack_url'], $f['rtrack_url']))
					{
						$rtrack_forb=1;
					}
					else if($f['forb_type']=='i' && stristr($userlist['rtrack_url'], $f['rtrack_url']))
					{
						$rtrack_forb=1;
					}
					else if($f['forb_type']=='r' && preg_match("{$f['rtrack_url']}", $userlist['rtrack_url']))
					{
						$rtrack_forb=1;
					}
				}
			}
		}
		if(!$rtrack_forb)
		{
			$i3+=1;
			$is_admod ? $rtrack_url=$userlist['rtrack_url'] : $rtrack_url=parse_url($userlist['rtrack_url']);

			$assign_vars[$i3]['TORRENT_RTRACKURL'] = htmlspecialchars($is_admod ? $rtrack_url : $rtrack_url['scheme'].'://'.$rtrack_url['host'].(isset($rtrack_url['port']) ? ':'.$rtrack_url['port'] : ''));

			$assign_vars[$i3]['TORRENT_NANNOUNCE'] = $userlist['locked'] ? $user->lang['IN_ANNOUNCE'] : $td->spelldelta($dt, $userlist['next_announce']);
			$assign_vars[$i3]['TORRENT_SNANNOUNCE'] = $userlist['locked'] ? 0 : $userlist['next_announce']-$dt;

			$assign_vars[$i3]['TORRENT_AMESSAGE'] = htmlspecialchars($userlist['err_count'] && !$userlist['a_message'] ? $userlist['s_message'] : $userlist['a_message']);

			$assign_vars[$i3]['TORRENT_AINTERVAL'] = $userlist['a_interval'] ? $userlist['a_interval'].$user->lang['TSEC'] : '';
			$assign_vars[$i3]['TORRENT_SAINTERVAL'] = $userlist['a_interval'];

			$assign_vars[$i3]['TORRENT_ERRCOUNT'] = $userlist['err_count'];

			$assign_vars[$i3]['TORRENT_SEEDERS'] = $userlist['seeders'];

			$assign_vars[$i3]['TORRENT_LEECHERS'] = $userlist['leechers'];

			$assign_vars[$i3]['TORRENT_TCOMPLETED'] = $userlist['times_completed'];

			$assign_vars[$i3]['TORRENT_PEERS'] = $userlist['peers'];
		}
	}
	$db->sql_freeresult($result);
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
//$torrent_info[$opt]='<b>'.$torrent_info[$opt].'</b> ('.$i3.')';
?>
