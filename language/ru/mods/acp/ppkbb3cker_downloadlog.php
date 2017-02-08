<?php
/**
*
* common [Russian]
*
* @package ppkBB3cker
* @version $Id: ppkbb3cker_acheats.php, v 1.000 2009-12-23 19:25:00 PPK Exp $
* @copyright (c) 2009 PPK
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
	'ACP_TRACKER_DOWNLOADLOG'				=> 'Лог скачиваний торрент файлов',
	'ACP_TRACKER_DOWNLOADLOG_EXPLAIN'				=> 'Лог скачиваний торрент файлов',
	'ACP_TRACKER_DOWNLOADLOG_SETTINGS'				=> 'Лог скачиваний торрент файлов',


	'SORT_TTITLE' => 'Торрент',
	'SORT_FILENAME' => 'Имя файла',

	'TOTAL_LOGS' => 'Всего записей: <strong>%d</strong>',
	'TRACKER_ANONYMOUS' => 'Гость',
));

?>
