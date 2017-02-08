<?php
/**
*
* @package ppkBB3cker
* @version $Id: tgmaxsl.php 1.000 2009-11-08 13:29:00 PPK $
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

$sql="SELECT SUM(IF(p.seeder='1', 1, 0)) seed, SUM(IF(p.seeder='0', 1, 0)) leech FROM ".TRACKER_PEERS_TABLE." p, ".TRACKER_GUESTS_TABLE." g WHERE g.session_id='{$session_id}' AND p.userid=g.user_id AND p.last_action > ".($dt-$config['ppkbb_tcdead_time'])." AND p.guests!='0' AND g.unreg='0'";
$result=my_sql_query($sql);
$max_seed_leech=mysql_fetch_array($result);
mysql_free_result($result);

if($attachment['id'])
{
	$max_seed_leech['seed']-=1;
	$max_seed_leech['leech']-=1;
}

if($seeder && $config['ppkbb_tcmax_seed'] && $max_seed_leech['seed'] >= $config['ppkbb_tcmax_seed'])
{
	err("Maximum torrents for guests to seeding: {$config['ppkbb_tcmax_seed']}");
}
if(!$seeder && $config['ppkbb_tcmax_leech'] && $max_seed_leech['leech'] >= $config['ppkbb_tcmax_leech'])
{
	err("Maximum torrents for guests to leeching: {$config['ppkbb_tcmax_leech']}");
}
?>
