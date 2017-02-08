<?php
/**
*
* acp_board [Russian]
*
* @package language
* @version $Id: ppkbb3cker_rtracker.php, v 1.000 2012-06-14 12:52:05 PPK Exp $
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
	'ACP_TRACKER_RTRACKER'				=> 'Дополнительные анонс URL',
	'ACP_TRACKER_RTRACKER_EXPLAIN'				=> 'Управление анонс URL трекера.<br />Дополнительный - данный анонс URL будет автоматически добавляться в каждый торрент файл независимо от IP адреса пользователя<br />Внешний и дополнительный - данный анонс URL будет добавляться во все торрент файлы независимо от IP адреса пользователя, а так же самостоятельно анонсироваться трекером.<br />Строка {YOUR_PASSKEY} будет заменяться текущим пасскеем пользователя на трекере.',
	'ACP_TRACKER_RTRACKER_SETTINGS'				=> 'Дополнительные анонс URL трекера',
	'RTRACKER_STAT'=>'Сидеров: <b>%d</b>, личеров: <b>%d</b>, пиров: <b>%d</b>, скачавших: <b>%d</b>, торрентов: <b>%d</b>, ошибок: <b>%d</b>, в среднем ошибок на каждый торрент: <b style="color:#FF0000;">%01.2f</b>',
	'RTRACKER'=>'Дополнительные анонс URL',
	'RTRACKER_UNITED'=>'дополнительный',
	'RTRACKER_REMOTE_UNITED'=>'внешний и дополнительный',
	'INVALID_RTRACK_URL' => 'Некорректный URL трекера',
	'FORB_RTRACK_URL' => 'Запрещённый URL трекера',
	'TYPE' => 'Тип',
	'ZONE_RTRACK_URL' => 'Url трекера',
	'RTRACK_ENABLED' => 'Включён',
	'RTRACK_FORB' => 'Запрещён',
	'RTF_TYPE' => array('r'=>'Регулярное выражение', 's' => 'Строка с учётом регистра', 'i' => 'Строка без учёта регистра'),
	'RTRACK_FORBS' => array(0=>'Нет', 1=>'в загружаемых торрентах', 2=>'в пользовательских трекерах', 3=>'везде'),

	'RTRACK_BACK' => '<br /><br /><a href="%s">Вернуться назад</a>',
));
?>
