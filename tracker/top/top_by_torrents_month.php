<?php
/**
*
* @package ppkBB3cker
* @version $Id: top_by_torrents_month.php 1.000 2010-04-20 10:37:00 PPK $
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

//TOP_BY_TORRENTS_MONTH
$sql = "SELECT tor.*, t.*, u.username, u.user_colour, u.user_id, f.forum_id, f.forum_name, t.topic_title, t.topic_id, u.user_regdate, tor.times_completed
	FROM ". TRACKER_TORRENTS_TABLE ." tor, " . TOPICS_TABLE . " t , " . FORUMS_TABLE. " f , " . USERS_TABLE . " u
	WHERE tor.topic_id = t.topic_id
	AND tor.poster_id = u.user_id
	AND t.forum_id = f.forum_id
	AND tor.added>".( time() - 30*24*3600 )."
	AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ")
	".(sizeof($ex_fid_ary) ? " AND f.forum_id NOT IN('".implode("', '", $ex_fid_ary)."')" : '')."
	ORDER BY tor.times_completed DESC
	LIMIT {$top_limit}";
$result = $db->sql_query($sql, $config['ppkbb_tctstat_ctime']*60, "trtop#month");

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
	$complete = $row['times_completed'];
	$reg_time = $user->format_date($row['added']);
	$i++;
	$template->assign_block_vars('torrent30', array(
		'ROW_NUMBER' => $i,
		'USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
		'FORUM_NAME' => $forum_name,
		'FORUM_HREF' => append_sid("viewforum.$phpEx?f=". $row['forum_id']),
		'REG_TIME' => $reg_time,
		'COMPLETE_COUNT' => $complete,
		'TOPIC_TITLE' => censor_text($topic_title),
		'TOPIC_HREF'   => append_sid("viewtopic.$phpEx?t=". $row['topic_id']),
		'U_VIEWPROFILE' => append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=$poster_id"))
	);
}
$db->sql_freeresult($result);
?>
