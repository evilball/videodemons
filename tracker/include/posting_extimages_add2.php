<?php
/**
*
* @package ppkBB3cker
* @version $Id: posting_extimages_add2.php 1.000 2011-11-30 20:53:42 PPK $
* @copyright (c) 2011 PPK
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

if($is_submitted)
{
	$post_id ? '' : $post_id=$data['post_id'];
	$topic_id ? '' : $topic_id=$data['topic_id'];
	$db->sql_query("DELETE FROM ".ATTACHMENTS_TABLE." WHERE (topic_id='{$topic_id}' OR post_msg_id='{$post_id}') AND i_external='1'");
	$external_posters=sizeof($exs_posters);
	$external_screenshots=sizeof($exs_screenshots);
	if($external_posters)
	{
		foreach($exs_posters as $v)
		{
			$v['post_msg_id']=$post_id;
			$v['topic_id']=$topic_id;
			$v['poster_id']=$user->data['user_id'];
			$db->sql_query("INSERT INTO ".ATTACHMENTS_TABLE." (real_filename, i_width, i_height, extension, mimetype, i_poster, i_external, attach_comment, filetime, is_orphan, post_msg_id, topic_id, poster_id) VALUES('".(implode("', '", array_map('addslashes', $v)))."')");
		}
	}
	if($external_screenshots)
	{
		foreach($exs_screenshots as $v)
		{
			$v['post_msg_id']=$post_id;
			$v['topic_id']=$topic_id;
			$v['poster_id']=$user->data['user_id'];
			$db->sql_query("INSERT INTO ".ATTACHMENTS_TABLE." (real_filename, i_width, i_height, extension, mimetype, i_poster, i_external, attach_comment, filetime, is_orphan, post_msg_id, topic_id, poster_id) VALUES('".(implode("', '", array_map('addslashes', $v)))."')");
		}
	}
	if($external_posters || $external_screenshots)
	{
		$db->sql_query("UPDATE ".POSTS_TABLE." SET post_attachment='1' WHERE post_id='{$post_id}'");
	}
}

?>
