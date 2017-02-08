<?php
/**
*
* @package ppkBB3cker
* @version $Id: tcrestrport.php 1.000 2009-11-15 16:59:00 PPK $
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

//Массив забаненных или зарезервированных портов
$crestrport=array(
		//array(411, 413),// direct connect
		//array(6881, 6889),// bittorrent (AZUREUS)
		//array(1214),// kazaa
		//array(6346, 6347),// gnutella
		//array(4662),// emule
		//array(6699),// winmx
);

if($crestrport)
{
	foreach($crestrport as $ports)
	{
		if($port >= $ports[0] && $port <= $ports[1])
		{
			err("Banned port");
		}
	}
}
?>
