<?php
/**
*
* acp_board [Russian]
*
* @package language
* @version $Id: ppkbb3cker_restricts.php, v 1.000 2010/12/17 11:15:00 PPK Exp $
* @copyright (c) 2010 PPK
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
	'ACP_TRESTRICTS_SETTINGS_EXPLAIN'			=> 'Обработка данных в таблицах идёт сверху - вниз, ратио в единицах (до 0.001) или (если ратио пользователя имеет одно из следующих значений, ограничения так-же будут работать только если ограничение по ратио является одним из этих значений): Inf. (нет скачанного и загруженного), Seed. (есть загруженное, нет скачанного), Leech. (нет загруженного, есть скачанное), None. (если активна настройка и ратио подпадает под значение: <u>Начало учёта ратио</u> независимо от скачанного и загруженного будет отображаться это значение ратио), "аплоад" в гигабайтах (целое число), "скачано" в гигабайтах (целое число), "время" в часах (0 - запрет), "торренты" в единицах (0 - запрет)',

	'TRACKER_WAIT_TIME'					=> 'Время ожидания или запрет на скачивание торрента при низком ратио и/или низком значении аплоада',
	'TRACKER_WAIT_TIME_EXPLAIN'					=> 'Таблица в формате:<br />ратио|аплоад|время<br />0.001|1|72<br />0.25|5|48<br />0.5|10|24<br />0.65|30|12<br />0.8|50|6<br />0.95|70|2<br />1.0|100|1<br />значения должны располагаться от меньшего к большему<br />например: 0.65|30|12 - если ратио меньше 0.65 и аплоад меньше 30Гб время ожидания на скачивание торрента 12 часов (с даты добавления торрента)',

	'TRACKER_WAIT_TIME2'					=> 'Время ожидания или запрет на скачивание торрента при низком ратио и/или большом значении скачанного',
	'TRACKER_WAIT_TIME2_EXPLAIN'					=> 'Таблица в формате:<br />ратио|скачано|время<br />0.001|1|1<br />0.25|5|3<br />0.5|10|5<br />0.65|30|7<br />0.8|50|10<br />0.95|70|13<br />значения должны располагаться от меньшего к большему<br />например: 0.65|30|0 - если ратио меньше 0.65 и скачано больше 30Гб скачивание торрентов невозможно (применяется только к новым торрентам, т.е. уже скачиваемые докачать можно)',
	'TRACKER_MAXLEECH_RESTR'					=> 'Ограничение на максимальное количество скачиваемых торрентов при низком ратио и/или большом значении скачанного',
	'TRACKER_MAXLEECH_RESTR_EXPLAIN'					=> 'Таблица в формате:<br />ратио|скачано|торрентов<br />0.001|1|1<br />0.25|5|3<br />0.5|10|5<br />0.65|30|7<br />0.8|50|10<br />0.95|70|13<br />значения должны располагаться от меньшего к большему<br />Если ни одно из значений не подходит, будет использоваться значение из: <u>Максимальное количество торрентов для одновременного скачивания (на одного пользователя)</u><br />например: 0.8|50|10 - если ратио меньше 0.8 и скачано больше 50Гб максимальное количество торрентов для скачивания 10</u>',
				)
	);
?>
