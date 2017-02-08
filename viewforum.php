<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session
$user->session_begin();
$auth->acl($user->data);

// Start initial var setup
$forum_id	= request_var('f', 0);
$mark_read	= request_var('mark', '');
$start		= request_var('start', 0);

$default_sort_days	= (!empty($user->data['user_topic_show_days'])) ? $user->data['user_topic_show_days'] : 0;
$default_sort_key	= (!empty($user->data['user_topic_sortby_type'])) ? $user->data['user_topic_sortby_type'] : 't';
$default_sort_dir	= (!empty($user->data['user_topic_sortby_dir'])) ? $user->data['user_topic_sortby_dir'] : 'd';

$sort_days	= request_var('st', $default_sort_days);
$sort_key	= request_var('sk', $default_sort_key);
$sort_dir	= request_var('sd', $default_sort_dir);

// Check if the user has actually sent a forum ID with his/her request
// If not give them a nice error page.
if (!$forum_id)
{
	trigger_error('NO_FORUM');
}

$sql_from = FORUMS_TABLE . ' f';
$lastread_select = '';

// Grab appropriate forum data
if ($config['load_db_lastread'] && $user->data['is_registered'])
{
	$sql_from .= ' LEFT JOIN ' . FORUMS_TRACK_TABLE . ' ft ON (ft.user_id = ' . $user->data['user_id'] . '
		AND ft.forum_id = f.forum_id)';
	$lastread_select .= ', ft.mark_time';
}

if ($user->data['is_registered'])
{
	$sql_from .= ' LEFT JOIN ' . FORUMS_WATCH_TABLE . ' fw ON (fw.forum_id = f.forum_id AND fw.user_id = ' . $user->data['user_id'] . ')';
	$lastread_select .= ', fw.notify_status';
}

$sql = "SELECT f.* $lastread_select
	FROM $sql_from
	WHERE f.forum_id = $forum_id";
$result = $db->sql_query($sql);
$forum_data = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if (!$forum_data)
{
	trigger_error('NO_FORUM');
}
if($forum_data['forumas']==2 && $forum_data['forum_type'] != FORUM_CAT)
{
	trigger_error(defined('IN_PORTAL') ? 'CHAT_PORTAL_ERROR' : 'CHAT_INDEX_ERROR');
}

// Configure style, language, etc.
$user->setup('viewforum', $forum_data['forum_style']);

if (!class_exists('CGP') && $config['cgp_enabled'] && !$config['ppkbb_cgp_places'][2])
{
	include($phpbb_root_path . 'includes/cache_guests_pages.' . $phpEx);
}

if (defined('CGP_ENABLED') && !$config['ppkbb_cgp_places'][2])
{
	// process typical cases only to prevent cache size exploding
	if (CGP::is_cacheable_user($user) &&
		$forum_id &&
		$sort_days == $default_sort_days &&
		$sort_key == $default_sort_key &&
		$sort_dir == $default_sort_dir)
	{
		define('CGP_KEY', "_vf_f{$forum_id}_s{$start}" . CGP::user_type_suffix($user));

		CGP::display_if_cached(CGP_KEY);
	}
}

// Redirect to login upon emailed notification links
if (isset($_GET['e']) && !$user->data['is_registered'])
{
	login_box('', $user->lang['LOGIN_NOTIFY_FORUM']);
}

// Permissions check
if (!$auth->acl_gets('f_list', 'f_read', $forum_id) || ($forum_data['forum_type'] == FORUM_LINK && $forum_data['forum_link'] && !$auth->acl_get('f_read', $forum_id)))
{
	if ($user->data['user_id'] != ANONYMOUS)
	{
		trigger_error('SORRY_AUTH_READ');
	}

	login_box('', $user->lang['LOGIN_VIEWFORUM']);
}

// Forum is passworded ... check whether access has been granted to this
// user this session, if not show login box
if ($forum_data['forum_password'])
{
	login_forum_box($forum_data);
}

// Is this forum a link? ... User got here either because the
// number of clicks is being tracked or they guessed the id
if ($forum_data['forum_type'] == FORUM_LINK && $forum_data['forum_link'])
{
	// Does it have click tracking enabled?
	if ($forum_data['forum_flags'] & FORUM_FLAG_LINK_TRACK)
	{
		$sql = 'UPDATE ' . FORUMS_TABLE . '
			SET forum_posts = forum_posts + 1
			WHERE forum_id = ' . $forum_id;
		$db->sql_query($sql);
	}

	// We redirect to the url. The third parameter indicates that external redirects are allowed.
	redirect($forum_data['forum_link'], false, true);
	return;
}

// Build navigation links
generate_forum_nav($forum_data);

// Forum Rules
if ($auth->acl_get('f_read', $forum_id))
{
	generate_forum_rules($forum_data);
}

// Do we have subforums?
$active_forum_ary = $moderators = array();

if ($forum_data['left_id'] != $forum_data['right_id'] - 1)
{
	list($active_forum_ary, $moderators) = display_forums($forum_data, $config['load_moderators'], $config['load_moderators']);
}
else
{
	$template->assign_var('S_HAS_SUBFORUM', false);
	if ($config['load_moderators'])
	{
		get_moderators($moderators, $forum_id);
	}
}

// Dump out the page header and load viewforum template
page_header($user->lang['VIEW_FORUM'] . ' - ' . $forum_data['forum_name'], true, $forum_id);

($forum_data['forum_type'] == FORUM_POST && $forum_data['forumas']==1) ? $forum_astracker=1 : $forum_astracker=0;
$dt=time();
$t_pagination=$sql_addon=$s_torrents_selects='';
$posting_page=$viewforum_page=0;
$update_tstatus=array();

if($forum_astracker)
{
	$template->set_filenames(array(
		'body' => 'viewforum_tracker_body.html')
	);
}
else
{
	$template->set_filenames(array(
		'body' => 'viewforum_body.html')
	);
}

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"), $forum_id);

$template->assign_vars(array(
	'U_VIEW_FORUM'			=> append_sid("{$phpbb_root_path}viewforum.$phpEx", "f=$forum_id" . (($start == 0) ? '' : "&amp;start=$start")),
));

// Not postable forum or showing active topics?
if (!($forum_data['forum_type'] == FORUM_POST || (($forum_data['forum_flags'] & FORUM_FLAG_ACTIVE_TOPICS) && $forum_data['forum_type'] == FORUM_CAT)))
{
	page_footer();
}

// Ok, if someone has only list-access, we only display the forum list.
// We also make this circumstance available to the template in case we want to display a notice. ;)
if (!$auth->acl_get('f_read', $forum_id))
{
	$template->assign_vars(array(
		'S_NO_READ_ACCESS'		=> true,
	));

	page_footer();
}

// Handle marking posts
if ($mark_read == 'topics')
{
	$token = request_var('hash', '');
	if (check_link_hash($token, 'global'))
	{
		// Add 0 to forums array to mark global announcements correctly
		markread('topics', array($forum_id, 0));
	}
	$redirect_url = append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $forum_id);
	meta_refresh(3, $redirect_url);

	trigger_error($user->lang['TOPICS_MARKED'] . '<br /><br />' . sprintf($user->lang['RETURN_FORUM'], '<a href="' . $redirect_url . '">', '</a>'));
}

// Is a forum specific topic count required?
if ($forum_data['forum_topics_per_page'])
{
	$config['topics_per_page'] = $forum_data['forum_topics_per_page'];
}

// Do the forum Prune thang - cron type job ...
if ($forum_data['prune_next'] < time() && $forum_data['enable_prune'])
{
	$template->assign_var('RUN_CRON_TASK', '<img src="' . append_sid($phpbb_root_path . 'cron.' . $phpEx, 'cron_type=prune_forum&amp;f=' . $forum_id) . '" alt="cron" width="1" height="1" />');
}

// Forum rules and subscription info
$s_watching_forum = array(
	'link'			=> '',
	'title'			=> '',
	'is_watching'	=> false,
);

if (($config['email_enable'] || $config['jab_enable']) && $config['allow_forum_notify'] && $forum_data['forum_type'] == FORUM_POST && ($auth->acl_get('f_subscribe', $forum_id) || $user->data['user_id'] == ANONYMOUS))
{
	$notify_status = (isset($forum_data['notify_status'])) ? $forum_data['notify_status'] : NULL;
	watch_topic_forum('forum', $s_watching_forum, $user->data['user_id'], $forum_id, 0, $notify_status, $start, $forum_data['forum_name']);
}

$s_forum_rules = '';
gen_forum_auth_level('forum', $forum_id, $forum_data['forum_status']);

// Topic ordering options
$limit_days = array(0 => $user->lang['ALL_TOPICS'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);

$sort_by_text = array('a' => $user->lang['AUTHOR'], 't' => $user->lang['POST_TIME'], 'r' => $user->lang['REPLIES'], 's' => $user->lang['SUBJECT'], 'v' => $user->lang['VIEWS']);
$sort_by_sql = array('a' => 't.topic_first_poster_name', 't' => 't.topic_last_post_time', 'r' => 't.topic_replies', 's' => 't.topic_title', 'v' => 't.topic_views');
if($forum_astracker)
{
	//if($user->data['is_registered'])
	//{
		$sort_by_text=array_merge($sort_by_text, array('sz'=>$user->lang['TORRENT_SIZE'], 'ts'=>$user->lang['TORRENT_SEEDERS'], 'tl'=>$user->lang['TORRENT_LEECHERS'], 'tc'=>$user->lang['TORRENT_COMPLETED'], 'th'=>$user->lang['TORRENT_HEALTH'], 'tf'=>$user->lang['TORRENT_STATUS']));
		$sort_by_sql=array_merge($sort_by_sql, array('sz'=>'tr.size', 'ts'=>'tr.seeders'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_seeders' : ''), 'tl'=>'tr.leechers'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_leechers' : ''), 'tc'=>'tr.times_completed'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_times_completed' : ''), 'th'=>'tr.seeders'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_seeders' : '').'/tr.leechers'.($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_leechers' : ''), 'tf'=>'tr.forb'));
	//}
}

$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param, $default_sort_days, $default_sort_key, $default_sort_dir);

if($forum_astracker && $user->data['is_registered'])
{
	$s_torrents_selects=gen_torrents_selects();
	$show_torrents=request_var('show_torrents', 't');
	switch($show_torrents)
	{
		case 'dead':
			$sql_addon=" AND !ISNULL(tr.id) AND tr.leechers".($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_leechers' : '')."='0' AND tr.seeders".($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_seeders' : '')."='0'";
			$t_pagination='show_torrents=dead';
			break;

		case 'good':
			$sql_addon=" AND !ISNULL(tr.id) AND tr.seeders".($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_seeders' : '')."!='0'";
			$t_pagination='show_torrents=good';
			break;

		case 'noseed':
			$sql_addon=" AND !ISNULL(tr.id) AND tr.seeders".($config['ppkbb_tcenable_rannounces'][0] ? '+tr.rem_seeders' : '')."='0'";
			$t_pagination='show_torrents=noseed';
			break;

		case 'bad':
			$sql_addon=' AND ISNULL(tr.id)';
			$t_pagination='show_torrents=bad';
			break;

		case 'withoutbad':
			$sql_addon=' AND !ISNULL(tr.id)';
			$t_pagination='show_torrents=withoutbad';
			break;

		case 'all':
		default:

			break;
	}
}

// Limit topics to certain time frame, obtain correct topic count
// global announcements must not be counted, normal announcements have to
// be counted, as forum_topics(_real) includes them
if ($sort_days || $sql_addon)
{
	$min_post_time = time() - ($sort_days * 86400);
	if($sql_addon && !$sort_days)
	{
		$sql = 'SELECT COUNT(t.topic_id) AS num_topics
			FROM ' . TOPICS_TABLE . " t ".($forum_astracker ? "LEFT JOIN ".TRACKER_TORRENTS_TABLE." tr ON (t.topic_id=tr.topic_id)" : '')."
			WHERE t.forum_id = $forum_id
				AND (((t.topic_type <> " . POST_GLOBAL . ") AND t.topic_last_post_time >= $min_post_time)
					OR t.topic_type = " . POST_ANNOUNCE . ")
			" . (($auth->acl_get('m_approve', $forum_id)) ? '' : 'AND t.topic_approved = 1').($forum_astracker ? $sql_addon : '');
		$result = $db->sql_query($sql);
		$topics_count = (int) $db->sql_fetchfield('num_topics');
		$db->sql_freeresult($result);
		$sql_limit_time = '';
	}
	else
	{
		$sql = 'SELECT COUNT(t.topic_id) AS num_topics
			FROM ' . TOPICS_TABLE . " t ".($forum_astracker ? "LEFT JOIN ".TRACKER_TORRENTS_TABLE." tr ON (t.topic_id=tr.topic_id)" : '')."
			WHERE t.forum_id = $forum_id
				AND ((t.topic_type <> " . POST_GLOBAL . " AND t.topic_last_post_time >= $min_post_time)
					OR t.topic_type = " . POST_ANNOUNCE . ")
			" . (($auth->acl_get('m_approve', $forum_id)) ? '' : 'AND t.topic_approved = 1').($forum_astracker ? $sql_addon : '');
		$result = $db->sql_query($sql);
		$topics_count = (int) $db->sql_fetchfield('num_topics');
		$db->sql_freeresult($result);

		if (isset($_POST['sort']))
		{
			$start = 0;
		}
		$sql_limit_time = "AND t.topic_last_post_time >= $min_post_time";

		// Make sure we have information about day selection ready
		$template->assign_var('S_SORT_DAYS', true);
	}
}
else
{
	$topics_count = ($auth->acl_get('m_approve', $forum_id)) ? $forum_data['forum_topics_real'] : $forum_data['forum_topics'];
	$sql_limit_time = '';
}

// Make sure $start is set to the last page if it exceeds the amount
if ($start < 0 || $start > $topics_count)
{
	$start = ($start < 0) ? 0 : floor(($topics_count - 1) / $config['topics_per_page']) * $config['topics_per_page'];
}

// Basic pagewide vars
$post_alt = ($forum_data['forum_status'] == ITEM_LOCKED) ? $user->lang['FORUM_LOCKED'] : $user->lang['POST_NEW_TOPIC'];


!$user->data['is_registered'] && (!$config['ppkbb_chat_guests'] || $config['cgp_enabled']) ? $config['ppkbb_chat_enable']=0 : '';
if($config['ppkbb_chat_enable'] && !$config['ppkbb_chat_display'][2] && $config['ppkbb_index_chat'] && !defined('IN_PORTAL') && !defined('IN_CHAT') && !$user->data['user_tracker_options'][1])
{
	$config['ppkbb_portal_chat']=$config['ppkbb_index_chat'];
	include($phpbb_root_path . 'chat/ppkbb3cker_chat.' . $phpEx);
}
// Display active topics?
$s_display_active = ($forum_data['forum_type'] == FORUM_CAT && ($forum_data['forum_flags'] & FORUM_FLAG_ACTIVE_TOPICS)) ? true : false;
$s_search_hidden_fields = array('fid' => array($forum_id));
if ($_SID)
{
	$s_search_hidden_fields['sid'] = $_SID;
}

if (!empty($_EXTRA_URL))
{
	foreach ($_EXTRA_URL as $url_param)
	{
		$url_param = explode('=', $url_param, 2);
		$s_search_hidden_fields[$url_param[0]] = $url_param[1];
	}
}
$template->assign_vars(array(

	'S_CHAT_INDEX' => defined('SHOW_CHAT') ? true : false,
	'S_SELECT_TORRENTS'		=> $forum_data['forumas']==1 ? $s_torrents_selects : '',
	'S_IS_TRACKER' => $forum_astracker ? true : false,
	'MODERATORS'	=> (!empty($moderators[$forum_id])) ? implode(', ', $moderators[$forum_id]) : '',

	'POST_IMG'					=> ($forum_data['forum_status'] == ITEM_LOCKED) ? $user->img('button_topic_locked', $post_alt) : $user->img('button_topic_new', $post_alt),
	'NEWEST_POST_IMG'			=> $user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
	'LAST_POST_IMG'				=> $user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
	'FOLDER_IMG'				=> $user->img('topic_read', 'NO_UNREAD_POSTS'),
	'FOLDER_UNREAD_IMG'			=> $user->img('topic_unread', 'UNREAD_POSTS'),
	'FOLDER_HOT_IMG'			=> $user->img('topic_read_hot', 'NO_UNREAD_POSTS_HOT'),
	'FOLDER_HOT_UNREAD_IMG'		=> $user->img('topic_unread_hot', 'UNREAD_POSTS_HOT'),
	'FOLDER_LOCKED_IMG'			=> $user->img('topic_read_locked', 'NO_UNREAD_POSTS_LOCKED'),
	'FOLDER_LOCKED_UNREAD_IMG'	=> $user->img('topic_unread_locked', 'UNREAD_POSTS_LOCKED'),
	'FOLDER_STICKY_IMG'			=> $user->img('sticky_read', 'POST_STICKY'),
	'FOLDER_STICKY_UNREAD_IMG'	=> $user->img('sticky_unread', 'POST_STICKY'),
	'FOLDER_ANNOUNCE_IMG'		=> $user->img('announce_read', 'POST_ANNOUNCEMENT'),
	'FOLDER_ANNOUNCE_UNREAD_IMG'=> $user->img('announce_unread', 'POST_ANNOUNCEMENT'),
	'FOLDER_MOVED_IMG'			=> $user->img('topic_moved', 'TOPIC_MOVED'),
	'REPORTED_IMG'				=> $user->img('icon_topic_reported', 'TOPIC_REPORTED'),
	'UNAPPROVED_IMG'			=> $user->img('icon_topic_unapproved', 'TOPIC_UNAPPROVED'),
	'GOTO_PAGE_IMG'				=> $user->img('icon_post_target', 'GOTO_PAGE'),

	'L_NO_TOPICS' 			=> ($forum_data['forum_status'] == ITEM_LOCKED) ? $user->lang['POST_FORUM_LOCKED'] : $user->lang['NO_TOPICS'],

	'S_DISPLAY_POST_INFO'	=> ($forum_data['forum_type'] == FORUM_POST && ($auth->acl_get('f_post', $forum_id) || $user->data['user_id'] == ANONYMOUS)) ? true : false,

	'S_IS_POSTABLE'			=> ($forum_data['forum_type'] == FORUM_POST) ? true : false,
	'S_USER_CAN_POST'		=> ($auth->acl_get('f_post', $forum_id)) ? true : false,
	'S_DISPLAY_ACTIVE'		=> $s_display_active,
	'S_SELECT_SORT_DIR'		=> $s_sort_dir,
	'S_SELECT_SORT_KEY'		=> $s_sort_key,
	'S_SELECT_SORT_DAYS'	=> $s_limit_days,

	'S_TOPIC_ICONS'			=> ($s_display_active && sizeof($active_forum_ary)) ? max($active_forum_ary['enable_icons']) : (($forum_data['enable_icons']) ? true : false),
	'S_WATCH_FORUM_LINK'	=> $s_watching_forum['link'],
	'S_WATCH_FORUM_TITLE'	=> $s_watching_forum['title'],
	'S_WATCHING_FORUM'		=> $s_watching_forum['is_watching'],
	'S_FORUM_ACTION'		=> append_sid("{$phpbb_root_path}viewforum.$phpEx", "f=$forum_id" . (($start == 0) ? '' : "&amp;start=$start")),
	'S_DISPLAY_SEARCHBOX'	=> ($auth->acl_get('u_search') && $auth->acl_get('f_search', $forum_id) && $config['load_search']) ? true : false,
	'S_SEARCHBOX_ACTION'	=> append_sid("{$phpbb_root_path}search.$phpEx"),
	'S_SEARCH_LOCAL_HIDDEN_FIELDS'	=> build_hidden_fields($s_search_hidden_fields),
	'S_SINGLE_MODERATOR'	=> (!empty($moderators[$forum_id]) && sizeof($moderators[$forum_id]) > 1) ? false : true,
	'S_IS_LOCKED'			=> ($forum_data['forum_status'] == ITEM_LOCKED) ? true : false,
	'S_VIEWFORUM'			=> true,

	'U_MCP'				=> ($auth->acl_get('m_', $forum_id)) ? append_sid("{$phpbb_root_path}mcp.$phpEx", "f=$forum_id&amp;i=main&amp;mode=forum_view", true, $user->session_id) : '',
	'U_POST_NEW_TOPIC'	=> ($auth->acl_get('f_post', $forum_id) || $user->data['user_id'] == ANONYMOUS) ? append_sid("{$phpbb_root_path}posting.$phpEx", 'mode=post&amp;f=' . $forum_id) : '',
	'U_VIEW_FORUM'		=> append_sid("{$phpbb_root_path}viewforum.$phpEx", "f=$forum_id" . ((strlen($u_sort_param)) ? "&amp;$u_sort_param" : '') . (($start == 0) ? '' : "&amp;start=$start")),
	'U_MARK_TOPICS'		=> ($user->data['is_registered'] || $config['load_anon_lastread']) ? append_sid("{$phpbb_root_path}viewforum.$phpEx", 'hash=' . generate_link_hash('global') . "&amp;f=$forum_id&amp;mark=topics") : '',
));

// Grab icons
$icons = $cache->obtain_icons();

// Grab all topic data
$rowset = $announcement_list = $topic_list = $global_announce_list = array();

$sql_array = array(
	'SELECT'	=> 't.*',
	'FROM'		=> array(
		TOPICS_TABLE		=> 't'
	),
	'LEFT_JOIN'	=> array(),
);

$sql_approved = ($auth->acl_get('m_approve', $forum_id)) ? '' : 'AND t.topic_approved = 1';

if ($user->data['is_registered'])
{
	if ($config['load_db_track'])
	{
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(TOPICS_POSTED_TABLE => 'tp'), 'ON' => 'tp.topic_id = t.topic_id AND tp.user_id = ' . $user->data['user_id']);
		$sql_array['SELECT'] .= ', tp.topic_posted';
	}

	if ($config['load_db_lastread'])
	{
		$sql_array['LEFT_JOIN'][] = array('FROM' => array(TOPICS_TRACK_TABLE => 'tt'), 'ON' => 'tt.topic_id = t.topic_id AND tt.user_id = ' . $user->data['user_id']);
		$sql_array['SELECT'] .= ', tt.mark_time';

		if ($s_display_active && sizeof($active_forum_ary))
		{
			$sql_array['LEFT_JOIN'][] = array('FROM' => array(FORUMS_TRACK_TABLE => 'ft'), 'ON' => 'ft.forum_id = t.forum_id AND ft.user_id = ' . $user->data['user_id']);
			$sql_array['SELECT'] .= ', ft.mark_time AS forum_mark_time';
		}
	}
}
$torrents_cleanup=$torrents_hashes=$torrents_remote=array();
if($forum_astracker)
{
	$sql_array['LEFT_JOIN'][] = array('FROM' => array(TRACKER_TORRENTS_TABLE => 'tr'), 'ON' => 't.topic_id = tr.topic_id');
	$sql_array['SELECT'] .= ', tr.id torrent_id,
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
						tr.added,
						tr.req_ratio,
						tr.req_upload,
						tr.info_hash,
						tr.lastcleanup,
						tr.tsl_speed,
						tr.lastseed,
						tr.thanks,
						tr.lastleech
						';
	$u_canviewtopdowntorrents=$auth->acl_get('u_canviewtopdowntorrents') && $config['ppkbb_topdown_torrents'][2] && !$user->data['user_tracker_options'][2] ? true : false;
	if($config['ppkbb_topdown_torrents_exclude'] && $u_canviewtopdowntorrents)
	{
		if($config['ppkbb_topdown_torrents_exclude'] && (($config['ppkbb_topdown_torrents_trueexclude'] && in_array($forum_id, $config['ppkbb_topdown_torrents_exclude']))) || (!$config['ppkbb_topdown_torrents_trueexclude'] && !in_array($forum_id, $config['ppkbb_topdown_torrents_exclude'])))
		{
			$u_canviewtopdowntorrents=false;
		}
	}
	if($u_canviewtopdowntorrents)
	{
		$template->assign_vars(array(
			'TDT_URL'	=> append_sid("{$phpbb_root_path}topdown_torrents.{$phpEx}", "fid={$forum_id}&id=_f", false),
			'TDT_ID'	=> '_f',
			'TOPDOWN_TORRENTS_POSTERS' => true,
			'S_TOPDOWN_TORRENTS_WIDTH' => $config['ppkbb_topdown_torrents'][4],
			'S_TOPDOWN_TORRENTS_WIDTH2' => $config['ppkbb_topdown_torrents'][12]==1 ? $config['ppkbb_topdown_torrents'][5]*2 : false,
			'S_TOPDOWN_TORRENTS_HEIGHT' => $config['ppkbb_topdown_torrents'][5]+10,
			'S_TOPDOWN_TORRENTS_BUTTPOS' => my_int_val($config['ppkbb_topdown_torrents'][5]/2),
			'S_TDT_TYPE' => $config['ppkbb_topdown_torrents'][12],
			'S_TOPDOWN_TORRENTS' => ($config['ppkbb_topdown_torrents'][11] ? sprintf($user->lang['TOPDOWN_TORRENTS_ASNEWTORRENTS_INFORUM'], $forum_data['forum_name']) : sprintf($user->lang['TOPDOWN_TORRENTS_INFORUM'], $forum_data['forum_name'])),
			'S_TOPDOWN_TORRENTS_AUTOSTEP' => $config['ppkbb_topdown_torrents_options'][0] ? 'true' : 'false',
			'S_TOPDOWN_TORRENTS_MOVEBY' => $config['ppkbb_topdown_torrents_options'][1] ? $config['ppkbb_topdown_torrents_options'][1] : 1,
			'S_TOPDOWN_TORRENTS_PAUSE' => $config['ppkbb_topdown_torrents_options'][2] ? $config['ppkbb_topdown_torrents_options'][2]*1000 : 1000,
			'S_TOPDOWN_TORRENTS_SPEED' => $config['ppkbb_topdown_torrents_options'][3] ? $config['ppkbb_topdown_torrents_options'][3]*1000 : 3000,
			'S_TOPDOWN_TORRENTS_WRAPAROUND' => $config['ppkbb_topdown_torrents_options'][4] ? 'true' : 'false',
			'S_TOPDOWN_TORRENTS_WRAPBEHAVIOR' => in_array($config['ppkbb_topdown_torrents_options'][5], array('pushpull', 'slide')) ? $config['ppkbb_topdown_torrents_options'][5] : 'slide',
			'S_TOPDOWN_TORRENTS_PERSIST' => $config['ppkbb_topdown_torrents_options'][6] ? 'true' : 'false',
			'S_TOPDOWN_TORRENTS_DEFAULTBUTTONS' => $config['ppkbb_topdown_torrents_options'][7] ? 'true' : 'false',
			'S_TOPDOWN_TORRENTS_MOVEBY2' => $config['ppkbb_topdown_torrents_options'][8] ? $config['ppkbb_topdown_torrents_options'][8] : 1,
			)
		);
	}
}
if ($forum_data['forum_type'] == FORUM_POST)
{

	// Obtain announcements ... removed sort ordering, sort by time in all cases
	$sql = $db->sql_build_query('SELECT', array(
		'SELECT'	=> $sql_array['SELECT'],
		'FROM'		=> $sql_array['FROM'],
		'LEFT_JOIN'	=> $sql_array['LEFT_JOIN'],

		'WHERE'		=> 't.forum_id IN (' . $forum_id . ', 0)
			AND t.topic_type IN (' . POST_ANNOUNCE . ', ' . POST_GLOBAL . ')'.($forum_astracker ? $sql_addon : ''),

		'ORDER_BY'	=> 't.topic_time DESC',
	));
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		if (!$row['topic_approved'] && !$auth->acl_get('m_approve', $row['forum_id']))
		{
			// Do not display announcements that are waiting for approval.
			continue;
		}

		if($forum_astracker && isset($row['torrent_id']) && $row['torrent_id'])
		{
			if($row['topic_type'] == POST_GLOBAL)
			{
				continue;
			}
			else
			{
				$config['ppkbb_tccleanup_interval'] && $config['ppkbb_tcclean_place'][2] && $dt - $row['lastcleanup'] > $config['ppkbb_tccleanup_interval'] ? $torrents_cleanup[$row['torrent_id']]=$row['torrent_id'] : '';
				$config['ppkbb_tcenable_rannounces'][0] && $config['ppkbb_tcenable_rannounces'][1] && $row['forb'] < 1 ? $torrents_hashes[$row['torrent_id']]=$row['info_hash'] : '';
			}
		}


		$rowset[$row['topic_id']] = $row;
		$announcement_list[] = $row['topic_id'];

		if ($row['topic_type'] == POST_GLOBAL)
		{
			$global_announce_list[$row['topic_id']] = true;
		}
		else
		{
			$topics_count--;
		}

	}
	$db->sql_freeresult($result);
}

// If the user is trying to reach late pages, start searching from the end
$store_reverse = false;
$sql_limit = $config['topics_per_page'];
if ($start > $topics_count / 2)
{
	$store_reverse = true;

	if ($start + $config['topics_per_page'] > $topics_count)
	{
		$sql_limit = min($config['topics_per_page'], max(1, $topics_count - $start));
	}

	// Select the sort order
	$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'ASC' : 'DESC');
	$sql_start = max(0, $topics_count - $sql_limit - $start);
}
else
{
	// Select the sort order
	$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');
	$sql_start = $start;
}

if ($forum_data['forum_type'] == FORUM_POST || !sizeof($active_forum_ary))
{
	$sql_where = 't.forum_id = ' . $forum_id;
}
else if (empty($active_forum_ary['exclude_forum_id']))
{
	$sql_where = $db->sql_in_set('t.forum_id', $active_forum_ary['forum_id']);
}
else
{
	$get_forum_ids = array_diff($active_forum_ary['forum_id'], $active_forum_ary['exclude_forum_id']);
	$sql_where = (sizeof($get_forum_ids)) ? $db->sql_in_set('t.forum_id', $get_forum_ids) : 't.forum_id = ' . $forum_id;
}

// Grab just the sorted topic ids
$sql = 'SELECT t.topic_id
	FROM ' . TOPICS_TABLE . " t ".($forum_astracker ? 'LEFT JOIN '. TRACKER_TORRENTS_TABLE .' tr ON (t.topic_id=tr.topic_id)' : '')."
	WHERE $sql_where
		AND t.topic_type IN (" . POST_NORMAL . ', ' . POST_STICKY . ")
		$sql_approved".($forum_astracker ? $sql_addon : '')."
		$sql_limit_time
	ORDER BY t.topic_type " . ((!$store_reverse) ? 'DESC' : 'ASC') . ', ' . $sql_sort_order;
$result = $db->sql_query_limit($sql, $sql_limit, $sql_start);

while ($row = $db->sql_fetchrow($result))
{
	$topic_list[] = (int) $row['topic_id'];
}
$db->sql_freeresult($result);

// For storing shadow topics
$shadow_topic_list = array();

if (sizeof($topic_list))
{
	// SQL array for obtaining topics/stickies
	$sql_array = array(
		'SELECT'		=> $sql_array['SELECT'],
		'FROM'			=> $sql_array['FROM'],
		'LEFT_JOIN'		=> $sql_array['LEFT_JOIN'],

		'WHERE'			=> $db->sql_in_set('t.topic_id', $topic_list).($forum_astracker ? $sql_addon : ''),
	);

	// If store_reverse, then first obtain topics, then stickies, else the other way around...
	// Funnily enough you typically save one query if going from the last page to the middle (store_reverse) because
	// the number of stickies are not known
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		if ($row['topic_status'] == ITEM_MOVED)
		{
			$shadow_topic_list[$row['topic_moved_id']] = $row['topic_id'];
		}
		if($forum_astracker && isset($row['torrent_id']) && $row['torrent_id'])
		{
			$config['ppkbb_tccleanup_interval'] && $config['ppkbb_tcclean_place'][2] && $dt - $row['lastcleanup'] > $config['ppkbb_tccleanup_interval'] ? $torrents_cleanup[$row['torrent_id']]=$row['torrent_id'] : '';
			$config['ppkbb_tcenable_rannounces'][0] && $config['ppkbb_tcenable_rannounces'][1] && $row['forb'] < 1 ? $torrents_hashes[$row['torrent_id']]=$row['info_hash'] : '';
		}

		$rowset[$row['topic_id']] = $row;
	}
	$db->sql_freeresult($result);
}

// If we have some shadow topics, update the rowset to reflect their topic information
if (sizeof($shadow_topic_list))
{
	$sql = 'SELECT t.*'.($forum_astracker ? ', tr.id torrent_id,
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
			tr.added,
			tr.req_ratio,
			tr.req_upload,
			tr.info_hash,
			tr.lastcleanup,
			tr.tsl_speed,
			tr.lastseed,
			tr.thanks,
			tr.lastleech
			' : '').'
		FROM ' . TOPICS_TABLE .' t ' . ($forum_astracker ? 'LEFT JOIN '. TRACKER_TORRENTS_TABLE .' tr ON (t.topic_id=tr.topic_id)' : '').'
		WHERE ' . $db->sql_in_set('t.topic_id', array_keys($shadow_topic_list));
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$orig_topic_id = $shadow_topic_list[$row['topic_id']];

		// If the shadow topic is already listed within the rowset (happens for active topics for example), then do not include it...
		if (isset($rowset[$row['topic_id']]))
		{
			// We need to remove any trace regarding this topic. :)
			unset($rowset[$orig_topic_id]);
			unset($topic_list[array_search($orig_topic_id, $topic_list)]);
			$topics_count--;

			continue;
		}

		// Do not include those topics the user has no permission to access
		if (!$auth->acl_get('f_read', $row['forum_id']))
		{
			// We need to remove any trace regarding this topic. :)
			unset($rowset[$orig_topic_id]);
			unset($topic_list[array_search($orig_topic_id, $topic_list)]);
			$topics_count--;

			continue;
		}
		if($forum_astracker && isset($row['torrent_id']) && $row['torrent_id'])
		{
			$config['ppkbb_tccleanup_interval'] && $config['ppkbb_tcclean_place'][2] && $dt - $row['lastcleanup'] > $config['ppkbb_tccleanup_interval'] ? $torrents_cleanup[$row['torrent_id']]=$row['torrent_id'] : '';
			$config['ppkbb_tcenable_rannounces'][0] && $config['ppkbb_tcenable_rannounces'][1] && $row['forb'] < 1 ? $torrents_hashes[$row['torrent_id']]=$row['info_hash'] : '';
		}

		// We want to retain some values
		$row = array_merge($row, array(
			'topic_moved_id'	=> $rowset[$orig_topic_id]['topic_moved_id'],
			'topic_status'		=> $rowset[$orig_topic_id]['topic_status'],
			'topic_type'		=> $rowset[$orig_topic_id]['topic_type'],
			'topic_title'		=> $rowset[$orig_topic_id]['topic_title'],
		));

		// Shadow topics are never reported
		$row['topic_reported'] = 0;

		$rowset[$orig_topic_id] = $row;
	}
	$db->sql_freeresult($result);
}
unset($shadow_topic_list);

// Ok, adjust topics count for active topics list
if ($s_display_active)
{
	$topics_count = 1;
}

// We need to readd the local announcements to the forums total topic count, otherwise the number is different from the one on the forum list
$total_topic_count = $topics_count + sizeof($announcement_list) - sizeof($global_announce_list);


$template->assign_vars(array(
	'PAGINATION'	=> generate_pagination(append_sid("{$phpbb_root_path}viewforum.$phpEx", "f=$forum_id" . ((strlen($u_sort_param)) ? "&amp;$u_sort_param".($forum_astracker && $t_pagination ? '&amp;'.$t_pagination : '') : '')), $topics_count, $config['topics_per_page'], $start),
	'PAGE_NUMBER'	=> on_page($topics_count, $config['topics_per_page'], $start),
	'TOTAL_TOPICS'	=> ($s_display_active) ? false : (($total_topic_count == 1) ? $user->lang['VIEW_FORUM_TOPIC'] : sprintf($user->lang['VIEW_FORUM_TOPICS'], $total_topic_count)))
);

$topic_list = ($store_reverse) ? array_merge($announcement_list, array_reverse($topic_list)) : array_merge($announcement_list, $topic_list);
$topic_tracking_info = $tracking_topics = array();

// Okay, lets dump out the page ...
if (sizeof($topic_list))
{
	$mark_forum_read = true;
	$mark_time_forum = 0;

	// Active topics?
	if ($s_display_active && sizeof($active_forum_ary))
	{
		// Generate topic forum list...
		$topic_forum_list = array();
		foreach ($rowset as $t_id => $row)
		{
			$topic_forum_list[$row['forum_id']]['forum_mark_time'] = ($config['load_db_lastread'] && $user->data['is_registered'] && isset($row['forum_mark_time'])) ? $row['forum_mark_time'] : 0;
			$topic_forum_list[$row['forum_id']]['topics'][] = $t_id;
		}

		if ($config['load_db_lastread'] && $user->data['is_registered'])
		{
			foreach ($topic_forum_list as $f_id => $topic_row)
			{
				$topic_tracking_info += get_topic_tracking($f_id, $topic_row['topics'], $rowset, array($f_id => $topic_row['forum_mark_time']), false);
			}
		}
		else if ($config['load_anon_lastread'] || $user->data['is_registered'])
		{
			foreach ($topic_forum_list as $f_id => $topic_row)
			{
				$topic_tracking_info += get_complete_topic_tracking($f_id, $topic_row['topics'], false);
			}
		}

		unset($topic_forum_list);
	}
	else
	{
		if ($config['load_db_lastread'] && $user->data['is_registered'])
		{
			$topic_tracking_info = get_topic_tracking($forum_id, $topic_list, $rowset, array($forum_id => $forum_data['mark_time']), $global_announce_list);
			$mark_time_forum = (!empty($forum_data['mark_time'])) ? $forum_data['mark_time'] : $user->data['user_lastmark'];
		}
		else if ($config['load_anon_lastread'] || $user->data['is_registered'])
		{
			$topic_tracking_info = get_complete_topic_tracking($forum_id, $topic_list, $global_announce_list);

			if (!$user->data['is_registered'])
			{
				$user->data['user_lastmark'] = (isset($tracking_topics['l'])) ? (int) (base_convert($tracking_topics['l'], 36, 10) + $config['board_startdate']) : 0;
			}
			$mark_time_forum = (isset($tracking_topics['f'][$forum_id])) ? (int) (base_convert($tracking_topics['f'][$forum_id], 36, 10) + $config['board_startdate']) : $user->data['user_lastmark'];
		}
	}

	$s_type_switch = 0;
	$is_candownload = ($auth->acl_get('u_download') && $auth->acl_get('f_download', $forum_id)) ? 1 : 0;
	if($forum_astracker)
	{
		if(sizeof($torrents_hashes))
		{
			$r_torr=array('all'=>array(), 'torr'=>array());

			$r_exs=array();
			$torrents_id=array_keys($torrents_hashes);
			$sql='SELECT id tracker, torrent FROM '.TRACKER_RTRACK_TABLE." rt WHERE rt.rtrack_enabled='1' AND ((rt.zone_id='0' AND rt.rtrack_remote='1' AND rt.torrent='0') OR rt.torrent IN('".(implode("', '", $torrents_id))."'))";
			$result=$db->sql_query($sql);
			$ra=array();
			while($row_remote=$db->sql_fetchrow($result))
			{
				$ra[$row_remote['tracker']]=$row_remote['torrent'];
				if(!$row_remote['torrent'])
				{
					$r_torr['all'][$row_remote['tracker']]=$torrents_hashes;
				}
				else
				{
					isset($torrents_hashes[$row_remote['torrent']]) ? $r_torr['torr'][$row_remote['tracker']][$row_remote['torrent']]=$torrents_hashes[$row_remote['torrent']] : '';
				}
			}
			$db->sql_freeresult($result);

			$sql='SELECT tracker, torrent, next_announce FROM '.TRACKER_RANNOUNCES_TABLE." WHERE torrent IN('".(implode("', '", $torrents_id))."')";
			$result=$db->sql_query($sql);
			while($row_remote=$db->sql_fetchrow($result))
			{
				if(isset($ra[$row_remote['tracker']]))
				{
					$r_exs[$ra[$row_remote['tracker']].'_'.$row_remote['torrent']][$row_remote['tracker']]=$row_remote;
				}
			}
			$db->sql_freeresult($result);

			if(isset($r_torr['all']))
			{
				foreach($r_torr['all'] as $tr_id => $a_data)
				{
					foreach($a_data as $t_id => $t_hash)
					{
						if(isset($r_exs['0_'.$t_id][$tr_id]))
						{
							if($dt > $r_exs['0_'.$t_id][$tr_id]['next_announce'])
							{
								$torrents_remote[$t_id]=$torrents_hashes[$t_id];
							}
						}
						else
						{
							$torrents_remote[$t_id]=$torrents_hashes[$t_id];
						}
					}
				}
				unset($r_torr['all']);
			}
			if(isset($r_torr['torr']))
			{
				foreach($r_torr['torr'] as $tr_id => $a_data)
				{
					foreach($a_data as $t_id => $t_hash)
					{
						if(isset($r_exs[$t_id.'_'.$t_id][$tr_id]))
						{
							if($dt > $r_exs[$t_id.'_'.$t_id][$tr_id]['next_announce'])
							{
								$torrents_remote[$t_id]=$torrents_hashes[$t_id];
							}
						}
						else
						{
							$torrents_remote[$t_id]=$torrents_hashes[$t_id];
						}
					}
				}
				unset($r_torr['torr']);
			}
			unset($torrents_hashes);
		}
		$torrent_statuses=get_torrent_statuses();
		$user_ratio=get_ratio($user->data['user_uploaded'], $user->data['user_downloaded'], $config['ppkbb_tcratio_start'], $user->data['user_bonus']);
		$ppkbb_tcbonus_upvalue_reset=0;
	}

	foreach ($topic_list as $topic_id)
	{
		$row = &$rowset[$topic_id];
		$topic_forum_id = ($row['forum_id']) ? (int) $row['forum_id'] : $forum_id;

		// This will allow the style designer to output a different header
		// or even separate the list of announcements from sticky and normal topics
		if ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL)
		{
		    $s_type_switch_test = 1;
		}
		elseif ( $row['topic_type'] == POST_STICKY)
		{
		    $s_type_switch_test = 2;
		}
		else
		{
		    $s_type_switch_test = 0;
		}

		// Replies
		$replies = ($auth->acl_get('m_approve', $topic_forum_id)) ? $row['topic_replies_real'] : $row['topic_replies'];

		if ($row['topic_status'] == ITEM_MOVED)
		{
			$topic_id = $row['topic_moved_id'];
			$unread_topic = false;
		}
		else
		{
			$unread_topic = (isset($topic_tracking_info[$topic_id]) && $row['topic_last_post_time'] > $topic_tracking_info[$topic_id]) ? true : false;
		}

		// Get folder img, topic status/type related information
		$folder_img = $folder_alt = $topic_type = '';
		topic_status($row, $replies, $unread_topic, $folder_img, $folder_alt, $topic_type);

		// Generate all the URIs ...
		$view_topic_url_params = 'f=' . $topic_forum_id . '&amp;t=' . $topic_id;
		$view_topic_url = append_sid("{$phpbb_root_path}viewtopic.$phpEx", $view_topic_url_params);

		$topic_unapproved = (!$row['topic_approved'] && $auth->acl_get('m_approve', $topic_forum_id)) ? true : false;
		$posts_unapproved = ($row['topic_approved'] && $row['topic_replies'] < $row['topic_replies_real'] && $auth->acl_get('m_approve', $topic_forum_id)) ? true : false;
		$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=queue&amp;mode=' . (($topic_unapproved) ? 'approve_details' : 'unapproved_posts') . "&amp;t=$topic_id", true, $user->session_id) : '';

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
					$config['ppkbb_tcguests_enabled'][0] && $config['ppkbb_tctrestricts_options'][2] && $row['size'] < $config['ppkbb_tctrestricts_options'][2] ? $is_candowntorr=1 : '';
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

		$row['topic_replies']+=1;
		$viewtopic_page=$config['posts_per_page']*max(ceil($row['topic_replies'] / $config['posts_per_page']), 1)-$config['posts_per_page'];
		// Send vars to template
		$topicrow=array(
			'TOPIC_FORUM_IMG'		=> $forum_data['forum_image'] ? $phpbb_root_path . $forum_data['forum_image'] : '',
			'FORUM_ID'					=> $topic_forum_id,
			'TOPIC_ID'					=> $topic_id,
			'TOPIC_AUTHOR'				=> get_username_string('username', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
			'TOPIC_AUTHOR_COLOUR'		=> get_username_string('colour', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
			'TOPIC_AUTHOR_FULL'			=> get_username_string('full', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
			'FIRST_POST_TIME'			=> $user->format_date($row['topic_time']),
			'LAST_POST_SUBJECT'			=> censor_text($row['topic_last_post_subject']),
			'LAST_POST_TIME'			=> $user->format_date($row['topic_last_post_time']),
			'LAST_VIEW_TIME'			=> $user->format_date($row['topic_last_view_time']),
			'LAST_POST_AUTHOR'			=> get_username_string('username', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
			'LAST_POST_AUTHOR_COLOUR'	=> get_username_string('colour', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
			'LAST_POST_AUTHOR_FULL'		=> get_username_string('full', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),

			'PAGINATION'		=> topic_generate_pagination($replies, $view_topic_url),
			'REPLIES'			=> $replies,
			'VIEWS'				=> $row['topic_views'],
			'TOPIC_TITLE'		=> censor_text($row['topic_title']),
			'TOPIC_TYPE'		=> $topic_type,

			'TOPIC_FOLDER_IMG'		=> $user->img($folder_img, $folder_alt),
			'TOPIC_FOLDER_IMG_SRC'	=> $user->img($folder_img, $folder_alt, false, '', 'src'),
			'TOPIC_FOLDER_IMG_ALT'	=> $user->lang[$folder_alt],
 			'TOPIC_FOLDER_IMG_WIDTH'=> $user->img($folder_img, '', false, '', 'width'),
 			'TOPIC_FOLDER_IMG_HEIGHT'	=> $user->img($folder_img, '', false, '', 'height'),

			'TOPIC_ICON_IMG'		=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['img'] : '',
			'TOPIC_ICON_IMG_WIDTH'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['width'] : '',
			'TOPIC_ICON_IMG_HEIGHT'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['height'] : '',
			'ATTACH_ICON_IMG'		=> ($is_candownload && $row['topic_attachment']) ? $user->img('icon_topic_attach', $user->lang['TOTAL_ATTACHMENTS']) : '',
			'UNAPPROVED_IMG'		=> ($topic_unapproved || $posts_unapproved) ? $user->img('icon_topic_unapproved', ($topic_unapproved) ? 'TOPIC_UNAPPROVED' : 'POSTS_UNAPPROVED') : '',

			'S_TOPIC_TYPE'			=> $row['topic_type'],
			'S_USER_POSTED'			=> (isset($row['topic_posted']) && $row['topic_posted']) ? true : false,
			'S_UNREAD_TOPIC'		=> $unread_topic,
			'S_TOPIC_REPORTED'		=> (!empty($row['topic_reported']) && $auth->acl_get('m_report', $topic_forum_id)) ? true : false,
			'S_TOPIC_UNAPPROVED'	=> $topic_unapproved,
			'S_POSTS_UNAPPROVED'	=> $posts_unapproved,
			'S_HAS_POLL'			=> ($row['poll_start']) ? true : false,
			'S_POST_ANNOUNCE'		=> ($row['topic_type'] == POST_ANNOUNCE) ? true : false,
			'S_POST_GLOBAL'			=> ($row['topic_type'] == POST_GLOBAL) ? true : false,
			'S_POST_STICKY'			=> ($row['topic_type'] == POST_STICKY) ? true : false,
			'S_TOPIC_LOCKED'		=> ($row['topic_status'] == ITEM_LOCKED) ? true : false,
			'S_TOPIC_MOVED'			=> ($row['topic_status'] == ITEM_MOVED) ? true : false,

			'U_NEWEST_POST'			=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", $view_topic_url_params . '&amp;view=unread') . '#unread',
			'U_LAST_POST'			=> append_sid("{$phpbb_root_path}viewtopic.$phpEx", $view_topic_url_params.($viewtopic_page ? '&amp;start='.$viewtopic_page : '')) . '#p' . $row['topic_last_post_id'],
			'U_LAST_POST_AUTHOR'	=> get_username_string('profile', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
			'U_TOPIC_AUTHOR'		=> get_username_string('profile', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
			'U_VIEW_TOPIC'			=> $view_topic_url,
			'U_MCP_REPORT'			=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=reports&amp;mode=reports&amp;f=' . $topic_forum_id . '&amp;t=' . $topic_id, true, $user->session_id),
			'U_MCP_QUEUE'			=> $u_mcp_queue,

			'S_TOPIC_TYPE_SWITCH'	=> ($s_type_switch == $s_type_switch_test) ? -1 : 0,

		);
		if($forum_astracker && !$no_torrent)
		{
			$topicrow=array_merge($topicrow, array(
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
		$template->assign_block_vars('topicrow', $topicrow);
		if ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL)
		{
		    $s_type_switch = 1;
		}
		elseif ( $row['topic_type'] == POST_STICKY)
		{
		    $s_type_switch = 2;
		}
		else
		{
		    $s_type_switch = 0;
		}

		if ($unread_topic)
		{
			$mark_forum_read = false;
		}

		unset($rowset[$topic_id]);
	}
}

// This is rather a fudge but it's the best I can think of without requiring information
// on all topics (as we do in 2.0.x). It looks for unread or new topics, if it doesn't find
// any it updates the forum last read cookie. This requires that the user visit the forum
// after reading a topic
if ($forum_data['forum_type'] == FORUM_POST && sizeof($topic_list) && $mark_forum_read)
{
	update_forum_tracking_info($forum_id, $forum_data['forum_last_post_time'], false, $mark_time_forum);
}

if($forum_astracker && !defined('CGP_KEY'))
{
	include_once($phpbb_root_path.'tracker/include/viewforum_add_cron.'.$phpEx);
}
page_footer();

?>
