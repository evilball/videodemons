<?php
/**
*
* @package ppkBB3cker
* @version $Id: tracker_stat.php 1.000 2013-04-22 12:50:20 PPK $
* @copyright (c) 2013 PPK
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

$total_peers_size=my_split_config($config['ppkbb_total_peers_size'], 2, 'my_int_val');
$total_up_down=my_split_config($config['ppkbb_total_up_down'], 2, 'my_int_val');
$total_sup_sdown=my_split_config($config['ppkbb_total_sup_sdown'], 2, 'my_int_val');
$total_seed_leech=my_split_config($config['ppkbb_total_seed_leech'], 2, 'my_int_val');
$total_tdown_tup=my_split_config($config['ppkbb_total_tdown_tup'], 2, 'my_int_val');
$total_udown_uup=my_split_config($config['ppkbb_total_udown_uup'], 2, 'my_int_val');
$total_speedup_speeddown=my_split_config($config['ppkbb_total_speedup_speeddown'], 2, 'my_int_val');
$total_thanks_seedreq=my_split_config($config['ppkbb_total_thanks_seedreq'], 2, 'my_int_val');

$l_total_torrent_s = ($config['num_torrents'] == 0) ? 'TOTAL_TORRENT_ZERO' : 'TOTAL_TORRENT_OTHER';
$l_total_comment_s = ($config['num_comments'] == 0) ? 'TOTAL_COMMENT_ZERO' : 'TOTAL_COMMENT_OTHER';
$l_total_peer_s = ($total_peers_size[0] == 0) ? 'TOTAL_PEER_ZERO' : 'TOTAL_PEER_OTHER';
$l_total_seed_s = ($total_seed_leech[0] == 0) ? 'TOTAL_SEED_ZERO' : 'TOTAL_SEED_OTHER';
$l_total_leech_s = ($total_seed_leech[1] == 0) ? 'TOTAL_LEECH_ZERO' : 'TOTAL_LEECH_OTHER';
$l_total_up_s = ($total_up_down[0] == 0) ? 'TOTAL_UP_ZERO' : 'TOTAL_UP_OTHER';
$l_total_down_s = ($total_up_down[1] == 0) ? 'TOTAL_DOWN_ZERO' : 'TOTAL_DOWN_OTHER';
$l_total_sup_s = ($total_sup_sdown[0] == 0) ? 'TOTAL_SUP_ZERO' : 'TOTAL_SUP_OTHER';
$l_total_sdown_s = ($total_sup_sdown[1] == 0) ? 'TOTAL_SDOWN_ZERO' : 'TOTAL_SDOWN_OTHER';
$l_total_sizes_s = ($total_peers_size[1] == 0) ? 'TOTAL_SIZE_ZERO' : 'TOTAL_SIZE_OTHER';
$l_t_down_s = ($total_tdown_tup[0] == 0) ? 'TOTAL_TDOWN_ZERO' : 'TOTAL_TDOWN_OTHER';
$l_t_up_s = ($total_tdown_tup[1] == 0) ? 'TOTAL_TUP_ZERO' : 'TOTAL_TUP_OTHER';
$l_u_down_s = ($total_udown_uup[0] == 0) ? 'TOTAL_UDOWN_ZERO' : 'TOTAL_UDOWN_OTHER';
$l_u_up_s = ($total_udown_uup[1] == 0) ? 'TOTAL_UUP_ZERO' : 'TOTAL_UUP_OTHER';
$l_up_speed = !$total_speedup_speeddown[0] ? 'TOTAL_SPEEDUP_ZERO' : 'TOTAL_SPEEDUP_OTHER';
$l_down_speed = !$total_speedup_speeddown[1] ? 'TOTAL_SPEEDDOWN_ZERO' : 'TOTAL_SPEEDDOWN_OTHER';
$l_total_thanks = !$total_thanks_seedreq[0] ? 'TOTAL_THANKS_ZERO' : 'TOTAL_THANKS_OTHER';

$template->assign_vars(array(
	'TOTAL_TORRENTS'	=> sprintf($user->lang[$l_total_torrent_s], $config['num_torrents']),
	'TOTAL_COMMENTS'	=> sprintf($user->lang[$l_total_comment_s], $config['num_comments']),

	'TOTAL_PEERS'	=> sprintf($user->lang[$l_total_peer_s], $total_peers_size[0]),
	'TOTAL_SIZE'	=> sprintf($user->lang[$l_total_sizes_s], get_formatted_filesize($total_peers_size[1])),

	'TOTAL_UP'	=> sprintf($user->lang[$l_total_up_s], get_formatted_filesize($total_up_down[0])),
	'TOTAL_DOWN'	=> sprintf($user->lang[$l_total_down_s], get_formatted_filesize($total_up_down[1])),

	'TOTAL_SUP'	=> sprintf($user->lang[$l_total_sup_s], get_formatted_filesize($total_sup_sdown[0])),
	'TOTAL_SDOWN'	=> sprintf($user->lang[$l_total_sdown_s], get_formatted_filesize($total_sup_sdown[1])),

	'TOTAL_SEED'	=> sprintf($user->lang[$l_total_seed_s], $total_seed_leech[0]),
	'TOTAL_LEECH'	=> sprintf($user->lang[$l_total_leech_s], $total_seed_leech[1]),

	'TOTAL_TDOWN'	=> sprintf($user->lang[$l_t_down_s], $total_tdown_tup[0]),
	'TOTAL_TUP'	=> sprintf($user->lang[$l_t_up_s], $total_tdown_tup[1]),

	'TOTAL_UDOWN'	=> sprintf($user->lang[$l_u_down_s], $total_udown_uup[0]),
	'TOTAL_UUP'	=> sprintf($user->lang[$l_u_up_s], $total_udown_uup[1]),

	'TOTAL_SPEEDUP'	=> sprintf($user->lang[$l_up_speed], get_formatted_filesize($total_speedup_speeddown[0], 1 , false, 1)),
	'TOTAL_SPEEDDOWN'	=> sprintf($user->lang[$l_down_speed], get_formatted_filesize($total_speedup_speeddown[1], 1, false, 1)),
	'TOTAL_THANKS'	=> sprintf($user->lang[$l_total_thanks], $total_thanks_seedreq[0]),
	)
);
?>
