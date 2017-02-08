<?php
/**
*
* @package ppkBB3cker
* @version $Id: tvalidip.php 1.000 2009-08-13 15:12:00 PPK $
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

//Массив забаненных или зарезервированных IP адресов
$reserved_ips = array (
		//array('0.0.0.0', '2.255.255.255'),
		//array('10.0.0.0', '10.255.255.255'),
		//array('127.0.0.0', '127.255.255.255'),
		//array('169.254.0.0', '169.254.255.255'),
		//array('172.16.0.0', '172.31.255.255'),
		//array('192.0.2.0', '192.0.2.255'),
		//array('192.168.0.0', '192.168.255.255'),
		//array('255.255.255.0', '255.255.255.255')
);

if ($reserved_ips && !validip($ip, $reserved_ips))
{
	err('Invalid or reserved IP');
}

function validip($ip, $a=array())
{
	$ip=sprintf("%u", ip2long($ip));

	if ($ip && $a)
	{
		foreach ($a as $r)
		{
			$min = sprintf("%u", ip2long($r[0]));
			$max = sprintf("%u", ip2long($r[1]));
			if($ip >= $min && $ip <= $max)
			{
				return false;
			}
		}
		return true;
	}
	else
	{
		return true;
	}
}
?>
