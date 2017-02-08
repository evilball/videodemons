<?php

/**
*
* @package ppkBB3cker
* @version $Id: scrape.php 1.000 2008-10-05 12:30:00 PPK $
* @copyright (c) 2008 PPK
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

error_reporting(0);
@ini_set('register_globals', 0);
@ini_set('magic_quotes_runtime', 0);
@ini_set('magic_quotes_sybase', 0);

function_exists('date_default_timezone_set') ? date_default_timezone_set('Europe/Moscow') : '';

define('IN_PHPBB', true);
define('IS_GUESTS', 0);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
$tcachedir="{$phpbb_root_path}cache/";
$tincludedir="{$phpbb_root_path}tracker/tinc/";

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

define('TRACKER_TORRENTS_TABLE', $table_prefix . 'tracker_torrents');

$config=array();

$cache_config=t_getcache('tracker_sconfig');
if($cache_config===false)
{
	include($tincludedir.'tsconf.'.$phpEx);
}
else
{
	foreach($cache_config as $k => $v)
	{
		$config[$k]=$v;
	}
	unset($cache_config);
}

if(!$config['ppkbb_scrape_enabled'])
{
	err("Scrape functions disabled");
}

define('STRIP', (get_magic_quotes_gpc()) ? true : false);

preg_match_all('/info_hash=([^&]*)/i', $_SERVER['QUERY_STRING'], $info_hashs);
if(isset($info_hashs[1]) && $info_hashs[1])
{
	foreach($info_hashs[1] as $k => $info_hash)
	{
		$info_hash=urldecode($info_hash);
		STRIP ? $info_hash=stripslashes($info_hash) : '';
		$l_info_hash=strlen($info_hash);
		if($l_info_hash!=20)
		{
			err("invalid info_hash: {$info_hash} ({$l_info_hash})");
		}
		$info_hashs[1][$k] = mysql_real_escape_string($info_hash, $c);
	}
}
else
{
	err("Invalid info hash(s)");
}
$sql = "SELECT info_hash, seeders, leechers, times_completed FROM ".TRACKER_TORRENTS_TABLE." WHERE info_hash IN('".implode("', '", $info_hashs[1])."')";
$result=my_sql_query($sql);
$torrent = mysql_num_rows($result);

if (!$torrent)
{
	err("Torrent(s) not found on this tracker - hash(s): " . implode(", ", $info_hashs[1]));
}

$resp='d5:filesd';
while($row = mysql_fetch_array($result))
{
	$resp.='20:'.$row['info_hash'].'d';
	$resp.='8:completei'.$row['seeders'].'e';
	$resp.='10:downloadedi'.$row['times_completed'].'e';
	$resp.='10:incompletei'.$row['leechers'].'e';
	$resp.='e';
}
mysql_free_result($result);
$resp.='ee';
//resp.="5:flagsd20:min_request_intervali{$config['ppkbb_minscrape_interval']}eee";

benc_resp_raw($resp, $config['ppkbb_tcgz_rewrite']);

if($c)
{
	mysql_close($c);
}

exit();

//############################################################
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
	$c = $obj['value'];
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
	$s = "l";
	foreach ($a as $e)
	{
		$s .= benc($e);
	}
	$s .= "e";
	return $s;
}

function benc_dict($d)
{
	$s = "d";
	$keys = array_keys($d);
	sort($keys);
	foreach ($keys as $k)
	{
		$v = $d[$k];
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
?>
