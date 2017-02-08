<?php
/**
*
* @package ppkBB3cker
* @version $Id: top_by_torrents.php 1.000 2010-04-20 10:35:00 PPK $
* @copyright (c) 2010 PPK
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

$user->lang['TOP_BY_TORRENTS']=sprintf($user->lang['TOP_BY_TORRENTS'], $top_limit);

//TOP_BY_TORRENTS
$sql = "SELECT tor.added, tor.times_completed timescomplet, t.topic_title, t.topic_id, u.username, u.user_id, u.user_colour, u.user_regdate, f.forum_id, f.forum_name
	FROM ". TRACKER_TORRENTS_TABLE ." tor, " . TOPICS_TABLE . " t , " . FORUMS_TABLE. " f , " . USERS_TABLE . " u
	WHERE tor.topic_id = t.topic_id
	AND tor.poster_id = u.user_id
	AND t.forum_id = f.forum_id
	AND tor.added>".'%1$s'."
	AND tor.times_completed!=0
	AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ")
	".(sizeof($ex_fid_ary) ? " AND f.forum_id NOT IN('".implode("', '", $ex_fid_ary)."')" : '')."
	ORDER BY tor.times_completed DESC
	LIMIT {$top_limit}";
/*$sql = "SELECT tor.added, t.topic_title, t.topic_id, u.username, u.user_id, u.user_colour, u.user_regdate, f.forum_id, f.forum_name, COUNT(*) AS timescomplet
	FROM ". TRACKER_TORRENTS_TABLE ." tor, " . TOPICS_TABLE . " t , " . FORUMS_TABLE. " f , " . USERS_TABLE . " u, ".TRACKER_SNATCHED_TABLE." s
	WHERE tor.topic_id = t.topic_id
	AND tor.poster_id = u.user_id
	AND t.forum_id = f.forum_id
	AND tor.added>".'%1$s'."
	AND s.torrent=tor.id
	AND to_go=0
	AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ")
	".(sizeof($ex_fid_ary) ? " AND f.forum_id NOT IN('".implode("', '", $ex_fid_ary)."')" : '')."
	GROUP BY s.torrent
	ORDER BY timescomplet DESC
	LIMIT {$top_limit}";*/

$result = $db->sql_query(sprintf($sql, $top_time), $config['ppkbb_tracker_top'][1], md5($sql.$top_time));

$i = 0;
while ( $row = $db->sql_fetchrow($result) )
{
	$username = $row['username'];
	$user_id = $row['user_id'];
	$forum_name = $row ['forum_name'];
	$forum_id = $row['forum_id'];
	$poster_id = $row['user_id'];
	$topic_title = $row['topic_title'];
	$topic_id = $row['topic_id'];
	$complete = $row['timescomplet'];
	$reg_time = $user->format_date($row['added']);
	$i++;
	$template->assign_block_vars('torrentsrow', array(
		'ROW_NUMBER' => $i,
		'USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
		'FORUM_NAME' => $forum_name,
		'FORUM_HREF' => append_sid("viewforum.{$phpEx}?f={$row['forum_id']}"),
		'REG_TIME' => $reg_time,
		'COMPLETE_COUNT' => $complete,
		'TOPIC_TITLE' => censor_text($topic_title),
		'TOPIC_HREF' => append_sid("viewtopic.{$phpEx}?f={$row['forum_id']}&amp;t={$row['topic_id']}"),
		)
	);
}
$db->sql_freeresult($result);
?>
