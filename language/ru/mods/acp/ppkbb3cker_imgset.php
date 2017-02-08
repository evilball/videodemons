<?php
/**
*
* acp_board [Russian]
*
* @package language
* @version $Id: ppkbb3cker_imgset.php, v 1.000 2012-06-13 18:53:23 PPK Exp $
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
	'ACP_PPKBB_IMGSET'			=> 'Настройки постеров и скриншотов',
	'ACP_PPKBB_IMGSET_EXPLAIN'			=> '',

	'TRACKER_MAX_POSTERS'			=> 'Количество загружаемых постеров',
	'TRACKER_MAX_POSTERS_EXPLAIN'			=> 'Две опции,
		<br /><strong>опция 1</strong> минимально необходимое количество,
		<br /><strong>опция 2</strong> максимально возможное количество (опции действуют только на постеры загружаемые на сервер)',
	'TRACKER_MAX_SCREENSHOTS'			=> 'Количество загружаемых скриншотов',
	'TRACKER_MAX_SCREENSHOTS_EXPLAIN'			=> 'Две опции,
		<br /><strong>опция 1</strong> минимально необходимое количество,
		<br /><strong>опция 2</strong> максимально возможное количество (опции действуют только на скриншоты загружаемые на сервер)',
	'TRACKER_MAX_EXTPOSTERS'			=> 'Внешние постеры',
	'TRACKER_MAX_EXTPOSTERS_EXPLAIN'			=> 'Десять опций,
		<br /><strong>опция 1</strong> разрешить внешние постеры,
		<br /><strong>опция 2</strong> минимально необходимое количество,
		<br /><strong>опция 3</strong> максимально возможное количество,
		<br /><strong>опция 4</strong> минимальная ширина постера,
		<br /><strong>опция 5</strong> минимальная высота постера,
		<br /><strong>опция 6</strong> максимальная ширина постера,
		<br /><strong>опция 7</strong> максимальная высота постера,
		<br /><strong>опция 8</strong> таймаут при проверке внешних постеров,
		<br /><strong>опция 9</strong> максимальный размер внешнего постера (0 - без ограничений),
		<br /><strong>опция 10</strong> не используется',
	'TRACKER_EXTPOSTERS_EXCLUDE' => 'Отображение полей внешних постеров',
	'TRACKER_EXTPOSTERS_EXCLUDE_EXPLAIN' => 'Не отображать поля внешних постеров в отмеченных форумах',
	'TRACKER_EXTPOSTERS_TRUEEXCLUDE' => 'Исключить указанные форумы',
	'TRACKER_EXTPOSTERS_TRUEEXCLUDE_EXPLAIN' => 'Если выбрано Да - не отображать поля внешних постеров в отмеченных выше форумах, иначе наоборот - отображать поля внешних постеров только в отмеченных выше форумах',
	'TRACKER_MAX_EXTSCREENSHOTS'			=> 'Внешние скриншоты',
	'TRACKER_MAX_EXTSCREENSHOTS_EXPLAIN'			=> 'Десять опций,
		<br /><strong>опция 1</strong> разрешить внешние скриншоты,
		<br /><strong>опция 2</strong> минимально необходимое количество,
		<br /><strong>опция 3</strong> максимально возможное количество,
		<br /><strong>опция 4</strong> минимальная ширина скриншота,
		<br /><strong>опция 5</strong> минимальная высота скриншота,
		<br /><strong>опция 6</strong> максимальная ширина скриншота,
		<br /><strong>опция 7</strong> максимальная высота скриншота,
		<br /><strong>опция 8</strong> таймаут при проверке внешних скриншотов,
		<br /><strong>опция 9</strong> максимальный размер внешнего скриншота (0 - без ограничений),
		<br /><strong>опция 10</strong> не используется',
	'TRACKER_EXTSCREENSHOTS_EXCLUDE' => 'Отображение полей внешних скриншотов',
	'TRACKER_EXTSCREENSHOTS_EXCLUDE_EXPLAIN' => 'Не отображать поля внешних скриншотов в отмеченных форумах',
	'TRACKER_EXTSCREENSHOTS_TRUEEXCLUDE' => 'Исключить указанные форумы',
	'TRACKER_EXTSCREENSHOTS_TRUEEXCLUDE_EXPLAIN' => 'Если выбрано Да - не отображать поля внешних скриншотов в отмеченных выше форумах, иначе наоборот - отображать поля внешних скриншотов только в отмеченных выше форумах',
	'TRACKER_FORB_EXTPOSTSCR' => 'Запрещённые хостинги изображений',
	'TRACKER_FORB_EXTPOSTSCR_EXPLAIN' => 'Запрещать загружать внешние постеры и скриншоты с указанных доменов (имена доменов с новой строки, опция работает только при создании или редактировании торрента)',
	'TRACKER_FORB_EXTPOSTSCR_TRUEEXCLUDE' => 'Запретить указанные домены',
	'TRACKER_FORB_EXTPOSTSCR_TRUEEXCLUDE_EXPLAIN' => 'Если выбрано Да - запрещать вышеуказанные имена доменов, иначе наоборот - разрешать внешние постеры и скриншоты только с вышеуказанных доменов',

	'TRACKER_MAX_THUMBWIDTH'			=> 'Максимальная ширина миниатюр на трекере',
	'TRACKER_MAX_THUMBWIDTH_EXPLAIN'			=> 'Ширина создаваемых миниатюр не будет превышать указанного здесь размера.',
	'TRACKER_MIN_THUMBSIZE'			=> 'Минимальный размер файлов для миниатюр на трекере',
	'TRACKER_MIN_THUMBSIZE_EXPLAIN'			=> 'Миниатюры не будут создаваться для рисунков меньше указанного размера.',
	'TRACKER_MAX_IMGWIDTH'			=> 'Максимальная ширина рисунков на трекере',
	'TRACKER_MAX_IMGWIDTH_EXPLAIN'			=> 'Максимальные размеры загружаемых рисунков. Введите 0 для отключения проверки размеров.',
	'TRACKER_MAX_IMGHEIGHT'			=> 'Максимальные высота рисунков на трекере',
	'TRACKER_MAX_IMGHEIGHT_EXPLAIN'			=> 'Максимальные размеры загружаемых рисунков. Введите 0 для отключения проверки размеров.',
));
?>
