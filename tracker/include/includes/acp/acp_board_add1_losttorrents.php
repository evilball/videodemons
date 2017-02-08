<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_losttorrents.php 1.000 2015-10-01 10:36:00 PPK $
* @copyright (c) 2015 PPK
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

$trstat_title='ACP_TRACKER_LOSTTORRENTS';

$user->add_lang('mods/acp/ppkbb3cker_losttorrents');

$user->add_lang('mcp');

$submit = (isset($_POST['submit'])) ? true : false;
$form_key = 'acp_losttorrents';
add_form_key($form_key);

$error = array();

$user->add_lang('posting');

// Set up general vars
$action		= request_var('action', '');
$forum_id	= request_var('f', 0);
$topic_id	= request_var('t', 0);
$start		= request_var('start', 0);

$deletemarked = request_var('deletemarked', '');
$deleteall	= request_var('deleteall', '');
$marked		= request_var('mark', array(0));
$last_id=request_var('last_id', 0);

// Sort keys
$sort_days	= request_var('st', 0);
$sort_key	= request_var('sk', 't');
$sort_dir	= request_var('sd', 'd');

$forum_trackers=array();
$sql='SELECT forum_id FROM '.FORUMS_TABLE." WHERE forumas='1'";
$result=$db->sql_query($sql);
while($row=$db->sql_fetchrow($result))
{
	$forum_trackers[]=$row['forum_id'];
}
$db->sql_freeresult($result);

// Fix entries if requested and able
if (($deletemarked || $deleteall) && $auth->acl_get('a_clearlogs'))
{
	if (confirm_box(true) || ($deleteall && $last_id))
	{
		$torrents_id = array();
		if (($deletemarked) && sizeof($marked))
		{

			foreach ($marked as $mark)
			{
				$torrents_id[] = intval($mark);
			}
		}

		if($deleteall)
		{

			$sql='SELECT a.attach_id FROM '.POSTS_TABLE.' p, '.TOPICS_TABLE .' t, '.USERS_TABLE.' u, '.ATTACHMENTS_TABLE.' a LEFT JOIN '.TRACKER_TORRENTS_TABLE.' x ON(a.attach_id=x.id) WHERE '.$db->sql_in_set('p.forum_id', $forum_trackers)." AND p.post_id=t.topic_first_post_id  AND p.post_id=a.post_msg_id AND a.extension='torrent' AND a.in_message='0' AND p.poster_id=u.user_id AND x.id IS NULL AND a.attach_id > $last_id ORDER BY a.attach_id LIMIT $start, {$config['topics_per_page']}";
			$result=$db->sql_query($sql);
			while($row=$db->sql_fetchrow($result))
			{
				$torrents_id[]=$row['attach_id'];
			}
			$db->sql_freeresult($result);
		}

		if($deletemarked || $deleteall)
		{
			if (!function_exists('delete_attachments'))
			{
				include_once($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
			}

			if ($torrents_id || $deleteall)
			{
				if(!$torrents_id)
				{
					trigger_error(sprintf($user->lang['FIX_LOSTTORRENTS_FINISH'], $this->u_action));
				}

				delete_attachments('attach', $torrents_id);

				add_log('admin', 'LOG_DELETE_LOSTTORRENTS');

				if($deletemarked)
				{
					trigger_error(sprintf($user->lang['FIX_LOSTTORRENTS_FINISH'], $this->u_action));
				}

				if($deleteall && $torrents_id)
				{
					$redirect_url=$this->u_action.'&amp;deleteall=1&amp;last_id=1';

					meta_refresh(3, $redirect_url);
				}

				trigger_error(($deleteall ? $user->lang['FIX_LOSTTORRENTS_WAIT'] : '').sprintf($user->lang['FIX_LOSTTORRENTS_RESULT'], '', $this->u_action));


			}
		}
		else
		{

			if ($torrents_id)
			{
				if(!$torrents_id)
				{
					trigger_error(sprintf($user->lang['FIX_LOSTTORRENTS_FINISH'], $this->u_action));
				}
				include_once("{$phpbb_root_path}tracker/include/message_parser_add1.{$phpEx}");

				$tprivate_flags=array();
				$result=$db->sql_query("SELECT forum_id, tprivate_flag FROM ".FORUMS_TABLE."");
				while($row=$db->sql_fetchrow($result))
				{
					$tprivate_flags[$row['forum_id']]=$row['tprivate_flag'];
				}
				$db->sql_freeresult($result);

				$sql = 'SELECT a.physical_filename, a.real_filename, a.attach_id, a.topic_id, a.post_msg_id post_id, p.forum_id, p.poster_id FROM ' . ATTACHMENTS_TABLE . ' a, '.POSTS_TABLE.' p WHERE a.post_msg_id=p.post_id AND '.$db->sql_in_set('a.attach_id', $torrents_id);
				$result=$db->sql_query($sql);
				$message='';
				while($row=$db->sql_fetchrow($result))
				{

					$last_id=$row['attach_id'];
				}

				$db->sql_freeresult($result);

				add_log('admin', 'LOG_FIX_LOSTTORRENTS');


				trigger_error(sprintf($user->lang['FIX_LOSTTORRENTS_RESULT'], $message, $this->u_action));
			}
		}
	}
	else
	{
		confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
			'f'			=> $forum_id,
			'start'		=> $start,

					'deletemarked'	=> $deletemarked,
					'deleteall'	=> $deleteall,
			'mark'		=> $marked,
			'st'		=> $sort_days,
			'sk'		=> $sort_key,
			'sd'		=> $sort_dir,
			'i'			=> $id,
			'mode'		=> $mode,
			'action'	=> $action))
		);
	}
}

// Sorting
$limit_days = array(
	0 => $user->lang['ALL_ENTRIES'],
	1 => $user->lang['1_DAY'],
	7 => $user->lang['7_DAYS'],
	14 => $user->lang['2_WEEKS'],
	30 => $user->lang['1_MONTH'],
	90 => $user->lang['3_MONTHS'],
	180 => $user->lang['6_MONTHS'],
	365 => $user->lang['1_YEAR'],
);
$sort_by_text = array(
	'u' => $user->lang['SORT_USERNAME'],
	't' => $user->lang['SORT_DATE'],
	'f' => $user->lang['SORT_FILENAME'],
	'p' => $user->lang['SORT_TTITLE'],
);
$sort_by_sql = array(
	'u' => 'u.username_clean',
	't' => 'a.filetime',
	'f' => 'a.real_filename',
	'p'=>'p.post_subject',
);

$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

// Define where and sort sql for use in displaying logs
$sql_where = ($sort_days) ? (time() - ($sort_days * 86400)) : 0;
$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

// Grab log data
$log_data = array();
$log_count = 0;
if($forum_trackers)
{
	$sql='SELECT COUNT(*) log_count FROM '.POSTS_TABLE.' p, '.TOPICS_TABLE .' t, '.USERS_TABLE.' u, '.ATTACHMENTS_TABLE.' a LEFT JOIN '.TRACKER_TORRENTS_TABLE.' x ON(a.attach_id=x.id) WHERE '.$db->sql_in_set('p.forum_id', $forum_trackers)." AND p.post_id=t.topic_first_post_id AND p.post_id=a.post_msg_id AND a.extension='torrent' AND a.in_message='0' AND p.poster_id=u.user_id AND x.id IS NULL".($sql_where ? ' AND a.filetime > '.$sql_where : '')."";
	$result=$db->sql_query($sql);
	$log_count=$db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$log_count=intval($log_count['log_count']);
	if($log_count)
	{
		$sql='SELECT u.username, u.user_colour, a.real_filename, a.filetime, a.post_msg_id, a.attach_id, p.post_subject, p.topic_id, p.forum_id, p.poster_id FROM '.POSTS_TABLE.' p, '.TOPICS_TABLE .' t, '.USERS_TABLE.' u, '.ATTACHMENTS_TABLE.' a LEFT JOIN '.TRACKER_TORRENTS_TABLE.' x ON(a.attach_id=x.id) WHERE '.$db->sql_in_set('p.forum_id', $forum_trackers)." AND p.post_id=t.topic_first_post_id AND p.post_id=a.post_msg_id AND a.extension='torrent' AND a.in_message='0' AND p.poster_id=u.user_id AND x.id IS NULL".($sql_where ? ' AND a.filetime > '.$sql_where : '')." ORDER BY $sql_sort LIMIT $start, {$config['topics_per_page']}";
		$result=$db->sql_query($sql);
		while($row=$db->sql_fetchrow($result))
		{
			$log_data[]=$row;
		}
		$db->sql_freeresult($result);
	}
}

$template->assign_vars(array(

	'S_ON_PAGE'		=> on_page($log_count, $config['topics_per_page'], $start),
	'PAGINATION'	=> generate_pagination($this->u_action . "&amp;$u_sort_param", $log_count, $config['topics_per_page'], $start, true),

	'TOTAL_LOGS'	=> $log_count ? sprintf($user->lang['TOTAL_LOGS'], $log_count) : false,

	'S_LIMIT_DAYS'	=> $s_limit_days,
	'S_SORT_KEY'	=> $s_sort_key,
	'S_SORT_DIR'	=> $s_sort_dir,
	'S_CLEARLOGS'	=> $auth->acl_get('a_clearlogs'),
	)
);


foreach ($log_data as $row)
{
	$template->assign_block_vars('log', array(
		'USERNAME'			=> empty($row['username']) || $row['poster_id']==1 ? $user->lang['TRACKER_ANONYMOUS'] : get_username_string('full', $row['poster_id'], $row['username'], $row['user_colour'], $row['username']),

		'DATE'				=> $user->format_date($row['filetime'], 'Y-m-d H:i:s'),
		'FILENAME'			=> urldecode($row['real_filename']),
		'TTITLE'			=> !empty($row['post_subject']) ? $row['post_subject'] : $user->lang['TORRENT_DELETED'],
		'URL'			=> !empty($row['post_subject']) ? append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f={$row['forum_id']}&amp;t={$row['topic_id']}&amp;p={$row['post_msg_id']}")."#p{$row['post_msg_id']}" : '',
		'FILEURL'			=> !empty($row['real_filename']) ? append_sid("{$phpbb_root_path}download/file.$phpEx", "id={$row['attach_id']}") : '',

		'ID'				=> $row['attach_id'],
		)
	);
}


$template->assign_vars(array(
	'S_LOSTTORRENTS_INC'	=> true,
	'S_TRACKER_NOBUTT' => true,

	'L_TITLE'			=> $user->lang['ACP_TRACKER_LOSTTORRENTS'],
	'L_EXPLAIN'	=> $user->lang['ACP_TRACKER_LOSTTORRENTS_EXPLAIN'],

	'S_ERROR'			=> (sizeof($error)) ? true : false,
	'ERROR_MSG'			=> implode('<br />', $error),

	'U_ACTION'       => $this->u_action,

));


$display_vars = array(
	'title'	=> $trstat_title,
	'vars'	=> array(
		'legend1'				=> 'ACP_TRACKER_LOSTTORRENTS_SETTINGS',
	)
);


?>
