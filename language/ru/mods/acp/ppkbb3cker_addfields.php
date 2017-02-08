<?php
/**
*
* acp_board [Russian]
*
* @package language
* @version $Id: ppkbb3cker_addfields.php, v 1.000 2012-06-14 12:12:39 PPK Exp $
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
	'ACP_TRACKER_ADDFIELDS_TPL'				=> 'Шаблоны дополнительных полей',
	'ACP_TRACKER_ADDFIELDS_TPL_EXPLAIN'				=> 'Управление шаблонами дополнительных полей форумов.<br /><strong>Заголовок темы</strong> - шаблон для составления заголовка темы, строка в фомате: #название поля#, #название поля#, из данных указанных полей будет автоматически формироваться заголовок названия темы торрента, названия полей необходимо обрамлять символом #решётки#, необязательные для заполнения поля необходимо заключать в {фигурные} скобки.<br /><strong>Описание шаблона</strong> - описание шаблона дополнительных полей или правила их заполнения, данный текст будет отображаться при создании нового торрента перед дополнительными полями.',
	'ACP_TRACKER_ADDFIELDS_TPL_SETTINGS'				=> 'Управление шаблонами дополнительных полей форумов',
	'ACP_TRACKER_ADDFIELDS'				=> 'Дополнительные поля',
	'ACP_TRACKER_ADDFIELDS_EXPLAIN'				=> 'Управление шаблонами дополнительных полей форумов.<br />Если поле является выпадающим списком или списком со множественным выбором для него нельзя указать количество полей больше 1.<br />Если количество полей равно 0 - поле отображаться не будет.',

	'ADDFIELD_DESCR' => 'Описание шаблона',
	'ADDFIELD_DESCR_EXPLAIN' => '',
	'ADDFIELD_SUBJECT' => 'Заголовок темы',
	'COPY_SETS' => 'Копировать',
	'COPY_SETS_EXPLAIN' => 'Создать копию шаблона',
	'SETS_COPY' => 'Копия: ',
	'SETS_NAME'	=> 'Шаблон дополнительных полей',
	'SET_NAME' => 'Название шаблона',
	'VIEW_SETS'	=> 'Просмотреть',
	'REQUIRED'	=> 'Обязательное для заполнения количество',
	'COUNT'	=> 'Количество полей',
	'ORDER'	=> 'Порядок отображения',
	'FIELD_IDENT'	=> 'Идентификатор поля',
	'FIELD_NAME' => 'Название поля',
	'FIELD_DESCR' => 'Значение поля',

	'ADDF_SORT_SUCCESS' => 'Порядок успешно изменён.',
	'NO_NEW_ADDF' => 'Нет доступных полей для добавления.',
	'ADDF_TPL' => 'шаблон',
));
?>
