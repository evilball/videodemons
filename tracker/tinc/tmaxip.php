<?php
/**
*
* @package ppkBB3cker
* @version $Id: tmaxip.php 1.000 2009-03-03 11:51:00 PPK $
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

$sql="SELECT SUM(1) pertr, SUM(IF(torrent='{$torrentid}', 1, 0)) pertorr FROM ".TRACKER_PEERS_TABLE." WHERE ip=INET_ATON('{$ip}') AND last_action > ".($dt-$config['ppkbb_tcdead_time']).($config['ppkbb_tcguests_enabled'][0] ? (IS_GUESTS ? " AND guests!='0'" : " AND guests='0'") : '');//, SUM(IF(seeder='1', 1, 0)) perseed, SUM(IF(seeder='0', 1, 0)) perleech
$result=my_sql_query($sql);
$max_ip=mysql_fetch_array($result);
mysql_free_result($result);
if($attachment['id'])
{
	$max_ip['pertr']-=1;
	$max_ip['pertorr']-=1;
}

if($config['ppkbb_tcmaxip_pertr'] && $max_ip['pertr'] >= $config['ppkbb_tcmaxip_pertr'])
{
	err("Maximum connections".(IS_GUESTS ? ' for guests' : '')." (from one ip) per tracker reached: {$config['ppkbb_tcmaxip_pertr']}");
}
if($config['ppkbb_tcmaxip_pertorr'] && $max_ip['pertorr'] >= $config['ppkbb_tcmaxip_pertorr'])
{
	err("Maximum connections".(IS_GUESTS ? ' for guests' : '')." (from one ip) per torrent reached: {$config['ppkbb_tcmaxip_pertorr']}");
}
?>
