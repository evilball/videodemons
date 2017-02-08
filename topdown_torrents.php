<?php
/**
*
* @package ppkBB3cker
* @version $Id: topdown_torrents.php 1.000 2010-10-30 18:39:00 PPK $
* @copyright (c) 2010 PPK
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
{
	exit();
}

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin(false);
$auth->acl($user->data);
$user->setup();

$dt=time();
$topdown_torrents_fid=request_var('fid', 0);
$topdown_torrents_pid=request_var('pid', 0);
$topdown_torrents_id=request_var('id', '');
$topdown_torrents='';

if(!$auth->acl_get('u_canviewtopdowntorrents') || (!$config['ppkbb_topdown_torrents'][0] && $topdown_torrents_pid && !$topdown_torrents_fid) || (!$config['ppkbb_topdown_torrents'][1] && !$topdown_torrents_fid && !$topdown_torrents_pid) || (!$config['ppkbb_topdown_torrents'][2] && $topdown_torrents_fid && !$topdown_torrents_pid))
{
	$topdown_torrents=false;
}
else
{
	$disallow_access=array_unique(array_keys($auth->acl_getf('!f_read', true)));
	$post_time=($config['ppkbb_topdown_torrents'][10] ? " AND t.added > ".($dt-($config['ppkbb_topdown_torrents'][10])) : '');

	$sql="SELECT
		a.attach_id,
		a.post_msg_id,
		a.extension,
		a.real_filename,
		a.physical_filename,
		a.i_width,
		a.i_height,
		a.i_external,
		tt.topic_title,
		tt.topic_id,
		tt.forum_id,
		tt.topic_first_post_id,
		tt.topic_type,
		t.times_completed".($config['ppkbb_tcenable_rannounces'][0] ? '+t.rem_times_completed' : '')." times_completed,
		t.leechers".($config['ppkbb_tcenable_rannounces'][0] ? '+t.rem_leechers' : '')." leechers,
		t.seeders".($config['ppkbb_tcenable_rannounces'][0] ? '+t.rem_seeders' : '')." seeders,
		t.id torrent_id"
		." FROM ".ATTACHMENTS_TABLE." a, ".TOPICS_TABLE." tt, ". TRACKER_TORRENTS_TABLE ." t"
		." WHERE "
		.(sizeof($config['ppkbb_topdown_torrents_exclude']) ? " t.forum_id".($config['ppkbb_topdown_torrents_trueexclude'] ? ' NOT' : '')." IN('".implode("', '", $config['ppkbb_topdown_torrents_exclude'])."') AND " : '')
		.(sizeof($disallow_access) ? " t.forum_id NOT IN('".implode("', '", $disallow_access)."') AND " : '')
		.($topdown_torrents_fid ? " t.forum_id='{$topdown_torrents_fid}' AND " : '')
		."a.post_msg_id=tt.topic_first_post_id AND tt.topic_id=t.topic_id "
		.($config['ppkbb_topdown_torrents'][7] ? "AND t.seeders".($config['ppkbb_tcenable_rannounces'][0] ? '+t.rem_seeders' : '')." >= {$config['ppkbb_topdown_torrents'][7]} " : '')
		." AND (a.i_poster='1'"
		.($config['ppkbb_topdown_torrents'][9] ? " AND a.thumbnail='1'" : '').")".
		'%1$s'
		." ORDER BY "
		.($config['ppkbb_topdown_torrents'][11] ? 't.id' : 't.times_completed'.($config['ppkbb_tcenable_rannounces'][0] ? '+t.rem_times_completed' : ''))
		." DESC LIMIT 0, {$config['ppkbb_topdown_torrents'][3]}";

	$topdown_torrents=$cache->get('_ppkbb3cker_tdt_'.md5($sql));
	if(!$topdown_torrents)
	{
		$result=$db->sql_query(sprintf($sql, $post_time), $config['ppkbb_topdown_torrents'][6], md5($sql));
		$torrents_posters=array();
	while($row=$db->sql_fetchrow($result))
	{
		$torrents_posters[$row['torrent_id']]=$row;
	}
	$db->sql_freeresult($result);

	if($config['ppkbb_topdown_torrents'][8] && sizeof($torrents_posters) < $config['ppkbb_topdown_torrents'][8])
	{
			$topdown_torrents=false;
	}
	else
	{
		$i=0;
		foreach($torrents_posters as $k => $v)
		{
			$v['i_height'] ? $i_factor=my_float_val($v['i_width']/$v['i_height']) : $i_factor=0.675;
			$i_width=my_int_val($config['ppkbb_topdown_torrents'][5]*$i_factor);
				$t_title="{$v['topic_title']} - {$user->lang['TORRENT_COMPLETED']}: {$v['times_completed']}, {$user->lang['TORRENT_SEEDERS']}: {$v['seeders']}, {$user->lang['TORRENT_LEECHERS']}: {$v['leechers']}";

			$tdt_image='<img class="tdt_image" rel="tdt'.$i.'" src="'.($v['i_external'] ? $v['real_filename'] : append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . $v['attach_id']).($config['ppkbb_topdown_torrents'][9] ? '&amp;t=1' : '')).'" alt="'.$t_title.'" height="'.$config['ppkbb_topdown_torrents'][5].'" width="'.$i_width.'" onError="this.onerror=null;this.src=\''.$phpbb_root_path.'images/tracker/file.png'.'\';" />';
			$topdown_torrents.='
					<div class="panel">
						<a href="'.append_sid($phpbb_root_path.'viewtopic.' . $phpEx . '?f=' . $v['forum_id'] . '&amp;t=' . $v['topic_id']).'" title = "'.$t_title.'">'.$tdt_image.'</a>
					</div>
			';
			$i+=1;
		}
	}
		$cache->put('_ppkbb3cker_tdt_'.md5($sql), array($topdown_torrents, 0), $config['ppkbb_topdown_torrents'][6]);
}
	else
	{
		$topdown_torrents=$topdown_torrents[0];
	}

}

header('Content-type: text/html; charset=UTF-8');

if($topdown_torrents)
{
	echo $topdown_torrents;
}
else
{
	echo '
		<div id="panel" style="margin-left:10px;white-space:nowrap;">'.$user->lang['NO_TDT'].'</div>
		<script type="text/javascript">
		// <![CDATA[
		jQuery(document).ready(
			function($)
			{
				//$(".tdtnav_buttons").remove();
				$("#topdown_torrents").animate({\'height\': \'15px\'});
				$("#gallerya'.htmlspecialchars($topdown_torrents_id).'").animate({\'height\': \'15px\'});
			}
		);
		stepcarousel.resetsettings();
		// ]]>
		</script>
	';
}

exit();

?>
