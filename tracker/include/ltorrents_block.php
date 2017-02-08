<?php
/**
*
* @package ppkBB3cker
* @version $Id: ltorrents_block.php 1.000 2014-06-19 13:41:13 PPK $
* @copyright (c) 2014 PPK
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

	$exclude_forums= $config['board3_ppkbb3cker_portal_exclude_forums_'.$module_id];

	$ppkbb_portal_last_torrents=$config['board3_ppkbb3cker_portal_last_torrents_'.$module_id];
	$ppkbb_portal_trueexclude_forums=$config['board3_ppkbb3cker_portal_trueexclude_forums_'.$module_id];
	$ppkbb_portal_torrents_posttime=$config['board3_ppkbb3cker_portal_torrents_posttime_'.$module_id];
	$ppkbb_portal_torrents_textlength=$config['board3_ppkbb3cker_portal_torrents_textlength_'.$module_id];
	$ppkbb_portal_lttorrents_display=$config['board3_ppkbb3cker_portal_lttorrents_display_'.$module_id];
	$ppkbb_portal_torrents_perpage=$config['board3_ppkbb3cker_portal_torrents_perpage_'.$module_id];

	$gen_page="portal.{$phpEx}";


$disallow_access = array_unique(array_keys($auth->acl_getf('!f_read', true)));

$forum_sql=$update_count=array();

in_array($ppkbb_portal_last_torrents[0], array(0, 1, 2)) ? '' : $ppkbb_portal_last_torrents[0]=0;
if(!$ppkbb_portal_last_torrents[0])
{
	return false;
}

$sql = 'SELECT left_id, right_id, parent_id, forum_id, forum_name, forum_image FROM ' . FORUMS_TABLE . " WHERE forum_topics_real > 0 AND forumas='1'".(sizeof($disallow_access) ? " AND forum_id NOT IN('".implode("', '", $disallow_access)."')" : '').($exclude_forums ? " AND forum_id ".(!$ppkbb_portal_trueexclude_forums ? ''  : 'NOT ')."IN('".implode("', '", $exclude_forums)."')" : '').' ORDER BY left_id ASC';
$result = $db->sql_query($sql, $ppkbb_portal_last_torrents[3]);
while($row = $db->sql_fetchrow($result))
{
	$forum_sql[$row['forum_id']]=$row;
}
$db->sql_freeresult($result);
if(!sizeof($forum_sql))
{
	$template->assign_vars(array(
		'LATEST_TORR_COUNT' => 0,
		)
	);

	return defined('IN_PORTAL') ? 'ppkbb3cker_ltorrents_center.html' : true;
}
$dt=time();
$start = request_var('tp', 0);
$start < 0 ? $start=0 : '';
$type='sticky';
$lt_count=0;

if($ppkbb_portal_torrents_posttime[0] < 0)
{
	$type='torrents';
	$ppkbb_portal_torrents_posttime[0]=0;
}

$exclude_forums=array_keys($forum_sql);

$fetch_announcements = ppkbb_fetch_posts($forum_sql, $start, $ppkbb_portal_torrents_perpage, $ppkbb_portal_torrents_textlength, $ppkbb_portal_torrents_posttime, $type, $ppkbb_portal_last_torrents[0], array(), $exclude_forums, $ppkbb_portal_last_torrents, $lt_count);

$ltdisplay_images=$ppkbb_portal_lttorrents_display[1]+$ppkbb_portal_lttorrents_display[2];
$attachments_post=$attachments_tps=array();
if(isset($fetch_announcements[0]) && sizeof($fetch_announcements[0]))
{
	$sql="SELECT * FROM ".ATTACHMENTS_TABLE." WHERE post_msg_id IN('".implode("', '", $fetch_announcements[0])."') AND in_message='0' ORDER BY attach_id DESC";
	$query=$db->sql_query($sql, $ppkbb_portal_last_torrents[3]);
	$image_ext=array('jpg', 'jpeg', 'gif', 'png');
	while($row=$db->sql_fetchrow($query))
	{
		$row['skip_attachment']=0;
		if($row['extension']=='torrent')
		{
			$attachments_tps[$row['post_msg_id']]['torrents'][]=$row;
			$row['skip_attachment']=1;
		}
		else if(in_array($row['extension'], $image_ext) && $ppkbb_portal_last_torrents[0]!=2)
		{
			if($row['i_poster']==1)
			{
				$attachments_tps[$row['post_msg_id']]['posters'][]=$row;
			}
			else
			{
				$attachments_tps[$row['post_msg_id']]['screenshots'][]=$row;
			}
			$row['skip_attachment']=1;
		}
		if(!$ltdisplay_images)
		{
			$attachments_tps=array();
		}
		$row['i_external'] ? $row['skip_attachment']=1 : '';
		$attachments_post[$row['post_msg_id']][]=$row;
	}
	$db->sql_freeresult($query);
}
unset($fetch_announcements[0]);
$ppkbb_tcbonus_upvalue_reset=$ic=0;

$extensions=$a_ext=$torrents_hashes=array();
$torrent_top_notice=$torrent_bottom_notice=false;
if($config['ppkbb_noticedisclaimer_blocks'][5])
{
	if($user->data['is_registered'] && $config['ppkbb_noticedisclaimer_blocks'][5]!=2)
	{
		$config['ppkbb_noticedisclaimer_blocks'][1] && $user->lang['TORRENT_TOP_NOTICE'] ? $torrent_top_notice=$user->lang['TORRENT_TOP_NOTICE'] : '';
		$config['ppkbb_noticedisclaimer_blocks'][2] && $user->lang['TORRENT_BOTTOM_NOTICE'] ? $torrent_bottom_notice=$user->lang['TORRENT_BOTTOM_NOTICE'] : '';
	}
	else if(!$user->data['is_registered'] && $config['ppkbb_noticedisclaimer_blocks'][5]!=1)
	{
		$config['ppkbb_noticedisclaimer_blocks'][3] && $user->lang['TORRENT_TOP_NOTICE_GUEST'] ? $torrent_top_notice=$user->lang['TORRENT_TOP_NOTICE_GUEST'] : '';
		$config['ppkbb_noticedisclaimer_blocks'][4] && $user->lang['TORRENT_BOTTOM_NOTICE_GUEST'] ? $torrent_bottom_notice=$user->lang['TORRENT_BOTTOM_NOTICE_GUEST'] : '';
	}
}

if(!$user->data['is_registered'])
{
	$user->lang['LOGORREG_DOWNLOAD']=sprintf($user->lang['LOGORREG_DOWNLOAD'], reapply_sid("{$phpbb_root_path}ucp.{$phpEx}?mode=register&amp;redirect=".build_url()), reapply_sid("{$phpbb_root_path}ucp.{$phpEx}?mode=login&amp;redirect=".build_url()));
}
$torrents_count=sizeof($fetch_announcements);
for ($i = 1; $i < $torrents_count+1; $i++)
{
	$a_fid = (intval($fetch_announcements[$i]['forum_id']));

	if(!$user->data['is_registered'])
	{
		$is_candowntorr = $auth->acl_get('u_candowntorr') && $auth->acl_get('f_candowntorr', $a_fid) && $config['ppkbb_tcguests_enabled'][0] ? 1 : 0;
		$config['ppkbb_tcguests_enabled'][0] && $config['ppkbb_tctrestricts_options'][2] && $fetch_announcements[$i]['size'] < $config['ppkbb_tctrestricts_options'][2] ? $is_candowntorr=1 : '';
		$is_candownpostscr = $auth->acl_get('u_candownpostscr') && $auth->acl_get('f_candownpostscr', $a_fid) ? 1 : 0;
	}
	else
	{
		$is_candowntorr=$is_candownpostscr=1;
		if($user->data['user_id']!=$fetch_announcements[$i]['user_id'])
		{
			$is_candowntorr=$auth->acl_get('u_candowntorr') && $auth->acl_get('f_candowntorr', $a_fid) ? 1 : 0;
			$is_candownpostscr=$auth->acl_get('u_candownpostscr') && $auth->acl_get('f_candownpostscr', $a_fid) ? 1 : 0;

		}
	}
	$is_torrents=isset($attachments_tps[$fetch_announcements[$i]['post_id']]['torrents']) && sizeof($attachments_tps[$fetch_announcements[$i]['post_id']]['torrents']) ? true : false;
	$is_posters=isset($attachments_tps[$fetch_announcements[$i]['post_id']]['posters']) && sizeof($attachments_tps[$fetch_announcements[$i]['post_id']]['posters']) ? true : false;
	$is_screenshots=isset($attachments_tps[$fetch_announcements[$i]['post_id']]['screenshots']) && sizeof($attachments_tps[$fetch_announcements[$i]['post_id']]['screenshots']) ? true : false;
	$is_attachments=isset($attachments_post[$fetch_announcements[$i]['post_id']]) && sizeof($attachments_post[$fetch_announcements[$i]['post_id']]) ? true : false;
	if((!$is_torrents && !$is_posters && !$is_screenshots) || !array_sum($ppkbb_portal_lttorrents_display))
	{
		$ltdisplay=0;
	}
	else
	{
		$ltdisplay=1;
	}
	$message=$attachments='';
	if ($is_attachments && $fetch_announcements[$i]['post_attachment'] && ($ppkbb_portal_last_torrents[0]==1))
	{
		// Grab extensions
		if(!isset($a_ext[$fetch_announcements[$i]['forum_id']]))
		{
			$extensions = $cache->obtain_attach_extensions($fetch_announcements[$i]['forum_id']);
			$a_ext[$fetch_announcements[$i]['forum_id']]=$extensions;
		}
		else
		{
			$extensions=$a_ext[$fetch_announcements[$i]['forum_id']];
		}
		$message=$fetch_announcements[$i]['post_text'];
		$attachments=$attachments_post[$fetch_announcements[$i]['post_id']];
		parse_attachments($fetch_announcements[$i]['forum_id'], $message, $attachments, $update_count);
	}

	$postrow=array(
		'TITLE'				=> $fetch_announcements[$i]['topic_title'],
		'POSTER'			=> $fetch_announcements[$i]['username'],
		'POSTER_LAST'			=> $fetch_announcements[$i]['username_last'],
		'TIME'				=> $fetch_announcements[$i]['topic_time'],
		//'TIME'				=> $fetch_announcements[$i]['added']>$fetch_announcements[$i]['topic_time'] ? $fetch_announcements[$i]['added'] : $fetch_announcements[$i]['topic_time'],
		'LAST_POST_TIME'				=> $fetch_announcements[$i]['topic_last_post_time'],
		'ADDED'				=> $fetch_announcements[$i]['added'],
		'TEXT'				=> $message,
		'REPLIES'			=> $fetch_announcements[$i]['topic_replies'],
		'TOPIC_VIEWS'		=> $fetch_announcements[$i]['topic_views'],
		'U_TOPICS_VIEWS'	=> append_sid($phpbb_root_path . 'viewtopic.' . $phpEx . '?p=' . $fetch_announcements[$i]['post_id']).'#p'.$fetch_announcements[$i]['post_id'],
		'U_LAST_COMMENTS'	=> append_sid($phpbb_root_path . 'viewtopic.' . $phpEx . '?f=' . $a_fid. '&amp;t=' . $fetch_announcements[$i]['topic_id'].'&amp;p='.$fetch_announcements[$i]['topic_last_post_id']).'#p'.$fetch_announcements[$i]['topic_last_post_id'],
		'U_VIEW_COMMENTS'	=> append_sid($phpbb_root_path . 'viewtopic.' . $phpEx . '?f=' . $a_fid. '&amp;t=' . $fetch_announcements[$i]['topic_id']),
		'U_POST_COMMENT'	=> append_sid($phpbb_root_path . 'posting.' . $phpEx . '?mode=reply&amp;t=' . $fetch_announcements[$i]['topic_id'] . '&amp;f=' . $a_fid),
		'S_NOT_LAST'		=> ($i < sizeof($fetch_announcements) - 1) ? true : false,

		'S_HAS_TRACKER_TORRENT' => $ppkbb_portal_lttorrents_display[0] && $is_candowntorr && $is_torrents ? true : false,
		'S_HAS_TRACKER_POSTER' => $ppkbb_portal_lttorrents_display[1] && $is_candownpostscr && $is_posters ? true : false,
		'S_HAS_TRACKER_SCREENSHOT' => $ppkbb_portal_lttorrents_display[2] && $is_candownpostscr && $is_screenshots ? true : false,

		'S_TORRENT_INFO' => $ppkbb_portal_lttorrents_display[3] &&/* $is_candowntorr && */$is_torrents ? true : false,

		'TORRENT_FORUM_LINK'	=> append_sid($phpbb_root_path . 'viewforum.' . $phpEx . '?f=' . $a_fid),
		'TORRENT_FORUM_NAME'	=> $forum_sql[$a_fid]['forum_name'],
		'TORRENT_FORUM_IMAGE' => $forum_sql[$a_fid]['forum_image'] ? $forum_sql[$a_fid]['forum_image'] : false,

		'TORRENT_COUNT' => $i,

		'TORRENTS_COUNT' => $is_torrents && sizeof($attachments_tps[$fetch_announcements[$i]['post_id']]['torrents']) > 1 ? true : false,
		'POSTERS_COUNT' => $is_posters && sizeof($attachments_tps[$fetch_announcements[$i]['post_id']]['posters']) > 1 ? true : false,
		'SCREENSHOTS_COUNT' => $is_screenshots && sizeof($attachments_tps[$fetch_announcements[$i]['post_id']]['screenshots']) > 1 ? true : false,

		'S_LTDISPLAY_IMAGES' => $ltdisplay_images && $ltdisplay ? true : false,
		'S_LTDISPLAY' => $ltdisplay ? true : false,
		'S_HAS_ATTACHMENTS' => $fetch_announcements[$i]['post_attachment'] && $attachments ? true : false,

		'S_TORRENT_TOP_NOTICE' => $torrent_top_notice,
		'S_TORRENT_BOTTOM_NOTICE' => $torrent_bottom_notice,
	);
	if(/*$ppkbb_portal_lttorrents_display[0] && $is_candowntorr && */$is_torrents)
	{
		/*if($config['ppkbb_tcenable_rannounces'][0] && $config['ppkbb_tcenable_rannounces'][6] && $fetch_announcements[$i]['forb'] < 1 && $ppkbb_portal_last_torrents[0]!=2)
		{
			$torrents_hashes[$row['torrent_id']]=$fetch_announcements[$i]['infohash'];
		}*/
		if(!$config['ppkbb_tcbonus_fsize'][1])
		{
			$ppkbb_tcbonus_upvalue_reset=1;
			$config['ppkbb_tcbonus_fsize'][1]=$fetch_announcements[$i]['size'];
		}
		$tsl_speed=my_split_config($fetch_announcements[$i]['tsl_speed'], 3, 'my_int_val');
		$total_updown_speed=array();
		if($fetch_announcements[$i]['forb'] > 0 || ($config['ppkbb_tcannounce_interval'] && $dt-$fetch_announcements[$i]['lastseed']>$config['ppkbb_tcannounce_interval'] && $dt-$fetch_announcements[$i]['lastleech']>$config['ppkbb_tcannounce_interval']))
		{
			$total_updown_speed['up_speed']=$total_updown_speed['down_speed']=0;
		}
		else
		{
			$total_updown_speed['up_speed']=$dt-$fetch_announcements[$i]['lastseed']>$config['ppkbb_tcdead_time'] ? 0 : $tsl_speed[0];
			$total_updown_speed['down_speed']=$dt-$fetch_announcements[$i]['lastleech']>$config['ppkbb_tcdead_time'] ? 0 : $tsl_speed[1];
		}

		$freetorr_percent=$fetch_announcements[$i]['free'];
		$postrow=array_merge($postrow, array(
			'TORRENT_SIZE_VAL' => get_formatted_filesize($fetch_announcements[$i]['size']),

			'TORRENT_FREE' => $fetch_announcements[$i]['free'] && $freetorr_percent ? true : false,
			'TORRENT_FREE_TEXT' => $fetch_announcements[$i]['free'] && $freetorr_percent ? sprintf($user->lang['FORM_TORRENT_FREE'], $fetch_announcements[$i]['free'], '%') : '',
			'TORRENT_FREE_SRC_IMG' => $phpbb_root_path . 'images/tracker/bookmark'.($ppkbb_portal_last_torrents[0]!=1 ? '' : '_big').'.png',

			'TORRENT_THANKS' => $fetch_announcements[$i]['thanks'],

			'TORRENT_BONUS' => $fetch_announcements[$i]['size'] > $config['ppkbb_tcbonus_fsize'][0] && $config['ppkbb_tcbonus_value'][0] > 0 && $config['ppkbb_tcbonus_value'][3]!=0.000 ? true : false,
			'TORRENT_BONUS_SRC_IMG' => $phpbb_root_path . 'images/tracker/add'.($ppkbb_portal_last_torrents[0]!=1 ? '' : '_big').'.png',
			'TORRENT_BONUS_TEXT' => $fetch_announcements[$i]['size'] > $config['ppkbb_tcbonus_fsize'][0] && $config['ppkbb_tcbonus_value'][0] > 0 && $config['ppkbb_tcbonus_value'][3]!=0.000 ? sprintf($user->lang['TORRENT_BONUS'], get_formatted_filesize($config['ppkbb_tcbonus_fsize'][1]), $config['ppkbb_tcbonus_value'][3]) : '',

			'TORRENT_SEEDERS_VAL' => $fetch_announcements[$i]['seeders'],
			'TORRENT_LEECHERS_VAL' => $fetch_announcements[$i]['leechers'],
			'TORRENT_COMPLETED_VAL' => $fetch_announcements[$i]['completed'],
			'TORRENT_HEALTH_VAL' => get_torrent_health($fetch_announcements[$i]['seeders'], $fetch_announcements[$i]['leechers']),

			'TORRENT_REAL_SEEDERS_VAL'		=> intval($row['real_seeders']),
			'TORRENT_REAL_LEECHERS_VAL'		=> intval($row['real_leechers']),
			'TORRENT_REAL_COMPLETED_VAL'		=> intval($row['real_times_completed']),
			'TORRENT_REM_SEEDERS_VAL'		=> intval($row['rem_seeders']),
			'TORRENT_REM_LEECHERS_VAL'		=> intval($row['rem_leechers']),
			'TORRENT_REM_COMPLETED_VAL'		=> intval($row['rem_times_completed']),

			//'TORRENT_REQ_UPLOAD' => $fetch_announcements[$i]['req_upload'],
			//'TORRENT_REQ_RATIO' => $fetch_announcements[$i]['req_ratio'],

			'TORRENT_UPSPEED' => get_formatted_filesize($total_updown_speed['up_speed'], 1, false, 1),
			'TORRENT_DOWNSPEED' => get_formatted_filesize($total_updown_speed['down_speed'], 1, false, 1),

			)
		);
		$ppkbb_tcbonus_upvalue_reset ? $config['ppkbb_tcbonus_fsize'][1]=0 : '';
	}
	$template->assign_block_vars('torrents_row', $postrow);
	if ($attachments)
	{
		foreach ($attachments as $attachment)
		{
			$template->assign_block_vars('torrents_row.attachment', array(
				'DISPLAY_ATTACHMENT'	=> $attachment)
			);
		}
	}
	if($ppkbb_portal_lttorrents_display[0]/* && $is_candowntorr*/ && $is_torrents)
	{
		foreach ($attachments_tps[$fetch_announcements[$i]['post_id']]['torrents'] as $torrent_data)
		{
			$torrent_basename=utf8_basename(urldecode($torrent_data['real_filename']));
			$torrent_shortname=$config['ppkbb_torrblock_width'][2] && utf8_strlen($torrent_basename) > $config['ppkbb_torrblock_width'][2] ? utf8_substr($torrent_basename, 0, $config['ppkbb_torrblock_width'][2]).'...' : $torrent_basename;

			$torrent_src_link=append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . (int) $torrent_data['attach_id'], true, ($torrent_data['is_orphan']) ? $user->session_id : false);
			$is_candowntorr ? $torrent_link=$torrent_src_link : $torrent_link='';

			$magnet_src_link='';
			if($torrent_link && (($config['ppkbb_torrent_magnetlink'][0] && $user->data['is_registered']) || (!$user->data['is_registered'] && $config['ppkbb_torrent_gmagnetlink'][0] && $config['ppkbb_tcguests_enabled'][0])))
			{
				$magnet_src_link="{$torrent_src_link}&amp;magnet=1";
			}

			$template->assign_block_vars('torrents_row.torrent_fields', array(
				'TORRENT_LINK' => $torrent_link ? true : false,
				'TORRENT_SRC_LINK' => $torrent_link ? $torrent_src_link : '',

				'TORRENT_MAGNET_LINK' => $magnet_src_link ? $magnet_src_link : false,
				'TORRENT_MAGNET_SRC_IMG'	=> $phpbb_root_path.'images/tracker/filesaveas'.($ppkbb_portal_last_torrents[0]!=1 ? '' : '_big').'.png',

				'TORRENT_DOWNLOAD_SRC_IMG' => $phpbb_root_path . 'images/tracker/filesave'.($ppkbb_portal_last_torrents[0]!=1 ? '' : '_big').'.png',
				'TORRENT_DOWNLOAD_SRC_SMALLIMG' => $phpbb_root_path . 'images/tracker/filesave.png',

				'TORRENT_FILENAME' => $torrent_basename,
				'TORRENT_SHORTNAME' => $torrent_shortname,
				)
			);
		}
	}
	if($ppkbb_portal_lttorrents_display[1] && $is_candownpostscr && $ppkbb_portal_last_torrents[0]!=2)
	{
		if($is_posters)
		{
			$ic+=1;
			foreach ($attachments_tps[$fetch_announcements[$i]['post_id']]['posters'] as $poster_data)
			{
				$poster_addon='';
				if(!$poster_data['i_external'])
				{
					if($poster_data['thumbnail'] && $ppkbb_portal_lttorrents_display[1]==1)
					{
						$poster_addon='&amp;t=1';
					}
				}

					$poster_wh=tracker_get_thumb_size($poster_data['i_width'], $poster_data['i_height'], $config['ppkbb_torrblock_width'][4], $config['ppkbb_torrblock_width'][5], ($config['ppkbb_torrblock_width'][12] ? true : false));

				$template->assign_block_vars('torrents_row.torrent_poster_fields', array(
					'POSTER_LINK' => !$poster_data['i_external'] ? append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . (int) $poster_data['attach_id'], true, ($poster_data['is_orphan']) ? $user->session_id : false).'&amp;ext=.'.$poster_data['extension'] : $poster_data['real_filename'],
					'POSTER_SRC' => !$poster_data['i_external'] ? append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . $poster_data['attach_id'] . $poster_addon) : $poster_data['real_filename'],
					'POSTER_WH_WIDTH' => $poster_wh[0] ? $poster_wh[0] : false,
					'POSTER_WH_HEIGHT' => $poster_wh[1] ? $poster_wh[1] : false,
					'POSTER_COUNT' => $ic,
					'POSTER_FORUM' => !$poster_data['i_external'] ? 1 : 0,
					'POSTER_HEIGHT' => $ppkbb_portal_last_torrents[0]==3  && $ppkbb_portal_last_torrents[4] && $x > $ppkbb_portal_last_torrents[4] ? false : $ppkbb_portal_last_torrents[2],
					'POSTER_WIDTH' => $ppkbb_portal_last_torrents[0]==3  && $ppkbb_portal_last_torrents[4] ? $ppkbb_portal_last_torrents[4] : false,
					)
				);
			}
		}
	}

	if($ppkbb_portal_lttorrents_display[2] && $is_candownpostscr && $is_screenshots && $ppkbb_portal_last_torrents[0]==1)
	{
		$ic+=1;
		foreach ($attachments_tps[$fetch_announcements[$i]['post_id']]['screenshots'] as $screenshot_data)
		{
			$screenshot_addon='';
			if(!$screenshot_data['i_external'])
			{

				if($screenshot_data['thumbnail'] && $ppkbb_portal_lttorrents_display[2]==1)
				{
					$screenshot_addon='&amp;t=1';
				}
			}
			$screenshot_basename=utf8_basename($screenshot_data['real_filename']);
			$screenshot_wh=tracker_get_thumb_size($screenshot_data['i_width'], $screenshot_data['i_height'], $config['ppkbb_torrblock_width'][6], $config['ppkbb_torrblock_width'][7], ($config['ppkbb_torrblock_width'][12] ? true : false));
			$template->assign_block_vars('torrents_row.torrent_screenshot_fields', array(
				'SCREENSHOT_LINK' => !$screenshot_data['i_external'] ? append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . (int) $screenshot_data['attach_id'], true, ($screenshot_data['is_orphan']) ? $user->session_id : false).'&amp;ext=.'.$screenshot_data['extension'] : $screenshot_data['real_filename'],
				'SCREENSHOT_SRC' => !$screenshot_data['i_external'] ? append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . $screenshot_data['attach_id'] . $screenshot_addon) : $screenshot_data['real_filename'],
				'SCREENSHOT_WH_WIDTH' => $screenshot_wh[0] ? $screenshot_wh[0] : false,
				'SCREENSHOT_WH_HEIGHT' => $screenshot_wh[1] ? $screenshot_wh[1] : false,
				'SCREENSHOT_COUNT' => $ic,
				'SCREENSHOT_FORUM' => !$screenshot_data['i_external'] ? 1 : 0,
				)
			);
		}
	}
}

$template->assign_vars(array(
	'POSTERS_TABLE_WIDTH' => $config['ppkbb_torrblock_width'][4]+$config['ppkbb_torrblock_width'][0],
	'SCREENSHOTS_TABLE_WIDTH' => $config['ppkbb_torrblock_width'][6]+$config['ppkbb_torrblock_width'][0],
	//'IMG_THUMB_WIDTH' => $config['ppkbb_tmax_thumbwidth'],
	'TP_PAGINATION' => generate_pagination(append_sid("{$phpbb_root_path}{$gen_page}").'#t', $lt_count, $ppkbb_portal_torrents_perpage, $start, false, '', 'tp'),
	'TP_PAGE_NUMBER' => on_page($lt_count, $ppkbb_portal_torrents_perpage, $start),
	'TP_TOTAL_TORRENTS' => ($lt_count == 1) ? $user->lang['VIEW_LATEST_TORRENT'] : sprintf($user->lang['VIEW_LATEST_TORRENTS'], $lt_count),
	'LATEST_TORR_COUNT' => $lt_count ? 1 : 0,
	'S_AS_MESSAGES' => $ppkbb_portal_last_torrents[0]==1 ? true : false,
	)
);

//##############################################################################
function ppkbb_fetch_posts($forum_sql, $start, $number_of_posts, $text_length, $ppkbb_portal_torrents_posttime, $type, $mode=1, $ltorrents_catfilters=array(), $exclude_forums=array(), $ppkbb_portal_last_torrents=array(), &$lt_count=0)
{
	global $db, $phpbb_root_path, $phpEx, $auth, $user, $config, $dt, $ppkbb_portal_torrents_perpage;

	$from_forums=implode("', '", array_keys($forum_sql));
	$from_forum=($from_forums) ? "tr.forum_id IN ('" . $from_forums . "') AND " : '';
	$post_time=($ppkbb_portal_torrents_posttime[0] == 0) ? '' : ($ppkbb_portal_torrents_posttime[1] ? 'tr.added > ' : 't.topic_time > ') . ($dt - $ppkbb_portal_torrents_posttime[0] * 86400) . ' AND ';

	if ($type == 'announcements')
	{
		// only global announcements for announcements block
		$topic_type = '( t.topic_type = ' . POST_ANNOUNCE . ') AND';
	}
	else if ($type == 'sticky')
	{
		// only sticky topics/posts for torrents block
		$topic_type = '( t.topic_type = ' . POST_STICKY . ' ) AND';
	}
	else if ($type == 'news_all')
	{
		// not show global announcements
		$topic_type = '( t.topic_type != ' . POST_ANNOUNCE . ' ) AND';
	}
	else if ($type == 'torrents')
	{
		// not show global announcements
		$topic_type = '( t.topic_type != ' . POST_GLOBAL . ' ) AND';
	}
	else
	{
		// only normal topic
		$topic_type = 't.topic_type = ' . POST_NORMAL . ' AND';
	}

	$posts = array();

		$sql = 'SELECT
				COUNT(*) lt_count
			FROM
				' . TOPICS_TABLE . ' AS t,
				' . USERS_TABLE . ' AS u,
				' . TRACKER_TORRENTS_TABLE . ' AS tr
				'.($mode==1 ? ', '.POSTS_TABLE . ' AS p ' : '').
			'WHERE
				' . $topic_type . '
				' . $from_forum . '
				%1$s
				t.topic_poster = u.user_id AND '
				.($mode==1 ? ' t.topic_first_post_id = p.post_id AND ' : '').
				't.topic_approved = 1 AND '
				.($mode==1 ? ' tr.topic_id = p.topic_id AND ' : ' t.topic_id=tr.topic_id AND ').
				'tr.forb < 1
				'.($ppkbb_portal_torrents_posttime[2] ? ' AND tr.seeders'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_seeders' : '').' > '.($ppkbb_portal_torrents_posttime[2]-1) : '');
		$result = $db->sql_query(sprintf($sql, $post_time), $ppkbb_portal_last_torrents[3], md5($sql));
		$lt_count = (int) $db->sql_fetchfield('lt_count');
		$db->sql_freeresult($result);

		if(!$lt_count)
		{
			return $posts;
		}

		$sql = 'SELECT
			t.forum_id,
			t.topic_id,
			t.topic_last_post_id,
			t.topic_time,
			t.topic_title,
			t.topic_attachment,
			t.topic_views,
			t.topic_replies,
			t.forum_id,
			t.topic_poster,
			t.topic_type,
			t.topic_last_post_id,
			t.topic_last_poster_id,
			t.topic_last_poster_name,
			t.topic_last_poster_colour,
			t.topic_last_post_time,
			u.username,
			u.user_id,
			u.user_type,
			u.user_colour,
			tr.id torrent_id,
			tr.post_msg_id,
			tr.size,
			tr.free,
			tr.upload,
			tr.times_completed'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_times_completed' : '').' times_completed,
			tr.seeders'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_seeders' : '').' seeders,
			tr.leechers'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_leechers' : '').' leechers,
			tr.times_completed real_times_completed,
			tr.leechers real_leechers,
			tr.seeders real_seeders,
			'.($config['ppkbb_tcenable_rannounces'][0] ? '
			tr.rem_times_completed rem_times_completed,
			tr.rem_leechers rem_leechers,
			tr.rem_seeders rem_seeders,
			' : '').'
			tr.req_upload,
			tr.req_ratio,
			tr.tsl_speed,
			tr.info_hash,
			tr.forb,
			tr.added,
			tr.thanks,
			tr.lastseed,
			tr.lastleech '
			.($mode==1 ? ',
			p.post_text,
			p.enable_smilies,
			p.enable_bbcode,
			p.enable_magic_url,
			p.bbcode_bitfield,
			p.bbcode_uid
			' : '').
		'FROM
			' . TOPICS_TABLE . ' AS t,
			' . USERS_TABLE . ' AS u,
			' . TRACKER_TORRENTS_TABLE . ' AS tr
			'.($mode==1 ? ', '.POSTS_TABLE . ' AS p ' : '').
		'WHERE
			' . $topic_type . '
			' . $from_forum . '
			%1$s
			t.topic_poster = u.user_id AND '
			.($mode==1 ? ' t.topic_first_post_id = p.post_id AND ' : '').
			't.topic_approved = 1 AND '
			.($mode==1 ? ' tr.topic_id = p.topic_id AND ' : ' t.topic_id=tr.topic_id AND ').
			'tr.forb < 1
			'.($ppkbb_portal_torrents_posttime[2] ? ' AND tr.seeders'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_seeders' : '').' > '.($ppkbb_portal_torrents_posttime[2]-1) : '')."
		ORDER BY
			".($ppkbb_portal_torrents_posttime[1] ? 'tr.added' : 't.topic_time')." DESC LIMIT {$start}, {$number_of_posts}";

	$result = $db->sql_query(sprintf($sql, $post_time), $ppkbb_portal_last_torrents[3], md5($sql));

	if($mode==1)
	{
		include_once($phpbb_root_path . 'includes/bbcode.' . $phpEx);
		$bbcode = new bbcode();
	}

	$i = 1;
	while ( ($row = $db->sql_fetchrow($result))/* && ( ($i-1 < $number_of_posts) || ($number_of_posts == '0') ) */)
	{
		if($mode==1)
		{
			$message = $row['post_text'];

			// Parse the message and subject
			$message = censor_text($message);

			// Second parse bbcode here
			if ($row['bbcode_bitfield'])
			{
				$bbcode->bbcode_second_pass($message, $row['bbcode_uid'], $row['bbcode_bitfield']);
			}
			$message = bbcode_nl2br($message);
			$message = smiley_text($message);

			if (($text_length != 0) && (strlen($message) > $text_length))
			{
				$message = utf8_substr($message, 0, $text_length);
				$message = closetags($message);
				$message .= '...';
			}

			$row['post_text']= $message;
		}
		$row['topic_title'] = censor_text($row['topic_title']);

		$posts[0][]=$row['post_msg_id'];
		$posts[$i]['post_text'] = ($mode==1) ? $row['post_text'] : '';
		$posts[$i]['topic_id'] = $row['topic_id'];
		$posts[$i]['topic_last_post_id'] = $row['topic_last_post_id'];
		$posts[$i]['forum_id'] = $row['forum_id'];
		$posts[$i]['topic_replies'] = $row['topic_replies'];
		$posts[$i]['topic_type'] = $row['topic_type'];
		$posts[$i]['topic_time'] = $user->format_date($row['topic_time']);
		$posts[$i]['added'] = $user->format_date($row['added']);
		$posts[$i]['topic_time2'] = $ppkbb_portal_torrents_posttime[1] ? $row['added'] : $row['topic_time'];
		$posts[$i]['topic_title'] = $row['topic_title'];
		$posts[$i]['post_id'] = $row['post_msg_id'];
		$posts[$i]['username'] =  get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
		$posts[$i]['username_last'] =  get_username_string('full', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']);
		$posts[$i]['user_id'] = $row['user_id'];
		$posts[$i]['user_type'] = $row['user_type'];
		$posts[$i]['user_user_colour'] = $row['user_colour'];
		$posts[$i]['post_attachment'] = true;
		$posts[$i]['topic_views'] = $row['topic_views'];
		$posts[$i]['tid'] = $row['torrent_id'];
		$posts[$i]['size'] = $row['size'];
		$posts[$i]['free'] = $row['free'];
		$posts[$i]['thanks'] = $row['thanks'];
		$posts[$i]['upload'] = $row['upload'];
		$posts[$i]['seeders'] = $row['seeders'];
		$posts[$i]['leechers'] = $row['leechers'];
		$posts[$i]['completed'] = $row['times_completed'];

		$posts[$i]['topic_last_post_id'] = $row['topic_last_post_id'];
		$posts[$i]['topic_last_post_time'] = $user->format_date($row['topic_last_post_time']);

		$posts[$i]['real_seeders'] = $row['real_seeders'];
		$posts[$i]['real_leechers'] = $row['real_leechers'];
		$posts[$i]['real_completed'] = $row['real_times_completed'];
		$posts[$i]['rem_seeders'] = isset($row['rem_seeders']) ? $row['rem_seeders'] : 0;
		$posts[$i]['rem_leechers'] = isset($row['rem_leechers']) ? $row['rem_leechers'] : 0;
		$posts[$i]['rem_completed'] = isset($row['rem_times_completed']) ? $row['rem_times_completed'] : 0;

		$posts[$i]['req_upload'] = $row['req_upload'];
		$posts[$i]['req_ratio'] = $row['req_ratio'];
		$posts[$i]['tsl_speed'] = $row['tsl_speed'];
		$posts[$i]['infohash'] = $row['info_hash'];
		$posts[$i]['forb'] = $row['forb'];
		$posts[$i]['lastseed'] = $row['lastseed'];
		$posts[$i]['lastleech'] = $row['lastleech'];
		$i++;
	}
	$db->sql_freeresult($result);

	return $posts;
}

function closetags($text, $ex=array('area', 'base', 'basefont', 'br', 'col', 'frame', 'hr', 'img', 'input', 'isindex', 'link', 'meta'))
{
	$text = substr($text, 0, strrpos($text," "));
	$text = preg_replace("/<[^>]*$/i", "", $text);
	preg_match_all("/<[^a-z>\/]*([a-z]{1,50})/i", $text, $otags);
	if(count($otags[0])>0)
	{
		$fotags=$fctags=array();
		preg_match_all("/<[ t]*\/[^a-z]*([a-z]{1,50})/i", $text, $ctags);
		foreach($otags[1] as $otag)
		{
			$otag = strtolower($otag);
			if(isset($fotags[$otag]))
			{
				$fotags[$otag]++;
			}
			else
			{
				$fotags[$otag] = 1;
			}
		}
		foreach($ctags[1] as $ctag)
		{
			$ctag = strtolower($ctag);
			if(isset($fctags[$ctag]))
			{
				$fctags[$ctag]++;
			}
			else
			{
				$fctags[$ctag] = 1;
			}
		}
		while(list($tag, $cnt) = each($fotags))
		{
			if(in_array($tag, $ex))
			{
				continue;
			}
			$fctags[$tag] = isset($fctags[$tag]) ? $fctags[$tag] : 0;
			$text.=str_repeat("</{$tag}>", abs($fctags[$tag] - $cnt));
		}
	}
	return $text;
}

?>
