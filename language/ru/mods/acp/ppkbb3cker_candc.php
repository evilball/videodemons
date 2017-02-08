<?php
/**
*
* acp_board [Russian]
*
* @package language
* @version $Id: ppkbb3cker_candc.php, v 1.000 2012-06-14 09:53:33 PPK Exp $
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
	'ACP_PPKBB_CANDC'			=> 'Обслуживание, удаление и очистка',
	'ACP_PPKBB_CANDC_EXPLAIN'			=> '',

	'TRACKER_RECOUNT_FINISHED'	=> 'Пересчёт скачавших',
	'TRACKER_RECOUNT_FINISHED_EXPLAIN'	=> 'Пересчитать значение <em>Скачали</em> на всех торрентах',
	'TRACKER_RECOUNT_THANKS'	=> 'Пересчёт спасибо',
	'TRACKER_RECOUNT_THANKS_EXPLAIN'	=> 'Пересчитать значение <em>Спасибо</em> на всех торрентах',
	'TRACKER_CLEAN_STICKY'	=> 'Сброс новинок трекера',
	'TRACKER_CLEAN_STICKY_EXPLAIN'	=> '<span style="color:#FF0000;">Безвозвратно отмечает все прилепленные торренты как обычные, т.е. удаляет из Новинок трекера</span>',
	'TRACKER_CLEAR_FILES'	=> 'Удаление списков файлов',
	'TRACKER_CLEAR_FILES_EXPLAIN'	=> '<span style="color:#FF0000;">Безвозвратно удаляет все списки файлов находящиеся в торрентах</span>',
	'TRACKER_CLEAR_THANKS'	=> 'Удаление списков "спасибо"',
	'TRACKER_CLEAR_THANKS_EXPLAIN'	=> '<span style="color:#FF0000;">Безвозвратно удаляет все списки "благодарностей", т.е. записи по которым пользователи говорили или получали "Спасибо"</span>',
	'TRACKER_CLEAR_TORRENTS'	=> 'Удаление списков торрентов',
	'TRACKER_CLEAR_TORRENTS_EXPLAIN'	=> '<span style="color:#FF0000;">Безвозвратно удаляет все списки торрентов которые были загружены на трекер</span>',
	'TRACKER_CLEAR_SNATCHED'	=> 'Удаление статистики по торрентам',
	'TRACKER_CLEAR_SNATCHED_EXPLAIN'	=> '<span style="color:#FF0000;">Безвозвратно удаляет всю статистику по торрентам</span>, Только нулевую - удалить только "нулевую" статистику, Только не нулевую - удалить только НЕ "нулевую" статистику (торрентом с нулевой статистикой будет считаться торрент на котором не было ничего скачано и роздано)',
	'TRACKER_CLEAR_SNATCHED_NULL'	=> 'Только нулевую',
	'TRACKER_CLEAR_SNATCHED_NOTNULL'	=> 'Только НЕ нулевую',
	'TRACKER_CLEAN_POLLS'	=> 'Удаление опросов по торрентам',
	'TRACKER_CLEAN_POLLS_EXPLAIN'	=> '<span style="color:#FF0000;">Безвозвратно удаляет все имеющиеся опросы (включая ответы) по торрентам</span>',
	'TRACKER_CLEAR_POLLRES'	=> 'Удаление результатов опросов по торрентам',
	'TRACKER_CLEAR_POLLRES_EXPLAIN'	=> '<span style="color:#FF0000;">Безвозвратно удаляет результаты опросов (ответы) по торрентам</span>',
	'TRACKER_CLEAR_GUESTSESS'	=> 'Удаление сессий гостей',
	'TRACKER_CLEAR_GUESTSESS_EXPLAIN'	=> '<span style="color:#FF0000;">Безвозвратно удаляет данные гостевых сессий трекера (для продолжения раздачи или скачивания гостям необходимо будет заново скачать торрент файлы)</span>',
	'TRACKER_CLEAR_RANNOUNCES' => 'Удаление данных внешних трекеров',
	'TRACKER_CLEAR_RANNOUNCES_EXPLAIN' => '<span style="color:#FF0000;">Безвозвратно удаляет данные о пирах полученных с внешних трекеров</span>',
	'TRACKER_CLEAR_TRTRACK' => 'Удаление внешних трекеров из торрентов',
	'TRACKER_CLEAR_TRTRACK_EXPLAIN' => '<span style="color:#FF0000;">Безвозвратно удаляет внешние трекеры из загруженных торрент файлов</span>',
	'TRACKER_CLEAR_URTRACK' => 'Удаление пользовательских внешних трекеров',
	'TRACKER_CLEAR_URTRACK_EXPLAIN' => '<span style="color:#FF0000;">Безвозвратно удаляет внешние трекеры добавленные пользователями</span>',
	'TRACKER_CLEAR_CRONJOBS'	=> 'Удаление заданий крона',
	'TRACKER_CLEAR_CRONJOBS_EXPLAIN'	=> '<span style="color:#FF0000;">Безвозвратно удаляет задания крона для трекера</span>',
	'TRACKER_CLEAR_UNREGTORR' => 'Удаление незарегистрированных торрентов',
	'TRACKER_CLEAR_UNREGTORR_EXPLAIN' => '<span style="color:#FF0000;">Безвозвратно удаляет незарегистрированные (не загруженные на сервер) торренты</span>',
	'TRACKER_CLEAR_UNREGTORR_REG' => 'Зарегистрированных пользователей',
	'TRACKER_CLEAR_UNREGTORR_GUEST' => 'Гостей',
	'TRACKER_CLEAR_UNREGTORR_ALL' => 'Все',
	'TRACKER_FIX_STATUSES' => 'Исправить статусы торрентов',
	'TRACKER_FIX_STATUSES_EXPLAIN' => 'Сбросить несуществующие статусы торрентов (торренты получат статус: Без статуса)',

	'TRACKER_DEADTORRENTS_AUTODELETE' => 'Удаление "устаревших" торрентов',
	'TRACKER_DEADTORRENTS_AUTODELETE_EXPLAIN' => 'Шесть опций,
		<br /><strong>опция 1</strong> удалять устаревшие торренты через указанный промежуток времени (0 - не удалять),
		<br /><strong>опция 2</strong> считать устаревшими торренты на которых не было сидеров более указанного количества времени (если опция 2 равна 0, опция 1 так-же будет считаться равной 0),
		<br /><strong>опция 3</strong> удалять только те торренты которые были добавлены или изменены позднее указанного количества времени (если опция 2 не равна 0 и опция 3 не равна 0, так-же будут удаляться торренты на которых никогда не было сидеров),
		<br /><strong>опция 4</strong> не используется,
		<br /><strong>опция 5</strong> учитывать внешних сидеров на торрентах, т.е. если на момент проверки на торренте будут внешние сидеры - торрент не будет считаться "устаревшим",
		<br /><strong>опция 6</strong> удалять указанное количество торрентов за один раз (0 - без ограничений)',
	'TRACKER_CRON_OPTIONS' => 'Функции "крона"',
	'TRACKER_CRON_OPTIONS_EXPLAIN' => 'Опции заданий крона, пять опций,
		<br /><strong>опция 1</strong> очищать задания крона старее указанного значения,
		<br /><strong>опция 2</strong> производить очистку устаревших значений крона через указанное время, при значении опции 1 равным 0 - функции очистки заданий крона работать не будут,
		<br /><strong>опция 3</strong> каждое задание крона запускать отдельным файлом, иначе все задания крона запускать в одном файле,
		<br /><strong>опция 4</strong> не выполнять задание если с момента его добавления до момента запуска прошло указанное время,
		<br /><strong>опция 5</strong> вероятность запуска "общих" заданий крона, чем больше число, тем меньше вероятность запуска задания (0 - никогда)',
	'TRACKER_CRON_JOBS' => 'Задания "крона"',
	'TRACKER_CRON_JOBS_EXPLAIN' => 'Пять опций,
		<br /><strong>опция 1</strong> выполнять пересчёт торрентов пользователя через указанное время,
		<br /><strong>опция 2</strong> выполнять пересчёт комментариев пользователя через указанное время,
		<br /><strong>опция 3</strong> не используется,
		<br /><strong>опция 4</strong> автоматически исправлять несуществующие статусы торрентов, торренты получат статус: Без статуса,
		<br /><strong>опция 5</strong> автоматически удалять "устаревшие" торренты',
	'TRACKER_CLEAN_SNATCH'				=> 'Удалить данные торрентов',
	'TRACKER_CLEAN_SNATCH_EXPLAIN'				=> 'Удаляет статистику торрентов которые были удалены и/или торренты которые были потеряны, а также списки файлов в соответствующих торрентах и данные по торрентам за которые пользователи говорили или получали "Спасибо"',
	'TRACKER_CLEAR_PEERS'				=> 'Очистить список пиров',
	'TRACKER_CLEAR_PEERS_EXPLAIN'				=> 'Удаляет списки пиров по всем скачиваемым и раздаваемым пользователями торрентов',
	'TRACKER_CLEAR_PEERS_TIME'					=> 'только "мёртвые"',
	'TRACKER_CLEAR_PEERS_ALL'					=> 'все',
	'TRACKER_CLEAR_PEERS_OFF'					=> 'нет',
	'TRACKER_RESET_RATIO'					=> 'Сбросить значение ратио у всех пользователей',
	'TRACKER_RESET_RATIO_EXPLAIN'					=> '<span style="color:#FF0000;">Безвозвратно удаляет данные о количестве скачанного/розданного у пользователей</span>',
	'TRACKER_RESET_BONUS'					=> 'Сбросить значение бонуса у всех пользователей',
	'TRACKER_RESET_BONUS_EXPLAIN'					=> '<span style="color:#FF0000;">Безвозвратно удаляет данные о начисленных бонусах у пользователей</span>',
	'TRACKER_UNSET_TCACHE'			=> 'Сбросить кэш прав доступа',
	'TRACKER_UNSET_TCACHE_EXPLAIN'			=> 'Может быть нужно при массовом изменениии прав доступа на трекер или понижении в правах на трекере каких либо пользователей',

));
?>
