<?php
/**
*
* @package ppkBB3cker
* @copyright (c) PPK
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
	'PPKBB3CKER_LTORRENTS'			=> 'Новинки трекера',
	'LATEST_TORRENTS_IN'	=> 'В форуме',
	'NO_LATEST_TORRENTS'	=> 'Нет новинок',
	'LATEST_TORRENTS'	=> 'Темы',
	'VIEW_LATEST_TORRENT'  => '1 торрент',
	'VIEW_LATEST_TORRENTS' => 'Торрентов: %d',
	'POSTED_BY'				=> 'Автор',
	'COMMENTS'				=> 'Комментарии',
	'VIEW_COMMENTS'			=> 'Просмотр комментариев',
	'POST_REPLY'			=> 'Комментировать',
	'TOPIC_VIEWS'			=> 'Просмотры',

	// ACP
	'ACP_PPKBB3CKER_LTORRENTS_SETTINGS'					=> 'Настройки отображения блока новинок трекера',
	'ACP_PPKBB3CKER_LTORRENTS_SETTINGS_EXP'			=> 'Здесь настраивается информация о новинках трекера.',

	'DISPLAY_LATESTTORRENTS'		=> 'Новинки трекера',
	'DISPLAY_LATESTTORRENTS_EXP'	=> 'Девять опций,
		<br /><strong>опция 1</strong> отображать блок на портале,
		<br /><strong>опция 2</strong> не используется,
		<br /><strong>опция 3</strong> не используется,
		<br /><strong>опция 4</strong> время кэширования новинок трекера на портале,
		<br /><strong>опция 5</strong> не используется,
		<br /><strong>опция 6</strong> не используется,
		<br /><strong>опция 7</strong> не используется,
		<br /><strong>опция 8</strong> не используется,
		<br /><strong>опция 9</strong> не используется',
	'DISPLAY_LATESTTORRENTS_MESSAGES'		=> 'В виде сообщений',
	'DISPLAY_LATESTTORRENTS_DATE'		=> 'По дате',
	'DISPLAY_LATESTTORRENTS_CAT'		=> 'По категориям',
	'PORTAL_TORR_PERPAGE'	=> 'Отображаемое количество торрентов на странице портала',
	'PORTAL_TORR_TEXTLENGTH'	=> 'Отображаемое количество символов описания торрента',
	'PORTAL_TORR_TIME'	=> 'Отображение торрентов как Новинок трекера',
	'PORTAL_TORR_TIME_EXP'	=> 'Три опции,
		<br /><strong>опция 1</strong> значение -1 - выводить все торренты как Новинки независимо от того, является тема торрента прилепленной или нет, значение 0 - для вывода в Новинки трекера тема торрента должна быть прилепленной и будет считаться новинкой пока будет прилепленной, значение больше 0 - для вывода в Новинки трекера тема торрента должна быть прилепленной и будет считаться новинкой пока будет прилепленной или время с даты добавления торрента или создания темы не превысит указанное здесь значение (в днях),
		<br /><strong>опция 2</strong> учитывать не время создания темы, а время добавления торрента, это так же означает порядок сортировки по дате добавления торрента, а не создания темы, т.е. при изменении торрент файла тема торрента поднимется вверх в блоке новинок трекера,
		<br /><strong>опция 3</strong> отображать только торренты с имеющимся (или бОльшим) количеством сидеров',
	'PORTAL_LTDISPLAY_OPT'	=> 'Отображение блоков торрента',
	'PORTAL_LTDISPLAY_OPT_EXP'	=> 'Четыре опции,
		<br /><strong>опция 1</strong> отображать торренты,
		<br /><strong>опция 2</strong> отображать постеры,
		<br /><strong>опция 3</strong> отображать скриншоты,
		<br /><strong>опция 4</strong> отображать блок информации',
	'PORTAL_LTDISPLAY_OPT_THUMB'	=> 'Миниатюры',
	'PORTAL_LTDISPLAY_OPT_FULL'	=> 'Полноразмерные изображения',
	'PORTAL_EXCLUDE_FORUMS'	=> 'Форумы для новинок трекера',
	'PORTAL_EXCLUDE_FORUMS_EXP'	=> 'Выберите форумы которые необходимо исключить из новинок трекера, (<font color ="#FF0000">в зависимости от нижерасположенной опции указанные форумы будут исключены или включены в новинки трекера, <b>форумы которые НЕ являются разделами трекера будут исключены автоматически</b></font>)',
	'PORTAL_TRUEEXCLUDE_FORUMS'	=> 'Исключить из новинок трекера форумы',
	'PORTAL_TRUEEXCLUDE_FORUMS_EXP'	=> 'Исключить указанные трекеры из новинок на Портале, иначе наоборот включить только вышеотмеченные',

	'MISSING_INLINE_ATTACHMENT'	=> 'Вложение <strong>%s</strong> больше недоступно',

));
