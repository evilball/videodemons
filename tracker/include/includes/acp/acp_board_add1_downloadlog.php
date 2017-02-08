<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_downloadlog.php 1.000 2015-09-28 10:24:13 PPK $
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

$trstat_title='ACP_TRACKER_DOWNLOADLOG';

$user->add_lang('mods/acp/ppkbb3cker_downloadlog');

$user->add_lang('mcp');

// $submit = (isset($_POST['submit'])) ? true : false;
// $form_key = 'acp_downloadlog';
// add_form_key($form_key);

$error = array();
// $config['topics_per_page']=2;

//$alogs_title='ACP_TRACKER_DOWNLOADLOG';
//$user->add_lang('mods/acp/ppkbb3cker_tracker');
$user->add_lang('mcp');

// Set up general vars
$action		= request_var('action', '');
$forum_id	= request_var('f', 0);
$topic_id	= request_var('t', 0);
$start		= request_var('start', 0);
$deletemark = request_var('delmarked', '');
$deleteall	= request_var('delall', '');
$marked		= request_var('mark', array(0));

// Sort keys
$sort_days	= request_var('st', 0);
$sort_key	= request_var('sk', 't');
$sort_dir	= request_var('sd', 'd');

// Delete entries if requested and able
if (($deletemark || $deleteall) && $auth->acl_get('a_clearlogs'))
{
	if (confirm_box(true))
	{
		$where_sql = '';

		if ($deletemark && sizeof($marked))
		{
			$sql_in = array();
			foreach ($marked as $mark)
			{
				$sql_in[] = $mark;
			}
			$where_sql = ' WHERE ' . $db->sql_in_set('id', $sql_in);
			unset($sql_in);
		}

		if ($where_sql || $deleteall)
		{
			$sql = 'DELETE FROM ' . TRACKER_DOWNLOADS_TABLE . "
				$where_sql";
			$db->sql_query($sql);

			add_log('admin', 'LOG_CLEAR_DOWNLOADS_' . strtoupper($mode));
		}
	}
	else
	{
		confirm_box(false, $user->lang['CONFIRM_OPERATION'], build_hidden_fields(array(
			'f'			=> $forum_id,
			'start'		=> $start,
			'delmarked'	=> $deletemark,
			'delall'	=> $deleteall,
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
	'i' => $user->lang['IP'],
	'p' => $user->lang['SORT_TTITLE'],
);
$sort_by_sql = array(
	'u' => 'u.username_clean',
	't' => 'd.dl_time',
	'f' => 'a.real_filename',
	'i' => 'd.dl_ip',
	'p'=>'p.post_subject',
);

$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

// Define where and sort sql for use in displaying logs
$sql_where = ($sort_days) ? (time() - ($sort_days * 86400)) : 0;
$sql_sort = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

//$l_title = $user->lang['ACP_' . strtoupper($mode) . '_LOGS'];
//$l_title_explain = $user->lang['ACP_' . strtoupper($mode) . '_LOGS_EXPLAIN'];

//$this->page_title = $l_title;

// Grab log data
$log_data = array();
$log_count = 0;
$sql="SELECT COUNT(*) log_count FROM ".TRACKER_DOWNLOADS_TABLE." d LEFT JOIN ".USERS_TABLE." u ON (d.downloader_id=u.user_id) LEFT JOIN ".ATTACHMENTS_TABLE." a ON (d.attach_id=a.attach_id) LEFT JOIN ".POSTS_TABLE." p ON (d.post_msg_id=p.post_id)".($sql_where ? ' WHERE d.dl_time > '.$sql_where : '')."";
// 		echo $sql;
$result=$db->sql_query($sql);
$log_count=$db->sql_fetchrow($result);
$log_count=intval($log_count['log_count']);
if($log_count)
{
	$sql="SELECT d.*, u.username, u.user_colour, a.real_filename, p.post_subject, p.topic_id, p.forum_id FROM ".TRACKER_DOWNLOADS_TABLE." d LEFT JOIN ".USERS_TABLE." u ON (d.downloader_id=u.user_id) LEFT JOIN ".ATTACHMENTS_TABLE." a ON (d.attach_id=a.attach_id) LEFT JOIN ".POSTS_TABLE." p ON (d.post_msg_id=p.post_id)".($sql_where ? ' WHERE d.dl_time > '.$sql_where : '')." ORDER BY $sql_sort LIMIT $start, {$config['topics_per_page']}";
	$result=$db->sql_query($sql);
	while($row=$db->sql_fetchrow($result))
	{
		$log_data[]=$row;
	}
}

$template->assign_vars(array(
	//'S_ALOGS_INC'			=> true,
	//'L_TITLE'		=> $l_title,
	//'L_EXPLAIN'		=> $l_title_explain,
	//'U_ACTION'		=> $this->u_action,

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
		'USERNAME'			=> empty($row['username']) || $row['downloader_id']==1 ? $user->lang['TRACKER_ANONYMOUS'] : get_username_string('full', $row['downloader_id'], $row['username'], $row['user_colour'], $row['username']),

		'DATE'				=> $user->format_date($row['dl_time'], 'Y-m-d H:i:s'),
		'FILENAME'			=> urldecode($row['real_filename']),
		'TTITLE'			=> !empty($row['post_subject']) ? $row['post_subject'] : $user->lang['TORRENT_DELETED'],
		'URL'			=> !empty($row['post_subject']) ? append_sid("{$phpbb_root_path}viewtopic.$phpEx", "f={$row['forum_id']}&amp;t={$row['topic_id']}&amp;p={$row['post_msg_id']}")."#p{$row['post_msg_id']}" : '',
		'FILEURL'			=> !empty($row['real_filename']) ? append_sid("{$phpbb_root_path}download/file.$phpEx", "id={$row['attach_id']}") : '',
		'IP'				=> $row['dl_ip'],

		'ID'				=> $row['id'],
		)
	);
}

$template->assign_vars(array(
	'S_DOWNLOADLOG_INC'	=> true,
	'S_TRACKER_NOBUTT' => true,

	'L_TITLE'			=> $user->lang['ACP_TRACKER_DOWNLOADLOG'],
	'L_EXPLAIN'	=> $user->lang['ACP_TRACKER_DOWNLOADLOG_EXPLAIN'],

	'S_ERROR'			=> (sizeof($error)) ? true : false,
	'ERROR_MSG'			=> implode('<br />', $error),

	'U_ACTION'       => $this->u_action,

));


$display_vars = array(
	'title'	=> $trstat_title,
	'vars'	=> array(
		'legend1'				=> 'ACP_TRACKER_DOWNLOADLOG_SETTINGS',
	)
);







?>
