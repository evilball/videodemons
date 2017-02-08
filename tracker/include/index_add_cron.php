<?php
/**
*
* @package ppkBB3cker
* @version $Id: index_add_cron.php 1.000 2009-04-25 19:26:00 PPK $
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

$dt=time();
$tracker_cron=array();
if($user->data['is_registered'] && $config['ppkbb_tccleanup_interval'] && $dt - $config['ppkbb_tracker_last_cleanup'] > $config['ppkbb_tccleanup_interval'])
{
	$sql="INSERT INTO ".TRACKER_CRON_TABLE."(type, data, added) VALUES('t_clean', '".$db->sql_escape(serialize(array()))."', '{$dt}')";
	$db->sql_query($sql);
	$cron_id=$db->sql_nextid();
	if($cron_id)
	{
		$tracker_cron[]=$cron_id;
	}
}
if($dt-$config['ppkbb_last_stattime'] > $config['ppkbb_tctstat_ctime'])
{
	$sql="INSERT INTO ".TRACKER_CRON_TABLE."(type, data, added) VALUES('t_stat', '', '{$dt}')";
	$db->sql_query($sql);
	$cron_id=$db->sql_nextid();
	if($cron_id)
	{
		$tracker_cron[]=$cron_id;
	}
}
if(sizeof($tracker_cron))
{
	if(!$config['ppkbb_cron_options'][2])
	{
		$cron_id=implode('&amp;id[]=', $tracker_cron);
		$template->assign_block_vars('tracker_cron', array(
			'CRON_TASK'=>'<img src="' . append_sid($phpbb_root_path . 'tracker/cron.' . $phpEx, 'id[]='.$cron_id) . '" alt="cron" width="1" height="1" />'
			)
		);
	}
	else
	{
		foreach($tracker_cron as $cron_id)
		{
			$template->assign_block_vars('tracker_cron', array(
				'CRON_TASK'=>'<img src="' . append_sid($phpbb_root_path . 'tracker/cron.' . $phpEx, 'id[]='.$cron_id) . '" alt="cron" width="1" height="1" />'
				)
			);
		}
	}
}
else if($config['ppkbb_cron_options'][4])
{
	if(rand(1, $config['ppkbb_cron_options'][4])==rand(1, $config['ppkbb_cron_options'][4]))
	{
		$template->assign_block_vars('tracker_cron', array(
			'CRON_TASK'=>'<img src="' . append_sid($phpbb_root_path . 'tracker/cron.' . $phpEx) . '" alt="cron" width="1" height="1" />'
			)
		);
	}
}
?>
