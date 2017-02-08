<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_rtrack.php 1.000 2012-06-14 10:47:23 PPK $
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

$user->add_lang('mods/acp/ppkbb3cker_rtrack');

$display_vars = array(
	'title'	=> 'ACP_PPKBB_RTRACK',
	'vars'	=> array(

		'legend7'				=> 'ACP_PPKBB_RTRACKER',
		'ppkbb_tcenable_rannounces'		=> array('lang' => 'TRACKER_ENABLE_RANNOUNCES', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_tcrannounces_options'		=> array('lang' => 'TRACKER_RANNOUNCES_OPTIONS', 'validate' => 'array', 'type' => 'array:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_tfile_annreplace'	=> array('lang' => 'TRACKER_TFILE_ANNREPLACE', 'validate' => 'array', 'type' => 'array:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_rtrack_enable'		=> array('lang' => 'TRACKER_RTRACK_ENABLE', 'validate' => 'array', 'type' => 'array:4:4', 'method' => false, 'explain' => true,),

	)
);
?>
