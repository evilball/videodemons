<?php
/**
*
* @package phpBB3
* @version $Id: viewchat.php 1.000 2008-08-02 21:28:00 PPK $
* @copyright (c) 2008 PPK
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
define('IN_CHAT', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session
$user->session_begin();
$auth->acl($user->data);

// Start initial var setup
$forum_id	= request_var('f', 0);
$chat=request_var('chat', '');
$chat_error=request_var('r', 0);
$dt=time();

// Check if the user has actually sent a forum ID with his/her request
// If not give them a nice error page.
if (!$forum_id)
{
	trigger_error('NO_FORUM');
}

//$db->sql_query("SET sql_mode = ''");

$sql_from = FORUMS_TABLE . ' f';

$sql = "SELECT f.*
	FROM {$sql_from}
	WHERE f.forum_id = {$forum_id}";
$result = $db->sql_query($sql);
$forum_data = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if (!$forum_data)
{
	trigger_error('NO_FORUM');
}

($forum_data['forum_type'] == FORUM_POST && $forum_data['forumas']==2) ? $forum_aschat=1 : $forum_aschat=0;

if(!$forum_aschat)
{
	header("Location: ".append_sid("{$phpbb_root_path}index.{$phpEx}"));
	exit();
}

// Configure style, language, etc.
$user->setup('viewforum', $forum_data['forum_style']);

// Redirect to login upon emailed notification links
if (!$user->data['is_registered'] && !$config['ppkbb_chat_guests'])
{
	login_box('', $user->lang['LOGIN_VIEWFORUM']);
}

if(!$config['ppkbb_chat_enable'])
{
	trigger_error('CHATS_DISABLED');
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

// Dump out the page header and load viewchat template
page_header($user->lang['VIEW_CHAT'] . ' - ' . $forum_data['forum_name'], true, $forum_id);

$template->set_filenames(array(
	'body' => 'viewforum_chat_body.html')
);


make_jumpbox(append_sid("{$phpbb_root_path}viewforum.{$phpEx}"), $forum_id);

$template->assign_vars(array(
	'U_VIEW_FORUM'			=> append_sid("{$phpbb_root_path}viewchat.{$phpEx}", "f={$forum_id}"),
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

$chat_alt=$forum_data['forum_status'] == ITEM_LOCKED ? true : false;

include($phpbb_root_path.'chat/chat.'.$phpEx);

page_footer();

?>
