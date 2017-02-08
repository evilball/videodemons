<?php
/**
*
* @package ppkBB3cker
* @version $Id: top_by_thanks.php 1.000 2013-12-10 12:31:54 PPK $
* @copyright (c) 2013 PPK
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

$user->lang['TOP_BY_THANKS']=sprintf($user->lang['TOP_BY_THANKS'], $top_limit);

//TOP_BY_THANKS
$sql = "SELECT tor.added, tor.times_completed, u.username, u.user_id, u.user_colour, u.user_regdate, f.forum_id, f.forum_name, t.topic_title, t.topic_id, COUNT(*) AS thanks
	FROM ". TRACKER_TORRENTS_TABLE ." tor, " . TOPICS_TABLE . " t , " . FORUMS_TABLE. " f , " . USERS_TABLE . " u, ".TRACKER_THANKS_TABLE." th
	WHERE tor.topic_id = t.topic_id
	AND tor.poster_id = u.user_id
	AND t.forum_id = f.forum_id
	AND th.tadded>".'%1$s'."
	AND th.torrent_id=tor.id
	AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ")
	".(sizeof($ex_fid_ary) ? " AND f.forum_id NOT IN('".implode("', '", $ex_fid_ary)."')" : '')."
	GROUP BY th.torrent_id
	ORDER BY thanks DESC
	LIMIT {$top_limit}";

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
	$thanks = $row['thanks'];
	$reg_time = $user->format_date($row['added']);
	$i++;
	$template->assign_block_vars('thanksrow', array(
		'ROW_NUMBER' => $i,
		'USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
		'FORUM_NAME' => $forum_name,
		'FORUM_HREF' => append_sid("viewforum.{$phpEx}?f={$row['forum_id']}"),
		'REG_TIME' => $reg_time,
		'THANKS_COUNT' => $thanks,
		'TOPIC_TITLE' => censor_text($topic_title),
		'TOPIC_HREF' => append_sid("viewtopic.{$phpEx}?f={$row['forum_id']}&amp;t={$row['topic_id']}"),
		)
	);
}
$db->sql_freeresult($result);
?>
