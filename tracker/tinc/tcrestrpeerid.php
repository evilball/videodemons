<?php
/**
*
* @package ppkBB3cker
* @version $Id: tcrestrpeerid.php 1.000 2009-11-15 18:07:00 PPK $
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

//Массив забаненных клиентов по идентификатору пира
//значение 1 в массиве: строка для совпадения, значение 2: модификатор обработки
//s - обрабатывать как обычную регистро-зависимую строку
//i - обрабатывать как обычную регистро-независимую строку
//r - обрабатывать как регулярное выражение
$crestrpeerid = array (
		//array('#(MLDonkey|ed2k_plugin)#', 'r'),
		//array('AZ', 's'),
		//array('UT', 'i'),
);

if($crestrpeerid)
{
	foreach($crestrpeerid as $peerid)
	{
		if($peerid[1]=='s' && strstr($c_peer_id, $peerid[0]))
		{
			err("Banned User Agent");
		}
		else if($peerid[1]=='i' && stristr($c_peer_id, $peerid[0]))
		{
			err("Banned User Agent");
		}
		else if($peerid[1]=='r' && preg_match("{$peerid[0]}", $c_peer_id))
		{
			err("Banned User Agent");
		}
	}
}
?>
