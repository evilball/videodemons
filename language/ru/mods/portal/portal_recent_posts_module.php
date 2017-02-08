<?php
/**
*
* @package Board3 Portal v2 - Recent
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
	'PORTAL_RECENT_POSTS'		=> 'Последние сообщения',
	
	// ACP
	'ACP_PORTAL_RECENT_SETTINGS'				=> 'Настройка последних сообщений',
	'ACP_PORTAL_RECENT_SETTINGS_EXP'			=> 'Здесь настраивается модуль последние сообщений',
	'PORTAL_MAX_TOPIC'							=> 'Тем в блоке:',
	'PORTAL_MAX_TOPIC_EXP'						=> '0 - без ограничений',
	'PORTAL_RECENT_TITLE_LIMIT'					=> 'Максимальное количество символов в названии темы (остальные обрезаются)',
	'PORTAL_RECENT_TITLE_LIMIT_EXP'				=> '0 - без ограничений',
	'PORTAL_RECENT_FORUM'						=> 'Форумы "последних сообщений"',
	'PORTAL_RECENT_FORUM_EXP'					=> 'Форумы, откуда выбираются последние сообщения.<br />Если в пункте "Исключить" выбрано значение "Да", укажите форумы, темы которых хотите исключить.<br />Если в пункте "Исключить" выбрано значение "Нет", укажите форумы, темы которых хотите видеть в блоке последних сообщений.<br />Можно выбрать несколько форумов, удерживая <samp>CTRL</samp>.',
	'PORTAL_EXCLUDE_FORUM'						=> 'Исключить форумы',
	'PORTAL_EXCLUDE_FORUM_EXP'					=> 'Выберите "Да", если хотите исключить темы из выбранных форумов в блоке последних сообщений, и выберите "Нет", если хотите, чтобы темы из выбранных форумов отображались в этом блоке.',
));
