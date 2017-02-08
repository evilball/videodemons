<?php
/**
*
* acp_board [Russian]
*
* @package language
* @version $Id: ppkbb3cker_torrents.php, v 1.000 2014-09-19 16:22:34 PPK Exp $
* @copyright (c) 2014 PPK
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'DEL_USER_TORRENTS_WARN_FINISHED' => 'Удаление статистики пользователя по скачанным торрентам',
	'DEL_USER_TORRENTS_WARN_SEED' => 'Удаление записей пользователя по раздаваемым торрентам',
	'DEL_USER_TORRENTS_WARN_LEECH' => 'Удаление записей пользователя по скачиваемым торрентам',
	'DEL_USER_TORRENTS_WARN_HISTORY' => 'Удаление статистики пользователя по торрентам',
	'DEL_USER_TORRENTS_WARN_LEAVE' => 'Удаление статистики пользователя по торрентам которые он не раздаёт',
	'DEL_USER_TORRENTS_WARN_SEEDREQ' => 'Удаление записей о сделанных пользователем запросах сидеров',
	'DEL_USER_TORRENTS_WARN_DOWNLOADS' => 'Удаление записей о скачанных пользователем торрент файлах',
	'DEL_USER_TORRENTS_WARN_TORRENT' => 'Удаление записей о загруженных пользователем торрентах',

	'DEL_USER_TORRENTS_WARN_TOTHANKS' => 'Удаление записей о полученных пользователем спасибо',
	'DEL_USER_TORRENTS_WARN_FROMTHANKS' => 'Удаление записей о сказанных пользователем спасибо',
	'DEL_USER_TORRENTS_WARN_FROMSEEDREQ' => 'Удаление записей о сделанных пользователем запросах сидеров',
));
?>
