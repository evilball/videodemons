<?php
/**
*
* common [Russian]
*
* @package language
* @version $Id: ppkbb3cker_chat.php,v 1.000 2008/08/02 11:00:00 PPK Exp $
* @copyright (c) 2008 PPK
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
	'CHAT_EXIT'			=> 'Выйти из чата',
	'CHAT_ENTER'			=> 'Войти в чат',
	'CHAT_MESSAGE'			=> 'Сообщение',
	'CHAT_RESET'			=> 'Очистить',
	'CHAT_SEND'			=> 'Отправить',
	'CHAT_LOGIN'			=> 'Зайдите в чат',
	'CHAT_IN'			=> 'В чате',
	'CHAT_CANT_VIEWCHATU'			=> 'Вы <strong>не можете</strong> видеть пользователей в чате',
	'CHAT_CANT_VIEWCHATM'			=> 'Вы <strong>не можете</strong> видеть сообщения в чате',
	'CHAT_CANT_ADDCHATM'			=> 'Вы <strong>не можете</strong> добавлять сообщения в чат',
	'CHAT_LA_CHECK'			=> 'Вы были удалены из чата',
	'CHAT_DELMESS'			=> 'Удалить сообщение?',
	'CHAT_LANG_ERROR' => 'Не найден языковой файл',

	'CHAT_PM'			=> 'ЛС',
	'CHAT_PMHLP'			=> 'Включить/выключить режим личных сообщений',
	'CHAT_PMIN'			=> ' <strong>лс от</strong> ',
	'CHAT_PMOUT'			=> ' <strong>лс для</strong> ',
	'CHAT_PMFOR'			=> ' <strong>для</strong> ',
	'CHAT_PMFROM'		=> ' <strong>от</strong> ',
	'CHAT_KICK'			=> 'К',
	'CHAT_BAN'			=> 'Б',
	'CHAT_UNBAN'			=> 'Р',
	'CHAT_QUICKBAN'			=> 'С',
	'CHAT_KICKHLP'			=> 'Удалить пользователя из чата',
	'CHAT_BANHLP'			=> 'Забанить пользователя',
	'CHAT_UNBANHLP'			=> 'Разбанить пользователя',
	'CHAT_QUICKBANHLP'			=> 'Быстрый бан пользователя',
	'CHAT_SETHIDDENHLP' => 'Включить/выключить скрытый режим',
	'CHAT_BANNED' => 'Вы забанены в этом чате до %s',
	'CHAT_REFRESH' => 'Нельзя обновлять и/или входить чат ранее установленной задержки',
	'CHAT_UBANNED' => 'Забанен в этом чате до %s',
	'CHAT_UKICK' => 'Удалить пользователя из чата?',
	'CHAT_UUNBAN' => 'Разбанить пользователя?',
	'CHAT_ERRDESCR' => array(
		1=>'Вы были забанены в этом чате администратором.',
		2=>'Нельзя отправлять сообщение в чат ранее установленной задержки.',
		3=>'Вы НЕ можете читать и добавлять сообщения в чат.',
		4=>'Вы были удалены из чата из-за неактивности.',
		5=>'Вы были удалены из чата администратором.',
	),
	'UBAN_TIME' => 'Время бана в часах (не более 999)',
	'UBAN_CONFIRM' => 'Забанить пользователя на',
	'UBAN_CONFIRM2' => '?',
	'UBAN_CONFIRM3' => 'часов?',
	'CHAT_PMUSER_EXITED' => 'Пользователь которому вы отправляете личное сообщение вышел из чата, отправить сообщение в общий чат?',
	'CHAT_DELETE' => 'Удалить',
	'CHAT_DELETE_USERS' => 'Удалить пользователей из чата?',
	'CHAT_DELETE_MESS' => 'Удалить сообщения чата?',
	'CHAT_DELETE_AMESS' => 'Удалить архив сообщений чата?',
	'CHAT_MCLEAN' => 'все сообщения',
	'CHAT_UCLEAN' => 'пользователей',
	'CHAT_ACLEAN' => 'архив',
	'CHAT_ARCHIVE' => 'Архив',
	'CHAT_VIEW_ARCHIVE' => 'просмотреть',
	'CHAT_HIDE_ARCHIVE' => 'скрыть',
	'CHAT_ARCHIVE' => 'Архива чата',
	'CHAT_TUPDATE' => '<strong>Обновление</strong>: каждые [<strong>%d</strong>] секунд',
	'CHAT_HEIGHT' => 'Высота',
	'CHAT_MHEIGHT' => 'Уменьшить высоту чата',
	'CHAT_PHEIGHT' => 'Увеличить высоту чата',
	'CHAT_OHEIGHT' => 'Восстановить высоту чата',
	'CHAT_CHATKEY_ERROR' => 'Ошибка создания чат-ключа ...',
	'CHAT_ARCH_EMPTY' => 'В архиве нет сообщений.',
	'CHAT_USER_ADM' => 'Администрировать пользователя',
	'CHAT_TO_USER' => 'Обратиться к пользователю',
	'CHAT_EDIT' => 'Редактировать',
	'CHAT_EDIT_CANCEL' => 'Отменить',
	'CHAT_GUEST' => 'Гость',
	'CHAT_BOT' => '[Бот]',
	'CHAT_WAIT_MESSAGES' => 'Подождите, сообщения загрузятся через <strong>%d</strong> секунд ..',
	'CHAT_AJAX_ERROR' => 'Ошибка: невозможно создать XmlHttpRequest объект. Пожалуйста обновите свой браузер или используйте другой.',
	'CHAT_SOUND_SWITCH' => 'Включить/выключить звуковое оповещение о новых сообщениях',

	'MESSAGE_EDIT_ERROR' => 'Сообщение не существует или нет прав для редактирования.',
	'MESS_DEL' => 'удалить',
	'MESS_EDIT' => 'редактировать',

	'BBCHAT_B' => 'Текст жирным шрифтом [b]текст[/b]',
	'BBCHAT_I' => 'Текст курсивом [i]текст[/i]',
	'BBCHAT_U' => 'Текст с подчёркиванием [u]текст[/u]',
	'BBCHAT_S' => 'Зачёркнутый текст  [s]текст[/s]',
	'BBCHAT_SAMP' => 'Текст программы/скрипта (моноширинным шрифтом)  [samp]текст[/samp]',
	'BBCHAT_COLOR' => 'Текст другим цветом [color=цвет]текст[/color], цвет названием (red) или буквенно-цифровым кодом FF0000',
	'BBCHAT_SIZE' => 'Текст другим размером [size=размер]текст[/size], размер от 0.7 до 3.0',
	'BBCHAT_URL' => 'Ссылка [url]ссылка[/url]',
	'BBCHAT_URL2' => 'Текст ссылкой [url=ссылка]текст[/url]',
	'BBCHAT_Q' => 'Простая цитата [q]цитата[/q]',
	'BBCHAT_BLOCKQUOTE' => 'Цитата [blockquote]цитата[/blockquote]',
	'BBCHAT_PRE' => 'Текст без форматирования [pre]текст[/pre]',
	'BBCHAT_CODE' => 'Исходный код программы/скрипта [code]текст[/code]',
	'BBCHAT_NOPARSE' => 'Текст с отключением бб-кодов [noparse]текст с бб-кодом[/noparse]',
	'BBCHAT_IMG' => 'Изображение [img=ссылка на изображение][/img]',
	'datetime'			=> array(
		'TODAY'		=> 'Сегодня',
		'TOMORROW'	=> 'Завтра',
		'YESTERDAY'	=> 'Вчера',

		'AGO'		=> array(
			0		=> 'менее минуты назад',
			1		=> '%d минуту назад',
			2		=> '%d минуты назад',
			5		=> '%d минут назад',
			21		=> '%d минуту назад',
			22		=> '%d минуты назад',
			25		=> '%d минут назад',
			31		=> '%d минуту назад',
			32		=> '%d минуты назад',
			35		=> '%d минут назад',
			41		=> '%d минуту назад',
			42		=> '%d минуты назад',
			45		=> '%d минут назад',
			51		=> '%d минуту назад',
			52		=> '%d минуты назад',
			55		=> '%d минут назад',
			60		=> '1 час назад',
		),

		'Sunday'	=> 'Воскресенье',
		'Monday'	=> 'Понедельник',
		'Tuesday'	=> 'Вторник',
		'Wednesday'	=> 'Среда',
		'Thursday'	=> 'Четверг',
		'Friday'	=> 'Пятница',
		'Saturday'	=> 'Суббота',

		'Sun'		=> 'Вс',
		'Mon'		=> 'Пн',
		'Tue'		=> 'Вт',
		'Wed'		=> 'Ср',
		'Thu'		=> 'Чт',
		'Fri'		=> 'Пт',
		'Sat'		=> 'Сб',

		'January'	=> 'Январь',
		'February'	=> 'Февраль',
		'March'		=> 'Март',
		'April'		=> 'Апрель',
		'May'		=> 'Май',
		'June'		=> 'Июнь',
		'July'		=> 'Июль',
		'August'	=> 'Август',
		'September'	=> 'Сентябрь',
		'October'	=> 'Октябрь',
		'November'	=> 'Ноябрь',
		'December'	=> 'Декабрь',

		'Jan'		=> 'янв',
		'Feb'		=> 'фев',
		'Mar'		=> 'мар',
		'Apr'		=> 'апр',
		'May_short'	=> 'май',	// Short representation of "May". May_short used because in English the short and long date are the same for May.
		'Jun'		=> 'июн',
		'Jul'		=> 'июл',
		'Aug'		=> 'авг',
		'Sep'		=> 'сен',
		'Oct'		=> 'окт',
		'Nov'		=> 'ноя',
		'Dec'		=> 'дек',
	),
));

?>
