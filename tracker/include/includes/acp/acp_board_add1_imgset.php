<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_imgset.php 1.000 2012-06-13 18:41:43 PPK $
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

$user->add_lang('mods/acp/ppkbb3cker_imgset');

$display_vars = array(
	'title'	=> 'ACP_PPKBB_IMGSET',
	'vars'	=> array(
		'legend2'				=> 'ACP_PPKBB_IMGSET',
		'ppkbb_tmax_thumbwidth'		=> array('lang' => 'TRACKER_MAX_THUMBWIDTH', 'validate' => 'int:0', 'type' => 'text:3:3', 'method' => false, 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),
		'ppkbb_tmin_thumbsize'		=> array('lang' => 'TRACKER_MIN_THUMBSIZE', 'validate' => 'array', 'type' => 'array:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_tmax_imgwidth'		=> array('lang' => 'TRACKER_MAX_IMGWIDTH', 'validate' => 'int:0', 'type' => 'text:4:4', 'method' => false, 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),
		'ppkbb_tmax_imgheight'		=> array('lang' => 'TRACKER_MAX_IMGHEIGHT', 'validate' => 'int:0', 'type' => 'text:4:4', 'method' => false, 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),
		'ppkbb_max_posters'		=> array('lang' => 'TRACKER_MAX_POSTERS', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_max_screenshots'		=> array('lang' => 'TRACKER_MAX_SCREENSHOTS', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_max_extposters'		=> array('lang' => 'TRACKER_MAX_EXTPOSTERS', 'validate' => 'array', 'type' => 'array:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_extposters_exclude'		=> array('lang' => 'TRACKER_EXTPOSTERS_EXCLUDE', 'validate' => 'array', 'type' => 'custom', 'method' => 'select_tracker_forums', 'explain' => true,),
		'ppkbb_extposters_trueexclude'		=> array('lang' => 'TRACKER_EXTPOSTERS_TRUEEXCLUDE', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_max_extscreenshots'		=> array('lang' => 'TRACKER_MAX_EXTSCREENSHOTS', 'validate' => 'array', 'type' => 'array:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_extscreenshots_exclude'		=> array('lang' => 'TRACKER_EXTSCREENSHOTS_EXCLUDE', 'validate' => 'array', 'type' => 'custom', 'method' => 'select_tracker_forums', 'explain' => true,),
		'ppkbb_extscreenshots_trueexclude'		=> array('lang' => 'TRACKER_EXTSCREENSHOTS_TRUEEXCLUDE', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_forb_extpostscr'		=> array('lang' => 'TRACKER_FORB_EXTPOSTSCR', 'validate' => 'string', 'type' => 'textarea:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_forb_extpostscr_trueexclude'		=> array('lang' => 'TRACKER_FORB_EXTPOSTSCR_TRUEEXCLUDE', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
	)
);
?>
