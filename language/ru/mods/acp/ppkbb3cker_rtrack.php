<?php
/**
*
* acp_board [Russian]
*
* @package language
* @version $Id: ppkbb3cker_rtrack.php, v 1.000 2012-06-14 10:44:28 PPK Exp $
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
	'ACP_PPKBB_RTRACK'			=> 'Внешние анонс URL и мультитрекер',
	'ACP_PPKBB_RTRACK_EXPLAIN'			=> '',

	'ACP_PPKBB_RTRACKER' => 'Внешние анонс URL и мультитрекер',
	'TRACKER_ENABLE_RANNOUNCES' => 'Включить внешние анонс URL',
	'TRACKER_ENABLE_RANNOUNCES_EXPLAIN' => 'Дополнительные анонс URL трекера отмеченные как <strong>Внешние и дополнительные</strong> будут самостоятельно анонсироваться трекером для получения количества пиров с указанных анонс URL, девять опций,
		<br /><strong>опция 1</strong> включить отображение пиров с внешних анонс URL (при значении равным<em> Нет</em> - все последующие опции будут считаться установленными в <em>Нет</em>),
		<br /><strong>опция 2</strong> включить анонс на странице просмотра списка тем  (viewforum.'.$phpEx.'),
		<br /><strong>опция 3</strong> включить анонс на странице отображения результатов поиска (search.'.$phpEx.'),
		<br /><strong>опция 4</strong> включить анонс на странице темы торрента (viewtopic.'.$phpEx.'),
		<br /><strong>опция 5</strong> включить анонс на странице добавления/редактирования торрента (posting.'.$phpEx.'),
		<br /><strong>опция 6</strong> не используется,
		<br /><strong>опция 7</strong> включить анонс в файле портала (portal.'.$phpEx.', <span style="color:#FF0000;">не реализовано</span>),
		<br /><strong>опция 8</strong> не используется,
		<br /><strong>опция 9</strong> включить анонс в файле анонса (announce.'.$phpEx.', <span style="color:#FF0000;">не реализовано</span>)',
	'TRACKER_RANNOUNCES_OPTIONS' => 'Опции внешних анонс URL',
	'TRACKER_RANNOUNCES_OPTIONS_EXPLAIN' => 'Одиннадцать опций,
		<br /><strong>опция 1</strong> принудительно устанавливать время анонса внешних анонс URL на указанное число, если внешний трекер не устанавливает время анонс интервала или если анонс интервал меньше опции 2,
		<br /><strong>опция 2</strong> если анонс интервал установленный внешним трекером меньше этого значения, будет использоваться время анонс интервала опции 1,
		<br /><strong>опция 3</strong> количество запрашиваемых пиров при анонсе,
		<br /><strong>опция 4</strong> при получении от трекера только количества пиров, считать сидерами это количество пиров (в процентах), остальные будут считаться личерами,
		<br /><strong>опция 5</strong> таймаут при анонсе внешних анонс URL, чем больше число, тем больше время ожидания при анонсе, <br /><strong>опция 6</strong> анонсировать за один раз не более этого числа внешних анонс URL (на один торрент, 0 - не ограниченно),
		<br /><strong>опция 7</strong> коэффициент умножения анонс интервала при ошибке, в случае возникновения ошибки при анонсе внешнего анонс URL автоматически увеличивать время анонса на указанное значение (каждая следующая ошибка последовательно увеличивает время анонса),
		<br /><strong>опция 8</strong> принудительно отключать скрэйп запрос на внешних анонс URL (скрэйп запрос меньше нагружает сервер и внешний трекер, но поддерживается не всеми трекерами),
		<br /><strong>опция 9</strong> общее максимальное число внешних трекеров для анонса, за один раз (0 - без ограничений),
		<br /><strong>опция 10</strong> User Agent клиента для анонса,
		<br /><strong>опция 11</strong> <a href="https://wiki.theory.org/BitTorrentSpecification#peer_id" target="_blank"><u>ID пира</u></a> клиента для анонса',
	'TRACKER_TFILE_ANNREPLACE' => 'Обработка торрент файлов',
	'TRACKER_TFILE_ANNREPLACE_EXPLAIN' => 'Три опции,
		<br /><strong>опция 1</strong> обрабатывать торрент файлы при загрузе на трекер, удалять - удалять все анонс URL из файла, извлекать как внешние и дополнительные - извлекать анонс URL из файла и определять их как <em>Внешние и дополнительные</em> анонс URL (для самостоятельного анонсирования трекером и добавления в торрент файлы), извлекать как дополнительные - извлекать анонс URL из файла и определять их как <em>Дополнительные</em> анонс URL (для добавления в торрент файлы),
		<br /><strong>опция 2</strong> максимальное число внешних анонс URL в загружаемых торрентах (0 - не ограниченно),
		<br /><strong>опция 3</strong> при значении опции 1 не равной <em>удалять</em>, по умолчанию отмечать опцию удаления внешних анонс URL из торрент файлов',
	'TRACKER_TFILE_ANNREPLACE_DEL' => 'удалять',
	'TRACKER_TFILE_ANNREPLACE_EXTERNAL' => 'извлекать как внешние и дополнительные',
	'TRACKER_TFILE_ANNREPLACE_ADDIT' => 'извлекать как дополнительные',
	'TRACKER_RTRACK_ENABLE'			=> 'Включить систему мультитрекеров',
	'TRACKER_RTRACK_ENABLE_EXPLAIN'			=> 'Мультитрекер добавляет дополнительные анонс URL в файлы торрентов (<span style="color:#FF0000;">при включении системы мультитрекеров возможны искажения в статистике трекера</span>), три опции,
		<br /><strong>опция 1</strong> включить систему мультитрекеров,
		<br /><strong>опция 2</strong> значение отличное от 0 - включить пользовательские трекеры (в личном разделе пользователя появится возможность определять собственные трекеры) указанное число будет действовать как максимально разрешённое количество трекеров которое можно указать  в личном разделе, 0 - выключить,
		<br /><strong>опция 3</strong> при включённой системе мультитрекеров исключать действующий анонс URL трекера из торррент файлов',
	'TRACKER_RTRACK_ENABLE_OFF'			=> 'выключить',
	'TRACKER_RTRACK_ENABLE_ALL'			=> 'включить',
	'TRACKER_RTRACK_ENABLE_FORALL'			=> 'для всех',
	'TRACKER_RTRACK_ENABLE_FORGUEST'			=> 'для гостей',
	'TRACKER_RTRACK_ENABLE_FORREG'			=> 'для зарегистрированных',
	'TRACKER_RTRACK_ENABLE_FOROFF'			=> 'не исключать',
));
?>
