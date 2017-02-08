<?php
/**
*
* common [Russian]
*
* @package ppkBB3cker
* @version $Id: ppkbb3cker_acheats.php, v 1.000 2009-12-23 19:25:00 PPK Exp $
* @copyright (c) 2009 PPK
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
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//


$lang = array_merge($lang, array(
	'ACP_TRACKER_ACHEAT'				=> 'Анализ данных',
	'ACP_TRACKER_ACHEAT_EXPLAIN'				=> 'Анализ данных',
	'ACP_TRACKER_ACHEAT_SETTINGS'				=> 'Анализ данных',

	'VIEW_ACHEAT2'	=> 'Анализ по торрентам',
	'VIEW_ACHEAT3'	=> 'Анализ по пользователям',

	'ACHEAT_UPSPEED'	=> 'Скорость раздачи',
	'ACHEAT_DOWNSPEED'	=> 'Скорость скачивания',
	'ACHEAT_UPLOADED'	=> 'Роздано',
	'ACHEAT_DOWNLOADED'	=> 'Скачано',
	'ACHEAT_SEEDERS'	=> 'Сидеров',
	'ACHEAT_LEECHERS'	=> 'Личеров',
	'ACHEAT_DATEFROM'	=> 'С даты (ГГГГММДД)',
	'ACHEAT_DATETO'	=> 'До даты (ГГГГММДД)',
	'ACHEAT_START'	=> 'Начиная с записи',
	'ACHEAT_END'	=> 'Показывать до записи',
	'ACHEAT_INCTORRENT'	=> 'Название торрента',
	'ACHEAT_INCUNAME'	=> 'Имя пользователя',
	'ACHEAT_INCDT'	=> 'Дата и время',
	'ACHEAT_INCUA'	=> 'Торрент клиент',
	'ACHEAT_TORRENTID'	=> 'ID торрента(ов)',
	'ACHEAT_USERID'	=> 'ID пользователя(ей)',
	'ACHEAT_SORT'	=> 'Сортировать по',
	'ACHEAT_ORDER'	=> 'если отмечено - возрастанию (иначе по убыванию)',
	'ACHEAT_MORELESS'	=> 'если отмечено - меньше (иначе больше)',

	'ACHEAT_SDIFF'	=> 'Расхождение',
	'ACHEAT_INCUNAMES'	=> 'Имена пользователей',
	'ACHEAT_INCTNAMES'	=> 'Названия торрентов',
	'ACHEAT_URATIO'	=> 'Ратио пользователя',
	'ACHEAT_URRATIO'	=> 'Ратио в действительности',
	'ACHEAT_TRATIO'	=> 'Ратио на торренте',
	'ACHEAT_UBONUS'	=> 'Бонус за аплоад',
	'ACHEAT_UDATEFROM'	=> 'Дата регистрации с (ГГГГ-ММ-ДД)',
	'ACHEAT_UDATETO'	=> 'Дата регистрации до (ГГГГ-ММ-ДД)',
	'ACHEAT_UMAX'	=> 'Пользователей не более',
	'ACHEAT_TMAX'	=> 'Торрентов не более',
	'ACHEAT_UUPLOADED'	=> 'Роздано пользователем',
	'ACHEAT_UDOWNLOADED'	=> 'Скачано пользователем',
	'ACHEAT_URDOWNLOADED'	=> 'Скачано в действительности',
	'ACHEAT_COMPLETED'	=> 'Скачали',
	'ACHEAT_TCOMPLETED'	=> 'Скачан',
	'ACHEAT_UCOMPLETED'	=> 'Скачал',

	'ACHEAT_SORTS_STAT'	=> array(
		'p.post_subject' => 'названию торрента',
		'u.username' => 'имени пользователя',
		's.torrent' => 'ID торрента',
		'u.user_id' => 'ID пользователя',
		's.to_go' => ' осталось скачать',
		's.event' => 'событию',
		's.uploaded' => 'загруженному',
		's.downloaded' => 'скачанному',
		's.upspeed' => 'скорости раздачи',
		's.downspeed' => 'скорости скачивания',
		's.seeders' => 'количеству сидеров',
		's.leechers' => 'количеству личеров',
		's.agent' => 'названию клиента',
		's.added' => 'дате статистики',
		's.a_interval' => 'интервалу',
		's.d_stamp' => 'дате дня',
	),

	'ACHEAT_SORTS_TORR' => array(
		'p.post_subject' => 'названию торрента',
		's_diff' => 'расхождению',
		't.times_completed' => 'количеству скачивших',
		'uploaded' => 'загруженному',
		'downloaded' => 'скачанному',
		'u.username' => 'имени пользователя',
		's.uploaded' => 'розданному на торренте',
		's.downloaded' => 'скачанному на торренте',
		's.torrent' => 'ID торрента',
		's.userid' => 'ID пользователя',
		'u.user_bonus' => 'бонусу за аплоад',
		'u.user_uploaded' => 'роздано пользователем',
		'u.user_downloaded' => 'скачано пользователем',
		'u.user_shadow_downloaded' => 'скачано в действительности',
		't_ratio' => 'ратио на торренте',
		'u_ratio' =>'ратио пользователя',
		'u_rratio' =>'ратио в действительности'
	),

	'ACHEAT_SORTS_USER' => array(
		'p.post_subject' => 'названию торрента',
		's_diff' => 'расхождению',
		't.times_completed' => 'количеству скачивших',
		'uploaded' => 'загруженному',
		'downloaded' => 'скачанному',
		'u.username' => 'имени пользователя',
		'uploaded' => 'розданному на торренте',
		'downloaded' => 'скачанному на торренте',
		'torrent' => 'ID торрента',
		's.userid' => 'ID пользователя',
		'u.user_bonus' => 'бонусу за аплоад',
		'u.user_uploaded' => 'роздано пользователем',
		'u.user_downloaded' => 'скачано пользователем',
		'u.user_shadow_downloaded' => 'скачано в действительности',
		't_ratio' => 'ратио на торренте',
		'u_ratio' =>'ратио пользователя',
		'u_rratio' =>'ратио в действительности'
	),

	'ACHEAT_HSEED' => 'Сидеров',
	'ACHEAT_SEED' => 'Сид.',
	'ACHEAT_HLEECH' => 'Личеров',
	'ACHEAT_LEECH' => 'Лич.',
	'ACHEAT_HDLSP' => 'Скорость скачивания',
	'ACHEAT_DLSP' => 'Ск. ск.',
	'ACHEAT_HUPSP' => 'Скорость раздачи',
	'ACHEAT_UPSP' => 'Ск. рз.',
	'ACHEAT_HEVENT' => 'Событие',
	'ACHEAT_EVENT' => 'Соб.',
	'ACHEAT_HINT' => 'Интервал',
	'ACHEAT_INT' => 'Инт.',
	'ACHEAT_HLEFT' => 'Осталось скачать',
	'ACHEAT_LEFT' => 'Ост. ск.',
	'ACHEAT_HUP' => 'Раздал',
	'ACHEAT_UP' => 'Рз.',
	'ACHEAT_HDL' => 'Скачал',
	'ACHEAT_DL' => 'Ск.',
	'ACHEAT_HTORR' => 'Торрент',
	'ACHEAT_TORR' => 'Торр.',
	'ACHEAT_HUSER' => 'Пользователь',
	'ACHEAT_USER' => 'Польз.',
	'ACHEAT_HDT' => 'Дата и время',
	'ACHEAT_DT' => 'ДВ.',
	'ACHEAT_HUA' => 'Торрент клиент',
	'ACHEAT_UA' => 'Торр. кл.',

	'ACHEAT_HUSERS' => 'Пользователи',
	'ACHEAT_USERS' => 'Польз.',
	'ACHEAT_HDIFF' => 'Расхождение',
	'ACHEAT_DIFF' => 'Расх.',
	'ACHEAT_HUPS' => 'Роздано',
	'ACHEAT_UPS' => 'Рз.',
	'ACHEAT_HDLS' => 'Скачано',
	'ACHEAT_DLS' => 'Ск.',
	'ACHEAT_HCOMPL' => 'Количество скачавших',
	'ACHEAT_COMPL' => 'Кл. Ск.',

	'ACHEAT_HUNAME' => 'Имя пользователя',
	'ACHEAT_UNAME' => 'Им. польз.',
	'ACHEAT_HUDL' => 'Скачал',
	'ACHEAT_UDL' => 'Ск.',
	'ACHEAT_HUUP' => 'Раздал',
	'ACHEAT_UUP' => 'Рз.',
	'ACHEAT_HURDL' => 'Скачал в действительности',
	'ACHEAT_URDL' => 'Ск. д.',
	'ACHEAT_HUBON1' => 'Бонус за аплоад',
	'ACHEAT_UBON1' => 'Бн. апл.',
	'ACHEAT_HUBON2' => 'Бонус за сидирование',
	'ACHEAT_UBON2' => 'Бн. сид.',
	'ACHEAT_HURAT' => 'Ратио',
	'ACHEAT_URAT' => 'Рат.',
	'ACHEAT_HURRAT' => 'Ратио в действительности',
	'ACHEAT_URRAT' => 'Рат. д.',

	'ACHEAT_HLP2' => '<b>TR:</b> Ратио пользователя на данном торренте<br /><b>TUP:</b> Роздано пользователем на данном торренте<br /><b>TDL:</b> Скачано пользователем на данном торренте<br /><b>UR:</b> Ратио пользователя<br /><b>UUP:</b> Роздано пользователем<br /><b>UDL:</b> Скачано пользователем<br /><b>URR:</b> Ратио пользователя в действительности<br /><b>URDL:</b> Скачано пользователем в действительности<br /><b>UBA:</b> Бонус за аплоад<br /><b>UBS:</b> Бонус за сидирование<br /><b>PRUP:</b> Процент розданного пользователем от общего числа розданного на торренте<br /><b>PRDL:</b> Процент скачанного пользователем от общего числа скачанного на торренте<br /><b>PRUT:</b> Процент попадания пользователя в каждый из данных торрентов',

	'ACHEAT_HLP3' => '<b>TR:</b> Ратио пользователя на данном торренте<br /><b>TUP:</b> Роздано пользователем на данном торренте<br /><b>TDL:</b> Скачано пользователем на данном торренте<br /><b>TPRUP:</b> Процент розданного пользователем на этом торренте от общего числа розданного пользователем<br /><b>TPRDL:</b> Процент скачанного пользователем на этом торренте от общего числа скачанного пользователем<br /><b>TD:</b> Количество скачиваний торрента<br /><b>TUD:</b> Количество скачиваний торрента пользователем',

	'ACHEAT_SORT_VAL' => array(0=>'по убыванию', 1=>'по возрастанию'),
	'ACHEAT_ML_VAL' => array(0=>'больше', 1=>'меньше'),
	'ACHEAT_SHOW' => 'Показывать',
	'ACHEAT_SHOW_ITEM' => 'показывать',
	'ACHEAT_TORRENT_DELETED' => 'Торрент удалён',
));

?>
