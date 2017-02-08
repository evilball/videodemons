<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_trestricts.php 1.000 2009-08-13 12:00:00 PPK $
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

$user->add_lang('mods/acp/ppkbb3cker_trestricts');

$display_vars = array(
	'title'	=> 'ACP_TRESTRICTS_SETTINGS',
	'vars'	=> array(
		'legend1'				=> 'ACP_TRESTRICTS_SETTINGS',

		'ppkbb_tcwait_time'		=> array('lang' => 'TRACKER_WAIT_TIME', 'validate' => 'string', 'type' => 'textarea:10:10', 'method' => false, 'explain' => true,),
		'ppkbb_tcwait_time2'		=> array('lang' => 'TRACKER_WAIT_TIME2', 'validate' => 'string', 'type' => 'textarea:10:10', 'method' => false, 'explain' => true,),
		'ppkbb_tcmaxleech_restr'		=> array('lang' => 'TRACKER_MAXLEECH_RESTR', 'validate' => 'string', 'type' => 'textarea:10:10', 'method' => false, 'explain' => true,),
	)
);
?>
