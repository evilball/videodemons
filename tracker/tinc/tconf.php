<?php
/**
*
* @package ppkBB3cker
* @version $Id: tconf.php 1.000 2009-08-13 13:36:00 PPK $
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

$sql = 'SELECT config_name, config_value FROM '.TRACKER_CONFIG_TABLE." WHERE config_name LIKE 'ppkbb\_tc%'";
$result = my_sql_query($sql);
while($row=mysql_fetch_array($result))
{
	isset($config_map[$row['config_name']]) ? $row['config_value']=$config_map[$row['config_name']][0]==1 ? $row['config_value'] : my_split_config($row['config_value'], $config_map[$row['config_name']][0], $config_map[$row['config_name']][1], $config_map[$row['config_name']][2]) : '';

	$config[$row['config_name']]=$row['config_value'];
	$trcache_config[$row['config_name']]=$row['config_value'];
}
mysql_free_result($result);

include_once("{$tincludedir}tcache.{$phpEx}");

t_recache('tracker_config', $trcache_config);
?>
