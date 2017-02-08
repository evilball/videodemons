<?php
/**
*
* @package ppkBB3cker
* @version $Id: top_by_ratio.php 1.000 2010-04-20 10:30:00 PPK $
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

$user->lang['TOP_BY_RATIO']=sprintf($user->lang['TOP_BY_RATIO'], $top_limit);
//TOP_BY_RATIO
$sql = "SELECT u.username, u.user_colour, u.user_id, u.user_uploaded, u.user_downloaded, u.user_bonus, round(u.user_uploaded/u.user_downloaded) AS rat1
	FROM " . USERS_TABLE . " u
	WHERE u.user_uploaded > u.user_downloaded AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ")".($config['ppkbb_tcratio_start'] ? ' AND u.user_downloaded > '.$config['ppkbb_tcratio_start'] : '')."
	ORDER BY rat1 DESC, u.user_uploaded DESC
	LIMIT {$top_limit}";

$result = $db->sql_query($sql, $config['ppkbb_tracker_top'][1]);

$i=0;
while ( $row = $db->sql_fetchrow($result) )
{
	$username = $row['username'];
	$user_id = $row['user_id'];
	$download = $row['user_downloaded'];
	$upload = $row['user_uploaded'];
	$bonus = $row['user_bonus'];
	$poster_id = $row['user_id'];
	$ratio = get_ratio_alias(get_ratio($row['user_uploaded'], $row['user_downloaded'], $config['ppkbb_tcratio_start'], $row['user_bonus']));
	$rratio = get_ratio_alias(get_ratio($row['user_uploaded'], $row['user_downloaded'], $config['ppkbb_tcratio_start']));
	$i++;
	$template->assign_block_vars('memberrow', array(
		'ROW_NUMBER' => $i,
		'USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
		'UP_DOWN_RATIO' => $ratio,
		'UP_DOWN_RRATIO' => $rratio,
		'UP' => get_formatted_filesize($upload),
		'DOWN' => get_formatted_filesize($download),
		'BONUS1' => $row['user_bonus'],
		)
	);
}
$db->sql_freeresult($result);
?>
