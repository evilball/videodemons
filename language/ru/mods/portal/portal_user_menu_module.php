<?php
/**
*
* @package Board3 Portal v2 - User Menu
* @copyright (c) Board3 Group ( www.board3.de )
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
	'USER_MENU'			=> 'Личное меню',
	'UM_LOG_ME_IN'		=> 'Запомнить меня',
	'UM_HIDE_ME'		=> 'Скрыть меня',
	'UM_REGISTER_NOW'	=> 'Зарегистрироваться!',
	'UM_MAIN_SUBSCRIBED'=> 'Подписки',
	'UM_BOOKMARKS'		=> 'Закладки',
	'M_MENU' 			=> 'Меню',
	'M_ACP'				=> 'Администраторский раздел',
	'USER_MENU_SETTINGS'	=> 'Настройки личного меню',
	'USER_MENU_REGISTER'	=> 'Показывать ссылку на регистрацию в личном меню',
));