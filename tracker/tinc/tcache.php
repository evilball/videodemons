<?php
/**
*
* @package ppkBB3cker
* @version $Id: tcache.php 1.000 2009-08-13 13:39:00 PPK $
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

################################################################################
function t_recache($fname, $data, $var='')
{
	global $tcachedir, $phpEx, $phpbb_root_path;

	if(!$fname || !is_writable($tcachedir))
	{
		return false;
	}

	$var ? '' : $var='cache_data';
	$data["{$fname}_cachetime"]=time();

	$fn="{$tcachedir}data_ppkbb3cker_{$fname}.{$phpEx}";
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

function t_cleancache($fname)
{
	global $tcachedir, $phpEx;

	if(!$fname)
	{
		return false;
	}

	$fn="{$tcachedir}data_ppkbb3cker_{$fname}.{$phpEx}";
	@unlink($fn);
}

?>
