<?php
/**
*
* @package ppkBB3cker
* @version $Id: announce2.php 1.000 2009-11-06 19:15:00 PPK $
* @copyright (c) 2009 PPK
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

error_reporting(0);
@ini_set('register_globals', 0);
@ini_set('magic_quotes_runtime', 0);
@ini_set('magic_quotes_sybase', 0);
//@ini_set('zlib.output_compression', 'Off');

function_exists('date_default_timezone_set') ? date_default_timezone_set('Europe/Moscow') : '';

define('IN_PHPBB', true);
define('IS_GUESTS', 1);

$phpbb_root_path=(defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx=substr(strrchr(__FILE__, '.'), 1);
$tcachedir="{$phpbb_root_path}cache/";
$tincludedir="{$phpbb_root_path}tracker/tinc/";
$upthis=$downthis=$unregtorr=0;

if(isset($_SERVER['HTTP_ACCEPT_CHARSET'])/* || isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])*/ || isset($_SERVER['HTTP_REFERER'])/*  || isset($_SERVER['HTTP_COOKIE'])*/ || isset($_SERVER['HTTP_X_REQUESTED_WITH']))//ut 3.2.2, iis
{
	err('Unknown Client');
}

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

$dt=time();
define('STRIP', (get_magic_quotes_gpc()) ? true : false);

$uploaded=my_int_val(@$_GET['uploaded']);

$downloaded= my_int_val(@$_GET['downloaded']);

$left=my_int_val(@$_GET['left']);

$compact=intval(@$_GET['compact']);

$no_peer_id=intval(@$_GET['no_peer_id']);

$event=@$_GET['event'];
$event=($event=='stopped' || $event=='completed' || $event=='started') ? $event : '';


$peer_id=@$_GET['peer_id'];
STRIP ? $peer_id=stripslashes($peer_id) : '';
$l_peer_id=strlen($peer_id);
if($l_peer_id != 20)
{
	err("invalid peer_id: {$peer_id} ({$l_peer_id})");
}
$c_peer_id=$peer_id;
$passkey=mysql_real_escape_string(md5($peer_id), $c);
$peer_id=mysql_real_escape_string($peer_id, $c);

$session_id=STRIP ? @$_GET['passkey'] : mysql_real_escape_string(@$_GET['passkey'], $c);
/*if(!$session_id)
{
	err('Passkey not defined');
}*/

$info_hash=@$_GET['info_hash'];
STRIP ? $info_hash=stripslashes($info_hash) : '';
$l_info_hash=strlen($info_hash);
$c_info_hash=$info_hash;
if ($l_info_hash != 20)
{
	err("Invalid info_hash: {$info_hash} ({$l_info_hash})");
}
$info_hash=mysql_real_escape_string($info_hash, $c);

$seeder=($left==0) ? 1 : 0;
$seeder && $event!='completed' ? $downloaded=0 : '';

define('ACL_USERS_TABLE', $table_prefix . 'acl_users');
define('ACL_ROLES_DATA_TABLE', $table_prefix . 'acl_roles_data');
define('USERS_TABLE', $table_prefix . 'users');
define('TRACKER_TORRENTS_TABLE', $table_prefix . 'tracker_torrents');
define('TRACKER_PEERS_TABLE', $table_prefix . 'tracker_peers');
define('TRACKER_SNATCHED_TABLE', $table_prefix . 'tracker_snatched');
define('FORUMS_TABLE', $table_prefix . 'forums');
define('POSTS_TABLE', $table_prefix . 'posts');
define('ATTACHMENTS_TABLE', $table_prefix . 'attachments');
define('TRACKER_GUESTS_TABLE', $table_prefix . 'tracker_guests');

define('ANONYMOUS', 1);
define('USER_NORMAL', 0);
define('USER_FOUNDER', 3);

$config=array();

$cache_config=t_getcache('tracker_gconfig');
if($cache_config===false)
{
	include($tincludedir.'tgconf.'.$phpEx);
}
else
{
	foreach($cache_config as $k => $v)
	{
		$config[$k]=$v;
	}
	unset($cache_config);
}
if($config['ppkbb_tctracker_disabled'])
{
	err('Sorry, tracker disabled');
}
if(!$config['ppkbb_tcguests_enabled'][0])
{
	err('Sorry, guests tracker disabled');
}

if($config['ppkbb_tcclients_restricts'][2])
{
	include($tincludedir.'tcrestrpeerid.'.$phpEx);
}

$agent=@$_SERVER['HTTP_USER_AGENT'];
STRIP ? $agent=stripslashes($agent) : '';
if($config['ppkbb_tcclients_restricts'][1])
{
	include($tincludedir.'tcrestrua.'.$phpEx);
}
$agent=substr($agent, 0, 64);
$agent=mysql_real_escape_string($agent, $c);

$port=my_int_val(@$_GET['port']);
if (!$port || $port > 65535)
{
	err("Invalid port");
}
if($config['ppkbb_tcclients_restricts'][0])
{
	include($tincludedir.'tcrestrport.'.$phpEx);
}

$ip=false;
if($config['ppkbb_tciptype']==2 && isset($_GET['ip']))
{
	$ip=check_ip($_GET['ip']);
}
else if($config['ppkbb_tciptype']==1 && isset($_SERVER['HTTP_X_FORWARDED_FOR']))
{
	$ip=check_ip($_SERVER['HTTP_X_FORWARDED_FOR']);
}
if(!$ip)
{
	$ip=$_SERVER['REMOTE_ADDR'];
	if($ip!=check_ip($ip))
	{
		err('Invalid IP address');
	}
}
!STRIP ? $ip=mysql_real_escape_string($ip, $c) : '';

$sql="SELECT tt.id, tt.seeders, tt.leechers, tt.times_completed, tt.added, tt.size, tt.forb, tt.poster_id, tt.unreg, tt.tsl_speed, g.user_id, s.finished FROM ".TRACKER_TORRENTS_TABLE." tt LEFT JOIN " . TRACKER_GUESTS_TABLE . " g ON (g.user_passkey='{$passkey}') LEFT JOIN ".TRACKER_SNATCHED_TABLE." s ON (tt.id=s.torrent AND g.user_id=s.userid AND s.guests!='0') WHERE tt.info_hash='{$info_hash}' LIMIT 1";

$result=my_sql_query($sql);
$user=mysql_fetch_array($result);
mysql_free_result($result);

$updateset=$updateuser=$updatesnatch=$updatepeers=$snatch_add=array();
$updatesnatch[]=$updatepeers[]="to_go='{$left}'";
$updatesnatch[]=$updatepeers[]="guests='1'";
$snatch_add['k'][]='guests';
$snatch_add['v'][]=1;
$torrentid=@$user['id'];
$numpeers=my_int_val(@$user['seeders']+@$user['leechers']);
$userid=@$user['user_id'];
$unregtorr=@$user['unreg'] && $config['ppkbb_tcallow_unregtorr'] ? 1 : 0;

if(!$torrentid)
{
	if(!$config['ppkbb_tcallow_unregtorr'])
	{
		err("Torrent not found on this tracker, hash: {$info_hash} ({$l_info_hash})");
	}
	else
	{
		include($tincludedir.'gtunreg.'.$phpEx);
	}
}
else
{
	if($unregtorr)
	{
		empty($config['ppkbb_tcunregtorr_sessid']) ? $config['ppkbb_tcunregtorr_sessid']=$session_id=md5(@$_SERVER['SERVER_ADDR']) : '';
		if($session_id!=$config['ppkbb_tcunregtorr_sessid'])
		{
			err('Invalid passkey');
		}
	}
	else
	{
		if(!$session_id)
		{
			err('Passkey not defined');
		}
	}
}

if ($user['forb'] > 0)
{
	err("Forbidden or Blocked torrent");
}
else if ($user['forb'] < 1 && in_array($user['forb'], $config['ppkbb_tcguest_cantdown']))
{
	err("For registered users only");

}

if(!$userid)
{
	$result=my_sql_query("SELECT user_id, user_passkey FROM ".TRACKER_GUESTS_TABLE." WHERE session_id='{$session_id}' LIMIT 1");
	$userid=mysql_fetch_array($result);
	if(!mysql_num_rows($result))
	{
		err('Session ID not found, please re-download torrent');
	}
	else
	{
		mysql_free_result($result);
		if(!$userid['user_passkey'])
		{
			$result=my_sql_query("UPDATE ".TRACKER_GUESTS_TABLE." SET user_passkey='{$passkey}', user_time='{$dt}' WHERE user_id='{$userid['user_id']}'");
			$userid=$userid['user_id'];
		}
		else
		{
			if($config['ppkbb_tcmax_sessions'])
			{
				$result=my_sql_query("SELECT DISTINCT(COUNT(session_id)) sessions FROM ".TRACKER_GUESTS_TABLE." WHERE unreg='0'");
				$sess_count=mysql_fetch_array($result);
				mysql_free_result($result);
				if(intval(@$sess_count['sessions']) >= $config['ppkbb_tcmax_sessions'])
				{
					err('Limit sessions for guests reached, try again later');
				}
			}
			$result=my_sql_query("INSERT INTO ".TRACKER_GUESTS_TABLE." (user_passkey, user_ip, user_time, user_last_time, session_id, unreg) VALUES('{$passkey}', INET_ATON('{$ip}'), '{$dt}', '{$dt}', '{$session_id}', '{$unregtorr}')");
			$userid=mysql_insert_id($c);
		}
	}
}

if(!$userid)
{
	err('Not found user with this passkey');
}

$user['user_id']=1;
$user['user_permissions']='';
$user['user_type']=ANONYMOUS;
$selfwhere="tp.torrent='{$torrentid}' AND tp.userid='{$userid}' AND tp.guests!='0'";

$sql="SELECT a.attach_id, a.in_message, a.extension, p.post_id, f.forum_id, f.forum_status, f.forum_password, f.forumas, tp.seeder, tp.last_action, tp.id, tp.uploaded, tp.downloaded, IF(tp.userid='{$userid}', tp.rights, 0) rights
	FROM ".ATTACHMENTS_TABLE." a LEFT JOIN ".POSTS_TABLE." p ON (p.post_id=a.post_msg_id) LEFT JOIN ".FORUMS_TABLE." f ON (f.forum_id=p.forum_id) LEFT JOIN ". TRACKER_PEERS_TABLE ." tp ON ({$selfwhere})
	WHERE a.attach_id='{$torrentid}' LIMIT 1";

$result=my_sql_query($sql);
$attachment=mysql_fetch_array($result);
mysql_free_result($result);

if ($config['ppkbb_tcminannounce_interval'] && !$event && $dt - $attachment['last_action'] < ($config['ppkbb_tcminannounce_interval'] - 10))
{
	err("Sorry, minimum announce interval={$config['ppkbb_tcminannounce_interval']} sec.");
}

if(!$unregtorr)
{
	if(!$attachment['attach_id'] || !$attachment['post_id'] || !$attachment['forum_id'])
	{
		err('Error, no attachment or post or forum');
	}

	if($attachment['forum_status']==1)
	{
		err('Sorry, this forum locked');
	}

	if(!$attachment['in_message'])
	{
		$forum_astracker=$attachment['forumas']==1 ? 1 : 0;
		$forum_id=$attachment['forum_id'];

		if(!$forum_astracker)
		{
			err('This torrent not in a tracker');
		}
	}
	else
	{
		err('Tracker functions disabled in pm');
	}

	if($config['ppkbb_tccheck_fext'])
	{
		include($tincludedir.'tattach.'.$phpEx);
	}
	else
	{
		if($attachment['extension']!='torrent')
		{
			err('Not .torrent extension');
		}
	}
}
else
{
	$forum_id=0;
}

@$attachment['rights']!='' ? $rights=explode(' ', $attachment['rights']) : '';
if(@$attachment['rights']=='' || $forum_id!=@$rights[1] || $dt - @$rights[0] > $config['ppkbb_tcrights_tcache'])
{
	include($tincludedir.'trights.'.$phpEx);
}
else
{
	array_shift($rights);
	array_shift($rights);
}

if(/*$rights[0] || */$rights[11])
{
	if ($attachment['forum_password'])
	{
		err('Access forbidden, password protected');
	}
}
else
{
	if($config['ppkbb_tctrestricts_options'][2] && $user['size'] < $config['ppkbb_tctrestricts_options'][2])
	{

	}
	else
	{
	err("Sorry, you can't download torrents");
}

}
if(!$rights[1])
{
	err("You can't use tracker");
}

if(!$unregtorr && ($seeder && $config['ppkbb_tcmax_seed'] && !$rights[7]) || (!$seeder && $config['ppkbb_tcmax_leech'] && !$rights[2]))
{
	include($tincludedir.'tgmaxsl.'.$phpEx);
}
if(!$rights[13] && ($config['ppkbb_tcmaxip_pertorr'] || $config['ppkbb_tcmaxip_pertr']))
{
	include($tincludedir.'tmaxip.'.$phpEx);
}
/*if (!$attachment['id'])
{
	if($userid!=$user['poster_id'])
	{
		include($tincludedir.'tanotself1.'.$phpEx);
	}
}
else
{*/
	include($tincludedir.'tgaself1.'.$phpEx);
//}

if($event=="stopped")
{
	if($attachment['id'])
	{
		include($tincludedir.'taselfstopped.'.$phpEx);
	}
}
else
{
	if($event=="completed")
	{
		include($tincludedir.'tacompleted.'.$phpEx);
	}
	if($attachment['id'])
	{
		include($tincludedir.'taself2.'.$phpEx);
	}
	else
	{
		include($tincludedir.'tanotself2.'.$phpEx);
	}
}

if($seeder)
{
	$updateset[]="lastseed='{$dt}'";
}
else
{
	$updateset[]="lastleech='{$dt}'";
}

$tsl_speed=my_split_config($user['tsl_speed'], 3, 'my_int_val');
if($dt - $tsl_speed[2] > $config['ppkbb_tctstat_ctime'])
{
	$sql="SELECT SUM(s.uploadoffset/(s.last_action-s.prev_action)) up_speed, SUM(s.downloadoffset/(s.last_action-s.prev_action)) down_speed FROM ".TRACKER_SNATCHED_TABLE." s WHERE torrent='{$torrentid}' AND s.last_action > ".($dt-$config['ppkbb_tcdead_time'])." AND s.last_action>s.prev_action";
	$result=my_sql_query($sql);
	$total_updown_speed=mysql_fetch_array($result);
	mysql_free_result($result);
	$updateset[]="tsl_speed='".intval($total_updown_speed['up_speed'])." ".intval($total_updown_speed['down_speed'])." {$dt}'";
}

if(sizeof($updateset))
{
	my_sql_query("UPDATE ". TRACKER_TORRENTS_TABLE ." SET " . implode(", ", $updateset) . " WHERE id='{$torrentid}'");
}

$limit='';
$rsize=my_int_val($config['ppkbb_tcmaxpeers_limit']);
if(!$config['ppkbb_tcmaxpeers_rewrite'])
{
	$rsize=my_int_val(@$_GET['numwant']);
}
if ($rsize && $numpeers && $numpeers > $rsize)
{
	$limit="ORDER BY RAND() LIMIT {$rsize}";
}
if(!$config['ppkbb_tcignore_connectable'])
{
	$limit="AND tp.connectable='1' {$limit}";
}

$config['ppkbb_tcannounce_interval'] ? '' : $config['ppkbb_tcannounce_interval']=1800;

$plist='';
$resp="d8:completei{$user['seeders']}e10:downloadedi{$user['times_completed']}e10:incompletei{$user['leechers']}e";
$resp .= benc_str('interval') . 'i' . $config['ppkbb_tcannounce_interval'] . 'e'
	. benc_str('min interval') . 'i' . $config['ppkbb_tcminannounce_interval'] . 'e'
	. benc_str('peers') . ($compact ? '' : 'l');

$sql="SELECT INET_NTOA(tp.ip) ip, tp.port".($no_peer_id ? '' : ', tp.peer_id')." FROM ". TRACKER_PEERS_TABLE ." tp WHERE tp.torrent='{$torrentid}'".($attachment['id'] ? " AND tp.id!='{$attachment['id']}'" : '').($seeder ? " AND tp.seeder!='1'" : '').($config['ppkbb_tcguests_enabled'][0] && !$config['ppkbb_tcguests_enabled'][1] ? " AND tp.guests!='0'" : '')." {$limit}";
$result=my_sql_query($sql);
while ($row=mysql_fetch_array($result))
{
	if($compact)
	{
		$peer_ip=explode('.', $row['ip']);
		$plist .= pack("C*", $peer_ip[0], $peer_ip[1], $peer_ip[2], $peer_ip[3]). pack("n*", (int) $row['port']);
	}
	else
	{
		$resp .= "d" .
			benc_str('ip') . benc_str($row['ip']) .
			(!$no_peer_id ? benc_str('peer id') . benc_str(str_pad(stripslashes($row['peer_id']), 20)) : '') .
			benc_str('port') . "i" . $row['port'] . "ee";
	}
}
mysql_free_result($result);

$resp .= ($compact ? benc_str($plist) : '') . 'ee';

benc_resp_raw($resp, $config['ppkbb_tcgz_rewrite']);

if($c)
{
	mysql_close($c);
}

exit();

//############################################################
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

function err($msg)
{
	global $c;

	if($msg)
	{
		benc_resp(array("failure reason" => array('type' => "string", 'value' => $msg)));
	}

	if($c)
	{
		mysql_close($c);
	}

	exit();
}

function warn($msg)
{
	global $c;

	if($msg)
	{
		benc_resp(array("warning message" => array('type' => "string", 'value' => $msg)));
	}

	if($c)
	{
		mysql_close($c);
	}

	exit();
}

function benc($obj)
{
	if (!is_array($obj) || !isset($obj['type']) || !isset($obj['value']))
	{
		return;
	}
	$c=$obj['value'];
	switch ($obj['type'])
	{
		case "string":
			return benc_str($c);
		case "integer":
			return benc_int($c);
		case "list":
			return benc_list($c);
		case "dictionary":
			return benc_dict($c);
		default:
			return;
	}
}

function benc_str($s)
{
	return strlen($s) . ":$s";
}

function benc_int($i)
{
	return "i" . $i . "e";
}

function benc_list($a)
{
	$s="l";
	foreach ($a as $e)
	{
		$s .= benc($e);
	}
	$s .= "e";
	return $s;
}

function benc_dict($d)
{
	$s="d";
	$keys=array_keys($d);
	sort($keys);
	foreach ($keys as $k)
	{
		$v=$d[$k];
		$s .= benc_str($k);
		$s .= benc($v);
	}
	$s .= "e";
	return $s;
}

function benc_resp($d)
{
	global $config;

	benc_resp_raw(benc(array('type' => "dictionary", 'value' => $d)), $config['ppkbb_tcgz_rewrite']);
}

function benc_resp_raw($x, $c=0)
{
	$gz_enc=strstr(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') && @extension_loaded('zlib') && (ini_get('zlib.output_compression')=='Off' || !ini_get('zlib.output_compression')) ? 1 : 0;

	header("Content-Type: text/plain");
	if($c==1 || (!$c && $gz_enc))
	{
		header("Content-Encoding: gzip");

		print gzencode($x, 9, FORCE_GZIP);
	}
	else if($c==2 || (!$c && !$gz_enc) || !$gz_enc)
	{
		header("Pragma: no-cache");

		print($x);
	}
}

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

function check_ip($ip)
{
	$long=ip2long($ip);

	if($long==-1 || $long===false)
	{
		return false;
	}

	return $ip;
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
