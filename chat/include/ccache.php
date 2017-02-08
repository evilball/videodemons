<?php
/**
*
* @package ppkBB3cker
* @version $Id: ccache.php 1.000 2013-04-22 13:29:46 PPK $
* @copyright (c) 2013 PPK
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

define('CHMOD_ALL', 7);// - all permissions (7)
define('CHMOD_READ', 4);// - read permission (4)
define('CHMOD_WRITE', 2);// - write permission (2)
define('CHMOD_EXECUTE', 1);// - execute permission (1)

################################################################################
function c_recache($fname, $data, $var='')
{
	global $ccachedir, $phpEx, $phpbb_root_path;

	if(!$fname || !is_writable($ccachedir))
	{
		return false;
	}

	$var ? '' : $var='cache_data';
	$data["{$fname}_cachetime"]=time();

	$fn="{$ccachedir}data_ppkbb3cker_chat_{$fname}.{$phpEx}";
	$fo=@fopen($fn, 'wb');
	if($fo)
	{
		@flock($fo, LOCK_EX);
		@fwrite($fo, "<?php if (!defined('IN_PHPBB')) exit;\n\${$var}=unserialize('".serialize($data) ."');\n?>");
		//@fflush($fo);
		@flock($fo, LOCK_UN);
		@fclose($fo);

		if(!function_exists('phpbb_chmod'))
		{
			include("{$phpbb_root_path}tracker/include/file_functions.{$phpEx}");
		}

		phpbb_chmod($fn, CHMOD_READ | CHMOD_WRITE);
	}
}

function c_cleancache($fname)
{
	global $ccachedir, $phpEx;

	if(!$fname)
	{
		return false;
	}

	$fn="{$ccachedir}data_ppkbb3cker_chat_{$fname}.{$phpEx}";
	@unlink($fn);
}

?>
