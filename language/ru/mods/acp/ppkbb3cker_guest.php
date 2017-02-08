<?php
/**
*
* acp_board [Russian]
*
* @package language
* @version $Id: ppkbb3cker_guest.php, v 1.000 2012-06-14 10:54:35 PPK Exp $
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
	'ACP_PPKBB_GUEST' => 'Трекер для гостей',
	'ACP_PPKBB_GUEST_EXPLAIN'	 => '',

	'TRACKER_GUESTS_ENABLED' => 'Функции трекера для гостей',
	'TRACKER_GUESTS_ENABLED_EXPLAIN' => 'Две опции,
		<br /><strong>опция 1</strong> разрешить анонс торрентов и скачивание торрент файлов для гостей (права на скачивание торрент файлов устанавливаются отдельно, <span style="color:#FF0000;">при каждом изменении, включении/отключении трекера для гостей, данные  статистики гостей по торрентам будут удаляться</span>),
		<br /><strong>опция 2</strong> разрешить для пиров гостей получать список пиров зарегистрированных пользователей (и наоборот), иначе гости будут скачивать и раздавать только для гостей, а зерегистрированные пользователи только для зарегистрированных',
	'TRACKER_GUESTSMAX_SESSIONS'	=> 'Количество сессий для гостей',
	'TRACKER_GUESTSMAX_SESSIONS_EXPLAIN' => 'Максимальное количество сессий для гостей при анонсе, одна сессия может использоваться более чем одним пользователем и содержать более чем один торрент, 0 - без ограничений',
	'TRACKER_GUESTSSESS_EXPIRE' => 'Время  жизни сессий',
	'TRACKER_GUESTSSESS_EXPIRE_EXPLAIN' => 'Две опции,
		<br /><strong>опция 1</strong> принудительно удалять сессии дата начала которых больше этого значения даже если сессии ещё активны,
		<br /><strong>опция 2</strong> удалять сессии последняя активность по которым была позднее этого значения',
	'TRACKER_GCLEANUP_INTERVAL' => 'Время очистки гостевых сессий',
	'TRACKER_GCLEANUP_INTERVAL_EXPLAIN' => 'Через указанный промежуток времени будет проводиться чистка устаревших гостевых сессий, устаревшими будут считаться сессии из опции <u>Время  жизни сессий</u>',
	'TRACKER_GUESTMAX_SEED' => 'Максимальное количество торрентов для одновременной раздачи',
	'TRACKER_GUESTMAX_SEED_EXPLAIN' => 'На одну гостевую сессию (0 - без ограничений, ограничение не действует на незарегистрированные торренты)',
	'TRACKER_GUESTMAX_LEECH'	 => 'Максимальное количество торрентов для одновременного скачивания',
	'TRACKER_GUESTMAX_LEECH_EXPLAIN' => 'На одну гостевую сессию (0 - без ограничений, ограничение не действует на незарегистрированные торренты)',
	'TRACKER_GUESTMAXIP_PERTORR' => 'Максимальное число соединений с одного IP адреса на один торрент',
	'TRACKER_GUESTMAXIP_PERTORR_EXPLAIN' => 'Суммарно для всех гостей (0 - без ограничений)',
	'TRACKER_GUESTMAXIP_PERTR' => 'Максимальное число соединений с одного IP адреса',
	'TRACKER_GUESTMAXIP_PERTR_EXPLAIN' => ' Суммарно для всех гостей (0 - без ограничений)',
	'TRACKER_GUNREGTORR_SESSID' => 'Пасскей для анонса незарегистрированных торрентов',
	'TRACKER_GUNREGTORR_SESSID_EXPLAIN' => 'Строка из цифр и латинских букв длиной 32 символа, пустая строка - разрешить анонс без пасскея или с любым пасскеем, текущий пасскей для анонса:<br />%s (<span style="color:#FF0000;">при каждом изменении этой опции гостям необходимо будет менять пасскей в анонс URL торрент файлов для их анонса</span>)',
	'TRACKER_GALLOW_UNREGTORR' => 'Анонс незарегистрированных торрентов',
	'TRACKER_GALLOW_UNREGTORR_EXPLAIN' => 'Разрешить для гостей на трекере анонс незарегистрированных (не загруженных на сервер) торрентов, разрешить с учётом - учитывать ограничения, разрешить без учёта - НЕ учитывать ограничения',
	'TRACKER_GALLOW_UNREGTORR_OFF' => 'запретить',
	'TRACKER_GALLOW_UNREGTORR_ONWSTAT' => 'разрешить с учётом',
	'TRACKER_GALLOW_UNREGTORR_ONWOSTAT' => 'разрешить без учёта',
	'TRACKER_GZREWRITE' => 'Управление gz сжатием',
	'TRACKER_GZREWRITE_EXPLAIN' => 'Ответ клиенту при анонсе, автоматически - определять на основе принятых от клиента заголовков',
	'TRACKER_GZREWRITE_AUTO' => 'автоматически',
	'TRACKER_GZREWRITE_GZ' => 'принудительно в сжатом',
	'TRACKER_GZREWRITE_NONGZ' => 'принудительно в несжатом',
	'TRACKER_MAXPEERS_REWRITE' => 'Переопределение количества возвращаемых подсоединённых клиентов',
	'TRACKER_MAXPEERS_REWRITE_EXPLAIN' => 'Переопределять данные клиентской программы указанным выше значением',
	'TRACKER_IPTYPE' => 'Определение IP адреса',
	'TRACKER_IPTYPE_EXPLAIN' => 'Метод определения IP адреса, (IP адреса в методах <em>по заголовку</em> и <em>по данным от клиента</em> могут быть подделаны пользователем, если эти методы вернут неправильный IP адрес, будет использован стандартный метод определения IP адреса)',
	'TRACKER_IPTYPE_STANDART' => 'стандартный',
	'TRACKER_IPTYPE_HEADER' => 'по заголовку X_FORWARDED_FOR',
	'TRACKER_IPTYPE_CLIENT' => 'по данным от клиента',
	'TRACKER_CLIENTS_RESTRICTS' => 'Включить ограничения клиентов',
	'TRACKER_CLIENTS_RESTRICTS_EXPLAIN' => 'Три опции (<span style="color:#FF0000;">соответствующие порты, заголовки USER_AGENT и идентификатиоры пиров должны быть определены в директории /tracker/tinc/ в файлах <strong>tcrestrport.'.$phpEx.', tcrestrua.'.$phpEx.', tcrestrpeerid.'.$phpEx.'</strong></span>),
		<br /><strong>опция 1</strong> включить бан клиентов по порту,
		<br /><strong>опция 2</strong> включить бан клиентов по заголовку USER_AGENT,
		<br /><strong>опция 3</strong> включить бан клиентов по ID пира',
	'TRACKER_MAXPEERS_LIMIT' => 'Ограничение на количество возвращаемых подсоединённых клиентов',
	'TRACKER_MAXPEERS_LIMIT_EXPLAIN' => 'Если клиентская программа не устанавливает ограничение на возвращаемое количество подключённых к торренту клиентов, используется это значение (0 - использовать данные программы, если не переопределено ниже)',
	'TRACKER_RIGHTS_TCACHE' => 'Кэширование прав доступа',
	'TRACKER_RIGHTS_TCACHE_EXPLAIN' => 'Кэшировать права доступа на трекере на указанное время, кэширование уменьшает "время выполнения" announce.php',

	'TRACKER_GMAGNET_LINK' => 'Магнет ссылки на торренты',
	'TRACKER_GMAGNET_LINK_EXPLAIN' => 'Отображение магнет ссылок на торренты для гостей, шесть опций,
		<br /><strong>опция 1</strong> отображать на странице портала,
		<br /><strong>опция 2</strong> отображать на странице списка тем и похожих торрентах,
		<br /><strong>опция 3</strong> отображать на странице результатов поиска,
		<br /><strong>опция 4</strong> отображать на странице торрента и странице создания сообщения,
		<br /><strong>опция 5</strong> не используется,
		<br /><strong>опция 6</strong> не используется',
));
?>
