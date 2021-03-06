<?php
/**
*
* @package ucp
* @version $Id$
* @copyright (c) 2005 phpBB Group
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

/**
* ucp_attachments
* User attachments
* @package ucp
*/
class ucp_attachments
{
	var $u_action;

	function main($id, $mode)
	{
		global $template, $user, $db, $config, $phpEx, $phpbb_root_path;

		$start		= request_var('start', 0);
		$sort_key	= request_var('sk', 'a');
		$sort_dir	= request_var('sd', 'a');

		$delete		= (isset($_POST['delete'])) ? true : false;
		$confirm	= (isset($_POST['confirm'])) ? true : false;
		$delete_ids	= array_keys(request_var('attachment', array(0)));

		$delete_trfiles=array();

		if ($delete && sizeof($delete_ids))
		{
			// Validate $delete_ids...
			$sql = 'SELECT attach_id, post_msg_id, f.forumas, t.topic_first_post_id, p.post_id
				FROM ' . ATTACHMENTS_TABLE . ' a
				LEFT JOIN ' . TOPICS_TABLE . ' t ON (a.topic_id = t.topic_id AND a.in_message = 0)
				LEFT JOIN ' . FORUMS_TABLE . ' f ON (f.forum_id = t.forum_id)
				LEFT JOIN '. POSTS_TABLE . ' p ON (p.post_id = a.post_msg_id)
				WHERE a.poster_id = ' . $user->data['user_id'] . '
					AND is_orphan = 0
					AND ' . $db->sql_in_set('attach_id', $delete_ids);
			$result = $db->sql_query($sql);

			$delete_ids = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$delete_ids[$row['attach_id']] = $row['attach_id'];

				$delete_trfiles[]=$row;

			}
			$db->sql_freeresult($result);
		}

		if($config['ppkbb_addit_options'][0] && $delete && sizeof($delete_ids))
		{
			foreach($delete_trfiles as $k => $v)
			{
				if($v['forumas']==1 && $v['topic_first_post_id']==$v['post_id'])
				{
					unset($delete_ids[$v['attach_id']]);
				}
			}
		}
		unset($delete_trfiles);

		if ($delete && sizeof($delete_ids))
		{
			$s_hidden_fields = array(
				'delete'	=> 1
			);

			foreach ($delete_ids as $attachment_id)
			{
				$s_hidden_fields['attachment'][$attachment_id] = 1;
			}

			if (confirm_box(true))
			{
				if (!function_exists('delete_attachments'))
				{
					include_once($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
				}

				delete_attachments('attach', $delete_ids);

				meta_refresh(3, $this->u_action);
				$message = ((sizeof($delete_ids) == 1) ? $user->lang['ATTACHMENT_DELETED'] : $user->lang['ATTACHMENTS_DELETED']) . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>');
				trigger_error($message);
			}
			else
			{
				confirm_box(false, (sizeof($delete_ids) == 1) ? 'DELETE_ATTACHMENT' : 'DELETE_ATTACHMENTS', build_hidden_fields($s_hidden_fields));
			}
		}

		// Select box eventually
		$sort_key_text = array('a' => $user->lang['SORT_FILENAME'], 'b' => $user->lang['SORT_COMMENT'], 'c' => $user->lang['SORT_EXTENSION'], 'd' => $user->lang['SORT_SIZE'], 'e' => $user->lang['SORT_DOWNLOADS'], 'f' => $user->lang['SORT_POST_TIME'], 'g' => $user->lang['SORT_TOPIC_TITLE']);
		$sort_key_sql = array('a' => 'a.real_filename', 'b' => 'a.attach_comment', 'c' => 'a.extension', 'd' => 'a.filesize', 'e' => 'a.download_count', 'f' => 'a.filetime', 'g' => 't.topic_title');

		$sort_dir_text = array('a' => $user->lang['ASCENDING'], 'd' => $user->lang['DESCENDING']);

		$s_sort_key = '';
		foreach ($sort_key_text as $key => $value)
		{
			$selected = ($sort_key == $key) ? ' selected="selected"' : '';
			$s_sort_key .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
		}

		$s_sort_dir = '';
		foreach ($sort_dir_text as $key => $value)
		{
			$selected = ($sort_dir == $key) ? ' selected="selected"' : '';
			$s_sort_dir .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
		}

		if (!isset($sort_key_sql[$sort_key]))
		{
			$sort_key = 'a';
		}

		$order_by = $sort_key_sql[$sort_key] . ' ' . (($sort_dir == 'a') ? 'ASC' : 'DESC');

		$sql = 'SELECT COUNT(attach_id) as num_attachments
			FROM ' . ATTACHMENTS_TABLE . '
			WHERE poster_id = ' . $user->data['user_id'] . '
				AND is_orphan = 0';
		$result = $db->sql_query($sql);
		$num_attachments = $db->sql_fetchfield('num_attachments');
		$db->sql_freeresult($result);

		$sql = 'SELECT a.*, t.topic_title, p.message_subject as message_title, f.forumas, t.topic_first_post_id, pp.post_id
			FROM ' . ATTACHMENTS_TABLE . ' a
				LEFT JOIN ' . TOPICS_TABLE . ' t ON (a.topic_id = t.topic_id AND a.in_message = 0)
				LEFT JOIN ' . PRIVMSGS_TABLE . ' p ON (a.post_msg_id = p.msg_id AND a.in_message = 1)
				LEFT JOIN ' . FORUMS_TABLE . ' f ON (f.forum_id = t.forum_id)
				LEFT JOIN '. POSTS_TABLE . ' pp ON (pp.post_id = a.post_msg_id)
			WHERE a.poster_id = ' . $user->data['user_id'] . "
				AND a.is_orphan = 0
			ORDER BY $order_by";
		$result = $db->sql_query_limit($sql, $config['topics_per_page'], $start);

		$row_count = 0;
		if ($row = $db->sql_fetchrow($result))
		{
			$template->assign_var('S_ATTACHMENT_ROWS', true);

			do
			{
				if ($row['in_message'])
				{
					$view_topic = append_sid("{$phpbb_root_path}ucp.$phpEx", "i=pm&amp;p={$row['post_msg_id']}");
				}
				else
				{
					$view_topic = append_sid("{$phpbb_root_path}viewtopic.$phpEx", "t={$row['topic_id']}&amp;p={$row['post_msg_id']}") . "#p{$row['post_msg_id']}";
				}

				$unreg_torr=0;
				if($row['forumas']==1 && $row['topic_first_post_id']==$row['post_id'] && $row['extension']=='torrent' && !$row['post_msg_id'] && !$row['topic_id'] && !$row['in_message'])
				{
					$unreg_torr=1;
					$view_topic='javascript:;';
					$row['topic_title']=$user->lang['TORRENT_UNREGISTERED'];
					$row['real_filename']=$user->lang['TORRENT_UNREGISTERED'];
				}


				$template->assign_block_vars('attachrow', array(

					'DISABLE_DELETE' => !$config['ppkbb_addit_options'] && $row['forumas']==1 && $row['topic_first_post_id']==$row['post_id'] ? true : false,

					'ROW_NUMBER'		=> $row_count + ($start + 1),
					'FILENAME'			=> $row['real_filename'],
					'COMMENT'			=> bbcode_nl2br($row['attach_comment']),
					'EXTENSION'			=> $row['extension'],
					'SIZE'				=> get_formatted_filesize($row['filesize']),
					'DOWNLOAD_COUNT'	=> $row['download_count'],
					'POST_TIME'			=> $user->format_date($row['filetime']),
					'TOPIC_TITLE'		=> ($row['in_message']) ? $row['message_title'] : $row['topic_title'],

					'ATTACH_ID'			=> $row['attach_id'],
					'POST_ID'			=> $row['post_msg_id'],
					'TOPIC_ID'			=> $row['topic_id'],

					'S_IN_MESSAGE'		=> $row['in_message'],

					'U_VIEW_ATTACHMENT'	=> $row['i_external'] ? $row['real_filename'] : ($unreg_torr ? $view_topic : append_sid("{$phpbb_root_path}download/file.$phpEx", 'id=' . $row['attach_id'])),
					'U_VIEW_TOPIC'		=> $view_topic)
				);

				$row_count++;
			}
			while ($row = $db->sql_fetchrow($result));
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'PAGE_NUMBER'			=> on_page($num_attachments, $config['topics_per_page'], $start),
			'PAGINATION'			=> generate_pagination($this->u_action . "&amp;sk=$sort_key&amp;sd=$sort_dir", $num_attachments, $config['topics_per_page'], $start),
			'TOTAL_ATTACHMENTS'		=> $num_attachments,

			'L_TITLE'				=> $user->lang['UCP_ATTACHMENTS'],

			'U_SORT_FILENAME'		=> $this->u_action . "&amp;sk=a&amp;sd=" . (($sort_key == 'a' && $sort_dir == 'a') ? 'd' : 'a'),
			'U_SORT_FILE_COMMENT'	=> $this->u_action . "&amp;sk=b&amp;sd=" . (($sort_key == 'b' && $sort_dir == 'a') ? 'd' : 'a'),
			'U_SORT_EXTENSION'		=> $this->u_action . "&amp;sk=c&amp;sd=" . (($sort_key == 'c' && $sort_dir == 'a') ? 'd' : 'a'),
			'U_SORT_FILESIZE'		=> $this->u_action . "&amp;sk=d&amp;sd=" . (($sort_key == 'd' && $sort_dir == 'a') ? 'd' : 'a'),
			'U_SORT_DOWNLOADS'		=> $this->u_action . "&amp;sk=e&amp;sd=" . (($sort_key == 'e' && $sort_dir == 'a') ? 'd' : 'a'),
			'U_SORT_POST_TIME'		=> $this->u_action . "&amp;sk=f&amp;sd=" . (($sort_key == 'f' && $sort_dir == 'a') ? 'd' : 'a'),
			'U_SORT_TOPIC_TITLE'	=> $this->u_action . "&amp;sk=g&amp;sd=" . (($sort_key == 'g' && $sort_dir == 'a') ? 'd' : 'a'),

			'S_DISPLAY_MARK_ALL'	=> ($num_attachments) ? true : false,
			'S_DISPLAY_PAGINATION'	=> ($num_attachments) ? true : false,
			'S_UCP_ACTION'			=> $this->u_action,
			'S_SORT_OPTIONS' 		=> $s_sort_key,
			'S_ORDER_SELECT'		=> $s_sort_dir)
		);

		$this->tpl_name = 'ucp_attachments';
		$this->page_title = 'UCP_ATTACHMENTS';
	}
}

?>
