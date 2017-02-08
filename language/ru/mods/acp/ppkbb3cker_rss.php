<?php
/**
*
* acp_board [Russian]
*
* @package language
* @version $Id: ppkbb3cker_rss.php, v 1.000 2012-06-14 09:45:24 PPK Exp $
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
	'ACP_PPKBB_RSS'			=> 'Каналы торрентов и комментариев',
	'ACP_PPKBB_RSS_EXPLAIN'			=> '',

	'ACP_FEED_TRTORRENTS' => 'Включить общий канал для торрентов',
	'ACP_FEED_TRTORRENTS_EXPLAIN' => 'Этот канал отображает последние торренты из всех форумов',
	'ACP_FEED_TRCOMMENTS' => 'Включить общий канал для комментариев',
	'ACP_FEED_TRCOMMENTS_EXPLAIN' => 'Этот канал отображает последние комментарии из всех форумов',
	'ACP_FEED_TRFTORRENTS' => 'Включить каналы торрентов для форумов',
	'ACP_FEED_TRFTORRENTS_EXPLAIN' => 'Этот канал отображает последние торренты из отдельных форумов',
	'ACP_FEED_TRFCOMMENTS' => 'Включить каналы комментариев для торрентов',
	'ACP_FEED_TRFCOMMENTS_EXPLAIN' => 'Этот канал отображает последние комментарии из отдельных форумов',
	'ACP_FEED_TRTCOMMENTS' => 'Включить каналы комментариев для тем торрентов',
	'ACP_FEED_TRTCOMMENTS_EXPLAIN' => 'Этот канал отображает последние комментарии из отдельных форумов для отдельных тем',
	'ACP_FEED_ENBLIST' => 'Форумы для каналов торрентов и комментариев',
	'ACP_FEED_ENBLIST_EXPLAIN' => 'Исключать указанные форумы из каналов',
	'ACP_FEED_TRUEENBLIST' => 'Исключить эти форумы',
	'ACP_FEED_TRUEENBLIST_EXPLAIN' => 'При выводе каналов торрентов или комментариев исключать из каналов форумы определённые выше в опции <u>Исключить эти форумы</u>, иначе наоборот включить только указанные выше форумы (<span style="color:#FF0000;">форумы НЕ трекеры - будут исключены автоматически</span>)',
	'ACP_FEED_TORRLIMIT' => 'Количество элементов на странице для отображения в канале торрентов',
	'ACP_FEED_TORRLIMIT_EXPLAIN' => '',
	'ACP_FEED_TORRSORT' => 'Сортировка по дате темы',
	'ACP_FEED_TORRSORT_EXPLAIN' => 'Сортировать сообщения в каналах торрентов по дате создания темы, иначе по дате создания/изменения торрента',
	'ACP_FEED_COMMLIMIT' => 'Количество элементов на странице для отображения в канале комментариев',
	'ACP_FEED_COMMLIMIT_EXPLAIN' => '',
	'ACP_FEED_TORRTIME' => 'Отображаемые торренты',
	'ACP_FEED_TORRTIME_EXPLAIN' => 'Отображать в каналах торрентов только торренты не старее указанного числа (в днях)',
	'ACP_FEED_COMMTIME' => 'Отображаемые комментарии',
	'ACP_FEED_COMMTIME_EXPLAIN' => 'Отображать в каналах комментариев только комментарии не старее указанного числа (в днях)',


));
?>
