<?php
/**
*
* @package mcp
* @version $Id$
* @copyright (c) 2005 phpBB Group
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

/**
* MCP Forum View
*/
function mcp_forum_view($id, $mode, $action, $forum_info)
{
	global $template, $db, $user, $auth, $cache, $module;
	global $phpEx, $phpbb_root_path, $config;

	$user->add_lang(array('viewtopic', 'viewforum'));

	include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);

	// merge_topic is the quickmod action, merge_topics is the mcp_forum action, and merge_select is the mcp_topic action
	$merge_select = ($action == 'merge_select' || $action == 'merge_topic' || $action == 'merge_topics') ? true : false;

	if ($merge_select)
	{
		// Fixes a "bug" that makes forum_view use the same ordering as topic_view
		unset($_POST['sk'], $_POST['sd'], $_REQUEST['sk'], $_REQUEST['sd']);
	}

	$forum_id			= $forum_info['forum_id'];
	$start				= request_var('start', 0);
	$topic_id_list		= request_var('topic_id_list', array(0));
	$post_id_list		= request_var('post_id_list', array(0));
	$source_topic_ids	= array(request_var('t', 0));
	$to_topic_id		= request_var('to_topic_id', 0);

	$url_extra = '';
	$url_extra .= ($forum_id) ? "&amp;f=$forum_id" : '';
	$url_extra .= ($GLOBALS['topic_id']) ? '&amp;t=' . $GLOBALS['topic_id'] : '';
	$url_extra .= ($GLOBALS['post_id']) ? '&amp;p=' . $GLOBALS['post_id'] : '';
	$url_extra .= ($GLOBALS['user_id']) ? '&amp;u=' . $GLOBALS['user_id'] : '';

	$url = append_sid("{$phpbb_root_path}mcp.$phpEx?$url_extra");

	// Resync Topics
	switch ($action)
	{
		case 'resync':
			$topic_ids = request_var('topic_id_list', array(0));
			mcp_resync_topics($topic_ids);
		break;

		case 'merge_topics':
			$source_topic_ids = $topic_id_list;
		case 'merge_topic':
			if ($to_topic_id)
			{
				merge_topics($forum_id, $source_topic_ids, $to_topic_id);
			}
		break;
	}


	$forum_astracker=$forum_info['forumas']==1 ? true : false;
	$form_forb=$forb_where=$forb_type='';
	if($forum_astracker)
	{
		$is_cansetforb = ($auth->acl_get('u_cansetforb') && $auth->acl_get('f_cansetforb', $forum_id)) ? 1 : 0;
		if($is_cansetforb || $is_cansetstatus)
		{
			$forb_selected=request_var('select_forb', '');

			if(in_array($forb_selected, array('e', 'm', 'p')))
			{
				$forb_type='string';
			}
			else if($forb_selected==='')
			{
				$forb_type='string';
			}
			else
			{
				$forb_selected=intval($forb_selected);
			}
			$form_forb.='<option value=""></option>';
			$torrent_statuses=get_torrent_statuses();
			ksort($torrent_statuses['TRACKER_FORB_REASON']);
			$forb_sel=array();
			foreach($torrent_statuses['TRACKER_FORB_REASON'] as $rk => $rv)
			{
				if($rk < 0 && $rk > -50 && @!$forb_sel[-1])
				{
					$form_forb.='<option value="m"'.($forb_selected==='m' ? ' selected="selected"' : '').'>'.$user->lang['TRACKER_FORB_MREASON'].'</option>';
					$forb_sel[-1]=1;

				}
				if($rk > 0 && @!$forb_sel[1])
				{
					$form_forb.='<option value="p"'.($forb_selected==='p' ? ' selected="selected"' : '').'>'.$user->lang['TRACKER_FORB_PREASON'].'</option>';
					$forb_sel[1]=1;

				}
				if($rk == 0 && @!$forb_sel[0])
				{
					$form_forb.='<option disabled="disabled">'.$user->lang['TRACKER_FORB_UREASON'].'</option>';
					$forb_sel[0]=1;
				}
				$form_forb.='<option value="'.$rk.'"'.($forb_selected===$rk ? ' selected="selected"' : '').'>&nbsp;&nbsp;&nbsp;'.$rv.'</option>';
				if($forb_type=='string')
				{
					if($forb_selected=='e')
					{
						$forb_where="tr.forb<-49";
					}
					else if($forb_selected=='m')
					{
						$forb_where="(tr.forb<0 AND tr.forb>-50)";
					}
					else if($forb_selected=='p')
					{
						$forb_where="tr.forb>0";
					}
				}
				else
				{
					$forb_selected==$rk ? $forb_where="tr.forb='{$rk}'" : '';
				}
			}
		}
	}


	$selected_ids = '';
	if (sizeof($post_id_list) && $action != 'merge_topics')
	{
		foreach ($post_id_list as $num => $post_id)
		{
			$selected_ids .= '&amp;post_id_list[' . $num . ']=' . $post_id;
		}
	}
	else if (sizeof($topic_id_list) && $action == 'merge_topics')
	{
		foreach ($topic_id_list as $num => $topic_id)
		{
			$selected_ids .= '&amp;topic_id_list[' . $num . ']=' . $topic_id;
		}
	}

	make_jumpbox($url . "&amp;i=$id&amp;action=$action&amp;mode=$mode" . (($merge_select) ? $selected_ids : ''), $forum_id, false, 'm_', true);

	$topics_per_page = ($forum_info['forum_topics_per_page']) ? $forum_info['forum_topics_per_page'] : $config['topics_per_page'];

	$sort_days = $total = 0;
	$sort_key = $sort_dir = '';
	$sort_by_sql = $sort_order_sql = array();
	mcp_sorting('viewforum', $sort_days, $sort_key, $sort_dir, $sort_by_sql, $sort_order_sql, $total, $forum_id);

	$forum_topics = ($total == -1) ? $forum_info['forum_topics'] : $total;
	$limit_time_sql = ($sort_days) ? 'AND t.topic_last_post_time >= ' . (time() - ($sort_days * 86400)) : '';

	if($forb_where)
	{
		$sql = "SELECT COUNT(*) torr_count
			FROM " . TOPICS_TABLE . " t, ".TRACKER_TORRENTS_TABLE." tr
			WHERE t.forum_id IN($forum_id, 0)
				" . (($auth->acl_get('m_approve', $forum_id)) ? '' : 'AND t.topic_approved = 1') . " AND t.topic_id=tr.topic_id AND {$forb_where}
				$limit_time_sql";
		$result = $db->sql_query($sql);
		$forum_topics=my_int_val($db->sql_fetchfield('torr_count'));
	}

	$template->assign_vars(array(

		'S_SELECT_FORB' => $form_forb ? $form_forb : false,
		'S_IS_TRACKER' => $forum_astracker ? true : false,

		'ACTION'				=> $action,
		'FORUM_NAME'			=> $forum_info['forum_name'],
		'FORUM_DESCRIPTION'		=> generate_text_for_display($forum_info['forum_desc'], $forum_info['forum_desc_uid'], $forum_info['forum_desc_bitfield'], $forum_info['forum_desc_options']),

		'REPORTED_IMG'			=> $user->img('icon_topic_reported', 'TOPIC_REPORTED'),
		'UNAPPROVED_IMG'		=> $user->img('icon_topic_unapproved', 'TOPIC_UNAPPROVED'),
		'LAST_POST_IMG'			=> $user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
		'NEWEST_POST_IMG'		=> $user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),

		'S_CAN_REPORT'			=> $auth->acl_get('m_report', $forum_id),
		'S_CAN_DELETE'			=> $auth->acl_get('m_delete', $forum_id),
		'S_CAN_MERGE'			=> $auth->acl_get('m_merge', $forum_id),
		'S_CAN_MOVE'			=> $auth->acl_get('m_move', $forum_id),
		'S_CAN_FORK'			=> $auth->acl_get('m_', $forum_id),
		'S_CAN_LOCK'			=> $auth->acl_get('m_lock', $forum_id),
		'S_CAN_SYNC'			=> $auth->acl_get('m_', $forum_id),
		'S_CAN_APPROVE'			=> $auth->acl_get('m_approve', $forum_id),
		'S_MERGE_SELECT'		=> ($merge_select) ? true : false,
		'S_CAN_MAKE_NORMAL'		=> $auth->acl_gets('f_sticky', 'f_announce', $forum_id),
		'S_CAN_MAKE_STICKY'		=> $auth->acl_get('f_sticky', $forum_id),
		'S_CAN_MAKE_ANNOUNCE'	=> $auth->acl_get('f_announce', $forum_id),

		'U_VIEW_FORUM'			=> append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $forum_id),
		'U_VIEW_FORUM_LOGS'		=> ($auth->acl_gets('a_', 'm_', $forum_id) && $module->loaded('logs')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=logs&amp;mode=forum_logs&amp;f=' . $forum_id) : '',

		'S_MCP_ACTION'			=> $url . "&amp;i=$id&amp;forum_action=$action&amp;mode=$mode&amp;start=$start" . (($merge_select) ? $selected_ids : ''),

		'PAGINATION'			=> generate_pagination($url . "&amp;i=$id&amp;action=$action&amp;mode=$mode&amp;sd=$sort_dir&amp;sk=$sort_key&amp;st=$sort_days" . (($merge_select) ? $selected_ids : '').($forb_where ? "&amp;select_forb={$forb_selected}" : ''), $forum_topics, $topics_per_page, $start),
		'PAGE_NUMBER'			=> on_page($forum_topics, $topics_per_page, $start),
		'TOTAL_TOPICS'			=> ($forum_topics == 1) ? $user->lang['VIEW_FORUM_TOPIC'] : sprintf($user->lang['VIEW_FORUM_TOPICS'], $forum_topics),
	));

	// Grab icons
	$icons = $cache->obtain_icons();

	$topic_rows = array();

	if ($config['load_db_lastread'])
	{
		$read_tracking_join = ' LEFT JOIN ' . TOPICS_TRACK_TABLE . ' tt ON (tt.topic_id = t.topic_id AND tt.user_id = ' . $user->data['user_id'] . ')';
		$read_tracking_select = ', tt.mark_time';
	}
	else
	{
		$read_tracking_join = $read_tracking_select = '';
	}

	$sql = "SELECT t.topic_id
		FROM " . TOPICS_TABLE . " t".($forb_where ? ', '.TRACKER_TORRENTS_TABLE.' tr' : '')."
		WHERE t.forum_id IN($forum_id, 0)
			" . (($auth->acl_get('m_approve', $forum_id)) ? '' : 'AND t.topic_approved = 1') . ($forb_where ? " AND t.topic_id=tr.topic_id AND {$forb_where}" : '')."
			$limit_time_sql
		ORDER BY t.topic_type DESC, $sort_order_sql";
	$result = $db->sql_query_limit($sql, $topics_per_page, $start);

	if($forum_astracker)
	{
		$dt=time();
		$read_tracking_select.=',
			tr.id torrent_id,
			tr.times_completed'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_times_completed' : '').' times_completed,
			tr.leechers'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_leechers' : '').' leechers,
			tr.seeders'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_seeders' : '').' seeders,
			tr.times_completed real_times_completed,
			tr.leechers real_leechers,
			tr.seeders real_seeders,
			'.($config['ppkbb_tcenable_rannounces'][0] ? '
			tr.rem_times_completed rem_times_completed,
			tr.rem_leechers rem_leechers,
			tr.rem_seeders rem_seeders,
			' : '').'
			tr.size,
			tr.free,
			tr.upload,
			tr.forb,
			tr.forb_reason,
			tr.forb_user_id,
			tr.forb_date,
			tr.tsl_speed,
			tr.added,
			tr.lastseed,
			tr.lastleech,
			tr.private,
			tr.req_ratio,
			tr.req_upload,
			tr.info_hash,
			tr.lastcleanup,
			tr.rem_leechers,
			tr.rem_seeders,
			tr.poster_id,
			tr.rem_times_completed,
			tr.lastremote,
			tr.thanks';
		$read_tracking_join.=' LEFT JOIN '.TRACKER_TORRENTS_TABLE.' tr ON (t.topic_id=tr.topic_id)';
	}

	$topic_list = $topic_tracking_info = array();

	while ($row = $db->sql_fetchrow($result))
	{
		$topic_list[] = $row['topic_id'];
	}
	$db->sql_freeresult($result);

	$sql = "SELECT t.*$read_tracking_select
		FROM " . TOPICS_TABLE . " t $read_tracking_join
		WHERE " . $db->sql_in_set('t.topic_id', $topic_list, false, true);

	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$topic_rows[$row['topic_id']] = $row;
	}
	$db->sql_freeresult($result);

	// If there is more than one page, but we have no topic list, then the start parameter is... erm... out of sync
	if (!sizeof($topic_list) && $forum_topics && $start > 0)
	{
		redirect($url . "&amp;i=$id&amp;action=$action&amp;mode=$mode");
	}

	// Get topic tracking info
	if (sizeof($topic_list))
	{
		if ($config['load_db_lastread'])
		{
			$topic_tracking_info = get_topic_tracking($forum_id, $topic_list, $topic_rows, array($forum_id => $forum_info['mark_time']), array());
		}
		else
		{
			$topic_tracking_info = get_complete_topic_tracking($forum_id, $topic_list, array());
		}
	}

	if($forum_astracker)
	{

		!isset($torrent_statuses) ? $torrent_statuses=get_torrent_statuses() : '';
		$user_ratio=get_ratio($user->data['user_uploaded'], $user->data['user_downloaded'], $config['ppkbb_tcratio_start'], $user->data['user_bonus']);

		$ppkbb_tcbonus_upvalue_reset=0;

	}

	foreach ($topic_list as $topic_id)
	{
		$topic_title = '';

		$row = &$topic_rows[$topic_id];

		$replies = ($auth->acl_get('m_approve', $forum_id)) ? $row['topic_replies_real'] : $row['topic_replies'];

		if ($row['topic_status'] == ITEM_MOVED)
		{
			$unread_topic = false;
		}
		else
		{
			$unread_topic = (isset($topic_tracking_info[$topic_id]) && $row['topic_last_post_time'] > $topic_tracking_info[$topic_id]) ? true : false;
		}

		// Get folder img, topic status/type related information
		$folder_img = $folder_alt = $topic_type = '';
		topic_status($row, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);

		$topic_title = censor_text($row['topic_title']);

		$topic_unapproved = (!$row['topic_approved'] && $auth->acl_get('m_approve', $row['forum_id'])) ? true : false;
		$posts_unapproved = ($row['topic_approved'] && $row['topic_replies'] < $row['topic_replies_real'] && $auth->acl_get('m_approve', $row['forum_id'])) ? true : false;
		$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? $url . '&amp;i=queue&amp;mode=' . (($topic_unapproved) ? 'approve_details' : 'unapproved_posts') . '&amp;t=' . $row['topic_id'] : '';

		$no_torrent = !isset($row['torrent_id']) || !$row['torrent_id']/* || (isset($row['torrent_id']) && $row['torrent_id'] && $row['topic_type'] == POST_GLOBAL)*/ ? 1 : 0;
		$req_ratio=$req_ratio_src_img=$req_ratio_text='';
		$req_upload=$req_upload_src_img=$req_upload_text='';
		$torrent_link=1;
		if($forum_astracker)
		{
			if(!$no_torrent)
			{
				$torrent_size = get_formatted_filesize($row['size']);

				$tsl_speed=my_split_config($row['tsl_speed'], 3, 'my_int_val');
				$total_updown_speed=array();
				if($row['forb'] > 0 || ($config['ppkbb_tcannounce_interval'] && $dt-$row['lastseed']>$config['ppkbb_tcannounce_interval'] && $dt-$row['lastleech']>$config['ppkbb_tcannounce_interval']))
				{
					$total_updown_speed['up_speed']=$total_updown_speed['down_speed']=0;
				}
				else
				{
					$total_updown_speed['up_speed']=$dt-$row['lastseed']>$config['ppkbb_tcdead_time'] ? 0 : $tsl_speed[0];
					$total_updown_speed['down_speed']=$dt-$row['lastleech']>$config['ppkbb_tcdead_time'] ? 0 : $tsl_speed[1];
				}

				if(!$user->data['is_registered'])
				{
					$is_candowntorr=$config['ppkbb_tcguests_enabled'][0] && $auth->acl_get('u_candowntorr') && $auth->acl_get('f_candowntorr', $forum_id) ? 1 : 0;
				}
				else
				{
					$is_candowntorr=1;
					if($user->data['user_id']!=$row['topic_poster'])
					{
						$is_candowntorr=$auth->acl_get('u_candowntorr') && $auth->acl_get('f_candowntorr', $forum_id) ? 1 : 0;
					}
				}

				if($is_candowntorr)
				{
					if(!isset($is_canusefree))
					{
						$is_canusefree = ($auth->acl_get('u_canusefree') && $auth->acl_get('f_canusefree', $forum_id)) && $user->data['is_registered'] ? 1 : 0;
						$is_canusebonus = ($auth->acl_get('u_canusebonus') && $auth->acl_get('f_canusebonus', $forum_id)) && $user->data['is_registered'] ? 1 : 0;
						$is_canskiprcheck =  $user->data['is_registered'] && $user->data['user_id']!=$row['topic_poster'] ? (($auth->acl_get('u_canskiprcheck') && $auth->acl_get('f_canskiprcheck', $forum_id)) ? 1 : 0) : 1;
						$is_canskiprequpload = $user->data['is_registered'] && $user->data['user_id']!=$row['topic_poster'] ? (($auth->acl_get('u_canskiprequpload') && $auth->acl_get('f_canskiprequpload', $forum_id)) ? 1 : 0) : 1;
						$is_canskipreqratio = $user->data['is_registered'] && $user->data['user_id']!=$row['topic_poster'] ? (($auth->acl_get('u_canskipreqratio') && $auth->acl_get('f_canskipreqratio', $forum_id)) ? 1 : 0) : 1;
					}

					$t_elapsed = intval(($dt - $row['added']) / 3600);
					$t_wait=-1;
					if(!$is_canskiprcheck && $config['ppkbb_tcwait_time'])
					{
						$t_wait=get_trestricts($user->data['user_uploaded'], $user->data['user_downloaded'], $user_ratio, $config['ppkbb_tcwait_time'], 'up');

						if ($t_wait > 0)
						{
							$t_elapsed < $t_wait ? $t_wait=$t_wait - $t_elapsed : $t_wait=-1;
						}
						$t_wait >= 0 ? $torrent_link=0 : '';
					}
					$t_wait2=-1;
					if(!$is_canskiprcheck && $config['ppkbb_tcwait_time2'])
					{
						$t_wait2=get_trestricts($user->data['user_uploaded'], $user->data['user_downloaded'], $user_ratio, $config['ppkbb_tcwait_time2']);
						if ($t_wait2 > 0)
						{
							$t_elapsed < $t_wait2 ? $t_wait2=$t_wait2 - $t_elapsed : $t_wait2=-1;
						}
						$t_wait2 >= 0 ? $torrent_link=0 : '';
					}

					if(!$is_canskiprequpload && $row['req_upload'])
					{
						if($user->data['user_uploaded'] < $row['req_upload'])
						{
							$req_upload_src_img=$phpbb_root_path . 'images/tracker/info_requpload.png';
							$req_upload_text=sprintf($user->lang['TORRENT_REQ_UPLOAD_1'], get_formatted_filesize($row['req_upload']), get_formatted_filesize($user->data['user_uploaded']));
							$req_upload=true;
							$torrent_link=0;
						}
						else
						{
							$req_upload_src_img=$phpbb_root_path . 'images/tracker/button_ok_requpload.png';
							$req_upload_text=sprintf($user->lang['TORRENT_REQ_UPLOAD_0'], get_formatted_filesize($row['req_upload']), get_formatted_filesize($user->data['user_uploaded']));
							$req_upload=true;
						}
					}

					if(!$is_canskipreqratio && $row['req_ratio']!=0.000)
					{
						if($user_ratio!='None.' && ($user_ratio < $row['req_ratio'] || $user_ratio=='Leech.' || $user_ratio=='Inf.'))
						{
							$req_ratio_src_img=$phpbb_root_path . 'images/tracker/info_reqratio.png';
							$req_ratio_text=sprintf($user->lang['TORRENT_REQ_RATIO_1'], $row['req_ratio'], '%');
							$req_ratio=true;
							$torrent_link=0;
						}
						else
						{
							$req_ratio_src_img=$phpbb_root_path . 'images/tracker/button_ok_reqratio.png';
							$req_ratio_text=sprintf($user->lang['TORRENT_REQ_RATIO_0'], $row['req_ratio'], '%');
							$req_ratio=true;
						}
					}

					if($row['forb'] && !isset($torrent_statuses['TRACKER_FORB_MARK'][$row['forb']]))
					{
						$update_tstatus[$row['forb']]=$row['forb'];
						$row['forb']=0;
					}
					$row['forb'] < 1 || ($row['forb'] > 0 && $user->data['user_id']==$row['topic_poster'] && in_array($row['forb'], $config['ppkbb_tcauthor_candown'])) ? '' : $torrent_link=0;

					$freetorr_percent=$is_canusefree ? $row['free'] : 0;

					$torrent_link ? $torrent_src_link=append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . (int) $row['torrent_id'], true) : '';

					$magnet_src_link='';
					if($torrent_link && (($config['ppkbb_torrent_magnetlink'][1] && $user->data['is_registered']) || (!$user->data['is_registered'] && $config['ppkbb_torrent_gmagnetlink'][1] && $config['ppkbb_tcguests_enabled'][0])))
					{
						$magnet_src_link="{$torrent_src_link}&amp;magnet=1";
					}

					if(!$config['ppkbb_tcbonus_fsize'][1])
					{
						$ppkbb_tcbonus_upvalue_reset=1;
						$config['ppkbb_tcbonus_fsize'][1]=$row['size'];
					}

				}

			}
		}

		$topic_row = array(

			'VIEWS'			=> $row['topic_views'],


			'ATTACH_ICON_IMG'		=> ($auth->acl_get('u_download') && $auth->acl_get('f_download', $row['forum_id']) && $row['topic_attachment']) ? $user->img('icon_topic_attach', $user->lang['TOTAL_ATTACHMENTS']) : '',
			'TOPIC_FOLDER_IMG'		=> $user->img($folder_img, $folder_alt),
			'TOPIC_FOLDER_IMG_SRC'	=> $user->img($folder_img, $folder_alt, false, '', 'src'),
			'TOPIC_ICON_IMG'		=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['img'] : '',
			'TOPIC_ICON_IMG_WIDTH'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['width'] : '',
			'TOPIC_ICON_IMG_HEIGHT'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['height'] : '',
			'UNAPPROVED_IMG'		=> ($topic_unapproved || $posts_unapproved) ? $user->img('icon_topic_unapproved', ($topic_unapproved) ? 'TOPIC_UNAPPROVED' : 'POSTS_UNAPPROVED') : '',

			'TOPIC_AUTHOR'				=> get_username_string('username', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
			'TOPIC_AUTHOR_COLOUR'		=> get_username_string('colour', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
			'TOPIC_AUTHOR_FULL'			=> get_username_string('full', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
			'U_TOPIC_AUTHOR'			=> get_username_string('profile', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),

			'LAST_POST_AUTHOR'			=> get_username_string('username', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
			'LAST_POST_AUTHOR_COLOUR'	=> get_username_string('colour', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
			'LAST_POST_AUTHOR_FULL'		=> get_username_string('full', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
			'U_LAST_POST_AUTHOR'		=> get_username_string('profile', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),

			'TOPIC_TYPE'		=> $topic_type,
			'TOPIC_TITLE'		=> $topic_title,
			'REPLIES'			=> ($auth->acl_get('m_approve', $row['forum_id'])) ? $row['topic_replies_real'] : $row['topic_replies'],
			'LAST_POST_TIME'	=> $user->format_date($row['topic_last_post_time']),
			'FIRST_POST_TIME'	=> $user->format_date($row['topic_time']),
			'LAST_POST_SUBJECT'	=> $row['topic_last_post_subject'],
			'LAST_VIEW_TIME'	=> $user->format_date($row['topic_last_view_time']),

			'S_TOPIC_REPORTED'		=> (!empty($row['topic_reported']) && empty($row['topic_moved_id']) && $auth->acl_get('m_report', $row['forum_id'])) ? true : false,
			'S_TOPIC_UNAPPROVED'	=> $topic_unapproved,
			'S_POSTS_UNAPPROVED'	=> $posts_unapproved,
			'S_UNREAD_TOPIC'		=> $unread_topic,
		);

		if($forum_astracker && !$no_torrent)
		{
			$topic_row=array_merge($topic_row, array(
				'TORRENT_LINK' => $is_candowntorr && $torrent_link ? true : false,

				'TORRENT_DOWNLOAD_SRC_IMG' => $phpbb_root_path . 'images/tracker/filesave.png',
				'TORRENT_SRC_LINK' => $is_candowntorr && $torrent_link ? $torrent_src_link : '',

				'TORRENT_MAGNET_LINK' => $is_candowntorr && $magnet_src_link ? $magnet_src_link : false,
				'TORRENT_MAGNET_SRC_IMG'	=> $phpbb_root_path.'images/tracker/filesaveas.png',

				'TORRENT_SEEDERS_VAL'		=> intval($row['seeders']),
				'TORRENT_LEECHERS_VAL'		=> intval($row['leechers']),
				'TORRENT_COMPLETED_VAL'		=> intval($row['times_completed']),
				'TORRENT_SIZE_VAL'		=> $torrent_size,
				'TORRENT_HEALTH_VAL'		=> get_torrent_health($row['seeders'], $row['leechers']),

				'TORRENT_REAL_SEEDERS_VAL'		=> intval($row['real_seeders']),
				'TORRENT_REAL_LEECHERS_VAL'		=> intval($row['real_leechers']),
				'TORRENT_REAL_COMPLETED_VAL'		=> intval($row['real_times_completed']),
				'TORRENT_REM_SEEDERS_VAL'		=> isset($row['rem_seeders']) ? intval($row['rem_seeders']) : '',
				'TORRENT_REM_LEECHERS_VAL'		=> isset($row['rem_leechers']) ? intval($row['rem_leechers']) : '',
				'TORRENT_REM_COMPLETED_VAL'		=> isset($row['rem_times_completed']) ? intval($row['rem_times_completed']) : '',

				'TORRENT_UPSPEED' => get_formatted_filesize($total_updown_speed['up_speed'], 1, false, 1),
				'TORRENT_DOWNSPEED' => get_formatted_filesize($total_updown_speed['down_speed'], 1, false, 1),

				'TORRENT_FREE' => $is_candowntorr && $is_canusefree && $row['free'] && $freetorr_percent ? true : false,
				'TORRENT_FREE_SRC_IMG' => $is_candowntorr ? $phpbb_root_path . 'images/tracker/bookmark.png' : '',
				'TORRENT_FREE_TEXT' => $is_candowntorr && $is_canusefree && $row['free'] && $freetorr_percent ? sprintf($user->lang['FORM_TORRENT_FREE'], $row['free'], '%') : '',

				'TORRENT_REQ_UPLOAD' => $req_upload ? true : false,
				'TORRENT_REQ_UPLOAD_SRC_IMG' => $req_upload_src_img,
				'TORRENT_REQ_UPLOAD_TEXT' => $req_upload_text,

				'TORRENT_REQ_RATIO' => $req_ratio ? true : false,
				'TORRENT_REQ_RATIO_SRC_IMG' => $req_ratio_src_img,
				'TORRENT_REQ_RATIO_TEXT' => $req_ratio_text,

				'TORRENT_THANKS' => $row['thanks'],

				'TORRENT_BONUS' => $is_candowntorr && $is_canusebonus && $row['size'] > $config['ppkbb_tcbonus_fsize'][0] && $config['ppkbb_tcbonus_value'][0] > 0 && $config['ppkbb_tcbonus_value'][3]!=0.000 ? true : false,
				'TORRENT_BONUS_SRC_IMG' => $phpbb_root_path . 'images/tracker/add.png',
				'TORRENT_BONUS_TEXT' => $is_candowntorr && $is_canusebonus && $row['size'] > $config['ppkbb_tcbonus_fsize'][0] && $config['ppkbb_tcbonus_value'][0] > 0 && $config['ppkbb_tcbonus_value'][3]!=0.000 ? sprintf($user->lang['TORRENT_BONUS'], get_formatted_filesize($config['ppkbb_tcbonus_fsize'][1]), $config['ppkbb_tcbonus_value'][3]) : '',

				'TORRENT_FORB' => $is_candowntorr && $row['forb'] > 0 ? true : false,
				'TORRENT_FORB_SRC_IMG' => $phpbb_root_path . 'images/tracker/'.($row['forb']==1 ? '' : 'half').'encrypted.png',
				'TORRENT_FORB_TEXT' => $is_candowntorr && $row['forb'] > 0 ? sprintf($user->lang['FORM_TORRENT_FORB'], $torrent_statuses['TRACKER_FORB_REASON'][$row['forb']]) : '',

				'TORRENT_WAIT' => $is_candowntorr && !$is_canskiprcheck && ($t_wait>=0 || $t_wait2 >= 0) ? true : false,
				'TORRENT_WAIT_SRC_IMG' => $phpbb_root_path . 'images/tracker/xclock.png',
				'TORRENT_WAIT_TEXT' => $is_candowntorr && !$is_canskiprcheck && ($t_wait>=0 || $t_wait2 >= 0) ? (($t_wait > 0 || $t_wait2 > 0) ? sprintf($user->lang['TORRENT_WAIT'], ($t_wait > $t_wait2 ? $t_wait : $t_wait2)) : $user->lang['TORRENT_WAIT_NEVER']) : '',

				'TORRENT_MARK'			=> $is_candowntorr && $row['forb']!=0 ? $torrent_statuses['TRACKER_FORB_MARK'][$row['forb']] : '',
				'S_HAS_TORRENT'		=> true,

				)
			);
			$ppkbb_tcbonus_upvalue_reset ? $config['ppkbb_tcbonus_fsize'][1]=0 : '';
		}

		if ($row['topic_status'] == ITEM_MOVED)
		{
			$topic_row = array_merge($topic_row, array(
				'U_VIEW_TOPIC'		=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", "t={$row['topic_moved_id']}"),
				'U_DELETE_TOPIC'	=> ($auth->acl_get('m_delete', $forum_id)) ? append_sid("{$phpbb_root_path}mcp.$phpEx", "i=$id&amp;f=$forum_id&amp;topic_id_list[]={$row['topic_id']}&amp;mode=forum_view&amp;action=delete_topic") : '',
				'S_MOVED_TOPIC'		=> true,
				'TOPIC_ID'			=> $row['topic_moved_id'],
			));
		}
		else
		{
			if ($action == 'merge_topic' || $action == 'merge_topics')
			{
				$u_select_topic = $url . "&amp;i=$id&amp;mode=forum_view&amp;action=$action&amp;to_topic_id=" . $row['topic_id'] . $selected_ids;
			}
			else
			{
				$u_select_topic = $url . "&amp;i=$id&amp;mode=topic_view&amp;action=merge&amp;to_topic_id=" . $row['topic_id'] . $selected_ids;
			}
			$topic_row = array_merge($topic_row, array(
				'U_VIEW_TOPIC'		=> append_sid("{$phpbb_root_path}mcp.$phpEx", "i=$id&amp;f=$forum_id&amp;t={$row['topic_id']}&amp;mode=topic_view"),

				'S_SELECT_TOPIC'	=> ($merge_select && !in_array($row['topic_id'], $source_topic_ids)) ? true : false,
				'U_SELECT_TOPIC'	=> $u_select_topic,
				'U_MCP_QUEUE'		=> $u_mcp_queue,
				'U_MCP_REPORT'		=> ($auth->acl_get('m_report', $forum_id)) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=main&amp;mode=topic_view&amp;t=' . $row['topic_id'] . '&amp;action=reports') : '',
				'TOPIC_ID'			=> $row['topic_id'],
				'S_TOPIC_CHECKED'	=> ($topic_id_list && in_array($row['topic_id'], $topic_id_list)) ? true : false,
			));
		}

		$template->assign_block_vars('topicrow', $topic_row);
	}
	unset($topic_rows);
}

/**
* Resync topics
*/
function mcp_resync_topics($topic_ids)
{
	global $auth, $db, $template, $phpEx, $user, $phpbb_root_path;

	if (!sizeof($topic_ids))
	{
		trigger_error('NO_TOPIC_SELECTED');
	}

	if (!check_ids($topic_ids, TOPICS_TABLE, 'topic_id', array('m_')))
	{
		return;
	}

	// Sync everything and perform extra checks separately
	sync('topic_reported', 'topic_id', $topic_ids, false, true);
	sync('topic_attachment', 'topic_id', $topic_ids, false, true);
	sync('topic', 'topic_id', $topic_ids, true, false);

	$sql = 'SELECT topic_id, forum_id, topic_title
		FROM ' . TOPICS_TABLE . '
		WHERE ' . $db->sql_in_set('topic_id', $topic_ids);
	$result = $db->sql_query($sql);

	// Log this action
	while ($row = $db->sql_fetchrow($result))
	{
		add_log('mod', $row['forum_id'], $row['topic_id'], 'LOG_TOPIC_RESYNC', $row['topic_title']);
	}
	$db->sql_freeresult($result);

	$msg = (sizeof($topic_ids) == 1) ? $user->lang['TOPIC_RESYNC_SUCCESS'] : $user->lang['TOPICS_RESYNC_SUCCESS'];

	$redirect = request_var('redirect', $user->data['session_page']);

	meta_refresh(3, $redirect);
	trigger_error($msg . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $redirect . '">', '</a>'));

	return;
}

/**
* Merge selected topics into selected topic
*/
function merge_topics($forum_id, $topic_ids, $to_topic_id)
{
	global $db, $template, $user, $phpEx, $phpbb_root_path, $auth;

	if (!sizeof($topic_ids))
	{
		$template->assign_var('MESSAGE', $user->lang['NO_TOPIC_SELECTED']);
		return;
	}
	if (!$to_topic_id)
	{
		$template->assign_var('MESSAGE', $user->lang['NO_FINAL_TOPIC_SELECTED']);
		return;
	}

	$topic_data = get_topic_data(array($to_topic_id), 'm_merge');
	if (!sizeof($topic_data))
	{
		$template->assign_var('MESSAGE', $user->lang['NO_FINAL_TOPIC_SELECTED']);
		return;
	}

	$result=$db->sql_query("SELECT f.forumas FROM ".FORUMS_TABLE." f, ".TOPICS_TABLE." t WHERE t.forum_id=f.forum_id AND t.topic_id IN('".implode("', '", $topic_ids)."') LIMIT 1");
	$forum_as=$db->sql_fetchrow($result);
	$forum_astracker=$forum_as['forumas']==1 ? 1 : 0;
	$forum_astracker2=$topic_data[$to_topic_id]['forumas']==1 ? 1 : 0;
	if($forum_astracker || $forum_astracker2)
	{
		$result2=$db->sql_query("SELECT topic_id FROM ".TRACKER_TORRENTS_TABLE." WHERE topic_id='{$to_topic_id}' LIMIT 1");
		$to_topic_tracker=$db->sql_fetchrow($result2);
		$to_topic_tracker=intval(@$to_topic_tracker['topic_id']);
		$result=$db->sql_query("SELECT topic_id FROM ".TRACKER_TORRENTS_TABLE." WHERE topic_id IN('".implode("', '", $topic_ids)."')");
		$from_topic_tracker=array();
		while($row=$db->sql_fetchrow($result))
		{
			$from_topic_tracker[$row['topic_id']]=$row['topic_id'];
		}
		if($to_topic_tracker || ($from_topic_tracker && $forum_astracker && $forum_astracker2))
		{
			$template->assign_var('MESSAGE', $user->lang['NO_MERGE_TRACKER_TOPICS']);
			return;
		}
	}

	$topic_data = $topic_data[$to_topic_id];

	$post_id_list	= request_var('post_id_list', array(0));
	$start			= request_var('start', 0);

	if (!sizeof($post_id_list) && sizeof($topic_ids))
	{
		$sql = 'SELECT post_id
			FROM ' . POSTS_TABLE . '
			WHERE ' . $db->sql_in_set('topic_id', $topic_ids);
		$result = $db->sql_query($sql);

		$post_id_list = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$post_id_list[] = $row['post_id'];
		}
		$db->sql_freeresult($result);
	}

	if (!sizeof($post_id_list))
	{
		$template->assign_var('MESSAGE', $user->lang['NO_POST_SELECTED']);
		return;
	}

	if (!check_ids($post_id_list, POSTS_TABLE, 'post_id', array('m_merge')))
	{
		return;
	}

	$redirect = request_var('redirect', build_url(array('quickmod')));

	$s_hidden_fields = build_hidden_fields(array(
		'i'				=> 'main',
		'f'				=> $forum_id,
		'post_id_list'	=> $post_id_list,
		'to_topic_id'	=> $to_topic_id,
		'mode'			=> 'forum_view',
		'action'		=> 'merge_topics',
		'start'			=> $start,
		'redirect'		=> $redirect,
		'topic_id_list'	=> $topic_ids)
	);
	$success_msg = $return_link = '';

	if (confirm_box(true))
	{
		$to_forum_id = $topic_data['forum_id'];

		move_posts($post_id_list, $to_topic_id);
		add_log('mod', $to_forum_id, $to_topic_id, 'LOG_MERGE', $topic_data['topic_title']);

		// Message and return links
		$success_msg = 'POSTS_MERGED_SUCCESS';

		if (!function_exists('phpbb_update_rows_avoiding_duplicates_notify_status'))
		{
			include($phpbb_root_path . 'includes/functions_database_helper.' . $phpEx);
		}

		// Update the topic watch table.
		phpbb_update_rows_avoiding_duplicates_notify_status($db, TOPICS_WATCH_TABLE, 'topic_id', $topic_ids, $to_topic_id);

		// Update the bookmarks table.
		phpbb_update_rows_avoiding_duplicates($db, BOOKMARKS_TABLE, 'topic_id', $topic_ids, $to_topic_id);

		// Link to the new topic
		$return_link .= (($return_link) ? '<br /><br />' : '') . sprintf($user->lang['RETURN_NEW_TOPIC'], '<a href="' . append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $to_forum_id . '&amp;t=' . $to_topic_id) . '">', '</a>');
	}
	else
	{
		confirm_box(false, 'MERGE_TOPICS', $s_hidden_fields);
	}

	$redirect = request_var('redirect', "index.$phpEx");
	$redirect = reapply_sid($redirect);

	if (!$success_msg)
	{
		return;
	}
	else
	{
		meta_refresh(3, append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f=$to_forum_id&amp;t=$to_topic_id"));
		trigger_error($user->lang[$success_msg] . '<br /><br />' . $return_link);
	}
}

?>
