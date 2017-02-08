<?php
/**
*
* @package ppkBB3cker
* @version $Id: tgconf.php 1.000 2009-11-08 12:22:00 PPK $
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

define('TRACKER_CONFIG_TABLE', $table_prefix . 'tracker_config');

include_once("{$phpbb_root_path}tracker/include/config_map.{$phpEx}");
$trcache_config=array();

$gconf=array('ppkbb_tccheck_fext', 'ppkbb_tcdead_time', 'ppkbb_tcguests_enabled', 'ppkbb_tcannounce_interval', 'ppkbb_tcminannounce_interval', 'ppkbb_tctracker_disabled', 'ppkbb_tctstat_ctime', 'ppkbb_tcignore_connectable');

$sql = 'SELECT config_name, config_value FROM '.TRACKER_CONFIG_TABLE." WHERE config_name IN('".implode("', '", $gconf)."') OR config_name LIKE 'ppkbb\_gtc%'";
$result = my_sql_query($sql);
while($row=mysql_fetch_array($result))
{
	isset($config_map[$row['config_name']]) ? $row['config_value']=$config_map[$row['config_name']][0]==1 ? $row['config_value'] : my_split_config($row['config_value'], $config_map[$row['config_name']][0], $config_map[$row['config_name']][1], $config_map[$row['config_name']][2]) : '';

	$row['config_name']=str_replace('ppkbb_gtc', 'ppkbb_tc', $row['config_name']);

	$config[$row['config_name']]=$row['config_value'];
	$trcache_config[$row['config_name']]=$row['config_value'];
}
mysql_free_result($result);

include_once("{$tincludedir}tcache.{$phpEx}");

t_recache('tracker_gconfig', $trcache_config);
?>
