<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_guest.php 1.000 2012-06-14 10:55:31 PPK $
* @copyright (c) 2012 PPK
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

$user->add_lang('mods/acp/ppkbb3cker_guest');

$display_vars = array(
	'title'	=> 'ACP_PPKBB_GUEST',
	'vars'	=> array(

		'legend5'				=> 'ACP_PPKBB_GUEST',
		'ppkbb_tcguests_enabled'		=> array('lang' => 'TRACKER_GUESTS_ENABLED', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_gtcgz_rewrite'		=> array('lang' => 'TRACKER_GZREWRITE', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_gtcmax_leech'		=> array('lang' => 'TRACKER_GUESTMAX_LEECH', 'validate' => 'int:0', 'type' => 'text:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_gtcmax_seed'		=> array('lang' => 'TRACKER_GUESTMAX_SEED', 'validate' => 'int:0', 'type' => 'text:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_gtcmaxip_pertr'		=> array('lang' => 'TRACKER_GUESTMAXIP_PERTR', 'validate' => 'int:0', 'type' => 'text:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_gtcmaxip_pertorr'		=> array('lang' => 'TRACKER_GUESTMAXIP_PERTORR', 'validate' => 'int:0', 'type' => 'text:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_gtcmaxpeers_limit'		=> array('lang' => 'TRACKER_MAXPEERS_LIMIT', 'validate' => 'int:0', 'type' => 'text:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_gtcmaxpeers_rewrite'		=> array('lang' => 'TRACKER_MAXPEERS_REWRITE', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_gtcrights_tcache'		=> array('lang' => 'TRACKER_RIGHTS_TCACHE', 'validate' => 'array', 'type' => 'array:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_gtcclients_restricts'		=> array('lang' => 'TRACKER_CLIENTS_RESTRICTS', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_gtcmax_sessions'		=> array('lang' => 'TRACKER_GUESTSMAX_SESSIONS', 'validate' => 'int:0', 'type' => 'text:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_gtcsession_expire'		=> array('lang' => 'TRACKER_GUESTSSESS_EXPIRE', 'validate' => 'array', 'type' => 'array:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_gtccleanup_interval'		=> array('lang' => 'TRACKER_GCLEANUP_INTERVAL', 'validate' => 'array', 'type' => 'array:10:10', 'method' => false, 'explain' => true,),
		'ppkbb_gtciptype'	=> array('lang' => 'TRACKER_IPTYPE',	'validate' => 'array',	'type' => 'array:1:1', 'explain' => true),
		'ppkbb_gtcallow_unregtorr'		=> array('lang' => 'TRACKER_GALLOW_UNREGTORR', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_gtcunregtorr_sessid'		=> array('lang' => 'TRACKER_GUNREGTORR_SESSID', 'validate' => 'string', 'type' => 'text:32:32', 'method' => false, 'explain' => true,),
		'ppkbb_torrent_gmagnetlink'		=> array('lang' => 'TRACKER_GMAGNET_LINK', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
	)
);
?>
