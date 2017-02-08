<?php
/**
*
* @package ppkBB3cker
* @version $Id: config_map.php 1.000 2013-04-22 10:08:57 PPK $
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

/* phpbb_config dynamic

ppkbb_chat_last_cleanup,
ppkbb_cron_last_cleanup,
ppkbb_gtracker_last_cleanup,
ppkbb_last_stattime,
ppkbb_newversion_lastcheck,
ppkbb_total_peers_size,
ppkbb_total_seed_leech,
ppkbb_total_speedup_speeddown,
ppkbb_total_sup_sdown,
ppkbb_total_tdown_tup,
ppkbb_total_udown_uup,
ppkbb_total_up_down,
ppkbb_tracker_last_cleanup
*/

$config_map=array(
	'ppkbb_spoiler_options' => array(7, array('my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'strval'), '', 'field'=>array('text:4:4', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'select:0=PP:1=NEW:2=CURRENT', 'radio:1=YES:0=NO', 'select:pp_default=pp_default:light_rounded=light_rounded:dark_rounded=dark_rounded:light_square=light_square:dark_square=dark_square:facebook=facebook'), 'default'=>array('0', '0', '0', '1', '0', '0', 'pp_default'), 'append'=>array('PIXEL')),
	'ppkbb_smartmenus' => array(3, array('my_int_val', 'strval', 'strval'), '', 'field'=>array('radio:1=YES:0=NO', 'select:blue=blue:clean=clean:mint=mint:simple=simple:subsilver2=subsilver2', 'select:blue=blue:clean=clean:mint=mint:simple=simple:prosilver=prosilver'), 'default'=>array('0', 'clean', 'blue')),
	'ppkbb_ppkbb3cker_version' => array(3, array('strval', 'my_int_val', 'floatval'), '_'),
	'ppkbb_poll_options' => array(2, 'my_int_val', '', 'field'=>array('select:0=OFF:1=AUTO:2=RIGHTS', 'radio:1=YES:0=NO'), 'default'=>array('1', '0')),
	'ppkbb_portal_torrents_posttime' => array(3, array('intval', 'my_int_val', 'my_int_val'), ''),

	'ppkbb_tcguests_enabled' => array(2, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('0', '1')),
	'ppkbb_tcrewr_updown' => array(4, 'my_int_val', '', 'field'=>array('text:3:3', 'text:3:3', 'radio:1=YES:0=NO', 'text:3:3'), 'default'=>array('0', '0', '0', '0')),
	'ppkbb_torr_blocks' => array(10, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'select:0=OFF:1=THUMB:2=FULL', 'select:0=OFF:1=THUMB:2=FULL', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('1', '1', '1', '1', '1', '1', '1', '1', '1', '1')),
	'ppkbb_tcenable_rannounces' => array(9, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('1', '1', '1', '1', '1', '1', '1', '1', '1')),
	'ppkbb_tcrannounces_options' => array(11, array('my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'my_float_val', 'my_int_val', 'my_int_val', 'strval'), '', 'field'=>array('time:6:12', 'time:6:12', 'text:4:4', 'text:4:4', 'time:6:12', 'text:4:4', 'text:7:7', 'radio:1=YES:0=NO', 'text:4:4', 'text:32:128', 'text:8:8'), 'default'=>array('30', '30', '50', '75', '5', '1', '0.1', '0', '20', 'uTorrent/1820', '-UT1820-')),
	'ppkbb_tcclean_place' => array(6, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('0', '0', '0', '0', '0', '0')),
	'ppkbb_tcauthor_candown' => array(0 , 'intval', ''),
	'ppkbb_tcguest_cantdown' => array(0 , 'intval', ''),
	'ppkbb_torrblock_width' => array(13, 'my_int_val', '', 'field'=>array('text:4:4', 'text:4:4', 'text:4:4', 'text:4:4', 'text:4:4', 'radio:1=YES:0=NO', 'text:4:4', 'radio:1=YES:0=NO', 'text:4:4', 'radio:1=YES:0=NO', 'text:4:4', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('0', '0', '35', '35', '150', '0', '150', '0', '150', '0', '150', '0', '0'), 'append'=>array('PIXEL', 'PIXEL', '', '', 'PIXEL', '', 'PIXEL', '', 'PIXEL', '', 'PIXEL')),
	'ppkbb_topdown_torrents' => array(15, array('my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'strval', 'my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'my_int_val'), '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'text:4:4', 'text:6:6', 'text:4:4', 'time:6:12', 'text:4:4', 'text:4:4', 'radio:1=YES:0=NO', 'time:6:12', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'select:0=STANDART', 'text:4:4'), 'default'=>array('0', '0', '0', '15', '100%', '120', '10', '0', '0', '0'), 'append'=>array('', '', '', '', '', 'PIXEL', '')),
	'ppkbb_topdown_torrents_options' => array(9, array('my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'my_int_val', 'strval', 'my_int_val', 'my_int_val', 'my_int_val'), '', 'field'=>array('radio:1=YES:0=NO', 'text:4:4', 'time:6:12', 'time:6:12', 'radio:1=YES:0=NO', 'select:pushpull=pushpull:slide=slide', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'text:4:4'), 'default'=>array('1', '1', '1', '1', '2500', 'slide', '0', '1', '2')),
	'ppkbb_topdown_torrents_exclude' => array(0, 'my_int_val', ','),
	'ppkbb_tfile_annreplace' => array(3, 'my_int_val', '', 'field'=>array('select:0=DEL:1=EXTERNAL:2=ADDIT', 'text:3:3', 'radio:1=YES:0=NO'), 'default'=>array('1', '0', '0')),
	'ppkbb_torrent_statvt' => array(10, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('1', '1', '1', '1', '1', '1', '1', '1', '1', '1')),
	'ppkbb_tstatus_notify' => array(2, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('0', '0')),
	'ppkbb_tcdef_statuses' => array(2, 'intval', ''),
	'ppkbb_torrent_magnetlink' => array(6, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('0', '0', '0', '0', '0', '0')),
	'ppkbb_torrent_gmagnetlink' => array(6, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('0', '0', '0', '0', '0', '0')),
	'ppkbb_torrent_statml' => array(10, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('1', '1', '1', '1', '1', '1', '1', '1', '1', '1')),
	'ppkbb_tcbonus_value' => array(4, array('intval', 'my_int_val', 'my_int_val', 'my_float_val'), '', 'field'=>array('select:-1=FULLOFF:0=OFF:1=ON', 'select:1=AUTO:0=MANUAL', 'select:0=ALL:1=SELF:2=NOTSELF', 'text:5:5'), 'default'=>array('0', '0', '0', '0.000')),
	'ppkbb_tcbonus_fsize' => array(2, 'my_int_val', '', 'field'=>array('bytes:8:16', 'bytes:8:16'), 'default'=>array('0', '0')),
	'ppkbb_tcclients_restricts' => array(3, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('0', '0', '0')),
	'ppkbb_tczones_enable' => array(2, array('strval', 'my_int_val'), '', 'field'=>array('select:0=OFF:1=ON', 'radio:1=YES:0=NO'), 'default'=>array('0', '0')),
	'ppkbb_tccron_jobs' => array(5, 'my_int_val', '', 'field'=>array('time:6:12', 'time:6:12', 'time:6:12', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('12', '12', '60', '0', '0')),
	'ppkbb_tprivate_flag' => array(1, 'intval', '', 'field'=>array('select:0=ASIS:1=PRIVATE:2=NONPRIVATE:-1=UPLOADPRIVATE:-2=UPLOADNONPRIVATE'), 'default'=>array('0')),
	'ppkbb_tcratio_start' => array(1, 'my_int_val', '', 'field'=>array('bytes:8:16'), 'default'=>array('0')),
	'ppkbb_tcgz_rewrite' => array(1, 'my_int_val', '', 'field'=>array('select:0=AUTO:1=GZ:2=NONGZ'), 'default'=>array('0')),
	'ppkbb_tciptype' => array(1, 'my_int_val', '', 'field'=>array('select:0=STANDART:1=HEADER:2=CLIENT'), 'default'=>array('0')),
	'ppkbb_tcallow_unregtorr' => array(1, 'my_int_val', '', 'field'=>array('select:0=OFF:1=ONWSTAT:2=ONWOSTAT'), 'default'=>array('0')),
	'ppkbb_tmin_thumbsize' => array(1, 'my_int_val', '', 'field'=>array('bytes:8:16'), 'default'=>array('0')),
	'ppkbb_trclear_snatched' => array(1, 'my_int_val', '', 'field'=>array('select:0=NO:1=YES:2=NULL:3=NOTNULL'), 'default'=>array('0')),
	'ppkbb_trclear_unregtorr' => array(1, 'my_int_val', '', 'field'=>array('select:0=NO:1=REG:2=GUEST:3=ALL'), 'default'=>array('0')),
	'ppkbb_tcrights_tcache' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('3600')),
	'ppkbb_tccleanup_interval' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('3600')),
	'ppkbb_tcdead_time' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('2700')),
	'ppkbb_tcannounce_interval' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('1800')),
	'ppkbb_tcminannounce_interval' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('1800')),
	'ppkbb_tctstat_ctime' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('600')),
	'ppkbb_tracker_top' => array(2, 'my_int_val', '', 'field'=>array('text:3:3', 'time:6:12'), 'default'=>array('0', '3600')),
	'ppkbb_tcignore_connectable' => array(1, 'my_int_val', '', 'field'=>array('select:0=NOIGNORE:1=IGNORENOCHECK:2=IGNOREYESCHECK'), 'default'=>array('1')),
	'ppkbb_tcignored_upload' => array(1, 'my_int_val', '', 'field'=>array('bytes:8:16'), 'default'=>array('0')),
	'ppkbb_tctrestricts_options' => array(5, 'my_int_val', '', 'field'=>array('text:5:10', 'text:5:10', 'bytes:8:16', 'text:5:10', 'text:5:10'), 'default'=>array('0', '0', '0', '0', '0')),
	'ppkbb_cron_options' => array(5, 'my_int_val', '', 'field'=>array('time:6:12', 'time:6:12', 'radio:1=YES:0=NO', 'time:6:12', 'text:2:2'), 'default'=>array('3', '1', '0', '60', '5')),
	'ppkbb_chat_height' => array(4, 'my_int_val', '', 'append'=>array('PIXEL', 'PIXEL', 'PIXEL', 'PIXEL')),
	'ppkbb_chat_marchive' => array(2, 'my_int_val', '', 'field'=>array('time:6:12', 'text:3:3'), 'default'=>array('1', '100')),
	'ppkbb_chat_avatars' => array(2, array('my_int_val', 'basename'), '', 'field'=>array('text:3:3', 'text:16:32'), 'default'=>array('150', 'no_avatar.gif'), 'append' => array('PIXEL')),
	'ppkbb_chat_guests_option' => array(3, array('strval', 'intval', 'strval'), '', 'field'=>array('lang', 'text:5:5', 'text:16:32'), 'default'=>array('1', '10000', 'CHAT_GUEST')),
	'ppkbb_chat_bot' => array(6, array('my_int_val', 'strval', 'my_int_val', 'strval', 'strval', 'basename'), '', 'field'=>array('radio:1=YES:0=NO', 'text:16:32', 'radio:1=YES:0=NO', 'text:6:6', 'text:6:6', 'text:16:32'), 'default'=>array('0', 'CHAT_BOT', '0', '', '', 'bot_avatar.gif')),
	'ppkbb_chat_botforums' => array(0, 'my_int_val', ','),
	'ppkbb_chat_murefresh' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('10')),
	'ppkbb_chat_inactive_time' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('60')),
	'ppkbb_chat_waittime' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('5')),
	'ppkbb_chat_cleanup_interval' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('3600')),
	'ppkbb_chat_killtime' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('300')),
	'ppkbb_chat_qbantime' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('3600')),
	'ppkbb_chat_sounds' => array(3, array('my_int_val', 'my_int_val', 'basename'), '', 'field'=>array('select:0=OFF:1=INCHAT:2=ALL', 'radio:1=YES:0=NO', 'text:16:32'), 'default'=>array('0', '0', 'exercises-ended.wav')),
	'ppkbb_chat_umclean' => array(4, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('0', '0', '0', '0')),
	'ppkbb_chat_logs' => array(5, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('1', '1', '1', '1', '1')),
	'ppkbb_chat_display' => array(4, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('0', '0', '0', '0')),
	'ppkbb_chat_enable' => array(1, 'my_int_val', '', 'field'=>array('select:0=OFF:1=ON:2=COLLAPSE:3=READ'), 'default'=>array('0')),
	'ppkbb_cgp_places' => array(4, 'my_int_val', ''),
	'ppkbb_clear_peers' => array(1, 'my_int_val', '', 'field'=>array('select:no=OFF:time=TIME:all=ALL'), 'default'=>array('0')),

	'ppkbb_ipreg_countrestrict' => array(2, 'my_int_val', '', 'field'=>array('text:5:5', 'bytes:8:16'), 'default'=>array('0', '0')),

	'ppkbb_noticedisclaimer_blocks' => array(6, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'select:0=OFF:1=REG:2=GUEST:3=ALL'), 'default'=>array('1', '1', '1', '1', '1', '1')),

	'ppkbb_max_posters' => array(2, 'my_int_val', ''),
	'ppkbb_max_screenshots' => array(2, 'my_int_val', ''),
	'ppkbb_max_extposters' => array(10, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'text:4:4', 'text:4:4', 'text:4:4', 'text:4:4', 'text:4:4', 'text:4:4', 'time:6:12', 'bytes:8:16', 'radio:1=YES:0=NO'), 'default'=>array('1', '0', '1', '10', '10', '1920', '1080', '3', '0', '0'), 'append'=>array('', '', '', 'PIXEL', 'PIXEL', 'PIXEL', 'PIXEL')),
	'ppkbb_max_extscreenshots' => array(10, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'text:4:4', 'text:4:4', 'text:4:4', 'text:4:4', 'text:4:4', 'text:4:4', 'time:6:12', 'bytes:8:16', 'radio:1=YES:0=NO'), 'default'=>array('1', '0', '3', '10', '10', '1920', '1080', '3', '0', '0'), 'append'=>array('', '', '', 'PIXEL', 'PIXEL', 'PIXEL', 'PIXEL')),
	//'ppkbb_mua_countlist' => array(0, 'my_int_val', ''),
	'ppkbb_minscrape_interval' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('1800')),

	'ppkbb_deadtorrents_autodelete' => array(6, 'my_int_val', '', 'field'=>array('time:6:12', 'time:6:12', 'time:6:12', 'text:5:5', 'radio:1=YES:0=NO', 'text:4:5'), 'default'=>array('3', '1', '0', '60', '5', '100')),

	'ppkbb_addit_options' => array(3, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'select:1=YES:0=NO:2=WOGUEST', 'radio:1=YES:0=NO'), 'default'=>array('1', '1', '1')),
	'ppkbb_addfields_type' => array(4, 'my_int_val', '', 'field'=>array('select:0=STANDART', 'radio:1=YES:0=NO', 'select:0=NO:1=YES:2=ONLY', 'radio:1=YES:0=NO'), 'default'=>array('0', '0', '1', '1')),
	'ppkbb_append_tfile' => array(1, 'my_int_val', '', 'field'=>array('select:0=OFF:2=AFTER'), 'default'=>array('2')),

	'ppkbb_rtrack_enable' => array(3, 'my_int_val', '', 'field'=>array('select:0=OFF:3=ALL', 'text:3:3', 'select:0=FOROFF:1=FORALL:2=FORREG:3=FORGUEST'), 'default'=>array('1', '0', '0')),

	'ppkbb_gtcsession_expire' => array(2, 'my_int_val', '', 'field'=>array('time:6:12', 'time:6:12'), 'default'=>array('0', '0')),
	'ppkbb_gtcclients_restricts' => array(3, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'radio:1=YES:0=NO', 'radio:1=YES:0=NO'), 'default'=>array('0', '0', '0')),
	'ppkbb_gtcgz_rewrite' => array(1, 'my_int_val', '', 'field'=>array('select:0=AUTO:1=GZ:2=NONGZ'), 'default'=>array('0')),
	'ppkbb_gtciptype' => array(1, 'my_int_val', '', 'field'=>array('select:0=STANDART:1=HEADER:2=CLIENT'), 'default'=>array('0')),
	'ppkbb_gtcallow_unregtorr' => array(1, 'my_int_val', '', 'field'=>array('select:0=OFF:1=ONWSTAT:2=ONWOSTAT'), 'default'=>array('0')),
	'ppkbb_gtcrights_tcache' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('43200')),
	'ppkbb_gtccleanup_interval' => array(1, 'my_int_val', '', 'field'=>array('time:6:12'), 'default'=>array('86400')),

	'ppkbb_extposters_exclude' => array(0, 'my_int_val', ','),
	'ppkbb_extscreenshots_exclude' => array(0, 'my_int_val', ','),

	'ppkbb_feed_enblist' => array(0, 'my_int_val', ','),
	'ppkbb_forums_qr' => array(1, 'my_int_val', '', 'field'=>array('select:0=ASIS:1=ON:2=OFF'), 'default'=>array('0')),
	'ppkbb_fpep_enable' => array(1, 'my_int_val', '', 'field'=>array('select:0=OFF:1=ON:2=OPTION:=ASIS'), 'default'=>array('')),
	'ppkbb_last_dtad' => array(2, 'my_int_val', ''),

	'board3_ppkbb3cker_portal_last_torrents_' => array(9, 'my_int_val', '', 'field'=>array('select:0=NO:1=MESSAGES', 'text:1:1', 'text:4:4', 'time:6:12', 'text:4:4', 'radio:1=YES:0=NO', 'select:0=NO:1=DATE:2=CAT', 'text:4:4', 'text:4:4'), 'default'=>array('1', '2', '200', '10', '250', '0', '0', '0', '0'), 'append'=>array('', '', 'PIXEL', '', 'PIXEL')),
	'board3_ppkbb3cker_portal_torrents_posttime_' => array(3, array('intval', 'my_int_val', 'my_int_val'), '', 'field'=>array('text:4:4', 'radio:1=YES:0=NO', 'text:5:5'), 'default'=>array('-1', '0', '0')),
	'board3_ppkbb3cker_portal_exclude_forums_' => array(0, 'my_int_val', ','),
	'board3_ppkbb3cker_portal_lttorrents_display_' => array(4, 'my_int_val', '', 'field'=>array('radio:1=YES:0=NO', 'select:0=NO:1=THUMB:2=FULL', 'select:0=NO:1=THUMB:2=FULL', 'radio:1=YES:0=NO'), 'default'=>array('1', '1', '1', '1')),

);

?>
