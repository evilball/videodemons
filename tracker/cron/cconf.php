<?php
/**
*
* @package ppkBB3cker
* @version $Id: cconf.php 1.000 2014-03-22 14:40:48 PPK $
* @copyright (c) 2014 PPK
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

include_once("{$phpbb_root_path}tracker/include/config_map.{$phpEx}");
$crcache_config=array();

$sql = 'SELECT config_name, config_value FROM '.TRACKER_CONFIG_TABLE." WHERE config_name IN('ppkbb_cron_options', 'ppkbb_tcdead_time', 'ppkbb_deadtorrents_autodelete', 'ppkbb_tcrannounces_options', 'ppkbb_tcannounce_interval', 'ppkbb_tccron_jobs', 'ppkbb_tcenable_rannounces', 'ppkbb_tctstat_ctime', 'ppkbb_thanks_enable', 'ppkbb_tcguests_enabled')";
$result = my_sql_query($sql);
while($row=mysql_fetch_array($result))
{
	isset($config_map[$row['config_name']]) ? $row['config_value']=$config_map[$row['config_name']][0]==1 ? $row['config_value'] : my_split_config($row['config_value'], $config_map[$row['config_name']][0], $config_map[$row['config_name']][1], $config_map[$row['config_name']][2]) : '';

	$config[$row['config_name']]=$row['config_value'];
	$crcache_config[$row['config_name']]=$row['config_value'];
}
mysql_free_result($result);

$sql = 'SELECT config_name, config_value FROM '.CONFIG_TABLE." WHERE config_name IN('ppkbb_cron_last_cleanup')";
$result = my_sql_query($sql);
while($row=mysql_fetch_array($result))
{
	isset($config_map[$row['config_name']]) ? $row['config_value']=$config_map[$row['config_name']][0]==1 ? $row['config_value'] : my_split_config($row['config_value'], $config_map[$row['config_name']][0], $config_map[$row['config_name']][1], $config_map[$row['config_name']][2]) : '';

	$config[$row['config_name']]=$row['config_value'];
	$crcache_config[$row['config_name']]=$row['config_value'];
}
mysql_free_result($result);

include_once("{$tincludedir}tcache.{$phpEx}");

t_recache('cron_config', $crcache_config);
?>
