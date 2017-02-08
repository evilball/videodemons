<?php
/**
*
* @package ppkBB3cker
* @version $Id: taself1.php 1.000 2009-02-13 12:02:00 PPK $
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

$uploaded ? $upthis=my_int_val($uploaded - $attachment['uploaded']) : '';
$upthis;

$downloaded ? $downthis=my_int_val($downloaded - $attachment['downloaded']) : '';
$shadowdownthis=$downthis;

$updatesnatch[]="uploadoffset='{$upthis}'";
$updatesnatch[]="downloadoffset='{$downthis}'";

if($upthis)
{
	$updatesnatch[]="uploaded=uploaded+{$upthis}";
	$updatepeers[]="uploaded=uploaded+{$upthis}";

	$config['ppkbb_tcignored_upload']=my_int_val($config['ppkbb_tcignored_upload']);
	if(
		($unregtorr && $config['ppkbb_tcallow_unregtorr']==2)
			|| ($config['ppkbb_tcignored_upload'] && $upthis > $config['ppkbb_tcignored_upload'])
		)
	{
		//$upthis=0;
	}
	else
	{
		$user_upthis=$upthis;
		if($userid==$user['poster_id'])
		{
			$updateuser[]="user_uploaded_self=user_uploaded_self+{$user_upthis}";
		}
		$config['ppkbb_tcbonus_fsize'][1] > 0 ? '' : $config['ppkbb_tcbonus_fsize'][1]=my_int_val($user['size']);
		if(
			(
				($userid==$user['poster_id'] && in_array($config['ppkbb_tcbonus_value'][2], array(0, 1)))
					|| ($userid!=$user['poster_id'] && in_array($config['ppkbb_tcbonus_value'][2], array(0, 2)))
			)
			&& $config['ppkbb_tcbonus_value'][0] > 0
			&& $rights[6]
			&& $user['size'] > $config['ppkbb_tcbonus_fsize'][0]
			)
		{
			if($config['ppkbb_tcbonus_value'][1])
			{
				/*$sql = 'SELECT bonus_count FROM '.TRACKER_SNATCHED_TABLE." WHERE userid='{$userid}' AND torrent='{$torrentid}' LIMIT 1";
				$result=my_sql_query($sql);
				$user_bonus=mysql_fetch_array($result);
				mysql_free_result($result);*/

				$bonus_value = ($upthis + my_int_val($user['bonus_count']));
				$bonus_value = intval($bonus_value / $config['ppkbb_tcbonus_fsize'][1]);

				$user_bonus_max=999.999;
				if($bonus_value > 0 && $user['user_bonus']+($bonus_value * $config['ppkbb_tcbonus_value'][3])<=$user_bonus_max)
			{
					$bonus_left=intval(($upthis + $user['bonus_count']) - ($bonus_value * $config['ppkbb_tcbonus_fsize'][1]));
					$updatesnatch[]="bonus_count='".($bonus_left ? $bonus_left : 0)."'";
					$updateuser[]='user_bonus=user_bonus+'.($bonus_value * $config['ppkbb_tcbonus_value'][3]);
			}
			else
			{
				$upthis ? $updatesnatch[]="bonus_count=bonus_count+{$upthis}" : '';
			}
			}
			else
			{
				$updatesnatch[]="bonus_count=bonus_count+{$upthis}";
			}
		}
		if($rights[12] && $user['upload'])
		{
			$user['upload'] > 250 ? $user['upload']=250 : '';
			$user_upthis=my_int_val($upthis+($upthis*$user['upload'])/100);
		}
		$updateuser[]="user_uploaded=user_uploaded+{$user_upthis}";
	}
}

if($downthis)
{
	$updatesnatch[]="downloaded=downloaded+{$downthis}";
	$updatepeers[]="downloaded=downloaded+{$downthis}";

	if($unregtorr && $config['ppkbb_tcallow_unregtorr']==2)
	{

	}
	else
	{
		$user_downthis=$downthis;
		if($rights[4])
		{
			$user_downthis=0;
		}
		else if($rights[3])
		{
			$user_downthis=$downthis/2;
		}
		else
		{
			if($user['free'] && $rights[5])
			{
				$user['free'] > 100 ? $user['free']=100 : '';
				$user_downthis=$downthis*((100-$user['free'])/100);
			}
		}
		$updateuser[]="user_downloaded=user_downloaded+{$user_downthis}";
	}
}

$shadowdownthis ? $updateuser[]="user_shadow_downloaded=user_shadow_downloaded+{$shadowdownthis}" : '';
if(sizeof($updateuser))
{
	my_sql_query('UPDATE '. USERS_TABLE .' SET '.implode(', ', $updateuser)." WHERE user_id='{$userid}' LIMIT 1");
}

?>
