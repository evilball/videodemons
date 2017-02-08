<?php
/**
*
* @package ppkBB3cker
* @version $Id: tcrestrua.php 1.000 2009-11-15 17:20:00 PPK $
* @copyright (c) 2009 PPK
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

//Массив забаненных клиентов по заголовку USER_AGENT
//значение 1 в массиве: строка для совпадения, значение 2: модификатор обработки
//s - обрабатывать как обычную регистро-зависимую строку
//i - обрабатывать как обычную регистро-независимую строку
//r - обрабатывать как регулярное выражение
$crestrua = array (
		array('#^(Mozilla|Opera|Links|Lynks)#', 'r'),
		//array('Windows', 's'),
		//array('Linux', 'i'),
);

if($crestrua)
{
	foreach($crestrua as $ua)
	{
		if($ua[1]=='s' && strstr($agent, $ua[0]))
		{
			err("Banned User Agent");
		}
		else if($ua[1]=='i' && stristr($agent, $ua[0]))
		{
			err("Banned User Agent");
		}
		else if($ua[1]=='r' && preg_match("{$ua[0]}", $agent))
		{
			err("Banned User Agent");
		}
	}
}
?>
