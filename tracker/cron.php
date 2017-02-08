<?php

/**
*
* @package ppkBB3cker
* @version $Id: cron.php 1.000 2014-03-22 12:50:03 PPK $
* @copyright (c) 2014 PPK
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

error_reporting(0);
@ini_set('register_globals', 0);
@ini_set('magic_quotes_runtime', 0);
@ini_set('magic_quotes_sybase', 0);

function_exists('date_default_timezone_set') ? date_default_timezone_set('Europe/Moscow') : '';
define('IN_PHPBB', true);
define('IN_CRON', true);
$phpbb_root_path=(defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx=substr(strrchr(__FILE__, '.'), 1);

ignore_user_abort(true);
@set_time_limit(0);
ob_start();

// Output transparent gif
header('Cache-Control: no-cache');
header('Content-type: image/gif');
header('Content-length: 43');

echo base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');

header('Connection: close');

ob_end_flush();
@ob_flush();
flush();

$dt = time();
$id=isset($_GET['id']) && is_array($_GET['id']) ? $_GET['id'] : array();
$ids=sizeof($id);
$c=false;

if($ids)
{
	require($phpbb_root_path . 'config.'.$phpEx);

	if(!in_array($dbms, array('mysql', 'mysqli')))
	{
		err('Only mysql(i) supported');
	}

	$c=@mysql_connect($dbhost.($dbport ? ":{$dbport}" : ''), $dbuser, $dbpasswd);
	if(!$c)
	{
		err('Error connecting database: '.mysql_error().' ['.mysql_errno().']');
	}

	$s=@mysql_select_db($dbname, $c);
	if(!$s)
	{
		err('Error selecting database: '.mysql_error($c));
	}

	//my_sql_query("SET sql_mode='NO_UNSIGNED_SUBTRACTION'");
	my_sql_query("SET NAMES 'utf8'");

	unset($dbpasswd);

	define('FORUMS_TABLE',				$table_prefix . 'forums');
	define('TOPICS_TABLE',				$table_prefix . 'topics');
	define('USERS_TABLE',				$table_prefix . 'users');
	define('POSTS_TABLE',				$table_prefix . 'posts');
	define('CONFIG_TABLE',				$table_prefix . 'config');

	define('TRACKER_CONFIG_TABLE',			$table_prefix . 'tracker_config');

// 	$t_dtad=false;
	$config=array();

	$tcachedir="{$phpbb_root_path}cache/";
	$tincludedir="{$phpbb_root_path}tracker/tinc/";
	$cincludedir="{$phpbb_root_path}tracker/cron/";

	$cache_config=t_getcache('cron_config');
	if($cache_config===false)
	{
		include("{$cincludedir}cconf.{$phpEx}");
	}
	else
	{
		foreach($cache_config as $k => $v)
		{
			$config[$k]=$v;
		}
		unset($cache_config);
	}

	define('TRACKER_TORRENTS_TABLE', $table_prefix . 'tracker_torrents');
	define('TRACKER_PEERS_TABLE', $table_prefix . 'tracker_peers');
	define('TRACKER_CRON_TABLE',			$table_prefix . 'tracker_cron');
	define('TRACKER_RANNOUNCES_TABLE',			$table_prefix . 'tracker_rannounces');
	define('TRACKER_RTRACK_TABLE',			$table_prefix . 'tracker_rtrack');
	define('TRACKER_SNATCHED_TABLE',			$table_prefix . 'tracker_snatched');
	define('TRACKER_THANKS_TABLE',			$table_prefix . 'tracker_thanks');
	define('TRACKER_FILES_TABLE',			$table_prefix . 'tracker_files');

	$config['ppkbb_cron_options'][2] && $ids > 1 ? $id=array_slice($id, 0, 1) : '';

	$sql="SELECT * FROM ".TRACKER_CRON_TABLE." WHERE id IN('".implode("', '", array_map('my_int_val', $id))."') AND status='0'";
	$result0=my_sql_query($sql);

	while($row=mysql_fetch_array($result0))
	{
		if($config['ppkbb_cron_options'][3] && $dt-$row['added'] > $config['ppkbb_cron_options'][3])
		{
			continue;
		}
		if($row['type']=='t_clean')
		{
			my_sql_query("UPDATE ".TRACKER_CRON_TABLE." SET status='1' WHERE id='{$row['id']}'");
			$torrents_cleanup=unserialize(stripslashes($row['data']));
			if(is_array($torrents_cleanup) && sizeof($torrents_cleanup))
			{
				$sql="SELECT torrent torrent_id, SUM(IF(seeder='1', 1, 0)) seeder, SUM(IF(seeder='0', 1, 0)) leecher FROM ". TRACKER_PEERS_TABLE ." WHERE torrent IN('".(implode("', '", $torrents_cleanup))."') GROUP BY torrent";
				$result2=my_sql_query($sql);
				$r_seeders_leechers=array();
				while($row_cleanup=mysql_fetch_array($result2))
				{
					$r_seeders_leechers[$row_cleanup['torrent_id']][]="seeders='".intval($row_cleanup['seeder'])."'";
					$r_seeders_leechers[$row_cleanup['torrent_id']][]="leechers='".intval($row_cleanup['leecher'])."'";
					$r_seeders_leechers[$row_cleanup['torrent_id']][]="lastcleanup='{$dt}'";
				}
				mysql_free_result($result2);

				foreach($torrents_cleanup as $k => $v)
				{
					if(!isset($r_seeders_leechers[$k]))
					{
						$r_seeders_leechers[$k][]="seeders='0'";
						$r_seeders_leechers[$k][]="leechers='0'";
						$r_seeders_leechers[$k][]="lastcleanup='{$dt}'";
					}
				}

				foreach($r_seeders_leechers as $k => $t)
				{
					$sql="UPDATE ".TRACKER_TORRENTS_TABLE." SET ".implode(', ', $t)." WHERE id='{$k}'";
					my_sql_query($sql);
				}

			}
			my_sql_query('DELETE FROM '. TRACKER_PEERS_TABLE ." WHERE last_action < ".($dt-$config['ppkbb_tcdead_time'])."");
			my_set_config('ppkbb_tracker_last_cleanup', $dt, true);
			//purge_tracker_config(true);

			my_sql_query("DELETE FROM ".TRACKER_CRON_TABLE." WHERE id='{$row['id']}'");
		}
		else if($row['type']=='t_announce')
		{
			my_sql_query("UPDATE ".TRACKER_CRON_TABLE." SET status='1' WHERE id='{$row['id']}'");
			$torrents_hashes=unserialize(stripslashes($row['data']));

			if(is_array($torrents_hashes) && sizeof($torrents_hashes))
			{
				include("{$phpbb_root_path}tracker/include/rannfunc.{$phpEx}");
				$torrents_id=array_keys($torrents_hashes['torrents_id']);
				$torrents_hashes=$torrents_hashes['torrents_id'];
				$rem_announces=0;
				$r_torr=$r_exs=$r_ann=$torrents_remote=$rem_announced=array();
				$sql='SELECT id, rtrack_url, torrent torrent2 FROM '.TRACKER_RTRACK_TABLE." WHERE rtrack_enabled='1' AND ((zone_id='0' AND rtrack_remote='1' AND torrent='0') OR torrent IN('".(implode("', '", $torrents_id))."'))";
				$result2=my_sql_query($sql);
				$ra=array();
				while($row_remote=mysql_fetch_array($result2))
				{
					$ra[$row_remote['id']]=$row_remote;
				}
				mysql_free_result($result2);

				$sql='SELECT tracker, torrent, next_announce, a_message, s_message, a_interval, err_count, seeders, leechers, times_completed, locked FROM '.TRACKER_RANNOUNCES_TABLE." WHERE  tracker IN('".implode("', '", array_keys($ra))."') AND torrent IN('".(implode("', '", $torrents_id))."')";
				$result2=my_sql_query($sql);
				while($row_remote=mysql_fetch_array($result2))
				{
					$ra[$row_remote['tracker']]+=$row_remote;
				}
				mysql_free_result($result2);
				$forb_rtracks=get_forb_rtrack();

				$r_lock=$config['ppkbb_tcrannounces_options'][4] * ($config['ppkbb_tcrannounces_options'][5] ? $config['ppkbb_tcrannounces_options'][5] * sizeof($torrents_id) : 5 * sizeof($torrents_id));
				$result3=my_sql_query("UPDATE ".TRACKER_RANNOUNCES_TABLE." SET next_announce='".($dt+$r_lock)."', locked='1' WHERE torrent IN('".(implode("', '", $torrents_id))."') AND next_announce < {$dt}");
				foreach($ra as $row_remote)
				{
					$rtrack_forb=0;
					if(sizeof($forb_rtracks))
					{
						foreach($forb_rtracks as $f)
						{
							if(in_array($f['rtrack_forb'], array(1, 3)))
							{
								if($f['forb_type']=='s' && strstr($row_remote['rtrack_url'], $f['rtrack_url']))
								{
									$rtrack_forb=1;
								}
								else if($f['forb_type']=='i' && stristr($row_remote['rtrack_url'], $f['rtrack_url']))
								{
									$rtrack_forb=1;
								}
								else if($f['forb_type']=='r' && preg_match("{$f['rtrack_url']}", $row_remote['rtrack_url']))
								{
									$rtrack_forb=1;
								}
							}
						}
					}
					if($rtrack_forb || (isset($row_remote['next_announce']) && $row_remote['next_announce'] && $row_remote['next_announce']>$dt) || (isset($row_remote['locked']) && $row_remote['locked']))
					{

					}
					else
					{
						if(!$row_remote['torrent2'])
						{
							$r_torr['all'][$row_remote['id']]=$torrents_hashes;
						}
						else
						{
							isset($torrents_hashes[$row_remote['torrent2']]) ? $r_torr['torr'][$row_remote['id']][$row_remote['torrent2']]=$torrents_hashes[$row_remote['torrent2']] : '';
						}
						if(!isset($r_ann[$row_remote['id']]))
						{
							$rtrack_url=$row_remote['rtrack_url'];
							$r_ann[$row_remote['id']]['rtrack_url']=$rtrack_url;
						}
						if(isset($row_remote['torrent']))
						{
							$r_exs[$row_remote['torrent2'].'_'.$row_remote['torrent']][$row_remote['id']]=$row_remote;
						}
					}
				}

				if(isset($r_torr['all']))
				{
					foreach($r_torr['all'] as $tr_id => $a_data)
					{
						foreach($a_data as $t_id => $t_hash)
						{
							if(($config['ppkbb_tcrannounces_options'][5] && @$rem_announced[$t_id] >= $config['ppkbb_tcrannounces_options'][5]) || ($config['ppkbb_tcrannounces_options'][8] && $rem_announces >= $config['ppkbb_tcrannounces_options'][8]))
							{
								break;
							}
							$t_hash=myhex2bin($t_hash);
							if(isset($r_exs['0_'.$t_id][$tr_id]))
							{
								if($dt > $r_exs['0_'.$t_id][$tr_id]['next_announce'])
								{
									$torrents_remote[$tr_id][$t_id]=array_merge($r_exs['0_'.$t_id][$tr_id], ($r_exs['0_'.$t_id][$tr_id]['s_message'] ? remote_announce($r_ann[$tr_id]['rtrack_url'], $t_hash) : remote_scrape($r_ann[$tr_id]['rtrack_url'], $t_hash)));
									@$rem_announced[$t_id]+=1;
									$rem_announces+=1;
								}
							}
							else
							{
								$torrents_remote[$tr_id][$t_id]=remote_scrape($r_ann[$tr_id]['rtrack_url'], $t_hash);
								@$rem_announced[$t_id]+=1;
								$rem_announces+=1;
							}
						}
					}
				}
				if(isset($r_torr['torr']))
				{
					foreach($r_torr['torr'] as $tr_id => $a_data)
					{
						foreach($a_data as $t_id => $t_hash)
						{
							if(($config['ppkbb_tcrannounces_options'][5] && @$rem_announced[$t_id] >= $config['ppkbb_tcrannounces_options'][5]) || ($config['ppkbb_tcrannounces_options'][8] && $rem_announces >= $config['ppkbb_tcrannounces_options'][8]))
							{
								break;
							}
							$t_hash=myhex2bin($t_hash);
							if(isset($r_exs[$t_id.'_'.$t_id][$tr_id]))
							{
								if($dt > $r_exs[$t_id.'_'.$t_id][$tr_id]['next_announce'])
								{
									$torrents_remote[$tr_id][$t_id]=array_merge($r_exs[$t_id.'_'.$t_id][$tr_id], ($r_exs[$t_id.'_'.$t_id][$tr_id]['s_message'] ? remote_announce($r_ann[$tr_id]['rtrack_url'], $t_hash) : remote_scrape($r_ann[$tr_id]['rtrack_url'], $t_hash)));
									@$rem_announced[$t_id]+=1;
									$rem_announces+=1;
								}
							}
							else
							{
								$torrents_remote[$tr_id][$t_id]=remote_scrape($r_ann[$tr_id]['rtrack_url'], $t_hash);
								@$rem_announced[$t_id]+=1;
								$rem_announces+=1;
							}
						}
					}
				}

				if(is_array($torrents_remote) && sizeof($torrents_remote))
				{
					$r_check=array();
					foreach($torrents_remote as $tr_id => $k)
					{
						foreach($k as $t_id => $v)
						{
							$a_time=@!$v['interval'] || $v['interval'] < $config['ppkbb_tcrannounces_options'][1] ? $config['ppkbb_tcrannounces_options'][0] : $v['interval'];
							$a_time ? '' : $a_time=$config['ppkbb_tcannounce_interval'];
							if((isset($v['s_message']) && !$v['s_message']) || (isset($v['s_message']) && isset($v['a_message']) && !$v['a_message']) || (!isset($v['s_message']) && isset($v['a_message']) && !$v['a_message']))
							{
								if(@$v['next_announce'])
								{
									$result3=my_sql_query("UPDATE ".TRACKER_RANNOUNCES_TABLE." SET seeders='".my_int_val($v['seeders'])."', leechers='".my_int_val($v['leechers'])."', times_completed='".my_int_val($v['times_completed'])."', next_announce='".($dt+$a_time)."', a_message='', s_message='', err_count='0', a_interval='{$a_time}', locked='0' WHERE  tracker='{$tr_id}' and torrent='{$t_id}'");
								}
								else
								{
									if(!isset($r_check[$tr_id.'_'.$t_id]))
									{
										/*$result3=my_sql_query("UPDATE ".TRACKER_RANNOUNCES_TABLE." SET seeders='".my_int_val($v['seeders'])."', leechers='".my_int_val($v['leechers'])."', peers='".my_int_val(@$v['peers'])."', times_completed='".my_int_val($v['times_completed'])."', next_announce='".($dt+$a_time)."', a_message='', s_message='', err_count='0', a_interval='{$a_time}', locked='0' WHERE  tracker='{$tr_id}' and torrent='{$t_id}'");
										if(!mysql_affected_rows($c))
										{*/
											$result3=my_sql_query("INSERT INTO ".TRACKER_RANNOUNCES_TABLE." (torrent, tracker, seeders, leechers, times_completed, next_announce, a_interval) VALUES('{$t_id}', '{$tr_id}', '".my_int_val(@$v['seeders'])."', '".my_int_val(@$v['leechers'])."', '".my_int_val(@$v['times_completed'])."', '".($dt+$a_time)."', '{$a_time}') ON DUPLICATE KEY UPDATE seeders='".my_int_val($v['seeders'])."', leechers='".my_int_val($v['leechers'])."', times_completed='".my_int_val($v['times_completed'])."', next_announce='".($dt+$a_time)."', a_message='', s_message='', err_count='0', a_interval='{$a_time}', locked='0'");
										//}
										$r_check[$tr_id.'_'.$t_id]=1;
									}
								}
							}
							else
							{
								$mpl_a_time=$config['ppkbb_tcrannounces_options'][6] ? $a_time * $config['ppkbb_tcrannounces_options'][6] : $a_time;
								if(@$v['next_announce'])
								{
									$result3=my_sql_query("UPDATE ".TRACKER_RANNOUNCES_TABLE." SET a_message='".mysql_real_escape_string(@$v['a_message'], $c)."', s_message='".mysql_real_escape_string(@$v['s_message'], $c)."', err_count=err_count+1, next_announce=(err_count*{$mpl_a_time})+".($a_time+$dt).", seeders='0', leechers='0', times_completed='0', a_interval='{$a_time}', locked='0' WHERE tracker='{$tr_id}' AND torrent='{$t_id}'");
								}
								else
								{
									if(!isset($r_check[$tr_id.'_'.$t_id]))
									{
										/*$result3=my_sql_query("UPDATE ".TRACKER_RANNOUNCES_TABLE." SET a_message='".mysql_real_escape_string(@$v['a_message'], $c)."', s_message='".mysql_real_escape_string(@$v['s_message'], $c)."', err_count=1, next_announce='".intval($dt+$mpl_a_time+$a_time)."', seeders='0', leechers='0', peers='0', times_completed='0', a_interval='{$a_time}', locked='0' WHERE tracker='{$tr_id}' AND torrent='{$t_id}'");
										if(!mysql_affected_rows($c))
										{*/
											$result3=my_sql_query("INSERT INTO ".TRACKER_RANNOUNCES_TABLE." (torrent, tracker, next_announce, a_message, a_interval, s_message, err_count) VALUES('{$t_id}', '{$tr_id}', '".intval($dt+$mpl_a_time+$a_time)."', '".mysql_real_escape_string(@$v['a_message'], $c)."', '{$a_time}', '".mysql_real_escape_string(@$v['s_message'], $c)."', '1') ON DUPLICATE KEY UPDATE a_message='".mysql_real_escape_string(@$v['a_message'], $c)."', s_message='".mysql_real_escape_string(@$v['s_message'], $c)."', err_count=1, next_announce='".intval($dt+$mpl_a_time+$a_time)."', seeders='0', leechers='0', times_completed='0', a_interval='{$a_time}', locked='0'");
										//}
										$r_check[$tr_id.'_'.$t_id]=1;
									}
								}
							}
						}
					}
				}
				else
				{
					$result3=my_sql_query("UPDATE ".TRACKER_RANNOUNCES_TABLE." SET locked='0' WHERE torrent IN('".(implode("', '", $torrents_id))."')");
				}
				$sql="UPDATE ".TRACKER_TORRENTS_TABLE." SET
					".TRACKER_TORRENTS_TABLE.".rem_seeders=IFNULL((SELECT SUM(".TRACKER_RANNOUNCES_TABLE.".seeders) FROM ".TRACKER_RANNOUNCES_TABLE." WHERE ".TRACKER_TORRENTS_TABLE.".id=".TRACKER_RANNOUNCES_TABLE.".torrent), 0),
					".TRACKER_TORRENTS_TABLE.".rem_leechers=IFNULL((SELECT SUM(".TRACKER_RANNOUNCES_TABLE.".leechers) FROM ".TRACKER_RANNOUNCES_TABLE." WHERE ".TRACKER_TORRENTS_TABLE.".id=".TRACKER_RANNOUNCES_TABLE.".torrent), 0),
					".TRACKER_TORRENTS_TABLE.".rem_times_completed=IFNULL((SELECT SUM(".TRACKER_RANNOUNCES_TABLE.".times_completed) FROM ".TRACKER_RANNOUNCES_TABLE." WHERE ".TRACKER_TORRENTS_TABLE.".id=".TRACKER_RANNOUNCES_TABLE.".torrent), 0),
					".TRACKER_TORRENTS_TABLE.".lastremote='{$dt}'
					 WHERE ".TRACKER_TORRENTS_TABLE.".id IN('".implode("', '", $torrents_id)."');";
				my_sql_query($sql);
			}

			my_sql_query("DELETE FROM ".TRACKER_CRON_TABLE." WHERE id='{$row['id']}'");
		}
		else if($row['type']=='update_tstatus' && $config['ppkbb_tccron_jobs'][3])
		{
			my_sql_query("UPDATE ".TRACKER_CRON_TABLE." SET status='1' WHERE id='{$row['id']}'");
			$update_tstatus=unserialize(stripslashes($row['data']));
			if(is_array($update_tstatus) && sizeof($update_tstatus))
			{
				my_sql_query("UPDATE ".TRACKER_TORRENTS_TABLE." SET forb='0' WHERE forb IN('".implode("', '", $update_tstatus)."')");

				my_sql_query("DELETE FROM ".TRACKER_CRON_TABLE." WHERE id='{$row['id']}'");
			}
		}
		else if($row['type']=='t_stat')
		{
			$total_rem_peers=$total_rem_leech=$total_rem_seed=0;
			if($config['ppkbb_tcenable_rannounces'][0])
			{
				$sql="SELECT SUM(rem_seeders) seeder, SUM(rem_leechers) leecher FROM ".TRACKER_TORRENTS_TABLE."";
				$result3=my_sql_query($sql, $config['ppkbb_tctstat_ctime']);
				$total_rem_peer=mysql_fetch_array($result3);
				mysql_free_result($result3);
				$total_rem_peers=my_int_val($total_rem_peer['seeder']+$total_rem_peer['leecher']);
				$total_rem_seed=my_int_val($total_rem_peer['seeder']);
				$total_rem_leech=my_int_val($total_rem_peer['leecher']);
			}

			$sql="SELECT SUM(IF(seeder='1',1,0)) seeder, SUM(IF(seeder='0',1,0)) leecher FROM ".TRACKER_PEERS_TABLE."";
			$result3=my_sql_query($sql, $config['ppkbb_tctstat_ctime']);
			$total_peer=mysql_fetch_array($result3);
			mysql_free_result($result3);
			$total_peers=my_int_val($total_peer['seeder']+$total_peer['leecher']+$total_rem_peers);
			$total_seed=my_int_val($total_peer['seeder']+$total_rem_seed);
			$total_leech=my_int_val($total_peer['leecher']+$total_rem_leech);
			my_set_config('ppkbb_total_seed_leech', "{$total_seed} {$total_leech}", true);

			$sql="SELECT SUM(uploaded) upload, SUM(downloaded) download FROM ".TRACKER_SNATCHED_TABLE."";
			$result3=my_sql_query($sql, $config['ppkbb_tctstat_ctime']);
			$total_updown=mysql_fetch_array($result3);
			mysql_free_result($result3);
			$total_up=my_int_val($total_updown['upload']);
			$total_down=my_int_val($total_updown['download']);
			my_set_config('ppkbb_total_up_down', "{$total_up} {$total_down}", true);

			$sql="SELECT SUM(uploaded) upload, SUM(downloaded) download FROM ".TRACKER_PEERS_TABLE."";
			$result3=my_sql_query($sql, $config['ppkbb_tctstat_ctime']);
			$total_supdown=mysql_fetch_array($result3);
			mysql_free_result($result3);
			$total_sup=my_int_val($total_supdown['upload']);
			$total_sdown=my_int_val($total_supdown['download']);
			my_set_config('ppkbb_total_sup_sdown', "{$total_sup} {$total_sdown}", true);

			$sql="SELECT SUM(size) size FROM ".TRACKER_FILES_TABLE."";
			$result3=my_sql_query($sql, $config['ppkbb_tctstat_ctime']);
			$total_size=mysql_fetch_array($result3);
			mysql_free_result($result3);
			$total_sizes=my_int_val($total_size['size']);
			my_set_config('ppkbb_total_peers_size', "{$total_peers} {$total_sizes}", true);

			$sql="SELECT COUNT(DISTINCT(torrent)) seeders, COUNT(DISTINCT(userid)) seeders2 FROM ".TRACKER_PEERS_TABLE." where seeder='1'";
			$result3=my_sql_query($sql, $config['ppkbb_tctstat_ctime']);
			$total_t_seeds=mysql_fetch_array($result3);
			mysql_free_result($result3);
			$t_seed=my_int_val($total_t_seeds['seeders']);

			$sql="SELECT COUNT(DISTINCT(torrent)) leechers, COUNT(DISTINCT(userid)) leechers2 FROM ".TRACKER_PEERS_TABLE." where seeder='0'";
			$result3=my_sql_query($sql, $config['ppkbb_tctstat_ctime']);
			$total_t_leechs=mysql_fetch_array($result3);
			mysql_free_result($result3);
			$t_leech=my_int_val($total_t_leechs['leechers']);
			my_set_config('ppkbb_total_tdown_tup', "{$t_seed} {$t_leech}", true);

			$u_seed=my_int_val($total_t_seeds['seeders2']);
			$u_leech=my_int_val($total_t_leechs['leechers2']);
			my_set_config('ppkbb_total_udown_uup', "{$u_seed} {$u_leech}", true);

			//if($dt - $config['ppkbb_trlast_stattime'] > $config['ppkbb_tctstat_ctime'])
			//{
				$sql="SELECT SUM(s.uploadoffset/(s.last_action-s.prev_action)) up_speed, SUM(s.downloadoffset/(s.last_action-s.prev_action)) down_speed FROM ".TRACKER_SNATCHED_TABLE." s WHERE s.last_action > ".($dt-$config['ppkbb_tcdead_time'])." AND s.last_action>s.prev_action";
				$result3=my_sql_query($sql, $config['ppkbb_tctstat_ctime'], 'trstat#up_speed-down_speed');
				$total_updown_speed=mysql_fetch_array($result3);
				mysql_free_result($result3);
				$up_speed=my_int_val($total_updown_speed['up_speed']);
				$down_speed=my_int_val($total_updown_speed['down_speed']);
				/*set_tracker_config('ppkbb_trlast_stattime', $dt);
				set_tracker_config('ppkbb_trstat_vals', "{$up_speed} {$down_speed}");
			}
			else
			{
				list($up_speed, $down_speed, )=@explode(' ', $config['ppkbb_trstat_vals']);
			}*/
			my_set_config('ppkbb_total_speedup_speeddown', "{$up_speed} {$down_speed}", true);

			/*$sql = 'SELECT COUNT(t.topic_id) AS stat
				FROM ' . TOPICS_TABLE . ' t, '.TRACKER_TORRENTS_TABLE.' tt, '.FORUMS_TABLE.' f
				WHERE t.topic_id=tt.topic_id AND f.forum_id=t.forum_id AND f.forumas=1';*/
			$sql = 'SELECT SUM(forum_topics) AS stat
				FROM '.FORUMS_TABLE.'
				WHERE forumas=1';
			$result = my_sql_query($sql);
			$num_torrents=mysql_fetch_row($result);
			$num_torrents=my_int_val($num_torrents[0]);
			mysql_free_result($result);
			my_set_config('num_torrents', (int) $num_torrents, true);

			/*$sql = 'SELECT COUNT(p.post_id) AS stat
				FROM ' . TOPICS_TABLE . ' t, '.POSTS_TABLE.' p, '.FORUMS_TABLE.' f
				WHERE t.topic_id=p.topic_id AND t.topic_first_post_id!=p.post_id AND f.forum_id=t.forum_id AND f.forumas=1';*/
			$sql = 'SELECT SUM(forum_posts)-SUM(forum_topics) AS stat
				FROM '.FORUMS_TABLE.'
				WHERE forumas=1';
			$result = my_sql_query($sql);
			$num_comments=mysql_fetch_row($result);
			$num_comments=my_int_val($num_comments[0]);
			mysql_free_result($result);
			my_set_config('num_comments', (int) $num_comments, true);

			my_set_config('ppkbb_last_stattime', $dt, true);
			my_sql_query("DELETE FROM ".TRACKER_CRON_TABLE." WHERE id='{$row['id']}'");
			$num_thanks=0;
			if($config['ppkbb_thanks_enable'])
			{
				$sql="SELECT COUNT(*) thanks FROM ".TRACKER_THANKS_TABLE;
				$result=my_sql_query($sql);
				$num_thanks=mysql_fetch_row($result);
				$num_thanks=my_int_val($num_thanks[0]);
				mysql_free_result($result);
			}
			my_set_config('ppkbb_total_thanks_seedreq', "{$num_thanks} 0", true);
		}
		else if($row['type']=='u_update')
		{
			my_sql_query("UPDATE ".TRACKER_CRON_TABLE." SET status='1' WHERE id='{$row['id']}'");
			$u_update=unserialize(stripslashes($row['data']));
			$u_cron=array();
			$user_tracker_data=my_split_config($u_update[4], 5, 'my_int_val');
			$u_update[3]=my_int_val($u_update[3]);
			if($u_update[0] && $config['ppkbb_tccron_jobs'][0])
			{
				$sql='SELECT COUNT(t.topic_id) AS torr
					FROM ' . TOPICS_TABLE . ' t, '.TRACKER_TORRENTS_TABLE.' tt, '.FORUMS_TABLE." f
					WHERE tt.poster_id='{$u_update[3]}' AND t.topic_id=tt.topic_id AND f.forum_id=t.forum_id AND f.forumas=1";
				$result3=my_sql_query($sql/*, $config['ppkbb_tctstat_ctime']*/);
				$user_torr_data=mysql_fetch_array($result3);
				$user_torr_data['torr']=intval(@$user_torr_data['torr']);
				mysql_free_result($result3);
				$u_cron[]="user_torrents='{$user_torr_data['torr']}'";
				$user_tracker_data[0]=$dt;
			}
			if($u_update[1] && $config['ppkbb_tccron_jobs'][1])
			{
				$sql = 'SELECT COUNT(p.post_id) AS comm
					FROM ' . TOPICS_TABLE . ' t, '.POSTS_TABLE.' p, '.FORUMS_TABLE." f
					WHERE p.poster_id='{$u_update[3]}' AND t.topic_id=p.topic_id AND t.topic_first_post_id!=p.post_id AND f.forum_id=t.forum_id AND f.forumas=1";
				$result3=my_sql_query($sql/*, $config['ppkbb_tctstat_ctime']*/);
				$user_comm_data=mysql_fetch_array($result3);
				$user_comm_data['comm']=intval(@$user_comm_data['comm']);
				mysql_free_result($result3);
				$u_cron[]="user_comments='{$user_comm_data['comm']}'";
				$user_tracker_data[1]=$dt;
			}
			if(sizeof($u_cron))
			{
				$u_cron[]="user_tracker_data='".implode(' ', $user_tracker_data)."'";
				$sql="UPDATE ".USERS_TABLE." SET ".implode(', ', $u_cron)." WHERE user_id='{$u_update[3]}'";
				my_sql_query($sql);
			}
			my_sql_query("DELETE FROM ".TRACKER_CRON_TABLE." WHERE id='{$row['id']}'");
		}
		/*else if($row['type']=='t_dtad')
		{
			if($config['ppkbb_deadtorrents_autodelete'][0] && $config['ppkbb_deadtorrents_autodelete'][1])
			{
				$delete_topics=array();
				$sql="SELECT topic_id FROM ".TRACKER_TORRENTS_TABLE." WHERE forum_id='{$row['forum_id']}' AND
					(
						(lastseed!='0' AND {$dt}-lastseed>{$config['ppkbb_deadtorrents_autodelete'][1]}"
							.($config['ppkbb_deadtorrents_autodelete'][2] ? " AND {$dt}-added>{$config['ppkbb_deadtorrents_autodelete'][2]}" : '').")"
							.($config['ppkbb_deadtorrents_autodelete'][2] ? " OR (lastseed='0' AND {$dt}-added>{$config['ppkbb_deadtorrents_autodelete'][2]})" : '')
					.")"
						.($config['ppkbb_deadtorrents_autodelete'][4] ? " AND rem_seeders='0'" : '');
				$result2=my_sql_query($sql);
				while($row2=mysql_fetch_array($result2))
				{
					$delete_topics[$row2['topic_id']]=$row2['topic_id'];
				}
				mysql_free_result($result2);
				if(sizeof($delete_topics))
				{
					include($phpbb_root_path . 'common.' . $phpEx);

					// Do not update users last page entry
					$user->session_begin(false);
					$auth->acl($user->data);

					include_once("{$phpbb_root_path}includes/functions_admin.{$phpEx}");

					delete_topics('topic_id', $delete_topics);

					set_tracker_config('ppkbb_last_dtad', "{$dt} {$row['forum_id']}");

					$t_dtad=true;
				}
			}
			my_sql_query("DELETE FROM ".TRACKER_CRON_TABLE." WHERE id='{$row['id']}'");
		}*/
	}
	mysql_free_result($result0);
	if($dt-$config['ppkbb_cron_last_cleanup'] > $config['ppkbb_cron_options'][1] && $config['ppkbb_cron_options'][0])
	{
		my_sql_query("DELETE FROM ".TRACKER_CRON_TABLE." WHERE added > {$dt} OR {$dt}-added > ".($config['ppkbb_cron_options'][0]));
		my_set_config('ppkbb_cron_last_cleanup', $dt, true);
		//purge_tracker_config(true);
	}
	/*if($t_dtad)
	{
		// Unloading cache and closing db after having done the dirty work.
		garbage_collection();

		exit();
	}*/
}
else
{

}

if($c)
{
	mysql_close($c);
}

//##############################################################################
//if(!function_exists('hex2bin'))
//{
	function myhex2bin($str)
	{
		$bin = "";
		$i = 0;
		do
		{
			$bin .= @chr(hexdec($str{$i}.$str{($i + 1)}));
			$i += 2;
		} while ($i < strlen($str));
		return $bin;
	}
//}
function my_split_config($config, $count=0, $type=false, $split='')
{
	$count=intval($count);

	if(!$count && $config==='')
	{
		return array();
	}

	$s_config=$count > 0 ? @explode($split ? $split : ' ', $config, $count) : @explode($split ? $split : ' ', $config);
	$count=$count > 0 ? $count : sizeof($s_config);
	if($count)
	{
		for($i=0;$i<$count;$i++)
		{
			if($type)
			{
				if(is_array($type) && @function_exists(@$type[$i]))
				{
					$s_config[$i]=call_user_func($type[$i], @$s_config[$i]);
				}
				else if(@function_exists($type))
				{
					$s_config[$i]=call_user_func($type, @$s_config[$i]);
				}
				else
				{
					$s_config[$i]=@$s_config[$i];
				}
			}
			else
			{
				$s_config[$i]=@$s_config[$i];
			}
		}
	}

	return $s_config;
}

function my_int_val($v=0, $max=0, $drop=false, $negative=false)
{
	if(!$v || ($v < 0 && !$negative))
	{
		return 0;
	}
	else if($drop && $v>$max)
	{
		return 0;
	}
	else if($max && $v>$max)
	{
		return $max;
	}

	return @number_format($v+0, 0, '', '');
}

function my_float_val($v=0, $n=3, $max=0, $drop=false, $negative=false)
{
	if(!$v || ($v < 0 && !$negative))
	{
		return "0.".str_repeat('0', $n);
	}
	else if($drop && $v>$max)
	{
		return "0.".str_repeat('0', $n);
	}
	else if($max && $v>$max)
	{
		return $max;
	}

	return @number_format($v+0, $n, '.', '');
}

function t_getcache($t, $var='')
{
	global $tcachedir, $phpEx;

	$cache_data=array();

	$f_name="{$tcachedir}data_ppkbb3cker_{$t}.{$phpEx}";
	if(@file_exists($f_name))
	{
		include($f_name);

		return $var ? $$var : $cache_data;
	}

	return false;
}

function err()
{
	global $c;

	if($c)
	{
		mysql_close($c);
	}

	exit();
}

//From includes/functions.php
function my_set_config($config_name, $config_value, $is_dynamic = false, $is_tracker=false)
{
	global $c, $config;

	if(!$is_tracker)
	{
	$sql = 'UPDATE ' . CONFIG_TABLE . "
			SET config_value = '" . mysql_real_escape_string($config_value, $c) . "', is_dynamic='".($is_dynamic ? 1 : 0)."'
			WHERE config_name = '" . mysql_real_escape_string($config_name, $c) . "'";
		$result=my_sql_query($sql);
	}
	else
	{
		$sql = 'UPDATE ' . TRACKER_CONFIG_TABLE . "
		SET config_value = '" . mysql_real_escape_string($config_value, $c) . "'
		WHERE config_name = '" . mysql_real_escape_string($config_name, $c) . "'";
	$result=my_sql_query($sql);
	}

	/*if (!mysql_affected_rows($c) && !isset($config[$config_name]))
	{
		$sql = 'INSERT INTO ' . CONFIG_TABLE . " (config_name, config_value, is_dynamic) VALUES ('".mysql_real_escape_string($config_name, $c)."', '".mysql_real_escape_string($config_value, $c)."', '".($is_dynamic ? 1 : 0)."')";
		my_sql_query($sql);
	}*/

}

function get_forb_rtrack()
{
	global $tincludedir, $phpEx;

	$forb_rtracks=array();

	$forb_rtracks=t_getcache('forb_rtrack');
	if($forb_rtracks===false)
	{
	$forb_rtracks=array();

	$sql='SELECT id, rtrack_url, rtrack_forb, forb_type FROM '.TRACKER_RTRACK_TABLE." WHERE rtrack_enabled='1' AND zone_id='0' AND rtrack_remote!='0' AND torrent='0' AND rtrack_forb!='0'";
		$result=my_sql_query($sql);
	while($row=mysql_fetch_array($result))
	{
			$forb_rtracks[]=array('id'=>$row['id'], 'rtrack_url'=>$row['rtrack_url'], 'rtrack_forb'=>$row['rtrack_forb'], 'forb_type'=>$row['forb_type']);
	}
	mysql_free_result($result);
		include_once("{$tincludedir}tcache.{$phpEx}");

		t_recache('forb_rtrack', $forb_rtracks);
	}

	unset($forb_rtracks['forb_rtrack_cachetime']);

	return $forb_rtracks;
}

function my_sql_query($query)
{
	global $c;

	$result=@mysql_query($query, $c);

	if(!$result)
	{
		err('Unknown sql error');
		mysql_close($c);
	}

	return $result;
}
?>
