<?php
/**
*
* @package ppkBB3cker
* @version $Id: message_parser_add1.php 1.000 2010-07-12 17:56:00 PPK $
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

include_once("{$phpbb_root_path}tracker/include/bencoding.{$phpEx}");

function torrents_import($data, $id, $tprivate_flag='', $addit_rtracks=array())
{
	global $config, $phpbb_root_path, $db, $user, $phpEx, $forum_id, $auth;

	@set_time_limit(0);

	$tmpname = "{$phpbb_root_path}{$config['upload_path']}/{$data['physical_filename']}";
	$dt=time();

	if(!file_exists($tmpname))
	{
		return sprintf($user->lang['TORRENT_UPLOAD_ERRORS'], 1);
	}

	if(!$tmpsize=@filesize($tmpname))
	{
		return sprintf($user->lang['TORRENT_UPLOAD_ERRORS'], 2);
	}
	$dict=bdecode_f($tmpname, $tmpsize);

	if(!$dict)
	{
		return sprintf($user->lang['TORRENT_UPLOAD_ERRORS'], 3);
	}
	if(strlen(@$dict['info']['pieces']) % 20!=0)
	{
		return sprintf($user->lang['TORRENT_UPLOAD_ERRORS'], 4);
	}
	if(@$dict['info']['name']=='' || @!$dict['info']['piece length'] || @!$dict['info']['pieces'])
	{
		return sprintf($user->lang['TORRENT_UPLOAD_ERRORS'], 5);
	}
	if(is_array(@$dict['info']['files']) && sizeof($dict['info']['files']) > 1)
	{
		$filelist=@$dict['info']['files'];
	}
	else
	{
		if(isset($dict['info']['length']))
		{
			$filelist[0]['length']=$dict['info']['length'];
			$filelist[0]['path'][0]=$dict['info']['name'];
		}
		else
		{
			$filelist=@$dict['info']['files'];
		}
	}

	$tracker_url=generate_board_url();
	if(!$config['ppkbb_announce_url'])
	{
		$config['ppkbb_announce_url']='/tracker/announce.'.$phpEx;
	}

	$rem_announces=$rem_rtracks_array=$forb_rtracks=array();
	if(!$config['ppkbb_tfile_annreplace'][0] || request_var('annwarn', 0))
	{
		unset($dict['announce-list']);// remove multi-tracker capability
	}
	else
	{
		$sql='SELECT id, rtrack_url, rtrack_forb, forb_type FROM '.TRACKER_RTRACK_TABLE." WHERE rtrack_enabled='1' AND (zone_id='0' AND rtrack_remote!='0' AND torrent='0')";
		$result=$db->sql_query($sql);
		while($row=$db->sql_fetchrow($result))
		{
			if(!$row['rtrack_forb'])
			{
				$rem_rtracks_array[]=$row['rtrack_url'];
			}
			else if(in_array($row['rtrack_forb'], array(1, 3)))
			{
				$forb_rtracks[]=$row;
			}
		}
		$db->sql_freeresult($result);
		$addit_rtracks ? $forb_rtracks=array_merge($forb_rtracks, $addit_rtracks) : '';

		$rem_ann_count=0;
		if($config['ppkbb_tfile_annreplace'][0] && isset($dict['announce']) && preg_match('#^(http|udp):\/\/(\w+|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})#', $dict['announce']) && !in_array($dict['announce'], $rem_rtracks_array) && !in_array($dict['announce'], $rem_announces) && !stristr($dict['announce'], $tracker_url.$config['ppkbb_announce_url']) && strlen($dict['announce']) < 513)
		{
			$rtrack_forb=0;
			if(sizeof($forb_rtracks))
			{
				foreach($forb_rtracks as $f)
				{
					if($f['forb_type']=='s' && strstr($dict['announce'], $f['rtrack_url']))
					{
						$rtrack_forb=1;
					}
					else if($f['forb_type']=='i' && stristr($dict['announce'], $f['rtrack_url']))
					{
						$rtrack_forb=1;
					}
					else if($f['forb_type']=='r' && preg_match("{$f['rtrack_url']}", $dict['announce']))
					{
						$rtrack_forb=1;
					}
				}
			}
			if(!$rtrack_forb)
			{
				$rem_announces[]=$dict['announce'];
				$rem_ann_count+=1;
			}
		}
		if($config['ppkbb_tfile_annreplace'][0] && isset($dict['announce-list']) && is_array($dict['announce-list']) && sizeof($dict['announce-list']))
		{
			foreach($dict['announce-list'] as $v)
			{
				foreach($v as $v2)
				{
					if($config['ppkbb_tfile_annreplace'][1] && $rem_ann_count > $config['ppkbb_tfile_annreplace'][1])
					{
						break;
					}
					$rtrack_forb=0;
					if(sizeof($forb_rtracks))
					{
						foreach($forb_rtracks as $f)
						{
							if($f['forb_type']=='s' && strstr($v2, $f['rtrack_url']))
							{
								$rtrack_forb=1;
							}
							else if($f['forb_type']=='i' && stristr($v2, $f['rtrack_url']))
							{
								$rtrack_forb=1;
							}
							else if($f['forb_type']=='r' && preg_match("{$f['rtrack_url']}", $v2))
							{
								$rtrack_forb=1;
							}
						}
					}
					if(!$rtrack_forb && preg_match('#^(http|udp):\/\/(\w+|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})#', $v2) && !in_array($v2, $rem_rtracks_array) && !in_array($v2, $rem_announces) && !stristr($v2, $tracker_url.$config['ppkbb_announce_url']) && strlen($v2) < 513)
					{
						$rem_announces[]=$v2;
						$rem_ann_count+=1;
					}
				}
			}
		}
		unset($dict['announce-list']);// remove multi-tracker capability
	}

	$dict['announce']=$tracker_url.$config['ppkbb_announce_url']."?passkey=";

	unset($dict['azureus_properties']);// remove azureus properties
	unset($dict['nodes']);// remove cached peers (Bitcomet & Azareus)

	$private=isset($dict['info']['private']) && $dict['info']['private'] ? 1 : 0;

	$dict['publisher']=$config['server_name'];// change publisher
	$dict['publisher.utf-8']=$config['server_name'];// change publisher

	//if(!$config['ppkbb_rtrack_enable'][0])
	//{
		if(($config['ppkbb_tprivate_flag']==1 && $private!=1) || ($config['ppkbb_tprivate_flag']==2 && $private==1))
		{
			unset($dict['info']['crc32']);// remove crc32
			unset($dict['info']['ed2k']);// remove ed2k
			unset($dict['info']['md5sum']);// remove md5sum
			unset($dict['info']['sha1']);// remove sha1
			unset($dict['info']['tiger']);// remove tiger
		}

		if($config['ppkbb_tprivate_flag']==1)
		{
			$dict['info']['private']=1;// add private tracker flag
			$private=1;
		}
		else if($config['ppkbb_tprivate_flag']==2)
		{
			isset($dict['info']['private']) ? $dict['info']['private']=0 : '';// remove private tracker flag
			$private=0;
		}
		else if($config['ppkbb_tprivate_flag']==-1 && $private!=1)
		{
			return $user->lang['TORRENT_ERROR_NONPRIVATE'];
		}
		else if($config['ppkbb_tprivate_flag']==-2 && $private==1)
		{
			return $user->lang['TORRENT_ERROR_PRIVATE'];
		}
	//}

	$enc_f=bencode($dict);

	$infohash=pack('H*', sha1(bencode($dict['info'])));

	$sql="SELECT id, post_msg_id, topic_id, unreg FROM ".TRACKER_TORRENTS_TABLE." WHERE info_hash='". $db->sql_escape($infohash) ."' LIMIT 1";
	$result=$db->sql_query($sql);
	$data3=$db->sql_fetchrow($result);
	$db->sql_freeresult($result);
	$unreg=0;

	if($data3['id'])
	{
		$sql="SELECT p.post_approved, t.topic_id FROM ".POSTS_TABLE." p, ".TOPICS_TABLE." t WHERE t.topic_id='{$data3['topic_id']}' AND p.topic_id=t.topic_id AND p.post_id='{$data3['post_msg_id']}' LIMIT 1";
		$result=$db->sql_query($sql);
		$data2=$db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if($data2['topic_id'])
		{
			return sprintf($user->lang['TORRENT_UPLOAD_ERROR'], "<a href='".append_sid($phpbb_root_path."viewtopic.{$phpEx}?t=".$data2['topic_id'])."' target=\"_blank\">#{$data2['topic_id']}</a>", ($data2['post_approved'] ? $user->lang['YES'] : $user->lang['NO']));
		}
		else
		{
			if($data3['unreg'])
			{
				$unreg=1;
				$r=$db->sql_query("DELETE FROM ".ATTACHMENTS_TABLE." WHERE attach_id='{$data3['id']}'");
				$r=$db->sql_query("UPDATE ".TRACKER_FILES_TABLE." SET id='{$id}' WHERE id='{$data3['id']}'");
				//$r=$db->sql_query("UPDATE ".TRACKER_THANKS_TABLE." SET torrent_id='{$id}' WHERE torrent_id='{$data3['id']}'");
				$r=$db->sql_query("UPDATE ".TRACKER_SNATCHED_TABLE." SET torrent='{$id}' WHERE torrent='{$data3['id']}'");
				$r=$db->sql_query("UPDATE ".TRACKER_PEERS_TABLE." SET torrent='{$id}' WHERE torrent='{$data3['id']}'");
				//$r=$db->sql_query("UPDATE ".TRACKER_RANNOUNCES_TABLE." SET torrent='{$id}' WHERE torrent='{$data3['id']}'");
				//$r=$db->sql_query("UPDATE ".TRACKER_RTRACK_TABLE." SET torrent='{$id}' WHERE torrent='{$data3['id']}'");
			}
			else
			{
				$r=$db->sql_query("DELETE FROM ".TRACKER_TORRENTS_TABLE." WHERE id='{$data3['id']}'");
				$r=$db->sql_query("DELETE FROM ".TRACKER_FILES_TABLE." WHERE id='{$data3['id']}'");
				$r=$db->sql_query("DELETE FROM ".TRACKER_THANKS_TABLE." WHERE torrent_id='{$data3['id']}'");
				$r=$db->sql_query("DELETE FROM ".TRACKER_SNATCHED_TABLE." WHERE torrent='{$data3['id']}'");
				$r=$db->sql_query("DELETE FROM ".TRACKER_PEERS_TABLE." WHERE torrent='{$data3['id']}'");
				$r=$db->sql_query("DELETE FROM ".TRACKER_RANNOUNCES_TABLE." WHERE torrent='{$data3['id']}'");
				$data3['id'] ? $r=$db->sql_query("DELETE FROM ".TRACKER_RTRACK_TABLE." WHERE torrent='{$data3['id']}'") : '';
			}
		}
	}

	$tsize=$type_conv=0;
	if(function_exists('iconv'))
	{
		$type_conv='iconv';
	}
	else if(function_exists('mb_convert_encoding'))
	{
		$type_conv='mb';
	}

	foreach($filelist as $i => $v)
	{
		$v['length']=my_int_val($v['length']);
		$tsize+=$v['length'];

		if($config['ppkbb_addit_options'][2])
		{
		$v['path']=implode('/', (isset($v['path.utf-8']) ? $v['path.utf-8'] : @$v['path']));
		if($type_conv && isset($dict['encoding']) && $dict['encoding']!='UTF-8')
		{
			$v['path']=torrent_enconvert($v['path'], $dict['encoding'], 'UTF-8', $type_conv);
		}
		$db->sql_query("INSERT INTO ".TRACKER_FILES_TABLE." (id, filename, size) VALUES ('{$id}', '". $db->sql_escape(utf8_normalize_nfc($v['path'])) ."', '{$v['length']}')");
		}
	}
	$forb = ($auth->acl_get('u_canskiptcheck') && $auth->acl_get('f_canskiptcheck', $forum_id)) ? $config['ppkbb_tcdef_statuses'][0] : $config['ppkbb_tcdef_statuses'][1];

	if($unreg)
	{
		$db->sql_query("UPDATE ".TRACKER_TORRENTS_TABLE." SET id='{$id}', info_hash='". $db->sql_escape($infohash) ."', numfiles='".sizeof($filelist)."', size='{$tsize}', added='{$dt}', forb='{$forb}', ip='".$db->sql_escape($user->ip)."', private='{$private}', unreg='0' WHERE id='{$data3['id']}'");
	}
	else
	{
		$db->sql_query("INSERT INTO ".TRACKER_TORRENTS_TABLE." (id, info_hash, numfiles, size, added, forb, ip, private) VALUES ('{$id}', '". $db->sql_escape($infohash) ."', '".sizeof($filelist)."', '{$tsize}', '{$dt}', '{$forb}', '".$db->sql_escape($user->ip)."', '{$private}')");
	}

	$fp=fopen($tmpname, "w");
	if($fp)
	{
		@fwrite($fp, $enc_f, strlen($enc_f));
		fclose($fp);
	}
	else
	{
		return sprintf($user->lang['TORRENT_UPLOAD_ERRORS'], 6);
	}

	if($config['ppkbb_tfile_annreplace'][0])
	{
		if(sizeof($rem_announces))
		{
			foreach($rem_announces as $v)
			{
				$db->sql_query("INSERT INTO ".TRACKER_RTRACK_TABLE."(rtrack_url, rtrack_remote, torrent, rtrack_enabled) VALUES('".$db->sql_escape(utf8_normalize_nfc($v))."', '".($config['ppkbb_tfile_annreplace'][0]==1 ? 1 : -1)."', '{$id}', '1')");
			}
		}
	}

	return true;
}

function torrent_enconvert($data, $from, $to, $type_conv='')
{
	if(!$type_conv && $data)
	{
		if(function_exists('iconv'))
		{
			$type_conv='iconv';
		}
		else if(function_exists('mb_convert_encoding'))
		{
			$type_conv='mb';
		}
	}

	if($data && $from && $to && $type_conv)
	{
		$data=($type_conv=='iconv' ? @iconv($from, $to, $data) : @mb_convert_encoding($data, $to, $from));
	}

	return $data;
}
?>
