<?php
/**
*
* @package Precise Similar Topics II
* @version $Id$
* @copyright (c) 2010 Matt Friedman
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
* Find similar topics based on matching topic titles. Currently requires MySQL
* due to the use of FULLTEXT indexes and MATCH and AGAINST and UNIX_TIMESTAMP.
* MySQL FULLTEXT has built-in English ignore words. We'll use phpBB's ignore words
* for non-English languages. We also remove any admin-defined special ignore words.
*
* @package Precise Similar Topics II
*/
class phpbb_similar_topics
{
	/**
	* Is the MOD enabled?
	*/
	var $is_active		= false;

	/**
	* The maximum number of similar topics to display
	*/
	var $topic_limit	= 5;

	/**
	* The maximum age of similar topics to display (in days)
	*/
	var $topic_age		= 365;

	/**
	* Cache static SQL queries for similar topics
	*/
	var $cache_time		= 0;

	/**
	* String of words defined in ACP to be ignored in similar topic searches
	*/
	var $ignore_words	= '';

	/**
	* String of forum IDs that are not to be searched for similar topics
	*/
	var $ignore_forums	= '';

	/**
	* Is the current forum allowed to display similar topics?
	*/
	var $allowed_forum	= true;

	/**
	* Is the board using a MySQL database?
	*/
	var $mysql_db		= true;

	/**
	* Similar Topics MOD constructor
	*/
	function phpbb_similar_topics()
	{
		global $config, $db, $forum_id;

		$this->is_active     = (bool) $config['similar_topics'];
		$this->topic_limit   = (int) $config['similar_topics_limit'];
		$this->topic_age     = (int) $config['similar_topics_time'];
		$this->cache_time    = (int) $config['similar_topics_cache'];
		$this->ignore_words  = (string) $config['similar_topics_words'];
		$this->ignore_forums = (string) $config['similar_topics_ignore'];
		$this->allowed_forum = (!in_array($forum_id, explode(',', $config['similar_topics_hide']))) ? true : false;
		$this->mysql_db      = (($db->sql_layer == 'mysql4') || ($db->sql_layer == 'mysqli')) ? true : false;
	}

	/**
	* Get similar topics by matching topic titles
	* @access public
	*/
	function get_similar_topics()
	{
		global $auth, $cache, $config, $user, $db, $topic_data, $template, $phpbb_root_path, $phpEx;

		// All reasons to bail out of the MOD
		if (!$this->is_active || !$this->mysql_db || !$this->topic_limit || !$this->allowed_forum)
		{
			return;
		}

		$topic_title = $this->_strip_topic_title($topic_data['topic_title']);

		// If the stripped down topic_title is empty, no need to continue
		if (empty($topic_title))
		{
			return;
		}

		// Similar Topics query
		$sql_array = array(
			'SELECT'	=> "f.forum_id, f.forum_name, t.*,
				MATCH (t.topic_title) AGAINST ('" . $db->sql_escape($topic_title) . "') AS score",

			'FROM'		=> array(
				TOPICS_TABLE	=> 't',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=>	array(FORUMS_TABLE	=> 'f'),
					'ON'	=> 'f.forum_id = t.forum_id',
				),
			),

			'WHERE'		=> "MATCH (t.topic_title) AGAINST ('" . $db->sql_escape($topic_title) . "') >= 0.5
				AND t.topic_status <> " . ITEM_MOVED . '
				AND t.topic_approved = 1
				AND t.topic_time > (UNIX_TIMESTAMP() - ' . $this->topic_age . ')
				AND t.topic_id <> ' . (int) $topic_data['topic_id'],

		//	'GROUP_BY'	=> 't.topic_id',
		//	'ORDER_BY'	=> 'score DESC', // this is done automatically by MySQL when not using IN BOOLEAN MODE
		);

		$dt=time();
		$forum_astracker=$config['similar_topics_tracker'] && $topic_data['forumas']==1 ? true : false;
		if($forum_astracker)
		{
			$sql_array = array(
				'SELECT'	=> "f.forum_id, f.forum_name, t.*,
					MATCH (t.topic_title) AGAINST ('" . $db->sql_escape($topic_title) . "') AS score
						, tr.id torrent_id,
						tr.times_completed".($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_times_completed' : '').' times_completed,
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
						tr.forb,
						tr.forb_reason,
						tr.added,
						tr.req_ratio,
						tr.req_upload,
						tr.info_hash,
						tr.lastcleanup,
						tr.tsl_speed,
						tr.lastseed,
						tr.thanks,
						tr.lastleech',

				'FROM'		=> array(
					TOPICS_TABLE	=> 't',
				),

				'LEFT_JOIN'	=> array(
					array(
						'FROM'	=>	array(FORUMS_TABLE	=> 'f'),
						'ON'	=> 'f.forum_id = t.forum_id',
					),
					array(
						'FROM'	=>	array(TRACKER_TORRENTS_TABLE=>'tr'),
						'ON'	=> 't.topic_id = tr.topic_id',
					),
				),

				'WHERE'		=> "MATCH (t.topic_title) AGAINST ('" . $db->sql_escape($topic_title) . "') >= 0.5
					AND t.topic_status <> " . ITEM_MOVED . '
					AND t.topic_approved = 1
					AND t.topic_time > (UNIX_TIMESTAMP() - ' . $this->topic_age . ')
					'.($topic_data['forumas']==1 ? " AND f.forumas='1' " : '').'
					AND t.topic_id <> ' . (int) $topic_data['topic_id'],

			//	'GROUP_BY'	=> 't.topic_id',
			//	'ORDER_BY'	=> 'score DESC', // this is done automatically by MySQL when not using IN BOOLEAN MODE
			);
			$ppkbb_tcbonus_upvalue_reset=0;

			$torrent_statuses=get_torrent_statuses();
			$user_ratio=get_ratio($user->data['user_uploaded'], $user->data['user_downloaded'], $config['ppkbb_tcratio_start'], $user->data['user_bonus']);
		}

		// Add topic tracking data to the query (only when query caching is off)
		if ($user->data['is_registered'] && $config['load_db_lastread'] && !$this->cache_time)
		{
			$sql_array['LEFT_JOIN'][] = array('FROM' => array(TOPICS_TRACK_TABLE => 'tt'), 'ON' => 'tt.topic_id = t.topic_id AND tt.user_id = ' . $user->data['user_id']);
			$sql_array['LEFT_JOIN'][] = array('FROM' => array(FORUMS_TRACK_TABLE => 'ft'), 'ON' => 'ft.forum_id = f.forum_id AND ft.user_id = ' . $user->data['user_id']);
			$sql_array['SELECT'] .= ', tt.mark_time, ft.mark_time as f_mark_time';
		}
		else if ($config['load_anon_lastread'] || $user->data['is_registered'])
		{
			// Cookie based tracking copied from search.php
			$tracking_topics = (isset($_COOKIE[$config['cookie_name'] . '_track'])) ? ((STRIP) ? stripslashes($_COOKIE[$config['cookie_name'] . '_track']) : $_COOKIE[$config['cookie_name'] . '_track']) : '';
			$tracking_topics = ($tracking_topics) ? tracking_unserialize($tracking_topics) : array();
		}

		// Now lets see if the current forum is set to search a specific forum search group, and search only those forums
		if (!empty($topic_data['similar_topic_forums']))
		{
			$sql_array['WHERE'] .= ' AND ' . $db->sql_in_set('f.forum_id', explode(',', $topic_data['similar_topic_forums']));
		}
		// Otherwise, lets see what forums are not allowed to be searched, and ignore those
		else if (!empty($this->ignore_forums))
		{
			$sql_array['WHERE'] .= ' AND ' . $db->sql_in_set('f.forum_id', explode(',', $this->ignore_forums), true);
		}

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query_limit($sql, $this->topic_limit, 0, $this->cache_time);

		// Grab icons
		$icons = $cache->obtain_icons();

		$rowset = array();

		while ($row = $db->sql_fetchrow($result))
		{
			$similar_forum_id = (int) $row['forum_id'];
			$similar_topic_id = (int) $row['topic_id'];
			$rowset[$similar_topic_id] = $row;

			if ($auth->acl_get('f_read', $similar_forum_id))
			{

				$is_candownload=$auth->acl_get('u_download') && $auth->acl_get('f_download', $similar_forum_id) ? 1 : 0;
				$no_torrent = !isset($row['torrent_id']) || !$row['torrent_id']/* || (isset($row['torrent_id']) && $row['torrent_id'] && $row['topic_type'] == POST_GLOBAL)*/ ? 1 : 0;
				if(!$no_torrent)
				{
					if($row['forb'] && !isset($torrent_statuses['TRACKER_FORB_MARK'][$row['forb']]))
					{
						$row['forb']=0;
					}
					$req_ratio=$req_ratio_src_img=$req_ratio_text='';
					$req_upload=$req_upload_src_img=$req_upload_text='';
					$torrent_link=1;
					if($forum_astracker)
					{
						$row['forb'] < 1 || ($row['forb'] > 0 && $user->data['user_id']==$row['topic_poster'] && in_array($row['forb'], $config['ppkbb_tcauthor_candown'])) ? '' : $torrent_link=0;
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
							$is_candowntorr=$config['ppkbb_tcguests_enabled'][0] && $auth->acl_get('u_candowntorr') && $auth->acl_get('f_candowntorr', $similar_forum_id) ? 1 : 0;
							$config['ppkbb_tcguests_enabled'][0] && $config['ppkbb_tctrestricts_options'][2] && $row['size'] < $config['ppkbb_tctrestricts_options'][2] ? $is_candowntorr=1 : '';
						}
						else
						{
							$is_candowntorr=1;
							if($user->data['user_id']!=$row['topic_poster'])
							{
								$is_candowntorr=$auth->acl_get('u_candowntorr') && $auth->acl_get('f_candowntorr', $similar_forum_id) ? 1 : 0;
								$user->data['user_lowratio_action'] ? $is_candowntorr=0 : '';
							}
						}

						if($is_candowntorr)
						{
							if(!isset($is_canusefree[$similar_forum_id]))
							{
								$is_canusefree[$similar_forum_id] = ($auth->acl_get('u_canusefree') && $auth->acl_get('f_canusefree', $similar_forum_id)) ? 1 : 0;
								$is_canusebonus[$similar_forum_id] = ($auth->acl_get('u_canusebonus') && $auth->acl_get('f_canusebonus', $similar_forum_id)) ? 1 : 0;
								$is_canskiprcheck[$similar_forum_id] = $user->data['is_registered'] && $user->data['user_id']!=$row['topic_poster'] ? (($auth->acl_get('u_canskiprcheck') && $auth->acl_get('f_canskiprcheck', $similar_forum_id)) ? 1 : 0) : 1;
								$is_canskiprequpload[$similar_forum_id] = $user->data['is_registered'] && $user->data['user_id']!=$row['topic_poster'] ? (($auth->acl_get('u_canskiprequpload') && $auth->acl_get('f_canskiprequpload', $similar_forum_id)) ? 1 : 0) : 1;
								$is_canskipreqratio[$similar_forum_id] = $user->data['is_registered'] && $user->data['user_id']!=$row['topic_poster'] ? (($auth->acl_get('u_canskipreqratio') && $auth->acl_get('f_canskipreqratio', $similar_forum_id)) ? 1 : 0) : 1;
							}

							$t_elapsed = intval(($dt - $row['added']) / 3600);
							$t_wait=-1;
							if(!$is_canskiprcheck[$similar_forum_id] && $config['ppkbb_tcwait_time'])
							{
								$t_wait=get_trestricts($user->data['user_uploaded'], $user->data['user_downloaded'], $user_ratio, $config['ppkbb_tcwait_time'], 'up');
								if ($t_wait > 0)
								{
									$t_elapsed < $t_wait ? $t_wait=$t_wait - $t_elapsed : $t_wait=-1;
								}
								$t_wait >= 0 ? $torrent_link=0 : '';
							}
							$t_wait2=-1;
							if(!$is_canskiprcheck[$similar_forum_id] && $config['ppkbb_tcwait_time2'])
							{
								$t_wait2=get_trestricts($user->data['user_uploaded'], $user->data['user_downloaded'], $user_ratio, $config['ppkbb_tcwait_time2']);
								if ($t_wait2 > 0)
								{
									$t_elapsed < $t_wait2 ? $t_wait2=$t_wait2 - $t_elapsed : $t_wait2=-1;
								}
								$t_wait2 >= 0 ? $torrent_link=0 : '';
							}
							if(!$is_canskiprequpload[$similar_forum_id] && $row['req_upload'])
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

							if(!$is_canskipreqratio[$similar_forum_id] && $row['req_ratio']!=0.000)
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

							$freetorr_percent=$is_canusefree[$similar_forum_id] ? $row['free'] : 0;

							$torrent_link ? $torrent_src_link=append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . $row['torrent_id'], true) : '';
							!$user->data['is_registered'] && !$config['ppkbb_tcguests_enabled'][0] ? $torrent_link='%s' : '';

							$magnet_src_link='';
							if($torrent_link && (($config['ppkbb_torrent_magnetlink'][1] && $user->data['is_registered']) || (!$user->data['is_registered'] && $config['ppkbb_torrent_gmagnetlink'][1] && $config['ppkbb_tcguests_enabled'][0])))
							{
								$magnet_src_link="{$torrent_src_link}&amp;magnet=1";
							}
						}
					}
				}


				// Get topic tracking info
				if ($user->data['is_registered'] && $config['load_db_lastread'] && !$this->cache_time)
				{
					$topic_tracking_info = get_topic_tracking($similar_forum_id, $similar_topic_id, $rowset, array($similar_forum_id => $row['f_mark_time']));
				}
				else if ($config['load_anon_lastread'] || $user->data['is_registered'])
				{
					$topic_tracking_info = get_complete_topic_tracking($similar_forum_id, $similar_topic_id);

					if (!$user->data['is_registered'])
					{
						$user->data['user_lastmark'] = (isset($tracking_topics['l'])) ? (int) (base_convert($tracking_topics['l'], 36, 10) + $config['board_startdate']) : 0;
					}
				}

				$folder_img = $folder_alt = $topic_type = '';
				$replies = ($auth->acl_get('m_approve', $similar_forum_id)) ? $row['topic_replies_real'] : $row['topic_replies'];
				$unread_topic = (isset($topic_tracking_info[$similar_topic_id]) && $row['topic_last_post_time'] > $topic_tracking_info[$similar_topic_id]) ? true : false;
				topic_status($row, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);

				$topic_unapproved = (!$row['topic_approved'] && $auth->acl_get('m_approve', $similar_forum_id)) ? true : false;
				$posts_unapproved = ($row['topic_approved'] && $row['topic_replies'] < $row['topic_replies_real'] && $auth->acl_get('m_approve', $similar_forum_id)) ? true : false;
				$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=queue&amp;mode=' . (($topic_unapproved) ? 'approve_details' : 'unapproved_posts') . '&amp;t=' . $similar_topic_id, true, $user->session_id) : '';


				$row['topic_replies']+=1;
				$viewtopic_page=$config['posts_per_page']*max(ceil($row['topic_replies'] / $config['posts_per_page']), 1)-$config['posts_per_page'];
				$similar_last_post_url	= append_sid("viewtopic.$phpEx", "f={$row['forum_id']}&amp;t={$row['topic_id']}".($viewtopic_page ? '&amp;start='.$viewtopic_page : ''))."#p{$row['topic_last_post_id']}";
				$row['topic_replies']-=1;

				$smlr_tpl_ary=array(
					'TOPIC_AUTHOR_FULL'		=> get_username_string('full', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
					'FIRST_POST_TIME'		=> $user->format_date($row['topic_time']),
					'LAST_POST_TIME'		=> $user->format_date($row['topic_last_post_time']),
					'LAST_POST_AUTHOR_FULL'	=> get_username_string('full', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),

					'PAGINATION'			=> topic_generate_pagination($row['topic_replies'], append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $similar_forum_id . '&amp;t=' . $similar_topic_id)),
					'TOPIC_REPLIES'			=> $row['topic_replies'],
					'TOPIC_VIEWS'			=> $row['topic_views'],
					'TOPIC_TITLE'			=> $row['topic_title'],
					'FORUM_TITLE'			=> $row['forum_name'],

					'TOPIC_FOLDER_IMG'		=> $user->img($folder_img, $folder_alt),
					'TOPIC_FOLDER_IMG_SRC'	=> $user->img($folder_img, $folder_alt, false, '', 'src'),

					'TOPIC_ICON_IMG'		=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['img'] : '',
					'TOPIC_ICON_IMG_WIDTH'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['width'] : '',
					'TOPIC_ICON_IMG_HEIGHT'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['height'] : '',
					'ATTACH_ICON_IMG'		=> ($row['topic_attachment'] && $auth->acl_get('u_download')) ? $user->img('icon_topic_attach', $user->lang['TOTAL_ATTACHMENTS']) : '',
					'UNAPPROVED_IMG'		=> ($topic_unapproved || $posts_unapproved) ? $user->img('icon_topic_unapproved', ($topic_unapproved) ? 'TOPIC_UNAPPROVED' : 'POSTS_UNAPPROVED') : '',

					'S_UNREAD_TOPIC'		=> $unread_topic,
					'S_TOPIC_REPORTED'		=> (!empty($row['topic_reported']) && $auth->acl_get('m_report', $similar_forum_id)) ? true : false,
					'S_TOPIC_UNAPPROVED'	=> $topic_unapproved,
					'S_POSTS_UNAPPROVED'	=> $posts_unapproved,

					'U_NEWEST_POST'			=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $similar_forum_id . '&amp;t=' . $similar_topic_id . '&amp;view=unread') . '#unread',
					'U_LAST_POST'			=> $similar_last_post_url,
					'U_VIEW_TOPIC'			=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $similar_forum_id . '&amp;t=' . $similar_topic_id),
					'U_VIEW_FORUM'			=> append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $similar_forum_id),
					'U_MCP_REPORT'			=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=reports&amp;mode=reports&amp;f=' . $similar_forum_id . '&amp;t=' . $similar_topic_id, true, $user->session_id),
					'U_MCP_QUEUE'			=> $u_mcp_queue,
				);

				if($forum_astracker && !$no_torrent)
				{
					if(!$config['ppkbb_tcbonus_fsize'][1])
					{
						$ppkbb_tcbonus_upvalue_reset=1;
						$config['ppkbb_tcbonus_fsize'][1]=$row['size'];
					}
					$smlr_tpl_ary=array_merge($smlr_tpl_ary, array(
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
				$template->assign_block_vars('similar', $smlr_tpl_ary);

			}
		}

		$db->sql_freeresult($result);

		$user->add_lang('mods/info_acp_similar_topics');

		$template->assign_vars(array(
			'L_SIMILAR_TOPICS'	=> $user->lang['PST_TITLE_ACP'],
			'NEWEST_POST_IMG'	=> $user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
			'LAST_POST_IMG'		=> $user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
			'REPORTED_IMG'		=> $user->img('icon_topic_reported', 'TOPIC_REPORTED'),
		));
	}

	/**
	* Remove problem characters (and if needed, ignore-words) from topic title
	* @access private
	*/
	function _strip_topic_title($text)
	{
		global $user;

		// Strip quotes, ampersands
		$text = str_replace(array('&quot;', '&amp;'), '', $text);

		$english_lang = ($user->lang_name == 'en' || $user->lang_name == 'en_us') ? true : false;
		$ignore_words = !empty($this->ignore_words) ? true : false;

		if (!$english_lang || $ignore_words)
		{
			$text = $this->_strip_stop_words($text, $english_lang, $ignore_words);
		}

		return $text;
	}

	/**
	* Remove any non-english and/or custom defined ignore-words
	* @access private
	*/
	function _strip_stop_words($text, $english_lang, $ignore_words)
	{
		global $user, $phpEx;

		$words = array();

		if (!$english_lang && file_exists("{$user->lang_path}{$user->lang_name}/search_ignore_words.$phpEx"))
		{
			// Retrieve a language dependent list of words to be ignored (method copied from search.php)
			include("{$user->lang_path}{$user->lang_name}/search_ignore_words.$phpEx");
		}

		if ($ignore_words)
		{
			// Merge any custom defined ignore words from the ACP to the stop-words array
			$words = array_merge($this->_make_word_array($this->ignore_words), $words);
		}

		// Remove stop-words from the topic title text
		$words = array_diff($this->_make_word_array($text), $words);

		// Convert our words array back to a string
		$text = !empty($words) ? implode(' ', $words) : '';

		return $text;
	}

	/**
	* Split string into an array of words
	* @access private
	*/
	function _make_word_array($text)
	{
		// Strip out any non-alpha-numeric characters using PCRE regex syntax
		$text = trim(preg_replace('#[^\p{L}\p{N}]+#u', ' ', $text));

		$words = explode(' ', utf8_strtolower($text));
		foreach ($words as $key => $word)
		{
			// Strip words of 2 characters or less
			if (utf8_strlen(trim($word)) < 3)
			{
				unset($words[$key]);
			}
		}

		return $words;
	}
}
