<?php
/**
*
* common [Russian]
*
* @package ppkBB3cker
* @version $Id: ppkbb3cker_losttorrents.php, v 1.000 2015-10-01 10:34:33 PPK Exp $
* @copyright (c) 2015 PPK
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
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//


$lang = array_merge($lang, array(
	'ACP_TRACKER_LOSTTORRENTS'				=> 'Потерянные торренты',
	'ACP_TRACKER_LOSTTORRENTS_EXPLAIN'				=> 'Потерянные торренты',
	'ACP_TRACKER_LOSTTORRENTS_SETTINGS'				=> 'Потерянные торренты',


	'SORT_TTITLE' => 'Торрент',
	'SORT_FILENAME' => 'Имя файла',

	'FIX_LOSTTORRENTS_RESULT' => '%s<br /><a href="%s">Вернуться назад</a>',
	'FIX_LOSTTORRENTS_FINISH' => 'Завершено<br /><br /><a href="%s">Вернуться назад</a>',
	'FIX_LOSTTORRENTS_SUCCESS' => 'Успех: ',
	'FIX_LOSTTORRENTS_ERROR' => '<span style="color:#FF0000;">Ошибка</span>: ',
	'FIX_LOSTTORRENTS_WAIT' => 'Подождите ..<br /><br />',

	'TRACKER_ANONYMOUS' => 'Гость',

	'TOTAL_LOGS' => 'Всего записей: <strong>%d</strong>',
));

?>
