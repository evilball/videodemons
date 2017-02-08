<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_tracker.php 1.000 2009-08-13 11:45:00 PPK $
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

$user->add_lang('mods/acp/ppkbb3cker_tracker');

$display_vars = array(
	'title'	=> 'ACP_TRACKER_SETTINGS',
	'vars'	=> array(
		'legend1'				=> 'ACP_TRACKER_SETTINGS',
		'ppkbb_tctracker_disabled'		=> array('lang' => 'TRACKER_DISABLED', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_scrape_enabled'		=> array('lang' => 'SCRAPE_ENABLED', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_peerstable_memory'		=> array('lang' => 'PPKBB_PEERSTABLE_MEMORY', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_minscrape_interval'		=> array('lang' => 'MINSCRAPE_INTERVAL', 'validate' => 'array', 'type' => 'array:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_tcannounce_interval'		=> array('lang' => 'TRACKER_ANNOUNCE_INTERVAL', 'validate' => 'array', 'type' => 'array:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_tcminannounce_interval'		=> array('lang' => 'TRACKER_MINANNOUNCE_INTERVAL', 'validate' => 'array', 'type' => 'array:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_tcbonus_value'		=> array('lang' => 'TRACKER_BONUS_VALUE', 'validate' => 'array', 'type' => 'array:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_tcbonus_fsize'		=> array('lang' => 'TRACKER_BONUS_FSIZE', 'validate' => 'array', 'type' => 'array:6:6', 'method' => false, 'explain' => true,),
		'ppkbb_tcclean_place'		=> array('lang' => 'TRACKER_CLEAN_PLACE', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_tcdead_time'		=> array('lang' => 'TRACKER_DEAD_TIME', 'validate' => 'array', 'type' => 'array:20:20', 'method' => false, 'explain' => true,),
		'ppkbb_tccleanup_interval'		=> array('lang' => 'TRACKER_CLEANUP_INTERVAL', 'validate' => 'array', 'type' => 'array:20:20', 'method' => false, 'explain' => true,),
		'ppkbb_tccheck_fext'		=> array('lang' => 'TRACKER_CHECK_FEXT', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_tccheck_ban'		=> array('lang' => 'TRACKER_CHECK_BAN', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_tcrights_tcache'		=> array('lang' => 'TRACKER_RIGHTS_TCACHE', 'validate' => 'array:0', 'type' => 'array:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_tcmax_seed'		=> array('lang' => 'TRACKER_MAX_SEED', 'validate' => 'int:0', 'type' => 'text:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_tcmax_leech'		=> array('lang' => 'TRACKER_MAX_LEECH', 'validate' => 'int:0', 'type' => 'text:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_tcmaxpeers_limit'		=> array('lang' => 'TRACKER_MAXPEERS_LIMIT', 'validate' => 'int:0', 'type' => 'text:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_tcmaxpeers_rewrite'		=> array('lang' => 'TRACKER_MAXPEERS_REWRITE', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_tcratio_start'		=> array('lang' => 'TRACKER_RATIO_START', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_tcmaxip_pertr'		=> array('lang' => 'TRACKER_MAXIP_PERTR', 'validate' => 'int:0', 'type' => 'text:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_tcmaxip_pertorr'		=> array('lang' => 'TRACKER_MAXIP_PERTORR', 'validate' => 'int:0', 'type' => 'text:4:4', 'method' => false, 'explain' => true,),
		'ppkbb_tprivate_flag'		=> array('lang' => 'TRACKER_TPRIVATE_FLAG', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_tcclients_restricts'		=> array('lang' => 'TRACKER_CLIENTS_RESTRICTS', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_tcignore_connectable'		=> array('lang' => 'TRACKER_IGNORE_CONNECTABLE', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_tcgz_rewrite'		=> array('lang' => 'TRACKER_GZREWRITE', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_tcignored_upload'		=> array('lang' => 'TRACKER_IGNORED_UPLOAD', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_append_tfile'		=> array('lang' => 'TRACKER_APPEND_TFILE', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_tctstat_ctime'		=> array('lang' => 'TRACKER_TSTAT_CTIME', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_tczones_enable'		=> array('lang' => 'TRACKER_ZONES_ENABLE', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_poll_options'		=> array('lang' => 'TRACKER_POLL_OPTIONS', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_ipreg_countrestrict'	=> array('lang' => 'TRIPREG_COUNTRESTRICT', 'validate' => 'array', 'type' => 'array:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_tstatus_notify'		=> array('lang' => 'TRACKER_TSTATUS_NOTIFY', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_tciptype'	=> array('lang' => 'TRACKER_IPTYPE',	'validate' => 'array',	'type' => 'array:1:1', 'explain' => true),
		'ppkbb_tcallow_unregtorr'		=> array('lang' => 'TRACKER_ALLOW_UNREGTORR', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_tracker_top'		=> array('lang' => 'TRACKER_TOP', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_torrent_magnetlink'		=> array('lang' => 'TRACKER_MAGNET_LINK', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_tctrestricts_options'		=> array('lang' => 'PPKBB_TCTRESTRICTS_OPTIONS', 'validate' => 'array', 'type' => 'array:2:2', 'method' => false, 'explain' => true,),
		'ppkbb_announce_url'	=> array('lang' => 'TRACKER_ANNOUNCE_URL',	'validate' => 'string',	'type' => 'text:40:255', 'explain' => true),

		'legend2'				=> 'ACP_PPKBB_ADDONS',
		'ppkbb_disable_fpquote'		=> array('lang' => 'PPKBB_DISABLE_FPQUOTE', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_subject_textlength'		=> array('lang' => 'PPKBB_SUBJECT_TEXTLENGTH', 'validate' => 'int:0', 'type' => 'text:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_maxspoiler_depth'		=> array('lang' => 'SPOILER_DEPTH_LIMIT', 'validate' => 'int:0', 'type' => 'text:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_spoiler_options'		=> array('lang' => 'SPOILER_OPTIONS', 'validate' => 'array', 'type' => 'array:16:16', 'method' => false, 'explain' => true,),
		'ppkbb_spoiler_banned_imghosts'		=> array('lang' => 'SPOILER_BANNED_IMGHOSTS', 'validate' => 'string', 'type' => 'text:48:1024', 'method' => false, 'explain' => true,),
		'ppkbb_hicons_fields'		=> array('lang' => 'TRACKER_HICONS_FIELDS', 'validate' => 'string', 'type' => 'text:48:255', 'method' => false, 'explain' => true,),
		'ppkbb_forums_qr'		=> array('lang' => 'TRACKER_FORUMS_QR', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_subforumslist'		=> array('lang' => 'TRACKER_SUBFORUMLIST', 'validate' => 'string', 'type' => 'text:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_fpep_enable'		=> array('lang' => 'PPKBB_FPEP_ENABLE', 'validate' => 'array', 'type' => 'array:1:1', 'method' => false, 'explain' => true,),
		'ppkbb_max_mfu'		=> array('lang' => 'PPKBB_MAX_MFU', 'validate' => 'int:0', 'type' => 'text:2:2', 'method' => false, 'explain' => true,),
		'ppkbb_torrblock_width'		=> array('lang' => 'TRACKER_TORRBLOCK_WIDTH', 'validate' => 'array', 'type' => 'array:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_torrent_statvt'		=> array('lang' => 'TRACKER_TORRENT_STATVT', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_torrent_statml'		=> array('lang' => 'TRACKER_TORRENT_STATML', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_torr_blocks'		=> array('lang' => 'PPKBB_TORR_BLOCKS', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_noticedisclaimer_blocks'		=> array('lang' => 'PPKBB_NOTICEDISCLAIMER_BLOCKS', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_search_astracker'		=> array('lang' => 'PPKBB_SEARCH_ASTRACKER', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_thanks_enable'		=> array('lang' => 'PPKBB_THANKS_ENABLE', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_cssjs_cache'		=> array('lang' => 'PPKBB_CSSJS_CACHE', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_forumid_remove'		=> array('lang' => 'PPKBB_FORUMID_REMOVE', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),
		'ppkbb_addit_options'		=> array('lang' => 'PPKBB_ADDIT_OPTIONS', 'validate' => 'array', 'type' => 'array:3:3', 'method' => false, 'explain' => true,),
		'ppkbb_mua_countlist'		=> array('lang' => 'PPKBB_MUA_COUNTLIST', 'validate' => 'string', 'type' => 'text:32:64', 'method' => false, 'explain' => true,),
		'ppkbb_smartmenus'		=> array('lang' => 'PPKBB_SMARTMENUS', 'validate' => 'array', 'type' => 'array:6:6', 'method' => false, 'explain' => true,),

		'legend3'				=> 'PPKBB_TOPDOWN_TORRENTS',
		'ppkbb_topdown_torrents'		=> array('lang' => 'PPKBB_TOPDOWN_TORRENTS', 'validate' => 'array', 'type' => 'array:5:5', 'method' => false, 'explain' => true,),
		'ppkbb_topdown_torrents_options'		=> array('lang' => 'PPKBB_TOPDOWN_TORRENTS_OPTIONS', 'validate' => 'array', 'type' => 'array:9:9', 'method' => false, 'explain' => true,),
		'ppkbb_topdown_torrents_exclude'		=> array('lang' => 'PPKBB_TOPDOWN_TORRENTS_EXCLUDE', 'validate' => 'array', 'type' => 'custom', 'method' => 'select_tracker_forums', 'explain' => true,),
		'ppkbb_topdown_torrents_trueexclude'		=> array('lang' => 'PPKBB_TOPDOWN_TORRENTS_TRUEEXCLUDE', 'validate' => 'int:0', 'type' => 'radio:yes_no', 'method' => false, 'explain' => true,),


		'legend5'				=> 'ACP_TRACKER_ADDFIELDS',
		'ppkbb_addfields_type'		=> array('lang' => 'TRACKER_ADDFIELDS_TYPE', 'validate' => 'array', 'type' => 'array:4:4', 'method' => false, 'explain' => true,),

	)
);
?>
