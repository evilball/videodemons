<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_candc.php 1.000 2012-06-14 09:55:12 PPK $
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

$user->add_lang('mods/acp/ppkbb3cker_candc');

$display_vars = array(
	'title'	=> 'ACP_PPKBB_CANDC',
	'vars'	=> array(

		'legend9'				=> 'ACP_PPKBB_CANDC',
		'ppkbb_cron_options'		=> array('lang' => 'TRACKER_CRON_OPTIONS', 'validate' => 'array', 'type' => 'array:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_tccron_jobs'		=> array('lang' => 'TRACKER_CRON_JOBS', 'validate' => 'array', 'type' => 'array:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_deadtorrents_autodelete'		=> array('lang' => 'TRACKER_DEADTORRENTS_AUTODELETE', 'validate' => 'array', 'type' => 'array:6:6', 'method' => false, 'explain' => true,),
		'ppkbb_clear_peers'		=> array('lang' => 'TRACKER_CLEAR_PEERS',	'validate' => 'array',	'type' => 'array:1:1', 'method' => false, 'explain' => true),
		'ppkbb_unset_tcache'		=> array('lang' => 'TRACKER_UNSET_TCACHE', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_reset_ratio'		=> array('lang' => 'TRACKER_RESET_RATIO', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_reset_bonus'		=> array('lang' => 'TRACKER_RESET_BONUS', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_clean_snatch'		=> array('lang' => 'TRACKER_CLEAN_SNATCH', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_recount_finished'		=> array('lang' => 'TRACKER_RECOUNT_FINISHED', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_recount_thanks'		=> array('lang' => 'TRACKER_RECOUNT_THANKS', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_trclean_sticky'		=> array('lang' => 'TRACKER_CLEAN_STICKY', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_trclear_files'		=> array('lang' => 'TRACKER_CLEAR_FILES', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_trclear_thanks'		=> array('lang' => 'TRACKER_CLEAR_THANKS', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_trclear_torrents'		=> array('lang' => 'TRACKER_CLEAR_TORRENTS', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_trclean_polls'		=> array('lang' => 'TRACKER_CLEAN_POLLS', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_trclear_pollres'		=> array('lang' => 'TRACKER_CLEAR_POLLRES', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_trclear_guestsess'		=> array('lang' => 'TRACKER_CLEAR_GUESTSESS', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_trclear_rannounces'		=> array('lang' => 'TRACKER_CLEAR_RANNOUNCES', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_trclear_trtrack'		=> array('lang' => 'TRACKER_CLEAR_TRTRACK', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_trclear_urtrack'		=> array('lang' => 'TRACKER_CLEAR_URTRACK', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_trclear_cronjobs'		=> array('lang' => 'TRACKER_CLEAR_CRONJOBS', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_trclear_snatched'		=> array('lang' => 'TRACKER_CLEAR_SNATCHED', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_trclear_unregtorr'		=> array('lang' => 'TRACKER_CLEAR_UNREGTORR', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_fix_statuses'		=> array('lang' => 'TRACKER_FIX_STATUSES', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
	)
);
?>
