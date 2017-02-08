<?php
/**
*
* @package ppkBB3cker
* @version $Id: tattach.php 1.000 2009-01-26 13:50:00 PPK $
* @copyright (c) 2008 PPK
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

define('EXTENSION_GROUPS_TABLE', $table_prefix . 'extension_groups');
define('EXTENSIONS_TABLE', $table_prefix . 'extensions');

if(!obtain_attach_extensions($forum_id, $attachment['extension']))
{
	err('Extension disabled in this forum');
}

//From includes/cache.php
function obtain_attach_extensions($forum_id, $extension)
{
	$extensions = array(
		'_allowed_post'	=> array(),
		'_allowed_pm'	=> array(),
	);

	// The rule is to only allow those extensions defined. ;)
	$sql = 'SELECT e.extension, g.*
			FROM ' . EXTENSIONS_TABLE . ' e, ' . EXTENSION_GROUPS_TABLE . ' g
			WHERE e.group_id = g.group_id
				AND (g.allow_group = 1 OR g.allow_in_pm = 1)';
	$result = my_sql_query($sql);

	while ($row = mysql_fetch_array($result))
	{
		$extension = strtolower(trim($row['extension']));

		$extensions[$extension] = array(
			'display_cat'	=> (int) $row['cat_id'],
			'download_mode'	=> (int) $row['download_mode'],
			'upload_icon'	=> trim($row['upload_icon']),
			'max_filesize'	=> (int) $row['max_filesize'],
			'allow_group'	=> $row['allow_group'],
			'allow_in_pm'	=> $row['allow_in_pm'],
		);

		$allowed_forums = ($row['allowed_forums']) ? unserialize(trim($row['allowed_forums'])) : array();

		// Store allowed extensions forum wise
		if ($row['allow_group'])
		{
			$extensions['_allowed_post'][$extension] = (!sizeof($allowed_forums)) ? 0 : $allowed_forums;
		}

		if ($row['allow_in_pm'])
		{
			$extensions['_allowed_pm'][$extension] = 0;
		}
	}
	mysql_free_result($result);

	$forum_id = (int) $forum_id;
	$return = array('_allowed_' => array());

	foreach ($extensions['_allowed_post'] as $extension => $check)
	{
		// Check for allowed forums
		if (is_array($check))
		{
			$allowed = (!in_array($forum_id, $check)) ? false : true;
		}
		else
		{
			$allowed = true;
		}

		if ($allowed)
		{
			$return['_allowed_'][$extension] = 0;
			$return[$extension] = $extensions[$extension];
		}
	}
	$extensions = $return;

	if (!isset($extensions['_allowed_']))
	{
		$extensions['_allowed_'] = array();
	}

	return (!isset($extensions['_allowed_'][$extension])) ? false : true;
}
?>
