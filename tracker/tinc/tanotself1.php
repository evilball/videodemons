<?php
/**
*
* @package ppkBB3cker
* @version $Id: tanotself1.php 1.000 2009-02-13 12:03:00 PPK $
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

if(!function_exists('get_trestricts'))
{
	include($tincludedir.'trestricts.'.$phpEx);
}

if (!$rights[2] && ($config['ppkbb_tcwait_time'] || $config['ppkbb_tcwait_time2']))
{
	if ($left > 0)
	{
		$elapsed = intval(($dt - $user['added']) / 3600);
		if($config['ppkbb_tcwait_time'])
		{
			$wait=get_trestricts($user['user_uploaded'], $user['user_downloaded'], $userratio, $config['ppkbb_tcwait_time'], 'up');
			if ($wait > 0 && $elapsed < $wait)
			{
				err("Bad ratio or upload (" . ($wait - $elapsed) . "h) - wait time!");
			}
			else if($wait==0)
			{
				err("Bad ratio or upload, you can't download this torrent");
			}
		}
		if($config['ppkbb_tcwait_time2'])
		{
			$wait2=get_trestricts($user['user_uploaded'], $user['user_downloaded'], $userratio, $config['ppkbb_tcwait_time2']);
			if ($wait2 > 0 && $elapsed < $wait2)
			{
				err("Bad ratio or download (" . ($wait2 - $elapsed) . "h) - wait time!");
			}
			else if($wait2==0)
			{
				err("Bad ratio or download, you can't download this torrent");
			}
		}
	}
}

if(!$rights[9])
{
	if($user['req_upload'] && ($user['user_uploaded'] < $user['req_upload'] || !$user['user_uploaded']))
	{
		err("Sorry, bad upload value, required (GB): {$user['req_upload']}");
	}
}

if(!$rights[10])
{
	if($user['req_ratio']!=0.000 && $userratio!='None.' && ($userratio < $user['req_ratio'] || $userratio=='Leech.' || $userratio=='Inf.'))
	{
		err("Sorry, bad ratio value, required: {$user['req_ratio']}");
	}
}
?>
