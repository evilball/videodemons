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
class portal_ppkbb3cker_topdown_torrents_module
{
	/**
	* Allowed columns: Just sum up your options (Exp: left + right = 10)
	* top		1
	* left		2
	* center	4
	* right		8
	* bottom	16
	*/
	public $columns = 21;

	/**
	* Default modulename
	*/
	public $name = 'PPKBB3CKER_TOPDOWN_TORRENTS';

	/**
	* Default module-image:
	* file must be in "{T_THEME_PATH}/images/portal/"
	*/
	public $image_src = 'portal_link_us.png';

	/**
	* module-language file
	* file must be in "language/{$user->lang}/mods/portal/"
	*/
	public $language = 'portal_ppkbb3cker_topdown_torrents_module';

	/**
	* hide module name in ACP configuration page
	*/
	public $hide_name = false;


	public function get_template_center($module_id)
	{
		global $phpbb_root_path, $template, $phpEx, $config, $db, $user, $auth;

		$show_module = false;
		if($auth->acl_get('u_canviewtopdowntorrents') && $config['ppkbb_topdown_torrents'][0] && !$user->data['user_tracker_options'][2])
		{
			$template->assign_vars(array(
				'TDT_URL'	=> append_sid("{$phpbb_root_path}topdown_torrents.{$phpEx}", 'fid=0&pid=1&id=_b3p'.$module_id, false),
				'TDT_ID'	=> '_b3p'.$module_id,
				'TOPDOWN_TORRENTS_POSTERS' => true,
				'S_TOPDOWN_TORRENTS_WIDTH' => $config['ppkbb_topdown_torrents'][4],
				'S_TOPDOWN_TORRENTS_WIDTH2' => $config['ppkbb_topdown_torrents'][12]==1 ? $config['ppkbb_topdown_torrents'][5]*2 : false,
				'S_TOPDOWN_TORRENTS_HEIGHT' => $config['ppkbb_topdown_torrents'][5]+10,
				'S_TOPDOWN_TORRENTS_BUTTPOS' => my_int_val($config['ppkbb_topdown_torrents'][5]/2),
				'S_TDT_TYPE' => $config['ppkbb_topdown_torrents'][12],
				'S_TOPDOWN_TORRENTS' => ($config['ppkbb_topdown_torrents'][11] ? sprintf($user->lang['TOPDOWN_TORRENTS_ASNEWTORRENTS']) : sprintf($user->lang['TOPDOWN_TORRENTS'])),
				'S_TOPDOWN_TORRENTS_AUTOSTEP' => $config['ppkbb_topdown_torrents_options'][0] ? 'true' : 'false',
				'S_TOPDOWN_TORRENTS_MOVEBY' => $config['ppkbb_topdown_torrents_options'][1] ? $config['ppkbb_topdown_torrents_options'][1] : 1,
				'S_TOPDOWN_TORRENTS_PAUSE' => $config['ppkbb_topdown_torrents_options'][2] ? $config['ppkbb_topdown_torrents_options'][2]*1000 : 1000,
				'S_TOPDOWN_TORRENTS_SPEED' => $config['ppkbb_topdown_torrents_options'][3] ? $config['ppkbb_topdown_torrents_options'][3]*1000 : 3000,
				'S_TOPDOWN_TORRENTS_WRAPAROUND' => $config['ppkbb_topdown_torrents_options'][4] ? 'true' : 'false',
				'S_TOPDOWN_TORRENTS_WRAPBEHAVIOR' => in_array($config['ppkbb_topdown_torrents_options'][5], array('pushpull', 'slide')) ? $config['ppkbb_topdown_torrents_options'][5] : 'slide',
				'S_TOPDOWN_TORRENTS_PERSIST' => $config['ppkbb_topdown_torrents_options'][6] ? 'true' : 'false',
				'S_TOPDOWN_TORRENTS_DEFAULTBUTTONS' => $config['ppkbb_topdown_torrents_options'][7] ? 'true' : 'false',
				'S_TOPDOWN_TORRENTS_MOVEBY2' => $config['ppkbb_topdown_torrents_options'][8] ? $config['ppkbb_topdown_torrents_options'][8] : 1,
				)
			);

			$show_module = true;
		}

		if($show_module)
		{
			return 'ppkbb3cker_topdown_torrents_center.html';
		}
	}

	public function get_template_acp($module_id)
	{
		return array(
			'title'	=> 'PPKBB3CKER_TOPDOWN_TORRENTS',
			'vars'	=> array(),
		);
	}

	/**
	* API functions
	*/
	public function install($module_id)
	{
		set_config('board3_ppk_topdown_torrents_number_' . $module_id, 1);
		//purge_tracker_config(true);
		return true;
	}

	public function uninstall($module_id)
	{
		global $db;

		$del_config = array(
			'board3_ppk_topdown_torrents_number_' . $module_id,
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
