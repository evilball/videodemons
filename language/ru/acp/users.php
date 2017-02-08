<?php
/**
*
* acp_users [Russian]
*
* @package language
* @version $Id: users.php 9767 2009-07-17 11:27:50Z toonarmy $
* @copyright (c) 2005 phpBB Group
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
	'ADMIN_SIG_PREVIEW'		=> 'Предварительный просмотр подписи',
	'AT_LEAST_ONE_FOUNDER'	=> 'Вы не можете понизить статус этого основателя до обычного пользователя. На конференции должен быть по крайней мере один основатель. Если вы хотите изменить статус основателя для этого пользователя, то сначала сделайте основателем другого пользователя.',

	'BAN_ALREADY_ENTERED'	=> 'Ранее запрет уже был осуществлён. Чёрный список не обновлён.',
	'BAN_SUCCESSFUL'		=> 'Пользователь успешно добавлен в чёрный список.',
	'CANNOT_BAN_ANONYMOUS'	=> 'Вы не можете закрыть доступ анонимному пользователю. Права доступа для гостей могут быть заданы на соответствующей вкладке администраторского раздела.',

	'CANNOT_BAN_FOUNDER'			=> 'Вы не можете закрыть доступ основателям.',
	'CANNOT_BAN_YOURSELF'			=> 'Вы не можете закрыть доступ самому себе.',
	'CANNOT_DEACTIVATE_BOT'			=> 'Вы не можете деактивировать учётные записи ботов. Деактивируйте бота на странице управления ботами.',
	'CANNOT_DEACTIVATE_FOUNDER'		=> 'Вы не можете деактивировать учётные записи основателей.',
	'CANNOT_DEACTIVATE_YOURSELF'	=> 'Вы не можете деактивировать собственную учётную запись.',
	'CANNOT_FORCE_REACT_BOT'		=> 'Вы не можете требовать повторной активации учётной записи бота. Повторно активируйте бота на странице управления ботами.',
	'CANNOT_FORCE_REACT_FOUNDER'	=> 'Вы не можете требовать повторной активации учётной записи основателя.',
	'CANNOT_FORCE_REACT_YOURSELF'	=> 'Вы не можете требовать повторной активации собственной учётной записи.',
	'CANNOT_REMOVE_ANONYMOUS'		=> 'Вы не можете удалить учётную запись гостя.',
	'CANNOT_REMOVE_FOUNDER'			=> 'Вы не можете удалить учётную запись основателя.',
	'CANNOT_REMOVE_YOURSELF'		=> 'Вы не можете удалить собственную учётную запись.',
	'CANNOT_SET_FOUNDER_IGNORED'	=> 'Вы не можете сделать игнорируемых пользователей основателями.',
	'CANNOT_SET_FOUNDER_INACTIVE'	=> 'Вы должны активировать пользователей, чтобы сделать их основателями. Только активированным пользователям можно повысить статус.',
	'CONFIRM_EMAIL_EXPLAIN'			=> 'Это поле необходимо заполнить только если вы изменили email-адрес пользователя.',

	'DELETE_POSTS'			=> 'Удалить сообщения',
	'DELETE_USER'			=> 'Удалить пользователя',
	'DELETE_USER_EXPLAIN'	=> 'Учтите, что удаление пользователя необратимо, он не может быть восстановлен. Непрочитанные личные сообщения, отправленные данным пользователем, также будут удалены и не будут доступны получателям.',

	'FORCE_REACTIVATION_SUCCESS'	=> 'Принудительная повторная активация успешно произведена.',
	'FOUNDER'						=> 'Основатель',
	'FOUNDER_EXPLAIN'				=> 'Основатели имеют все права администратора и не могут быть ограничены в доступе (заблокированы), удалены или понижены в статусе.',

	'GROUP_APPROVE'					=> 'Одобрить пользователя',
	'GROUP_DEFAULT'					=> 'Задать для пользователя группу по умолчанию',
	'GROUP_DELETE'					=> 'Удалить пользователя из группы',
	'GROUP_DEMOTE'					=> 'Снять лидера группы',
	'GROUP_PROMOTE'					=> 'Назначить лидером группы',

	'IP_WHOIS_FOR'			=> 'Об IP %s',

	'LAST_ACTIVE'			=> 'Последнее посещение',

	'MOVE_POSTS_EXPLAIN'	=> 'Выберите форум, в который вы хотите переместить все сообщения данного пользователя.',

	'NO_SPECIAL_RANK'		=> 'Специального звания не присвоено',
	'NO_WARNINGS'			=> 'Нет предупреждений.',
	'NOT_MANAGE_FOUNDER'	=> 'Вы попытались управлять учётной записью пользователя со статусом основателя. Только основатели могут управлять учётными записями других основателей.',

	'QUICK_TOOLS'			=> 'Быстрые действия',

	'REGISTERED'			=> 'Зарегистрирован',
	'REGISTERED_IP'			=> 'Зарегистрирован с IP-адреса',
	'RETAIN_POSTS'			=> 'Оставить сообщения',

	'SELECT_FORM'			=> 'Выбрать форму',
	'SELECT_USER'			=> 'Выбрать пользователя',

	'USER_ADMIN'					=> 'Управление пользователями',
	'USER_ADMIN_ACTIVATE'			=> 'Активировать учётную запись',
	'USER_ADMIN_ACTIVATED'			=> 'Пользователь успешно активирован.',
	'USER_ADMIN_AVATAR_REMOVED'		=> 'Аватара пользователя успешно удалена.',
	'USER_ADMIN_BAN_EMAIL'			=> 'Запретить email-адрес',
	'USER_ADMIN_BAN_EMAIL_REASON'	=> 'Email-адреса, запрещённые через управление пользователями',
	'USER_ADMIN_BAN_IP'				=> 'Запретить IP-адрес',
	'USER_ADMIN_BAN_IP_REASON'		=> 'IP-адреса, запрещённые через систему управления пользователями',
	'USER_ADMIN_BAN_NAME_REASON'	=> 'Имена, запрещённые через систему управления пользователями',
	'USER_ADMIN_BAN_USER'			=> 'Запретить имя пользователя',
	'USER_ADMIN_DEACTIVATE'			=> 'Деактивировать учётную запись',
	'USER_ADMIN_DEACTIVED'			=> 'Пользователь успешно деактивирован.',
	'USER_ADMIN_DEL_ATTACH'			=> 'Удалить все вложения',
	'USER_ADMIN_DEL_AVATAR'			=> 'Удалить аватару',
	'USER_ADMIN_DEL_OUTBOX'			=> 'Очистить папку «Исходящие»',
	'USER_ADMIN_DEL_POSTS'			=> 'Удалить все сообщения',
	'USER_ADMIN_DEL_SIG'			=> 'Удалить подпись',
	'USER_ADMIN_EXPLAIN'			=> 'Здесь вы можете изменять информацию о пользователях и некоторые специальные настройки.',
	'USER_ADMIN_FORCE'				=> 'Принудительная повторная активация',
	'USER_ADMIN_LEAVE_NR'			=> 'Удалить из группы «Новые пользователи»',
	'USER_ADMIN_MOVE_POSTS'			=> 'Переместить все сообщения',
	'USER_ADMIN_SIG_REMOVED'		=> 'Подпись пользователя успешно удалена.',
	'USER_ATTACHMENTS_REMOVED'		=> 'Все вложения данного пользователя успешно удалены.',
	'USER_AVATAR_NOT_ALLOWED'		=> 'Аватара не может быть отображена, поскольку аватары запрещены.',
	'USER_AVATAR_UPDATED'			=> 'Информация об аватаре пользователя успешно обновлена.',
	'USER_AVATAR_TYPE_NOT_ALLOWED'	=> 'Текущая аватара не может быть отображена, поскольку её тип запрещён.',
	'USER_CUSTOM_PROFILE_FIELDS'	=> 'Дополнительные поля профиля',
	'USER_DELETED'					=> 'Пользователь успешно удалён.',
	'USER_GROUP_ADD'				=> 'Добавить пользователя в группу',
	'USER_GROUP_NORMAL'				=> 'Созданные группы, в которые входит пользователь',
	'USER_GROUP_PENDING'			=> 'Группы, в которых пользователь является кандидатом',
	'USER_GROUP_SPECIAL'			=> 'Предустановленные группы, в которые входит пользователь',
	'USER_LIFTED_NR'				=> 'Пользователь успешно удалён из группы вновь зарегистрированных пользователей.',
	'USER_NO_ATTACHMENTS'			=> 'Вложения отсутствуют.',
	'USER_NO_POSTS_TO_DELETE'			=> 'Отсутствуют сообщения, принадлежащие пользователю.',
	'USER_OUTBOX_EMPTIED'			=> 'Папка «Исходящие» этого пользователя успешно очищена.',
	'USER_OUTBOX_EMPTY'				=> 'Папка «Исходящие» этого пользователя пустая.',
	'USER_OVERVIEW_UPDATED'			=> 'Информация о пользователе обновлена.',
	'USER_POSTS_DELETED'			=> 'Все сообщения данного пользователя успешно удалены.',
	'USER_POSTS_MOVED'				=> 'Сообщения пользователя успешно перемещены в указанный форум.',
	'USER_PREFS_UPDATED'			=> 'Настройки пользователя обновлены.',
	'USER_PROFILE'					=> 'Профиль пользователя',
	'USER_PROFILE_UPDATED'			=> 'Профиль пользователя обновлён.',
	'USER_RANK'						=> 'Звание пользователя',
	'USER_RANK_UPDATED'				=> 'Звание пользователя обновлено.',
	'USER_SIG_UPDATED'				=> 'Подпись пользователя успешно обновлена.',
	'USER_WARNING_LOG_DELETED'		=> 'Информация недоступна. Возможно, данная запись была удалена из журнала.',
	'USER_TOOLS'					=> 'Основные инструменты',
	'TORRENT_INFO_HEADER_TORRENT'		=> 'Загрузил',
	'TORRENT_INFO_HEADER_SEED'		=> 'Раздаёт',
	'TORRENT_INFO_HEADER_LEECH'		=> 'Скачивает',
	'TORRENT_INFO_HEADER_LEAVE'		=> 'Не раздаёт',
	'TORRENT_INFO_HEADER_HISTORY'		=> 'История',
	'TORRENT_INFO_HEADER_FROMTHANKS'		=> 'Сказал спасибо',
	'TORRENT_INFO_HEADER_TOTHANKS'		=> 'Сказали спасибо',
	'TORRENT_INFO_HEADER_FROMSEEDREQ'		=> 'Запрашивал сидеров',
	//'TORRENT_INFO_HEADER_TOSEEDREQ'		=> 'Запрошен как сидер',
	'TORRENT_INFO_HEADER_FINISHED'		=> 'Скачал',

	'ACP_ZONE_RTRACK_EXPLAIN' => 'Адреса данных трекеров будут автоматически добавляться в скачиваемые пользователем торрент файлы.<br />Строка {YOUR_PASSKEY} будет заменяться текущим пасскеем пользователя на трекере.',

	'ACP_USER_TRACKER'				=> 'Трекер',
	'ACP_USER_RTRACKER'				=> 'Мультитрекер',
	'ACP_USER_TORRENTS'				=> 'Торренты',

	'ACP_PREFS_TRACKER'				=> 'Данные трекера',
	'ACP_PREFS_PERSONAL'				=> 'Личные настройки',
	'ACP_PREFS_ADMIN'				=> 'Администраторские настройки',

	'ACP_TR_RATIO'					=> 'Ратио',
	'ACP_TR_RATIO_EXPLAIN'					=> '',
	'ACP_TR_RRATIO'				=> 'Ратио в действительности',
	'ACP_TR_RRATIO_EXPLAIN'				=> '',
	'ACP_TR_DOWN'					=> 'Скачал',
	'ACP_TR_DOWN_EXPLAIN'					=> '',
	'ACP_TR_UP'					=> 'Раздал',
	'ACP_TR_UP_EXPLAIN'					=> '',

	'ACP_TR_UP_SELF'					=> 'Раздал на своих торрентах',
	'ACP_TR_UP_SELF_EXPLAIN'					=> '',
	'ACP_TR_COMM'					=> 'Комментариев',
	'ACP_TR_COMM_EXPLAIN'					=> '',
	'ACP_TR_TORR'					=> 'Торрентов',
	'ACP_TR_TORR_EXPLAIN'					=> '',
	'ACP_TR_BONUS'					=> 'Бонус за аплоад',
	'ACP_TR_BONUS_EXPLAIN'					=> '',

	'ACP_TR_PASSKEY'					=> 'Пасскей',
	'ACP_TR_PASSKEY_EXPLAIN'					=> '',
	'ACP_TR_SHADOW_DOWN'					=> 'Скачал в действительности',
	'ACP_TR_SHADOW_DOWN_EXPLAIN'					=> '',
	'CHAT_KICK'					=> 'Выбросить пользователя из чата(ов)',
	'CHAT_KICK_EXPLAIN'					=> '',
	'TR_CLEAR_PEERS'					=> 'Очистить список раздач пользователя',
	'TR_CLEAR_PEERS_EXPLAIN'			=> 'Удаляет список всех скачиваемых и раздаваемых пользователем торрентов',
	'TR_CLEAR_PEERS_TIME'					=> 'Только "мёртвые"',
	'TR_CLEAR_PEERS_ALL'					=> 'Все',
	'TR_CLEAR_SNATCH'					=> 'Удалить данные торрентов пользователя',
	'TR_CLEAR_TRIGHTS_EXPLAIN'				=> 'Необходимо только при наличии уже раздаваемых или скачиваемых торрентов, может понадобиться в случае изменений прав пользователя на трекере',
	'TR_CLEAR_SNATCH_EXPLAIN'			=> 'Удаляет пользовательскую статистику торрентов которые были удалены и/или торренты которые были потеряны, а также списки файлов в соответствующих торрентах и данные по торрентам за которые пользователь говорил "Спасибо"',

	'TR_RESET_RATIO'					=> 'Сбросить значение ратио пользователя',
	'TR_RESET_RATIO_EXPLAIN'					=> '',
	'TR_RESET_BONUS'					=> 'Сбросить значение бонуса пользователя за аплоад',
	'TR_RESET_BONUS_EXPLAIN'					=> '',

	'ACP_TR_YTHANKS' => 'Спасибо от пользователя',
	'ACP_TR_YTHANKS_EXPLAIN' => '',
	'ACP_TR_TYTHANKS' => 'Спасибо пользователю',
	'ACP_TR_TYTHANKS_EXPLAIN' => '',
	'ACP_PREFS_TORRENTS'				=> 'Данные торрентов',
	'DEL_USER_TORRENTS_SUCCESS' => 'Выбранные записи успешно удалены',
));

?>
