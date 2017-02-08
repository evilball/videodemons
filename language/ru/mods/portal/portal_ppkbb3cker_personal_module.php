<?php
/**
*
* @package Board3 Portal v2 - Personal
* @copyright (c) PPK 2011
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
	'PORTAL_PPKBB3CKER_PERSONAL'		=> 'Персональный блок',

	// ACP
	'ACP_PORTAL_PERSONAL_SETTINGS'			=> 'Настройки персонального блока',
	'ACP_PORTAL_PERSONAL_SETTINGS_EXP'		=> 'Здесь можно настроить персональный блок',

	'ACP_PORTAL_PERSONAL_PHPTPL' => 'Имя подключаемого php файла',
	'ACP_PORTAL_PERSONAL_PHPTPL_EXP' => 'Файл должен находиться в директории /portal/modules/ (расширение файла указывать не нужно)',
	'ACP_PORTAL_PERSONAL_HTMLTPL' => 'Имя подключаемого html файла шаблона',
	'ACP_PORTAL_PERSONAL_HTMLTPL_EXP' => 'Файл должен находиться в директории /styles/имя_стиля/template/portal/modules/ (расширение файла указывать не нужно)',
	'ACP_PORTAL_PERSONAL_LANGTPL' => 'Имя подключаемого языкового файла',
	'ACP_PORTAL_PERSONAL_LANGTPL_EXP' => 'Файл должен находиться в директории /language/язык/mods/portal/ (расширение файла указывать не нужно)',

	'ACP_PORTAL_PERSONAL_PERMISSION'			=> 'Права доступа к персональному блоку',
	'ACP_PORTAL_PERSONAL_PERMISSION_EXP'		=> 'Выберите группы, которым разрешено видеть персональный блок. Оставьте поле пустым для отображения всем пользователям.<br />Можно выбрать несколько групп, удерживая <samp>CTRL</samp>.',

	'ACP_PORTAL_PERSONAL_HTMLTPL_EMPTY' => 'Не указан файл html шаблона',
	'ACP_PORTAL_PERSONAL_HTMLTPL_NOTEXISTS' => 'Файл html шаблона не существует',
));
