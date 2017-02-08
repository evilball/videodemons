<?php
/**
*
* acp_board [Russian]
*
* @package language
* @version $Id: ppkbb3cker_groupset.php, v 1.000 2014-10-06 19:05:04 PPK Exp $
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
	'ACP_TRACKER_GROUPSET' => 'Групповые действия',
	'ACP_TRACKER_GROUPSET_EXPLAIN' => 'Изменение опций или тем/торрентов одного или нескольких форумов',
	'ACP_TRACKER_GROUPSET_SETTINGS' => 'Групповые действия',

	'GROUPSET' => array(
		'ADDF' => 'Назначение шаблона дополнительных полей',
		'STATUSES' => 'Назначение статусов на торренты',
		'LSIC' => 'Назначение числа колонок для списка подфорумов',
		'FTYPE' => 'Назначение типа форума',
		'FPEP' => 'Назначение отображения первого сообщения темы на всех страницах',
		'FREE' => 'Назначение скидок на торренты',
		'REQRATIO' => 'Назначение требуемого ратио на торрентах',
		'REQUPLOAD' => 'Назначение требуемого аплоада на торрентах',
		'QREPLY' => 'Назначение быстрого ответа в форумах',
	),
	'GROUPSET_DESCR' => array(
		'ADDF' => 'Установить шаблон дополнительных полей в выбранных форумах',
		'STATUSES' => 'Установить статус торрента на все торренты в выбранных форумах',
		'LSIC' => 'Установить число колонок для списка подфорумов',
		'FTYPE' => 'Установить тип форума',
		'FPEP' => 'Установить отображение первого сообщения темы на всех страницах темы',
		'FREE' => 'Установить скидки на всех в торрентах',
		'REQRATIO' => 'Установить требуемое ратио на всех в торрентах',
		'REQUPLOAD' => 'Установить требуемый аплоад на всех в торрентах',
		'QREPLY' => 'Установить возможность быстрого ответа в форумах',
	),

	'GROUPSETS_ACTION' => 'Действие',
	'GROUPSETS_SET' => 'Назначить',
	'GROUPSETS_FORUMS' => 'На форумы',
	'GROUPSETS_DESCR' => 'Описание',

	'GS_OPTIONS_ALL' => 'Все',
	'GS_OPTIONS_SELECTED' => 'Только выбранные',
	'GS_OPTIONS_NOTSELECTED' => 'Только не выбранные',
	'GS_TORRENT_STATUS' => 'Статус торрента',
	'GS_TORRENT_LSIC' => 'Число колонок для списка подфорумов',
	'GS_TORRENT_LSIC_ADDIT' => 'Введите число колонок для отображения списка подфорумов. Значение "0" отключает вывод подфорумов в колонки (список выводится в строку)',
	'GS_TORRENT_FTYPE' => 'Тип форума',
	'GS_TORRENT_FTYPE_FORUM' => 'Форум',
	'GS_TORRENT_FTYPE_TRACKER' => 'Трекер',
	'GS_TORRENT_FTYPE_CHAT' => 'Чат',
	'GS_TORRENT_FREE' => 'Скачанное не учитывается на',
	'GS_TORRENT_REQRATIO' => 'Для скачивания необходимо ратио не меньше',
	'GS_TORRENT_REQRATIO_ADDIT' => 'от 0.001',
	'GS_TORRENT_REQUPLOAD' => 'Для скачивания необходим аплоад не меньше',
	'GS_TORRENT_FPEP' => 'Первое сообщение темы на всех страницах',
	'GS_TORRENT_FPEP_OFF' => 'Выключить',
	'GS_TORRENT_FPEP_ON' => 'Включить',
	'GS_TORRENT_FPEP_MESS' => 'На основе опции в сообщении',
	'GS_TORRENT_FPEP_F' => 'выключено',
	'GS_TORRENT_FPEP_N' => 'включено',
	'GS_TORRENT_FPEP_M' => 'на основе опции',
	'GS_TORRENT_ADDF' => 'Шаблон дополнительных полей',
	'GS_TORRENT_ADDF_WITHOUT' => 'Без дополнительных полей',
	'GS_TORRENT_QUICKREPLY' => 'Форма быстрого ответа в форумах',
	'GS_VIEW_CURR' => 'Показать текущие (в скобках)',
));
?>
