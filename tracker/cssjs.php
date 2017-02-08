<?php

/**
*
* @package ppkBB3cker
* @version $Id: cssjs.php 1.000 2010-12-18 18:52:00 PPK $
* @copyright (c) 2010 PPK
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

error_reporting(0);
@ini_set('register_globals', 0);
@ini_set('magic_quotes_runtime', 0);
@ini_set('magic_quotes_sybase', 0);

function_exists('date_default_timezone_set') ? date_default_timezone_set('Europe/Moscow') : '';

define('IN_PHPBB', true);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

$css_set=isset($_GET['css_set']) ? intval($_GET['css_set']) : 0;
$js_set=isset($_GET['js_set']) ? intval($_GET['js_set']) : 0;

$no_cache=isset($_GET['no_cache']) && $_GET['no_cache'] ? true : false;
$type=isset($_GET['type']) ? $_GET['type'] : '';

$ppkbb3cker_addons="{$phpbb_root_path}tracker/addons/";
$data['css'][0]=array(
	$ppkbb3cker_addons.'css/spoiler.css',
	//$ppkbb3cker_addons.'css/easySlider.css',
	$ppkbb3cker_addons.'css/ppkbb3cker.css',
	$ppkbb3cker_addons.'css/prettyPhoto.css',
	$ppkbb3cker_addons.'css/jquery.tooltip.css',
	$ppkbb3cker_addons.'css/tabs.css',
	$ppkbb3cker_addons.'css/fixedMenu.css',
);

$data['css'][1]=array(
	$ppkbb3cker_addons.'css/spoiler.css',
	//$ppkbb3cker_addons.'css/easySlider.css',
	$ppkbb3cker_addons.'css/ppkbb3cker.css',
	$ppkbb3cker_addons.'css/prettyPhoto.css',
	$ppkbb3cker_addons.'css/jquery.tooltip.css',
	$ppkbb3cker_addons.'css/tabs.css',
	$ppkbb3cker_addons.'css/fixedMenu.css',
	$ppkbb3cker_addons.'css/admin.css',
);

$data['js'][0]=array(
	$ppkbb3cker_addons.'js/jquery.tooltip.js',
	$ppkbb3cker_addons.'js/jquery.prettyPhoto.js',
	$ppkbb3cker_addons.'js/jquery.fixedMenu.js',
	$ppkbb3cker_addons.'js/spoiler.js',
	$ppkbb3cker_addons.'js/easySlider.js',
	$ppkbb3cker_addons.'js/tabs.js',
	$ppkbb3cker_addons.'js/ppkbb3cker.js',
);

$data['js'][1]=array(
	$ppkbb3cker_addons.'js/jquery.tooltip.js',
	$ppkbb3cker_addons.'js/jquery.prettyPhoto.js',
	$ppkbb3cker_addons.'js/jquery.fixedMenu.js',
	$ppkbb3cker_addons.'js/spoiler.js',
	//$ppkbb3cker_addons.'js/easySlider.js',
	$ppkbb3cker_addons.'js/tabs.js',
	$ppkbb3cker_addons.'js/ppkbb3cker.js',
);

if(!in_array($type, array('css', 'js')) || ($css_set && !isset($data[$type][$css_set])) || ($js_set && !isset($data[$type][$js_set])))
{
	exit();
}

$addit_cssjs['css']['sm']=array(
	'core' => $ppkbb3cker_addons.'css/smartmenus/sm-core-css.css',
	'blue' => $ppkbb3cker_addons.'css/smartmenus/sm-blue/sm-blue.css',
	'clean' => $ppkbb3cker_addons.'css/smartmenus/sm-clean/sm-clean.css',
	'mint' => $ppkbb3cker_addons.'css/smartmenus/sm-mint/sm-mint.css',
	'simple' => $ppkbb3cker_addons.'css/smartmenus/sm-simple/sm-simple.css',

	'prosilver' => $ppkbb3cker_addons.'css/smartmenus/sm-prosilver/sm-prosilver.css',
	'subsilver2' => $ppkbb3cker_addons.'css/smartmenus/sm-subsilver2/sm-subsilver2.css',
);
$addit_cssjs['js']['sm']=array(
	$ppkbb3cker_addons.'js/jquery.smartmenus.min.js',
);

$addit=isset($_GET['addit']) ? $_GET['addit'] : '';
if($addit)
{
	$addit=explode('|', $addit);
	if(sizeof($addit))
	{
		foreach($addit as $cssjs)
		{
			$cssjs=explode(',', $cssjs);
			if(isset($cssjs[0]))
			{
				if(sizeof($cssjs)==1)
				{
					isset($addit_cssjs[$type][$cssjs[0]]) ? $data[$type][0]=array_merge($data[$type][0], $addit_cssjs[$type][$cssjs[0]]) : '';
				}
				else
				{
					foreach($cssjs as $k=>$v)
					{
						$k && isset($addit_cssjs[$type][$cssjs[0]][$v]) ? $data[$type][0]=array_merge($data[$type][0], array($addit_cssjs[$type][$cssjs[0]][$v])) : '';
					}
				}
			}
		}
	}
}
echo header("Content-Type: ".($type=='css' ? "text/{$type}" : 'application/x-javascript')."; charset=UTF-8");
echo get_tracker_cssjs($data[$type][$type=='css' ? $css_set : $js_set], $type, $no_cache);

################################################################################
function get_tracker_cssjs($flist, $type, $no_cache=false)
{
	global $phpbb_root_path, $phpEx;

	if(is_array($flist) && sizeof($flist) && in_array($type, array('css', 'js')))
	{
		$md5_flist=md5(implode('|', $flist));
		$cssjs_file="{$phpbb_root_path}cache/data_ppkbb3cker_{$type}_{$md5_flist}.{$phpEx}";
		if(!@file_exists($cssjs_file) || !$no_cache)
		{
			return write_tracker_cssjs($cssjs_file, $flist, $type, $no_cache);
		}
		else
		{
			$last_modified=@filemtime($cssjs_file);
			if($last_modified)
			{
				//$etag=dechex(@fileinode($cssjs_file)).'-'.dechex(@filesize($cssjs_file)).'-'.dechex($last_modified);
				$last_modified=gmdate('D, d M Y H:i:s', $last_modified).' GMT';
				if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
				{
					$if_modified_since=preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
					if($if_modified_since==$last_modified)
					{
						header('HTTP/1.0 304 Not Modified');
						header('Cache-Control: max-age=86400, must-revalidate');
						exit;
					}
				}
				header('Last-Modified: '.$last_modified);
				header('Cache-Control: max-age=86400, must-revalidate');
			}
			$cssjs=@file($cssjs_file);
			if(sizeof($cssjs) > 2)
			{
				unset($cssjs[0]);
			}
			return implode('', $cssjs);
		}
	}

	return '';
}

function write_tracker_cssjs($cssjs_file, $flist, $type, $no_cache=false)
{
	global $phpbb_root_path, $phpEx;

	if($cssjs_file)
	{
		$cssjs_text='';
		foreach($flist as $fname)
		{
			if(@file_exists($fname))
			{
				$cssjs=@file($fname);
				$cssjs ? $cssjs_text.=implode('', $cssjs)."\n" : '';
			}
		}

		if(is_writable($phpbb_root_path.'cache/') && !$no_cache)
		{
		$fo=@fopen($cssjs_file, 'wb');
		if($fo)
		{
			@flock($fo, LOCK_EX);
			@fwrite($fo, "<?php if (!defined('IN_PHPBB')) exit;?>\n{$cssjs_text}\n");
			//@fflush($fo);
			@flock($fo, LOCK_UN);
			@fclose($fo);

				if(!function_exists('phpbb_chmod'))
				{
					include("{$phpbb_root_path}tracker/include/file_functions.{$phpEx}");
				}

			phpbb_chmod($cssjs_file, CHMOD_READ | CHMOD_WRITE);
		}

		$last_modified=@filemtime($cssjs_file);
		if($last_modified && !$no_cache)
		{
			//$etag=dechex(@fileinode($cssjs_file)).'-'.dechex(@filesize($cssjs_file)).'-'.dechex($last_modified);
			$last_modified=gmdate('D, d M Y H:i:s', $last_modified).' GMT';
			if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
			{
				$if_modified_since=preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
				if($if_modified_since==$last_modified)
				{
					header('HTTP/1.0 304 Not Modified');
					header('Cache-Control: max-age=86400, must-revalidate');
					exit;
				}
			}
			header('Last-Modified: '.$last_modified);
			header('Cache-Control: max-age=86400, must-revalidate');
			}
		}

		return $cssjs_text;
	}

	return '';
}

?>
