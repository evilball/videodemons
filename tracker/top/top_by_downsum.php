<?php
/**
*
* @package ppkBB3cker
* @version $Id: top_by_downsum.php 1.000 2010-04-20 10:39:00 PPK $
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

$user->lang['TOP_BY_DOWNSUM']=sprintf($user->lang['TOP_BY_DOWNSUM'], $top_limit);

//TOP_BY_DOWNSUM
$sql = "SELECT u.user_id, u.username, u.user_colour, u.user_regdate, u.user_torrents, SUM(times_completed) AS rc, COUNT(*) AS user_torrents
	FROM ". TRACKER_TORRENTS_TABLE ." t JOIN ". USERS_TABLE ." u ON (t.poster_id=user_id)
	WHERE u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ")
	GROUP BY t.poster_id
	ORDER BY rc DESC, u.user_torrents
	LIMIT {$top_limit}";

$result = $db->sql_query($sql, $config['ppkbb_tracker_top'][1]);

$i = 0;
while ( $row = $db->sql_fetchrow($result) )
{
	$username = $row['username'];
	$user_id = $row['user_id'];
	$poster_id = $row['user_id'];
	$i++;
	$template->assign_block_vars('countrow', array(
		'ROW_NUMBER' => $i,
		'USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
		'JOINED' => $user->format_date($row['user_regdate']),
		'DL_COUNT' => $row['rc'],
		'RELEASES' => $row['user_torrents'],
		'DL_AVG' => round( $row['rc'] / $row['user_torrents']),
		)
	);
}
$db->sql_freeresult($result);
?>
