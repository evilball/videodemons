<?php
/**
*
* @package ppkBB3cker
* @version $Id: viewtopic_add_cron.php 1.000 2010-07-11 18:35:00 PPK $
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

$tracker_cron=array();
if(sizeof($torrents_cleanup))
{
	$sql="INSERT INTO ".TRACKER_CRON_TABLE."(type, data, forum_id, added) VALUES('t_clean', '".$db->sql_escape(serialize($torrents_cleanup))."', '{$forum_id}', '{$dt}')";
	$db->sql_query($sql);
	$cron_id=$db->sql_nextid();
	if($cron_id)
	{
		$tracker_cron[]=$cron_id;
	}
}
if(sizeof($torrents_remote))
{
	$sql="INSERT INTO ".TRACKER_CRON_TABLE."(type, data, forum_id, added) VALUES('t_announce', '".$db->sql_escape(serialize(array('torrents_id' => array_map('bin2hex', $torrents_remote))))."', '{$forum_id}', '{$dt}')";
	$db->sql_query($sql);
	$cron_id=$db->sql_nextid();
	if($cron_id)
	{
		$tracker_cron[]=$cron_id;
	}
}
if(sizeof($update_tstatus) && $config['ppkbb_tccron_jobs'][3])
{
	$sql="INSERT INTO ".TRACKER_CRON_TABLE."(type, data, forum_id, added) VALUES('update_tstatus', '".$db->sql_escape(serialize($update_tstatus))."', '{$forum_id}', '{$dt}')";
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
