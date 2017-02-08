<?php
/**
*
* @package ppkBB3cker
* @version $Id: tmaxsl.php 1.000 2009-03-03 11:20:00 PPK $
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

$sql="SELECT SUM(IF(seeder='1', 1, 0)) seed, SUM(IF(seeder='0', 1, 0)) leech FROM ".TRACKER_PEERS_TABLE." WHERE userid='{$userid}' AND last_action > ".($dt-$config['ppkbb_tcdead_time']).($config['ppkbb_tcguests_enabled'][0] ? " AND guests='0'" : '');
$result=my_sql_query($sql);
$max_seed_leech=mysql_fetch_array($result);
mysql_free_result($result);

if($attachment['id'])
{
	$max_seed_leech['seed']-=1;
	$max_seed_leech['leech']-=1;
}
if(!$seeder && (!$rights[8] || !$rights[2]) && $config['ppkbb_tcmaxleech_restr'])
{
	if(!function_exists('get_trestricts'))
	{
		include($tincludedir.'trestricts.'.$phpEx);
	}
	$t_leech=get_trestricts($user['user_uploaded'], $user['user_downloaded'], $userratio, $config['ppkbb_tcmaxleech_restr']);
	if (($t_leech > 0 && $max_seed_leech['leech'] >= $t_leech) || ($t_leech==0))
	{
		err("Ratio and/or download restrictions: maximum torrents to leeching: {$t_leech}");
	}
}
if($seeder && $config['ppkbb_tcmax_seed'] && $max_seed_leech['seed'] >= $config['ppkbb_tcmax_seed'])
{
	err("Maximum torrents to seeding: {$config['ppkbb_tcmax_seed']}");
}
if(!$seeder && $config['ppkbb_tcmax_leech'] && $max_seed_leech['leech'] >= $config['ppkbb_tcmax_leech'])
{
	err("Maximum torrents to leeching: {$config['ppkbb_tcmax_leech']}");
}
?>
