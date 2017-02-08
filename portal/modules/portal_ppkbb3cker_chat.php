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
class portal_ppkbb3cker_chat_module
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
	public $name = 'PPKBB3CKER_CHAT';

	/**
	* Default module-image:
	* file must be in "{T_THEME_PATH}/images/portal/"
	*/
	public $image_src = 'portal_members.png';

	/**
	* module-language file
	* file must be in "language/{$user->lang}/mods/portal/"
	*/
	public $language = 'portal_ppkbb3cker_chat_module';

	/**
	* custom acp template
	* file must be in "adm/style/portal/"
	*/
	public $custom_acp_tpl = '';

	public function get_template_center($module_id)
	{
		global $config, $template, $user, $auth, $phpbb_root_path, $phpEx, $db;

		$chat_id=$config['board3_ppkbb3cker_chat_'.$module_id];

		if(!$config['ppkbb_chat_enable'] || (!$user->data['is_registered'] && (!$config['ppkbb_chat_guests'] || $config['cgp_enabled'])) || $user->data['user_tracker_options'][1] || !$chat_id || $config['ppkbb_chat_display'][0])
		{
			return false;
		}

		include($phpbb_root_path.'chat/ppkbb3cker_chat.'.$phpEx);

		return 'ppkbb3cker_chat_center.html';
	}

	public function get_template_acp($module_id)
	{
		return array(
			'title'	=> 'PPKBB3CKER_CHAT',
			'vars'	=> array(
				'legend1' => 'ACP_PPKBB3CKER_CHAT_SETTINGS',
				'board3_ppkbb3cker_chat_' . $module_id => array('lang' => 'CHAT_PORTAL_CHAT', 'validate' => 'int:0', 'type' => 'custom', 'method'=>'select_chat_forums', 'explain' => true, 'submit' => 'store_selected_chat_forums'),
			)
		);
	}

	public function select_chat_forums($value, $key)
	{
		global $user, $config, $config;

		$forum_list = make_forum_select(false, false, true, true, true, false, true, 2);

		$selected = array();
		if(isset($config[$key]))
		{
			if(is_array($config[$key]))
			{
				$selected = $config[$key];
			}
			else if(strlen($config[$key]) > 0)
			{
				$selected = explode(',', $config[$key]);
			}
		}
		// Build forum options
		$s_forum_options = '<select id="' . $key . '" name="' . $key . '[]"><option value="0"></option>';
		foreach ($forum_list as $f_id => $f_row)
		{
			$s_forum_options .= '<option value="' . $f_id . '"' . ((in_array($f_id, $selected)) ? ' selected="selected"' : '') . (($f_row['disabled']) ? ' disabled="disabled" class="disabled-option"' : '') . '>' . $f_row['padding'] . $f_row['forum_name'] . '</option>';
		}
		$s_forum_options .= '</select>';

		return $s_forum_options;

	}

	public function store_selected_chat_forums($key, $module_id)
	{
		global $db, $cache;

		// Get selected forums
		$values = request_var($key, array(0 => ''));

		$forums = implode(',', $values);

		set_tracker_config($key, $forums);

	}

	/**
	* API functions
	*/
	public function install($module_id)
	{
		set_config('board3_ppk_chat_number_' . $module_id, 1);
		set_tracker_config('board3_ppkbb3cker_chat_' . $module_id, '0');
		purge_tracker_config(true);
		return true;
	}

	public function uninstall($module_id)
	{
		global $db;

		$del_config = array(
			'board3_ppk_chat_number_' . $module_id,
			'board3_ppkbb3cker_chat_' . $module_id,
		);
		$sql = 'DELETE FROM ' . TRACKER_CONFIG_TABLE . '
			WHERE ' . $db->sql_in_set('config_name', $del_config);
		$db->sql_query($sql);
		purge_tracker_config(true);
		$sql = 'DELETE FROM ' . CONFIG_TABLE . '
			WHERE ' . $db->sql_in_set('config_name', $del_config);
		return $db->sql_query($sql);
	}

}
