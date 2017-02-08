<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);


// Thank you sun.
if (isset($_SERVER['CONTENT_TYPE']))
{
	if ($_SERVER['CONTENT_TYPE'] === 'application/x-java-archive')
	{
		exit;
	}
}
else if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Java') !== false)
{
	exit;
}

if (isset($_GET['avatar']))
{
	require($phpbb_root_path . 'includes/startup.' . $phpEx);
	require($phpbb_root_path . 'config.' . $phpEx);
	if (!defined('PHPBB_INSTALLED') || empty($dbms) || empty($acm_type))
	{
		exit;
	}
	require($phpbb_root_path . 'includes/acm/acm_' . $acm_type . '.' . $phpEx);
	require($phpbb_root_path . 'includes/cache.' . $phpEx);
	require($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);
	require($phpbb_root_path . 'includes/constants.' . $phpEx);
	require($phpbb_root_path . 'includes/functions.' . $phpEx);

	$db = new $sql_db();
	$cache = new cache();

	// Connect to DB
	if (!@$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, false))
	{
		exit;
	}
	unset($dbpasswd);

	// worst-case default
	$browser = (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : 'msie 6.0';

	$config = $cache->obtain_config();
	$filename = request_var('avatar', '');
	$avatar_group = false;
	$exit = false;

	if (isset($filename[0]) && $filename[0] === 'g')
	{
		$avatar_group = true;
		$filename = substr($filename, 1);
	}

	// '==' is not a bug - . as the first char is as bad as no dot at all
	if (strpos($filename, '.') == false)
	{
		send_status_line(403, 'Forbidden');
		$exit = true;
	}

	if (!$exit)
	{
		$ext		= substr(strrchr($filename, '.'), 1);
		$stamp		= (int) substr(stristr($filename, '_'), 1);
		$filename	= (int) $filename;
		$exit = set_modified_headers($stamp, $browser);
	}
	if (!$exit && !in_array($ext, array('png', 'gif', 'jpg', 'jpeg')))
	{
		// no way such an avatar could exist. They are not following the rules, stop the show.
		send_status_line(403, 'Forbidden');
		$exit = true;
	}


	if (!$exit)
	{
		if (!$filename)
		{
			// no way such an avatar could exist. They are not following the rules, stop the show.
			send_status_line(403, 'Forbidden');
		}
		else
		{
			send_avatar_to_browser(($avatar_group ? 'g' : '') . $filename . '.' . $ext, $browser);
		}
	}
	file_gc();
}

// implicit else: we are not in avatar mode
include($phpbb_root_path . 'common.' . $phpEx);

$download_id = request_var('id', 0);
$mode = request_var('mode', '');
$thumbnail = request_var('t', false);
// Start session management, do not update session page.
$user->session_begin(false);
$auth->acl($user->data);
$user->setup('viewtopic');

if (!$download_id)
{
	send_status_line(404, 'Not Found');
	trigger_error('NO_ATTACHMENT_SELECTED');
}

if (!$config['allow_attachments'] && !$config['allow_pm_attach'])
{
	send_status_line(404, 'Not Found');
	trigger_error('ATTACHMENT_FUNCTIONALITY_DISABLED');
}

$sql = 'SELECT attach_id, in_message, post_msg_id, extension, is_orphan, poster_id, filetime
	FROM ' . ATTACHMENTS_TABLE . "
	WHERE attach_id = $download_id";
$result = $db->sql_query_limit($sql, 1);
$attachment = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$forum_astracker=0;
if (!$attachment)
{
	send_status_line(404, 'Not Found');
	trigger_error('ERROR_NO_ATTACHMENT');
}

if ((!$attachment['in_message'] && !$config['allow_attachments']) || ($attachment['in_message'] && !$config['allow_pm_attach']))
{
	send_status_line(404, 'Not Found');
	trigger_error('ATTACHMENT_FUNCTIONALITY_DISABLED');
}

$row = array();

if ($attachment['is_orphan'])
{
	// We allow admins having attachment permissions to see orphan attachments...
	$own_attachment = ($auth->acl_get('a_attach') || $attachment['poster_id'] == $user->data['user_id']) ? true : false;

	if (!$own_attachment || ($attachment['in_message'] && !$auth->acl_get('u_pm_download')) || (!$attachment['in_message'] && !$auth->acl_get('u_download')))
	{
		send_status_line(404, 'Not Found');
		trigger_error('ERROR_NO_ATTACHMENT');
	}

	// Obtain all extensions...
	$extensions = $cache->obtain_attach_extensions(true);
}
else
{
	if (!$attachment['in_message'])
	{
		//
		$sql = 'SELECT p.forum_id, f.forum_name, f.forum_password, f.parent_id, f.forumas
			FROM ' . POSTS_TABLE . ' p, ' . FORUMS_TABLE . ' f
			WHERE p.post_id = ' . $attachment['post_msg_id'] . '
				AND p.forum_id = f.forum_id';
		$result = $db->sql_query_limit($sql, 1);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);


		$forum_astracker=isset($row['forum_id']) && $row['forumas']==1 ? 1 : 0;
		if($forum_astracker && $attachment['extension']=='torrent')
		{
			include_once($phpbb_root_path.'tracker/include/download/file_add1.'.$phpEx);
		}
		else
		{
			$image_ext=array('jpg', 'jpeg', 'gif', 'png');
			$is_candownpostscr=0;
			if($forum_astracker && in_array($attachment['extension'], $image_ext))
			{
				if(!$user->data['is_registered'])
				{
					$is_candownpostscr = $auth->acl_get('u_candownpostscr') && $auth->acl_get('f_candownpostscr', $row['forum_id']) ? 1 : 0;
				}
				else
				{
					$is_candownpostscr=1;
					if($user->data['user_id']!=$attachment['poster_id'])
					{
						$is_candownpostscr=$auth->acl_get('u_candownpostscr') && $auth->acl_get('f_candownpostscr', $row['forum_id']) ? 1 : 0;
					}
				}

				if(!$is_candownpostscr)
				{
					trigger_error('CANT_DOWN_POSTSCR');
				}
			}

			// Global announcement?
			$f_download = (!$row) ? $auth->acl_getf_global('f_download') : $auth->acl_get('f_download', $row['forum_id']);

			if (($auth->acl_get('u_download') && $f_download) || $is_candownpostscr)
			{
				if ($row && $row['forum_password'])
				{
					// Do something else ... ?
					login_forum_box($row);
				}
			}
			else
			{
			send_status_line(403, 'Forbidden');
				trigger_error('SORRY_AUTH_VIEW_ATTACH');
			}
		}
	}
	else
	{
		$row['forum_id'] = false;
		if (!$auth->acl_get('u_pm_download'))
		{
			send_status_line(403, 'Forbidden');
			trigger_error('SORRY_AUTH_VIEW_ATTACH');
		}

		// Check if the attachment is within the users scope...
		$sql = 'SELECT user_id, author_id
			FROM ' . PRIVMSGS_TO_TABLE . '
			WHERE msg_id = ' . $attachment['post_msg_id'];
		$result = $db->sql_query($sql);

		$allowed = false;
		while ($user_row = $db->sql_fetchrow($result))
		{
			if ($user->data['user_id'] == $user_row['user_id'] || $user->data['user_id'] == $user_row['author_id'])
			{
				$allowed = true;
				break;
			}
		}
		$db->sql_freeresult($result);

		if (!$allowed)
		{
			send_status_line(403, 'Forbidden');
			trigger_error('ERROR_NO_ATTACHMENT');
		}
	}

	// disallowed?
	$extensions = array();
	if (!extension_allowed($row['forum_id'], $attachment['extension'], $extensions))
	{
		send_status_line(404, 'Forbidden');
		trigger_error(sprintf($user->lang['EXTENSION_DISABLED_AFTER_POSTING'], $attachment['extension']));
	}
}

if (!download_allowed())
{
	send_status_line(403, 'Forbidden');
	trigger_error($user->lang['LINKAGE_FORBIDDEN']);
}

$download_mode = (int) $extensions[$attachment['extension']]['download_mode'];

$sql_addon=array();
if($forum_astracker)
{
	$sql_addon['select']=', poster_id, username';
	$sql_addon['from']=' LEFT JOIN '.USERS_TABLE.' ON (poster_id=user_id)';
}

// Fetching filename here to prevent sniffing of filename
$sql = 'SELECT attach_id, is_orphan, in_message, post_msg_id, extension, physical_filename, real_filename, mimetype, filetime'.($forum_astracker ? $sql_addon['select'] : '').'
	FROM ' . ATTACHMENTS_TABLE . ($forum_astracker ? $sql_addon['from'] : '') . "
	WHERE attach_id = $download_id";
$result = $db->sql_query_limit($sql, 1);
$attachment = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if (!$attachment)
{
	send_status_line(404, 'Not Found');
	trigger_error('ERROR_NO_ATTACHMENT');
}

$attachment['physical_filename'] = utf8_basename($attachment['physical_filename']);
$display_cat = $extensions[$attachment['extension']]['display_cat'];

if (($display_cat == ATTACHMENT_CATEGORY_IMAGE || $display_cat == ATTACHMENT_CATEGORY_THUMB) && !$user->optionget('viewimg'))
{
	$display_cat = ATTACHMENT_CATEGORY_NONE;
}

if ($display_cat == ATTACHMENT_CATEGORY_FLASH && !$user->optionget('viewflash'))
{
	$display_cat = ATTACHMENT_CATEGORY_NONE;
}

if ($thumbnail)
{
	$attachment['physical_filename'] = 'thumb_' . $attachment['physical_filename'];
}
else if (($display_cat == ATTACHMENT_CATEGORY_NONE/* || $display_cat == ATTACHMENT_CATEGORY_IMAGE*/) && !$attachment['is_orphan'])
{
	// Update download count
	$sql = 'UPDATE ' . ATTACHMENTS_TABLE . '
		SET download_count = download_count + 1
		WHERE attach_id = ' . $attachment['attach_id'];
	$db->sql_query($sql);
}

if ($display_cat == ATTACHMENT_CATEGORY_IMAGE && $mode === 'view' && (strpos($attachment['mimetype'], 'image') === 0) && (strpos(strtolower($user->browser), 'msie') !== false) && !phpbb_is_greater_ie_version($user->browser, 7))
{
	wrap_img_in_html(append_sid($phpbb_root_path . 'download/file.' . $phpEx, 'id=' . $attachment['attach_id']), $attachment['real_filename']);
	file_gc();
}
else
{
	// Determine the 'presenting'-method
	if ($download_mode == PHYSICAL_LINK)
	{
		// This presenting method should no longer be used
		if (!@is_dir($phpbb_root_path . $config['upload_path']))
		{
			send_status_line(500, 'Internal Server Error');
			trigger_error($user->lang['PHYSICAL_DOWNLOAD_NOT_POSSIBLE']);
		}

		redirect($phpbb_root_path . $config['upload_path'] . '/' . $attachment['physical_filename']);
		file_gc();
	}
	else
	{
		send_file_to_browser($attachment, $config['upload_path'], $display_cat);
		file_gc();
	}
}


/**
* A simplified function to deliver avatars
* The argument needs to be checked before calling this function.
*/
function send_avatar_to_browser($file, $browser)
{
	global $config, $phpbb_root_path;

	$prefix = $config['avatar_salt'] . '_';
	$image_dir = $config['avatar_path'];

	// Adjust image_dir path (no trailing slash)
	if (substr($image_dir, -1, 1) == '/' || substr($image_dir, -1, 1) == '\\')
	{
		$image_dir = substr($image_dir, 0, -1) . '/';
	}
	$image_dir = str_replace(array('../', '..\\', './', '.\\'), '', $image_dir);

	if ($image_dir && ($image_dir[0] == '/' || $image_dir[0] == '\\'))
	{
		$image_dir = '';
	}
	$file_path = $phpbb_root_path . $image_dir . '/' . $prefix . $file;

	if ((@file_exists($file_path) && @is_readable($file_path)) && !headers_sent())
	{
		header('Pragma: public');

		$image_data = @getimagesize($file_path);
		header('Content-Type: ' . image_type_to_mime_type($image_data[2]));

		if ((strpos(strtolower($browser), 'msie') !== false) && !phpbb_is_greater_ie_version($browser, 7))
		{
			header('Content-Disposition: attachment; ' . header_filename($file));

			if (strpos(strtolower($browser), 'msie 6.0') !== false)
			{
				header('Expires: -1');
			}
			else
			{
				header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
			}
		}
		else
		{
			header('Content-Disposition: inline; ' . header_filename($file));
			header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
		}

		$size = @filesize($file_path);
		if ($size)
		{
			header("Content-Length: $size");
		}

		if (@readfile($file_path) == false)
		{
			$fp = @fopen($file_path, 'rb');

			if ($fp !== false)
			{
				while (!feof($fp))
				{
					echo fread($fp, 8192);
				}
				fclose($fp);
			}
		}

		flush();
	}
	else
	{
		send_status_line(404, 'Not Found');
	}
}

/**
* Wraps an url into a simple html page. Used to display attachments in IE.
* this is a workaround for now; might be moved to template system later
* direct any complaints to 1 Microsoft Way, Redmond
*/
function wrap_img_in_html($src, $title)
{
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-Strict.dtd">';
	echo '<html>';
	echo '<head>';
	echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8" />';
	echo '<title>' . $title . '</title>';
	echo '</head>';
	echo '<body>';
	echo '<div>';
	echo '<img src="' . $src . '" alt="' . $title . '" />';
	echo '</div>';
	echo '</body>';
	echo '</html>';
}

/**
* Send file to browser
*/
function send_file_to_browser($attachment, $upload_dir, $category)
{
	global $user, $db, $config, $phpbb_root_path, $forum_astracker, $phpEx, $auth;

	$dt=time();
	if($forum_astracker && $attachment['extension']=='torrent')
	{
		$register_link=append_sid("{$phpbb_root_path}ucp.{$phpEx}", "mode=register", false);
		$login_link=append_sid("{$phpbb_root_path}ucp.{$phpEx}", "mode=login", false);

		if(!$user->data['is_registered'])
		{
			if($config['ppkbb_tctrestricts_options'][0])
			{
				$sql='SELECT COUNT(*) dl_count FROM '.TRACKER_DOWNLOADS_TABLE." WHERE downloader_id='1' AND dl_date='".date('Ymd', $dt)."'";
				$result=$db->sql_query($sql);
				$dl_count=$db->sql_fetchfield('dl_count');
				$db->sql_freeresult($result);
				if($dl_count >= $config['ppkbb_tctrestricts_options'][0])
				{
					trigger_error(sprintf($user->lang['GUEST_DOWNLOADS_LIMIT'], $register_link, $login_link));
				}
			}
			if($config['ppkbb_tctrestricts_options'][3])
			{
				$sql='SELECT COUNT(*) dl_count FROM '.TRACKER_DOWNLOADS_TABLE." WHERE downloader_id='1' AND dl_date='".date('Ymd', $dt)."' AND dl_ip='{$user->data['session_ip']}'";
				$result=$db->sql_query($sql);
				$dl_count=$db->sql_fetchfield('dl_count');
				$db->sql_freeresult($result);
				if($dl_count >= $config['ppkbb_tctrestricts_options'][3])
				{
					trigger_error($user->lang['GUEST_DOWNLOADS_IPLIMIT']);
				}
			}
		}
		else
		{
			if($config['ppkbb_tctrestricts_options'][1] && $user->data['user_type']!=USER_FOUNDER)
			{
				$sql='SELECT COUNT(*) dl_count FROM '.TRACKER_DOWNLOADS_TABLE." WHERE downloader_id='{$user->data['user_id']}' AND dl_date='".date('Ymd', $dt)."'";
				$result=$db->sql_query($sql);
				$dl_count=$db->sql_fetchfield('dl_count');
				$db->sql_freeresult($result);
				if($dl_count >= $config['ppkbb_tctrestricts_options'][1])
				{
					trigger_error($user->lang['USER_DOWNLOADS_LIMIT']);
				}
			}
			if($config['ppkbb_tctrestricts_options'][4])
			{
				$sql='SELECT COUNT(*) dl_count FROM '.TRACKER_DOWNLOADS_TABLE." WHERE downloader_id!='1' AND dl_date='".date('Ymd', $dt)."' AND dl_ip='{$user->data['user_ip']}'";
				$result=$db->sql_query($sql);
				$dl_count=$db->sql_fetchfield('dl_count');
				$db->sql_freeresult($result);
				if($dl_count >= $config['ppkbb_tctrestricts_options'][4])
				{
					trigger_error($user->lang['USER_DOWNLOADS_IPLIMIT']);
		}
			}
		}
		$t_magnet=request_var('magnet', 0);
		if($t_magnet && ((array_sum($config['ppkbb_torrent_magnetlink']) && $user->data['is_registered']) || (!$user->data['is_registered'] && array_sum($config['ppkbb_torrent_gmagnetlink']) && $config['ppkbb_tcguests_enabled'][0])))
		{
			$result=$db->sql_query("SELECT id, info_hash, size FROM ".TRACKER_TORRENTS_TABLE." WHERE id='{$attachment['attach_id']}' AND post_msg_id='{$attachment['post_msg_id']}' LIMIT 1");
			$torrents=$db->sql_fetchrow($result);
			$torrent_basename=utf8_basename(urldecode($attachment['real_filename']));

			if($user->data['is_registered'])
			{
				if(!$user->data['user_passkey'])
				{
					$user->data['user_passkey']=create_passkey();
				}
			}
			else
			{
				$config['ppkbb_announce_url']='/tracker/announce2.'.$phpEx;
				$user_passkey=$user->data['user_passkey']=$user->session_id;
				$config['ppkbb_rtrack_enable'][1]=0;
			}

			$tracker_url=generate_board_url();
			if(!$config['ppkbb_announce_url'])
			{
				$config['ppkbb_announce_url']='/tracker/announce.'.$phpEx;
			}
			$rtracks=urlencode($tracker_url.$config['ppkbb_announce_url']."?passkey={$user->data['user_passkey']}");
			if($config['ppkbb_rtrack_enable'][0] || $config['ppkbb_rtrack_enable'][1])
			{
				require($phpbb_root_path.'tracker/include/rtrackfunc.'.$phpEx);
				$forb_rtrack=get_forb_rtrack();
				$rtrack=get_rtrack($user->ip, $config['ppkbb_rtrack_enable'][0], $config['ppkbb_rtrack_enable'][1], $torrents['id'], $forb_rtrack);
				$rtracks=magnet_rtrack_url($rtrack, $config['ppkbb_rtrack_enable'][2]);
			}
			$confirm=request_var('confirm', '');
			$magnet_src_link="magnet:?xt=urn:btih:".bin2hex($torrents['info_hash'])."&dn=".urlencode($torrent_basename)."&xl={$torrents['size']}&tr={$rtracks}";
			if(!$user->data['is_registered'])
			{
				if($config['ppkbb_gtccleanup_interval'] && $dt - $config['ppkbb_gtracker_last_cleanup'] > $config['ppkbb_gtccleanup_interval'])
				{
					$sql_addon=$config['ppkbb_gtcsession_expire'][0] ? $dt.'-user_time > '.$config['ppkbb_gtcsession_expire'][0] : '';
					$config['ppkbb_gtcsession_expire'][1] && $sql_addon ? $sql_addon.=' OR ' : '';
					$config['ppkbb_gtcsession_expire'][1] ? $sql_addon.=$dt.'-user_last_time > '.$config['ppkbb_gtcsession_expire'][1] : '';
					if($sql_addon)
					{
						$result=$db->sql_query("DELETE FROM ".TRACKER_GUESTS_TABLE." WHERE {$sql_addon}");
					}
					set_config('ppkbb_gtracker_last_cleanup', $dt, 1);
					//purge_tracker_config(true);
				}

				$result=$db->sql_query("SELECT user_id FROM ".TRACKER_GUESTS_TABLE." WHERE session_id='".$db->sql_escape($user_passkey)."' LIMIT 1");
				$guest_user_id=$db->sql_fetchrow($result);
				$guest_user_id=$guest_user_id['user_id'];
				if(!$guest_user_id)
				{
					if($config['ppkbb_gtcmax_sessions'])
					{
						$result=$db->sql_query("SELECT DISTINCT(COUNT(session_id)) sessions FROM ".TRACKER_GUESTS_TABLE."");
						$sess_count=$db->sql_fetchrow($result);
						$db->sql_freeresult($result);
						if(intval($sess_count['sessions']) >= $config['ppkbb_gtcmax_sessions'])
						{
							trigger_error($user->lang['TRACKER_GUESTSSESS_LIMIT']);
						}
					}
					$sql="INSERT INTO ".TRACKER_GUESTS_TABLE." (session_id, user_ip) VALUES('".$db->sql_escape($user_passkey)."', INET_ATON('".$db->sql_escape($user->ip)."'))";
					$result=$db->sql_query($sql);
					$guest_user_id=$db->sql_nextid();
				}
				$db->sql_freeresult($result);
				if($confirm)
				{
					if(!$guest_user_id || $config['ppkbb_addit_options'][1]==2 || !$config['ppkbb_addit_options'][1])
					{
			}
					else
					{
						$sql='SELECT id, dl_ip FROM '.TRACKER_DOWNLOADS_TABLE." WHERE dl_time='".date('Ymd', $dt)."' AND downloader_id='{$user->data['user_id']}' AND attach_id='{$attachment['attach_id']}' LIMIT 1";
						$result=$db->sql_query($sql);
						$row=$db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						if(!$row || (!$user->data['is_registered'] && $row['dl_ip']!=$user->ip))
						{
							$sql="INSERT INTO ".TRACKER_DOWNLOADS_TABLE."(downloader_id, dl_time, dl_ip, attach_id, post_msg_id, guests, dl_date) VALUES('1', '{$dt}', '".$db->sql_escape($user->ip)."', '{$attachment['attach_id']}', '{$attachment['post_msg_id']}', '1', '".date('Ymd', $dt)."')";
							$result=$db->sql_query($sql);
						}
					}
					header("Location: {$magnet_src_link}");
	// 				exit();
				}
				else
				{
					$download_url=append_sid("{$phpbb_root_path}download/file.{$phpEx}");
					$download_amp=strpos($download_url, '?')!==false ? '&amp;' : '?';
					$magnet_src_link=$download_url.$download_amp."id={$attachment['attach_id']}&amp;magnet=1&amp;confirm=1";
				}
				trigger_error(sprintf($user->lang['TORRENT_MAGNET_DLINK'], $magnet_src_link).($confirm ? sprintf($user->lang['RETURN_BACK'], append_sid("{$phpbb_root_path}viewtopic.{$phpEx}", 'p='.$attachment['post_msg_id']).'#p'.$attachment['post_msg_id']) : $user->lang['TRACKER_RETURN_BACK']));
			trigger_error(sprintf($user->lang['TORRENT_MAGNET_DLINK'], $magnet_src_link));
		}
			else
			{

				if($confirm)
				{
					if(!$config['ppkbb_addit_options'][1] || $user->data['user_id']==$attachment['poster_id'])
					{

					}
					else
					{
						$sql='SELECT id, dl_ip FROM '.TRACKER_DOWNLOADS_TABLE." WHERE dl_time='".date('Ymd', $dt)."' AND downloader_id='{$user->data['user_id']}' AND attach_id='{$attachment['attach_id']}' LIMIT 1";
						$result=$db->sql_query($sql);
						$row=$db->sql_fetchrow($result);
						$db->sql_freeresult($result);

						if(!$row || (!$user->data['is_registered'] && $row['dl_ip']!=$user->ip))
						{
							$sql="INSERT INTO ".TRACKER_DOWNLOADS_TABLE."(downloader_id, dl_time, dl_ip, attach_id, post_msg_id, dl_date) VALUES('{$user->data['user_id']}', '{$dt}', '".$db->sql_escape($user->ip)."', '{$attachment['attach_id']}', '{$attachment['post_msg_id']}', '".date('Ymd', $dt)."')";
							$result=$db->sql_query($sql);
						}
					}
					header("Location: {$magnet_src_link}");
	// 				exit();
				}
				else
				{
					$download_url=append_sid("{$phpbb_root_path}download/file.{$phpEx}");
					$download_amp=strpos($download_url, '?')!==false ? '&amp;' : '?';
					$magnet_src_link=$download_url.$download_amp."id={$attachment['attach_id']}&amp;magnet=1&amp;confirm=1";
				}
				trigger_error(sprintf($user->lang['TORRENT_MAGNET_DLINK'], $magnet_src_link).($confirm ? sprintf($user->lang['RETURN_BACK'], append_sid("{$phpbb_root_path}viewtopic.{$phpEx}", 'p='.$attachment['post_msg_id']).'#p'.$attachment['post_msg_id']) : $user->lang['TRACKER_RETURN_BACK']));
			}
		}
		$user_passkey=$user->data['user_passkey'];
		if(!$user_passkey && $user->data['is_registered'])
		{
			$user_passkey=create_passkey($user->data['user_id']);
			$user->data['user_passkey']=$user_passkey;
		}

		if($config['ppkbb_append_tfile'] && stristr($attachment['real_filename'], "[{$config['server_name']}]")===false)
		{
				$p_array=explode('.', $attachment['real_filename']);
				unset($p_array[sizeof($p_array)-1]);
				$attachment['real_filename']=implode('.', $p_array)." [{$config['server_name']}].".$attachment['extension'];
		}
		$t_mtype=$attachment['mimetype'];
	}

	$filename = $phpbb_root_path . $upload_dir . '/' . $attachment['physical_filename'];

	if (!@file_exists($filename))
	{
		send_status_line(404, 'Not Found');
		trigger_error('ERROR_NO_ATTACHMENT');
	}

	// Correct the mime type - we force application/octetstream for all files, except images
	// Please do not change this, it is a security precaution
	if ($category != ATTACHMENT_CATEGORY_IMAGE || strpos($attachment['mimetype'], 'image') !== 0)
	{
		$attachment['mimetype'] = (strpos(strtolower($user->browser), 'msie') !== false || strpos(strtolower($user->browser), 'opera') !== false) ? 'application/octetstream' : 'application/octet-stream';
	}

	if (@ob_get_length())
	{
		@ob_end_clean();
	}

	// Now send the File Contents to the Browser
	$size = @filesize($filename);

	// To correctly display further errors we need to make sure we are using the correct headers for both (unsetting content-length may not work)

	// Check if headers already sent or not able to get the file contents.
	if (headers_sent() || !@file_exists($filename) || !@is_readable($filename))
	{
		// PHP track_errors setting On?
		if (!empty($php_errormsg))
		{
			send_status_line(500, 'Internal Server Error');
			trigger_error($user->lang['UNABLE_TO_DELIVER_FILE'] . '<br />' . sprintf($user->lang['TRACKED_PHP_ERROR'], $php_errormsg));
		}

		send_status_line(500, 'Internal Server Error');
		trigger_error('UNABLE_TO_DELIVER_FILE');
	}

	// Now the tricky part... let's dance
	header('Pragma: public');

	/**
	* Commented out X-Sendfile support. To not expose the physical filename within the header if xsendfile is absent we need to look into methods of checking it's status.
	*
	* Try X-Sendfile since it is much more server friendly - only works if the path is *not* outside of the root path...
	* lighttpd has core support for it. An apache2 module is available at http://celebnamer.celebworld.ws/stuff/mod_xsendfile/
	*
	* Not really ideal, but should work fine...
	* <code>
	*	if (strpos($upload_dir, '/') !== 0 && strpos($upload_dir, '../') === false)
	*	{
	*		header('X-Sendfile: ' . $filename);
	*	}
	* </code>
	*/

	if($forum_astracker && $attachment['extension']=='torrent')
	{
		$attachment['mimetype']=$t_mtype;

		$tracker_url=generate_board_url();
		if(!$config['ppkbb_announce_url'])
		{
			$config['ppkbb_announce_url']='/tracker/announce.'.$phpEx;
		}
		if(!$user->data['is_registered'])
		{
			if($config['ppkbb_tcguests_enabled'][0])
			{
				if($config['ppkbb_gtccleanup_interval'] && $dt - $config['ppkbb_gtracker_last_cleanup'] > $config['ppkbb_gtccleanup_interval'])
				{
					$sql_addon=$config['ppkbb_gtcsession_expire'][0] ? $dt.'-user_time > '.$config['ppkbb_gtcsession_expire'][0] : '';
					$config['ppkbb_gtcsession_expire'][1] && $sql_addon ? $sql_addon.=' OR ' : '';
					$config['ppkbb_gtcsession_expire'][1] ? $sql_addon.=$dt.'-user_last_time > '.$config['ppkbb_gtcsession_expire'][1] : '';
					if($sql_addon)
					{
						$result=$db->sql_query("DELETE FROM ".TRACKER_GUESTS_TABLE." WHERE {$sql_addon}");
					}
					set_config('ppkbb_gtracker_last_cleanup', $dt, 1);
					//purge_tracker_config(true);
				}

				$config['ppkbb_announce_url']='/tracker/announce2.'.$phpEx;
				$user_passkey=$user->data['user_passkey']=$user->session_id;
				$config['ppkbb_rtrack_enable'][1]=0;

				$result=$db->sql_query("SELECT user_id FROM ".TRACKER_GUESTS_TABLE." WHERE session_id='".$db->sql_escape($user_passkey)."' LIMIT 1");
				$guest_user_id=$db->sql_fetchrow($result);
				$guest_user_id=$guest_user_id['user_id'];
				if(!$guest_user_id)
				{
					if($config['ppkbb_gtcmax_sessions'])
					{
						$result=$db->sql_query("SELECT DISTINCT(COUNT(session_id)) sessions FROM ".TRACKER_GUESTS_TABLE."");
						$sess_count=$db->sql_fetchrow($result);
						$db->sql_freeresult($result);
						if(intval($sess_count['sessions']) >= $config['ppkbb_gtcmax_sessions'])
						{
							trigger_error($user->lang['TRACKER_GUESTSSESS_LIMIT']);
						}
					}
					$sql="INSERT INTO ".TRACKER_GUESTS_TABLE." (session_id, user_ip) VALUES('".$db->sql_escape($user_passkey)."', INET_ATON('".$db->sql_escape($user->ip)."'))";
					$result=$db->sql_query($sql);
					$guest_user_id=$db->sql_nextid();
					}
				if(!$guest_user_id || $config['ppkbb_addit_options'][1]==2 || !$config['ppkbb_addit_options'][1])
				{
				}
				else
				{
					$sql='SELECT id, dl_ip FROM '.TRACKER_DOWNLOADS_TABLE." WHERE dl_time='".date('Ymd', $dt)."' AND downloader_id='{$user->data['user_id']}' AND attach_id='{$attachment['attach_id']}' LIMIT 1";
					$result=$db->sql_query($sql);
					$row=$db->sql_fetchrow($result);
					$db->sql_freeresult($result);

					if(!$row || (!$user->data['is_registered'] && $row['dl_ip']!=$user->ip))
					{
						$sql="INSERT INTO ".TRACKER_DOWNLOADS_TABLE."(downloader_id, dl_time, dl_ip, attach_id, post_msg_id, guests, dl_date) VALUES('1', '{$dt}', '".$db->sql_escape($user->ip)."', '{$attachment['attach_id']}', '{$attachment['post_msg_id']}', '1', '".date('Ymd', $dt)."')";
						$result=$db->sql_query($sql);
					}
				}
			}
			else
			{
				trigger_error('TRGUESTS_DISABLED');
			}
		}
		else
		{
			if(!$config['ppkbb_addit_options'][1] || $user->data['user_id']==$attachment['poster_id'])
		{
			}
			else
			{
				$sql='SELECT id, dl_ip FROM '.TRACKER_DOWNLOADS_TABLE." WHERE dl_time='".date('Ymd', $dt)."' AND downloader_id='{$user->data['user_id']}' AND attach_id='{$attachment['attach_id']}' LIMIT 1";
				$result=$db->sql_query($sql);
				$row=$db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				if(!$row || (!$user->data['is_registered'] && $row['dl_ip']!=$user->ip))
				{
					$sql="INSERT INTO ".TRACKER_DOWNLOADS_TABLE."(downloader_id, dl_time, dl_ip, attach_id, post_msg_id, dl_date) VALUES('{$user->data['user_id']}', '{$dt}', '".$db->sql_escape($user->ip)."', '{$attachment['attach_id']}', '{$attachment['post_msg_id']}', '".date('Ymd', $dt)."')";
			$result=$db->sql_query($sql);
		}
	}
	}

}

	// Send out the Headers. Do not set Content-Disposition to inline please, it is a security measure for users using the Internet Explorer.
	header('Content-Type: ' . $attachment['mimetype']);

	if (phpbb_is_greater_ie_version($user->browser, 7))
	{
		header('X-Content-Type-Options: nosniff');
	}

	if ($category == ATTACHMENT_CATEGORY_FLASH && request_var('view', 0) === 1)
	{
		// We use content-disposition: inline for flash files and view=1 to let it correctly play with flash player 10 - any other disposition will fail to play inline
		header('Content-Disposition: inline');
	}
	else
	{
		if (empty($user->browser) || ((strpos(strtolower($user->browser), 'msie') !== false) && !phpbb_is_greater_ie_version($user->browser, 7)))
		{
			header('Content-Disposition: attachment; ' . header_filename(htmlspecialchars_decode($attachment['real_filename'])));
			if (empty($user->browser) || (strpos(strtolower($user->browser), 'msie 6.0') !== false))
			{
				header('expires: -1');
			}
		}
		else
		{
			header('Content-Disposition: ' . ((strpos($attachment['mimetype'], 'image') === 0) ? 'inline' : 'attachment') . '; ' . header_filename(htmlspecialchars_decode($attachment['real_filename'])));
			$forum_astracker && $attachment['extension']=='torrent' ? $as_tracker=1 : $as_tracker=0;
			if (phpbb_is_greater_ie_version($user->browser, 7) && (strpos($attachment['mimetype'], 'image') !== 0) && !$as_tracker)
			{
				header('X-Download-Options: noopen');
			}
		}
	}

	if($forum_astracker && $attachment['extension']=='torrent')
	{
		// Try to deliver in chunks
		@set_time_limit(0);

		include_once($phpbb_root_path."tracker/include/bencoding.$phpEx");

		$dict = bdecode_f($filename, $size);
		$dict['comment']=$dict['comment.utf-8']="{$user->lang['TORRENT_CREATED_FOR']}: {$config['server_name']}\r\n"
			."{$user->lang['TORRENT_SUBJECT']}: {$tracker_url}/viewtopic.{$phpEx}?p={$attachment['post_msg_id']}#p{$attachment['post_msg_id']}\r\n"
			."{$user->lang['AUTHOR']}: {$attachment['username']} {$tracker_url}/memberlist.$phpEx?mode=viewprofile&u={$attachment['poster_id']}\r\n"
			."{$user->lang['DOWNLOAD_FOR']}: {$user->data['username']} {$tracker_url}/memberlist.$phpEx?mode=viewprofile&u={$user->data['user_id']}"; // change torrent comment
		$dict['publisher-url']=$dict['publisher-url.utf-8']="{$tracker_url}/viewtopic.{$phpEx}?p={$attachment['post_msg_id']}#p{$attachment['post_msg_id']}"; // change publisher-url
		$dict['announce'] = $tracker_url.$config['ppkbb_announce_url']."?passkey={$user_passkey}";

		if($config['ppkbb_rtrack_enable'][0] || $config['ppkbb_rtrack_enable'][1])
		{
			//unset($dict['announce']);
			require($phpbb_root_path.'tracker/include/rtrackfunc.'.$phpEx);
			$forb_rtrack=get_forb_rtrack();
			$rtrack=get_rtrack($user->ip, $config['ppkbb_rtrack_enable'][0], $config['ppkbb_rtrack_enable'][1], $attachment['attach_id'], $forb_rtrack);
			@$dict['announce-list']=benc_rtrack_url($rtrack, $config['ppkbb_rtrack_enable'][2]);
			/*if($config['ppkbb_rtrack_enable'][2])
			{
				unset($dict['announce']);
				$dict['announce']='';
			}*/
		}

		/*if(!$config['ppkbb_rtrack_enable'][0])
		{
			if($config['ppkbb_tprivate_flag']!=0)
			{
				//$dict['info']['source']="{$tracker_url} [{$config['server_name']}]"; // add link for bitcomet users
				unset($dict['info']['crc32']); // remove crc32
				unset($dict['info']['ed2k']); // remove ed2k
				unset($dict['info']['md5sum']); // remove md5sum
				unset($dict['info']['sha1']); // remove sha1
				unset($dict['info']['tiger']); // remove tiger
			}
		}*/
		// Close the db connection before sending the file
	 	$db->sql_close();

	 	$enc_file=bencode($dict);
	 	$size=strlen($enc_file);
		if (!set_modified_headers($attachment['filetime'], $user->browser))
	  	{

		if ($size)
		{
			header("Content-Length: $size");
		}

			print $enc_file;
	 		flush();
	 	}
	 	file_gc();
	}
	else
	{

	 	// Close the db connection before sending the file
	 	$db->sql_close();

	 	if (!set_modified_headers($attachment['filetime'], $user->browser))
	  	{
			// Send Content-Length only if set_modified_headers() does not send
			// status 304 - Not Modified
			if ($size)
			{
				header("Content-Length: $size");
			}
	 		// Try to deliver in chunks
	 		@set_time_limit(0);

	 		$fp = @fopen($filename, 'rb');

	 		if ($fp !== false)
	  		{
	 			while (!feof($fp))
	 			{
	 				echo fread($fp, 8192);
	 			}
	 			fclose($fp);
	 		}
	 		else
	 		{
	 			@readfile($filename);
	  		}

	 		flush();
	 	}
	 	file_gc();

	}

}

/**
* Get a browser friendly UTF-8 encoded filename
*/
function header_filename($file)
{
	$user_agent = (!empty($_SERVER['HTTP_USER_AGENT'])) ? htmlspecialchars((string) $_SERVER['HTTP_USER_AGENT']) : '';

	// There be dragons here.
	// Not many follows the RFC...
	if (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Safari') !== false || strpos($user_agent, 'Konqueror') !== false)
	{
		return "filename=" . rawurlencode($file);
	}

	// follow the RFC for extended filename for the rest
	return "filename*=UTF-8''" . rawurlencode($file);
}

/**
* Check if downloading item is allowed
*/
function download_allowed()
{
	global $config, $user, $db;

	if (!$config['secure_downloads'])
	{
		return true;
	}

	$url = (!empty($_SERVER['HTTP_REFERER'])) ? trim($_SERVER['HTTP_REFERER']) : trim(getenv('HTTP_REFERER'));

	if (!$url)
	{
		return ($config['secure_allow_empty_referer']) ? true : false;
	}

	// Split URL into domain and script part
	$url = @parse_url($url);

	if ($url === false)
	{
		return ($config['secure_allow_empty_referer']) ? true : false;
	}

	$hostname = $url['host'];
	unset($url);

	$allowed = ($config['secure_allow_deny']) ? false : true;
	$iplist = array();

	if (($ip_ary = @gethostbynamel($hostname)) !== false)
	{
		foreach ($ip_ary as $ip)
		{
			if ($ip)
			{
				$iplist[] = $ip;
			}
		}
	}

	// Check for own server...
	$server_name = $user->host;

	// Forcing server vars is the only way to specify/override the protocol
	if ($config['force_server_vars'] || !$server_name)
	{
		$server_name = $config['server_name'];
	}

	if (preg_match('#^.*?' . preg_quote($server_name, '#') . '.*?$#i', $hostname))
	{
		$allowed = true;
	}

	// Get IP's and Hostnames
	if (!$allowed)
	{
		$sql = 'SELECT site_ip, site_hostname, ip_exclude
			FROM ' . SITELIST_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$site_ip = trim($row['site_ip']);
			$site_hostname = trim($row['site_hostname']);

			if ($site_ip)
			{
				foreach ($iplist as $ip)
				{
					if (preg_match('#^' . str_replace('\*', '.*?', preg_quote($site_ip, '#')) . '$#i', $ip))
					{
						if ($row['ip_exclude'])
						{
							$allowed = ($config['secure_allow_deny']) ? false : true;
							break 2;
						}
						else
						{
							$allowed = ($config['secure_allow_deny']) ? true : false;
						}
					}
				}
			}

			if ($site_hostname)
			{
				if (preg_match('#^' . str_replace('\*', '.*?', preg_quote($site_hostname, '#')) . '$#i', $hostname))
				{
					if ($row['ip_exclude'])
					{
						$allowed = ($config['secure_allow_deny']) ? false : true;
						break;
					}
					else
					{
						$allowed = ($config['secure_allow_deny']) ? true : false;
					}
				}
			}
		}
		$db->sql_freeresult($result);
	}

	return $allowed;
}

/**
* Check if the browser has the file already and set the appropriate headers-
* @returns false if a resend is in order.
*/
function set_modified_headers($stamp, $browser)
{
	// let's see if we have to send the file at all
	$last_load 	=  isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime(trim($_SERVER['HTTP_IF_MODIFIED_SINCE'])) : false;
	if (strpos(strtolower($browser), 'msie 6.0') === false && !phpbb_is_greater_ie_version($browser, 7))
	{
		if ($last_load !== false && $last_load >= $stamp)
		{
			send_status_line(304, 'Not Modified');
			// seems that we need those too ... browsers
			header('Pragma: public');
			header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
			return true;
		}
		else
		{
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $stamp) . ' GMT');
		}
	}
	return false;
}

function file_gc()
{
	global $cache, $db;
	if (!empty($cache))
	{
		$cache->unload();
	}
	$db->sql_close();
	exit;
}

/**
* Check if the browser is internet explorer version 7+
*
* @param string $user_agent	User agent HTTP header
* @param int $version IE version to check against
*
* @return bool true if internet explorer version is greater than $version
*/
function phpbb_is_greater_ie_version($user_agent, $version)
{
	if (preg_match('/msie (\d+)/', strtolower($user_agent), $matches))
	{
		$ie_version = (int) $matches[1];
		return ($ie_version > $version);
	}
	else
	{
		return false;
	}
}

?>
