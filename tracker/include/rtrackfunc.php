<?php
/**
*
* @package ppkBB3cker
* @version $Id: rtrackfunc.php 1.000 2009-05-29 18:13:00 PPK $
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

function get_rem_rtrack($t)
{
	$sql_where='';
	$rem_rtracks_array=array();

	$sql_where.="(zone_id='0' AND rtrack_remote='1' AND torrent='0')";
	$t ? $sql_where.=" OR (torrent='{$t}')" : '';

	$sql='SELECT id, rtrack_url FROM '.TRACKER_RTRACK_TABLE." WHERE rtrack_enabled='1' AND ({$sql_where})";
	$result=$db->sql_query($sql);
	$e_urtracks=0;
	while($row=$db->sql_fetchrow($result))
	{
		$rem_rtracks_array[$row['id']]=$row;
	}
	$db->sql_freeresult($result);

	return $rem_rtracks_array;
}

function get_rtrack($ip, $type=1, $users=0, $torrent=0, $forb_rtracks=array())
{
	global $db, $user;

	$rtracks_array=$sql_where=array();
	$user_zone=0;

	if($type)
	{
		$sql_where[]="(zone_id='0' AND rtrack_remote!='0' AND torrent='0')";
	}
	if($users)
	{
		$sql_where[]="(zone_id='{$user->data['user_id']}' AND rtrack_user='1')";
	}
	if($torrent)
	{
		$sql_where[]="(zone_id='0' AND torrent='{$torrent}')";
	}

	if($sql_where)
	{
		$sql='SELECT id, rtrack_url, zone_id, rtrack_user, torrent FROM '.TRACKER_RTRACK_TABLE." WHERE rtrack_enabled='1' AND (".(implode(' OR ', $sql_where)).") AND rtrack_forb='0'";
		$result=$db->sql_query($sql);
		$e_urtracks=0;
		while($row=$db->sql_fetchrow($result))
		{
			$rtrack_forb=0;
			if(sizeof($forb_rtracks))
			{
				foreach($forb_rtracks as $f)
				{
					if($row['zone_id'] && $row['rtrack_user'] && in_array($f['rtrack_forb'], array(2, 3)))
					{
						if($f['forb_type']=='s' && strstr($row['rtrack_url'], $f['rtrack_url']))
						{
							$rtrack_forb=1;
						}
						else if($f['forb_type']=='i' && stristr($row['rtrack_url'], $f['rtrack_url']))
						{
							$rtrack_forb=1;
						}
						else if($f['forb_type']=='r' && preg_match("{$f['rtrack_url']}", $row['rtrack_url']))
						{
							$rtrack_forb=1;
						}
					}
					else if($row['torrent'] && !$row['zone_id'] && !$row['rtrack_user'] && in_array($f['rtrack_forb'], array(1, 3)))
					{
						if($f['forb_type']=='s' && strstr($row['rtrack_url'], $f['rtrack_url']))
						{
							$rtrack_forb=1;
						}
						else if($f['forb_type']=='i' && stristr($row['rtrack_url'], $f['rtrack_url']))
						{
							$rtrack_forb=1;
						}
						else if($f['forb_type']=='r' && preg_match("{$f['rtrack_url']}", $row['rtrack_url']))
						{
							$rtrack_forb=1;
						}
					}
				}
			}
			if(!$rtrack_forb)
			{
				if($row['zone_id'] && $row['rtrack_user'])
				{
					$e_urtracks+=1;
					if(!$users || $e_urtracks > $users)
					{
						continue;
					}
				}
				$rtracks_array[$row['id']]=$row;
			}
		}
		$db->sql_freeresult($result);
	}

	return $rtracks_array;
}

function benc_rtrack_url($a, $a_ex=0)
{
	global $user, $config;

	$a_announce=$rtracks_url=array();

	$a[0]['rtrack_url']=generate_board_url().$config['ppkbb_announce_url']."?passkey={$user->data['user_passkey']}";

	if($a)
	{
		$a=array_reverse($a);

		foreach($a as $i => $a_url)
		{
			$rtrack_url=$a_url['rtrack_url'];
			if(!in_array($rtrack_url, $rtracks_url))
			{
				$rtrack_url=str_replace('{YOUR_PASSKEY}', $user->data['user_passkey'], $rtrack_url);
				$rtracks_url[]=$rtrack_url;
				$a_announce[][$i] = $rtrack_url;
			}
		}
	}
	if((($a_ex==1) || ($a_ex==2 && $user->data['is_registered']) || ($a_ex==3 && !$user->data['is_registered'])) && sizeof($a_announce) > 1)
	{
		unset($a_announce[0]);
	}

	return $a_announce;
}

function magnet_rtrack_url($a, $a_ex=0)
{
	global $user, $config;

	$a_announce=$rtracks_url=array();

	$a[0]['rtrack_url']=generate_board_url().$config['ppkbb_announce_url']."?passkey={$user->data['user_passkey']}";

	if($a)
	{
		$a=array_reverse($a);

		foreach($a as $i => $a_url)
		{
			$rtrack_url=$a_url['rtrack_url'];
			if(!in_array($rtrack_url, $rtracks_url))
			{
				$rtrack_url=str_replace('{YOUR_PASSKEY}', $user->data['user_passkey'], $rtrack_url);
				$rtracks_url[]=$rtrack_url;
				$a_announce[$i] = $rtrack_url;
			}
		}
	}
	if((($a_ex==1) || ($a_ex==2 && $user->data['is_registered']) || ($a_ex==3 && !$user->data['is_registered'])) && sizeof($a_announce) > 1)
	{
		unset($a_announce[0]);
	}

	return implode('&tr=', array_map('urlencode', $a_announce));
}

?>
