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
*/

/**
* @ignore
*/



define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

if (!class_exists('CGP') && $config['cgp_enabled'] && !$config['ppkbb_cgp_places'][1])
{
	include($phpbb_root_path . 'includes/cache_guests_pages.' . $phpEx);
}

if (defined('CGP_ENABLED') && !$config['ppkbb_cgp_places'][1])
{
	if (CGP::is_cacheable_user($user))
	{
		define('CGP_KEY', '_index' . CGP::user_type_suffix($user));

		CGP::display_if_cached(CGP_KEY);
	}
}


display_forums('', $config['load_moderators']);

// Set some stats, get posts count from forums data if we... hum... retrieve all forums data
$total_posts	= $config['num_posts'];
$total_topics	= $config['num_topics'];
$total_users	= $config['num_users'];

$l_total_user_s = ($total_users == 0) ? 'TOTAL_USERS_ZERO' : 'TOTAL_USERS_OTHER';
$l_total_post_s = ($total_posts == 0) ? 'TOTAL_POSTS_ZERO' : 'TOTAL_POSTS_OTHER';
$l_total_topic_s = ($total_topics == 0) ? 'TOTAL_TOPICS_ZERO' : 'TOTAL_TOPICS_OTHER';

if(!defined('CGP_KEY'))
{
	include($phpbb_root_path.'tracker/include/index_add_cron.'.$phpEx);
}
if($auth->acl_get('u_canviewtopdowntorrents') && $config['ppkbb_topdown_torrents'][1] && !$user->data['user_tracker_options'][2])
{
	$template->assign_vars(array(
		'TDT_URL'	=> append_sid("{$phpbb_root_path}topdown_torrents.{$phpEx}", 'fid=0&id=_i', false),
		'TDT_ID'	=> '_i',
		'TOPDOWN_TORRENTS_POSTERS' => true,
		'S_TOPDOWN_TORRENTS_WIDTH' => $config['ppkbb_topdown_torrents'][4],
		'S_TOPDOWN_TORRENTS_WIDTH2' => $config['ppkbb_topdown_torrents'][12]==1 ? $config['ppkbb_topdown_torrents'][5]*2 : false,
		'S_TOPDOWN_TORRENTS_HEIGHT' => $config['ppkbb_topdown_torrents'][5]+10,
		'S_TOPDOWN_TORRENTS_BUTTPOS' => my_int_val($config['ppkbb_topdown_torrents'][5]/2),
		'S_TDT_TYPE' => $config['ppkbb_topdown_torrents'][12],
		'S_TOPDOWN_TORRENTS' => ($config['ppkbb_topdown_torrents'][11] ? sprintf($user->lang['TOPDOWN_TORRENTS_ASNEWTORRENTS']) : sprintf($user->lang['TOPDOWN_TORRENTS'])),
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

// Grab group details for legend display
if ($auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
{
	$sql = 'SELECT group_id, group_name, group_colour, group_type
		FROM ' . GROUPS_TABLE . '
		WHERE group_legend = 1
		ORDER BY group_name ASC';
}
else
{
	$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type
		FROM ' . GROUPS_TABLE . ' g
		LEFT JOIN ' . USER_GROUP_TABLE . ' ug
			ON (
				g.group_id = ug.group_id
				AND ug.user_id = ' . $user->data['user_id'] . '
				AND ug.user_pending = 0
			)
		WHERE g.group_legend = 1
			AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . $user->data['user_id'] . ')
		ORDER BY g.group_name ASC';
}
$result = $db->sql_query($sql);

$legend = array();
while ($row = $db->sql_fetchrow($result))
{
	$colour_text = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';
	$group_name = ($row['group_type'] == GROUP_SPECIAL || @$user->lang['G_' . $row['group_name']]) ? $user->lang['G_' . $row['group_name']] : $row['group_name'];
	if ($row['group_name'] == 'BOTS' || ($user->data['user_id'] != ANONYMOUS && !$auth->acl_get('u_viewprofile')))
	{
		$legend[] = '<span' . $colour_text . '>' . $group_name . '</span>';
	}
	else
	{
		$legend[] = '<a' . $colour_text . ' href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=group&amp;g=' . $row['group_id']) . '">' . $group_name . '</a>';
	}
}
$db->sql_freeresult($result);

$legend = implode(', ', $legend);

// Generate birthday list if required ...
$birthday_list = '';
if ($config['load_birthdays'] && $config['allow_birthdays'] && $auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
{
	$now = phpbb_gmgetdate(time() + $user->timezone + $user->dst);

	// Display birthdays of 29th february on 28th february in non-leap-years
	$leap_year_birthdays = '';
	if ($now['mday'] == 28 && $now['mon'] == 2 && !$user->format_date(time(), 'L'))
	{
		$leap_year_birthdays = " OR u.user_birthday LIKE '" . $db->sql_escape(sprintf('%2d-%2d-', 29, 2)) . "%'";
	}
	$sql = 'SELECT u.user_id, u.username, u.user_colour, u.user_birthday
		FROM ' . USERS_TABLE . ' u
		LEFT JOIN ' . BANLIST_TABLE . " b ON (u.user_id = b.ban_userid)
		WHERE (b.ban_id IS NULL
			OR b.ban_exclude = 1)
			AND (u.user_birthday LIKE '" . $db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%' $leap_year_birthdays)
			AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$birthday_list .= (($birthday_list != '') ? ', ' : '') . get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);

		if ($age = (int) substr($row['user_birthday'], -4))
		{
			$birthday_list .= ' (' . max(0, $now['year'] - $age) . ')';
		}
	}
	$db->sql_freeresult($result);
}

!$user->data['is_registered'] && (!$config['ppkbb_chat_guests'] || $config['cgp_enabled']) ? $config['ppkbb_chat_enable']=0 : '';
if($config['ppkbb_chat_enable'] && !$config['ppkbb_chat_display'][1] && $config['ppkbb_index_chat'] && !defined('IN_PORTAL') && !defined('IN_CHAT') && !$user->data['user_tracker_options'][1])
{
	$config['ppkbb_portal_chat']=$config['ppkbb_index_chat'];
	include($phpbb_root_path . 'chat/ppkbb3cker_chat.' . $phpEx);
}

include($phpbb_root_path.'tracker/include/tracker_stat.'.$phpEx);

// Assign index specific vars
$template->assign_vars(array(


	'S_CHAT_INDEX' => defined('SHOW_CHAT') ? true : false,


	'TOTAL_POSTS'	=> sprintf($user->lang[$l_total_post_s], $total_posts),
	'TOTAL_TOPICS'	=> sprintf($user->lang[$l_total_topic_s], $total_topics),
	'TOTAL_USERS'	=> sprintf($user->lang[$l_total_user_s], $total_users),
	'NEWEST_USER'	=> sprintf($user->lang['NEWEST_USER'], get_username_string('full', $config['newest_user_id'], $config['newest_username'], $config['newest_user_colour'])),

	'LEGEND'		=> $legend,
	'BIRTHDAY_LIST'	=> $birthday_list,

	'FORUM_IMG'				=> $user->img('forum_read', 'NO_UNREAD_POSTS'),
	'FORUM_UNREAD_IMG'			=> $user->img('forum_unread', 'UNREAD_POSTS'),
	'FORUM_LOCKED_IMG'		=> $user->img('forum_read_locked', 'NO_UNREAD_POSTS_LOCKED'),
	'FORUM_UNREAD_LOCKED_IMG'	=> $user->img('forum_unread_locked', 'UNREAD_POSTS_LOCKED'),

	'S_LOGIN_ACTION'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login'),
	'S_DISPLAY_BIRTHDAY_LIST'	=> ($config['load_birthdays']) ? true : false,

	'U_MARK_FORUMS'		=> ($user->data['is_registered'] || $config['load_anon_lastread']) ? append_sid("{$phpbb_root_path}index.$phpEx", 'hash=' . generate_link_hash('global') . '&amp;mark=forums') : '',
	'U_MCP'				=> ($auth->acl_get('m_') || $auth->acl_getf_global('m_')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=main&amp;mode=front', true, $user->session_id) : '')
);

// Output page
page_header($user->lang['INDEX']);

$template->set_filenames(array(
	'body' => 'index_body.html')
);

page_footer();

?>
