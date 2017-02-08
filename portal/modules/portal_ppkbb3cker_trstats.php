<?php
/**
*
* @package ppkBB3cker
* @copyright (c) PPK
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

/**
* @package ppkBB3cker
*/
class portal_ppkbb3cker_trstats_module
{
	/**
	* Allowed columns: Just sum up your options (Exp: left + right = 10)
	* top		1
	* left		2
	* center	4
	* right		8
	* bottom	16
	*/
	public $columns = 31;

	/**
	* Default modulename
	*/
	public $name = 'PPKBB3CKER_TRSTATS';

	/**
	* Default module-image:
	* file must be in "{T_THEME_PATH}/images/portal/"
	*/
	public $image_src = 'portal_statistics.png';

	/**
	* module-language file
	* file must be in "language/{$user->lang}/mods/portal/"
	*/
	public $language = 'portal_ppkbb3cker_trstats_module';

	/**
	* hide module name in ACP configuration page
	*/
	public $hide_name = false;


	public function get_template_side($module_id)
	{
		global $phpbb_root_path, $template, $phpEx, $config, $db, $user;

		if(!defined('CGP_KEY'))
		{
			include($phpbb_root_path.'tracker/include/index_add_cron.'.$phpEx);
		}

		$show_module = true;

		include($phpbb_root_path.'tracker/include/tracker_stat.'.$phpEx);

		if($show_module)
		{
			return 'ppkbb3cker_trstats_side.html';
		}
	}

	public function get_template_center($module_id)
	{
		global $phpbb_root_path, $template, $phpEx, $config, $db, $user;

		if(!defined('CGP_KEY'))
		{
			include($phpbb_root_path.'tracker/include/index_add_cron.'.$phpEx);
		}

		$show_module = true;

		include($phpbb_root_path.'tracker/include/tracker_stat.'.$phpEx);

		if($show_module)
		{
			return 'ppkbb3cker_trstats_center.html';
		}
	}

	public function get_template_acp($module_id)
	{
		return array(
			'title'	=> 'PPKBB3CKER_TRSTATS',
			'vars'	=> array(),
		);
	}

	/**
	* API functions
	*/
	public function install($module_id)
	{
		set_config('board3_ppk_trstats_number_' . $module_id, 1);
		//purge_tracker_config(true);
		return true;
	}

	public function uninstall($module_id)
	{
		global $db;

		$del_config = array(
			'board3_ppk_trstats_number_' . $module_id,
		);
		/*$sql = 'DELETE FROM ' . TRACKER_CONFIG_TABLE . '
			WHERE ' . $db->sql_in_set('config_name', $del_config);
		$db->sql_query($sql);
		purge_tracker_config(true);	*/
		$sql = 'DELETE FROM ' . CONFIG_TABLE . '
			WHERE ' . $db->sql_in_set('config_name', $del_config);
		return $db->sql_query($sql);
	}
}
