<?php
/**
*
* @package cgp
* @version $Id: cgp_check_version.php, v.1.1.2, 2013/04/05 Kot $
* @copyright (c) 2013 Vitaly Filatenko
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package mod_version_check
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

class cgp_check_version
{
	function version()
	{
		return array(
			'author'	=> 'Kot Matroskin',
			'title'		=> 'Cache guests &amp; bots pages',
			'tag'		=> 'cache_guests_pages',
			'version'	=> '1.1.2',
			'file'		=> array('scooterclub.by', 'kot', 'mods.xml'),
		);
	}
}

?>