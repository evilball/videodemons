<?php
/**
*
* acp_board [Russian]
*
* @package language
* @version $Id: ppkbb3cker_statuses.php, v 1.000 2012-06-14 12:07:57 PPK Exp $
* @copyright (c) 2012 PPK
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
	'ACP_TRACKER_STATUSES' => 'Статусы торрентов',
	'ACP_TRACKER_STATUSES_EXPLAIN' => 'Управление статусами торрентов.',

	'ACP_TRACKER_STATUSES_SETTINGS' => 'Общие настройки',
	'STATUS' => 'Статус',
	'STATUS_ID' => 'ID',
	'STATUS_REASON' => 'Название статуса',
	'STATUS_REASON_EXPLAIN' => ' Название статуса торрента может быть как текстовой строкой так и языковой переменной (не используйте HTML код), поле обязательно для заполнения',
	'STATUS_MARK' => 'Описание статуса',
	'STATUS_MARK_EXPLAIN' => 'Описание статуса торрента может быть как текстовой строкой так и языковой переменной (можно использовать HTML код)',
	'STATUS_ENABLED' => 'Включён',
	'STATUS_CANDOWN' => 'Определение возможности скачивания торрент файла для автора торрента или невозможности для гостя (при включении опции для автора: для автора будет доступна возможность скачать торрент если он имеет статус запрещающий скачивание, при включении опции для гостя: для гостя будет доступна возможность скачать торрент только если он зайдёт на трекер)',
	'STATUS_DEF_FORB' => 'Устанавливать по умолчанию этот статус для загружаемого торрента, если пользователь имеет права &quot;Может загружать торренты на трекер без их предварительной проверки&quot;',
	'STATUS_DEF_NOTFORB' => 'Устанавливать по умолчанию этот статус для загружаемого торрента, если пользователь не имеет прав &quot;Может загружать торренты на трекер без их предварительной проверки&quot;',
	'STATUS_ENABLED_SHORT' => 'Включён',
	'STATUS_CANDOWN_SHORT' => 'Скачивание торрента',
	'STATUS_CANDOWN_AUTHOR' => 'Да (для автора)',
	'STATUS_CANDOWN_GUEST' => 'Нет (для гостя)',
	'STATUS_DEF_FORB_SHORT' => 'Есть права',
	'STATUS_DEF_NOTFORB_SHORT' => 'Нет прав',
	'STATUSES_SIGN' => array('p'=>'нельзя скачать', 'm'=>'можно скачать', 'd'=>'без статуса',),
	'STATUS_CHANGE_SIGN' => 'Изменить статус торрента?',
	'STATUSES_FULL' => 'Невозможно создать/изменить указанный статус, исчерпан лимит на количество выбранных статусов<br /><br /><a href="%s">Вернуться назад</a>',
	'STATUS_SUCCESS' => 'Статус торрента успешно изменён<br /><br /><a href="%s">Вернуться назад</a>',
	'STATUS_EXCHANGE_REPLACE' => 'обмен и замена',
	'STATUS_EXCHANGE_REPLACE_EXPLAIN' => 'Заменить или обменять статусы в торрентах с выбранным статусом на указанный статус (при выборе опций из этой колонки никаких других изменений в статусах сделано НЕ будет)',
	'STATUS_EXCHANGE_FROM' => 'обменять',
	'STATUS_REPLACE_FROM' => 'заменить',
	'STATUS_EXCHANGE_REPLACE_TO' => 'на этот',
));
?>
