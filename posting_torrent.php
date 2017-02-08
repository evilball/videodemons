<?php
/**
*
* @package ppkBB3cker
* @version $Id: posting_torrent.php 1.000 2014-06-03 13:04:25 PPK $
* @copyright (c) 2014 PPK
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

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('posting');

if(!$config['ppkbb_addfields_type'][2] || !$user->data['is_registered'])
{
	trigger_error('UPLOAD_TORRENT_DISABLED');
}

$allowed_forums=array_unique(array_keys($auth->acl_getf('f_post', true)));

if(!sizeof($allowed_forums))
{
	trigger_error('UPLOAD_TORRENT_NOAUTH');
}

$sql = 'SELECT f.forum_id, f.forum_name, f.parent_id, f.forum_type, f.left_id, f.right_id, f.forum_password, f.forumas, fa.user_id, f.forum_addfields
	FROM ' . FORUMS_TABLE . ' f
	LEFT JOIN ' . FORUMS_ACCESS_TABLE . " fa ON (fa.forum_id = f.forum_id
		AND fa.session_id = '" . $db->sql_escape($user->session_id) . "')
	ORDER BY f.left_id ASC";
$result = $db->sql_query($sql);

$right = $cat_right = 0;
$s_forums = $padding = $holding = '';
$pad_store = array('0' => '');

while ($row = $db->sql_fetchrow($result))
{
	if ($row['forum_type'] == FORUM_CAT && ($row['left_id'] + 1 == $row['right_id']))
	{
		// Non-postable forum with no subforums, don't display
		continue;
	}

	if ($row['forum_type'] == FORUM_LINK || ($row['forum_password'] && !$row['user_id']))
	{
		// if this forum is a link or password protected (user has not entered the password yet) then skip to the next branch
		continue;
	}

	if ($row['left_id'] < $right)
	{
		$padding .= '&nbsp; &nbsp;';
		$pad_store[$row['parent_id']] = $padding;
	}
	else if ($row['left_id'] > $right + 1)
	{
		if (isset($pad_store[$row['parent_id']]))
		{
			$padding = $pad_store[$row['parent_id']];
		}
		else
		{
			continue;
		}
	}

	$right = $row['right_id'];

	if (!in_array($row['forum_id'], $allowed_forums))
	{
		continue;
	}
	$disabled='';
	if ($row['forumas']!=1 || $row['forum_type'] != FORUM_POST || ($config['ppkbb_addfields_type'][2]==2 && !$row['forum_addfields']))
	{
		$disabled=' disabled="disabled" class="disabled-option"';
	}

	if ($row['left_id'] > $cat_right)
	{
		// make sure we don't forget anything
		$s_forums .= $holding;
		$holding = '';
	}

	if ($row['right_id'] - $row['left_id'] > 1)
	{
		$cat_right = max($cat_right, $row['right_id']);

		$holding .= '<option'.$disabled.' value="' . $row['forum_id'] . '">' . $padding . $row['forum_name'] . '</option>';
	}
	else
	{
		$s_forums .= $holding . '<option'.$disabled.' value="' . $row['forum_id'] . '">' . $padding . $row['forum_name'] . '</option>';
		$holding = '';
	}
}
$db->sql_freeresult($result);

$template->assign_vars(array(
	'S_UPLOAD_TORRENT' => $s_forums ? $s_forums : false,
	'S_POST_ACTION'			=> append_sid("{$phpbb_root_path}posting.{$phpEx}"),

	)
);

page_header((isset($user->lang[$config['ppkbb_addfields_type'][2]]) ? $user->lang[$config['ppkbb_addfields_type'][2]] : $user->lang['UPLOAD_TORRENT']), false);

$template->set_filenames(array(
	'body' => 'posting_torrent_body.html')
);

page_footer();

?>
