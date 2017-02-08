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
class portal_ppkbb3cker_ltorrents_module
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
	public $name = 'PPKBB3CKER_LTORRENTS';

	/**
	* Default module-image:
	* file must be in "{T_THEME_PATH}/images/portal/"
	*/
	public $image_src = 'portal_menu.png';

	/**
	* module-language file
	* file must be in "language/{$user->lang}/mods/portal/"
	*/
	public $language = 'portal_ppkbb3cker_ltorrents_module';

	/**
	* hide module name in ACP configuration page
	*/
	public $hide_name = false;

	/**
	* custom acp template
	* file must be in "adm/style/portal/"
	*/
	public $custom_acp_tpl = '';

	public function get_template_center($module_id)
	{
		global $phpbb_root_path, $template, $phpEx, $config, $db, $user, $auth, $cache;

		$show_module = true;

		include($phpbb_root_path.'tracker/include/ltorrents_block.'.$phpEx);

		if($show_module)
		{
			return 'ppkbb3cker_ltorrents_center.html';
		}
	}

	public function get_template_acp($module_id)
	{
		return array(
			'title'	=> 'PPKBB3CKER_LTORRENTS',
			'vars'	=> array(
				'legend1' => 'ACP_PPKBB3CKER_LTORRENTS_SETTINGS',
				'board3_ppkbb3cker_portal_last_torrents_'.$module_id => array('lang' => 'DISPLAY_LATESTTORRENTS', 'validate' => 'array', 'type' => 'array:5:5', 'explain' => true),
				'board3_ppkbb3cker_portal_torrents_perpage_'.$module_id => array('lang' => 'PORTAL_TORR_PERPAGE', 'validate' => 'int:0', 'type' => 'text:3:3', 'explain' => true),
				'board3_ppkbb3cker_portal_torrents_textlength_'.$module_id => array('lang' => 'PORTAL_TORR_TEXTLENGTH', 'validate' => 'int:0', 'type' => 'text:5:5', 'explain' => true),
				'board3_ppkbb3cker_portal_torrents_posttime_'.$module_id => array('lang' => 'PORTAL_TORR_TIME', 'validate' => 'array', 'type' => 'array:6:6', 'explain' => true),
				'board3_ppkbb3cker_portal_exclude_forums_'.$module_id => array('lang' => 'PORTAL_EXCLUDE_FORUMS', 'validate' => 'array', 'type' => 'custom', 'explain' => true, 'method' => 'select_tracker_forums2', 'submit' => 'store_selected_tracker_forums2'),
				'board3_ppkbb3cker_portal_trueexclude_forums_'.$module_id => array('lang' => 'PORTAL_TRUEEXCLUDE_FORUMS', 'validate' => 'int', 'type' => 'radio:yes_no', 'explain' => true),
				'board3_ppkbb3cker_portal_lttorrents_display_'.$module_id => array('lang' => 'PORTAL_LTDISPLAY_OPT', 'validate' => 'array', 'type' => 'array:3:3', 'explain' => true),
			),
		);
	}

	public function select_tracker_forums2($value, $key)
	{
		global $user, $config;

		$forum_list = make_forum_select(false, false, true, false, true, false, true, 1);

		$selected = array();
		if(is_array($config[$key]))
		{
			$selected = $config[$key];
		}
		else if(strlen($config[$key]) > 0)
		{
			$selected = explode(',', $config[$key]);
		}
		// Build forum options
		$s_forum_options = '<select id="' . $key . '" name="' . $key . '[]" multiple="multiple">';
		foreach ($forum_list as $f_id => $f_row)
		{
			$s_forum_options .= '<option value="' . $f_id . '"' . ((in_array($f_id, $selected)) ? ' selected="selected"' : '') . (($f_row['disabled']) ? ' disabled="disabled" class="disabled-option"' : '') . '>' . $f_row['padding'] . $f_row['forum_name'] . '</option>';
		}
		$s_forum_options .= '</select>';

		return $s_forum_options;
	}

	public function store_selected_tracker_forums2($key, $module_id)
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
		set_config('board3_ppk_ltorrents_number_' . $module_id, 1);
		set_tracker_config('board3_ppkbb3cker_portal_last_torrents_'.$module_id, '1 0 0 1');
		set_tracker_config('board3_ppkbb3cker_portal_torrents_perpage_'.$module_id, '10');
		set_tracker_config('board3_ppkbb3cker_portal_torrents_textlength_'.$module_id, '300');
		set_tracker_config('board3_ppkbb3cker_portal_torrents_posttime_'.$module_id, '-1 1 0');
		set_tracker_config('board3_ppkbb3cker_portal_exclude_forums_'.$module_id, '');
		set_tracker_config('board3_ppkbb3cker_portal_trueexclude_forums_'.$module_id, 1);
		set_tracker_config('board3_ppkbb3cker_portal_lttorrents_display_'.$module_id, '1 2 3 4');
		purge_tracker_config(true);

		return true;
	}

	public function uninstall($module_id)
	{
		global $db;

		$del_config = array(
			'board3_ppk_ltorrents_number_' . $module_id,
			'board3_ppkbb3cker_portal_last_torrents_'.$module_id,
			'board3_ppkbb3cker_portal_torrents_perpage_'.$module_id,
			'board3_ppkbb3cker_portal_torrents_textlength_'.$module_id,
			'board3_ppkbb3cker_portal_torrents_posttime_'.$module_id,
			'board3_ppkbb3cker_portal_exclude_forums_'.$module_id,
			'board3_ppkbb3cker_portal_trueexclude_forums_'.$module_id,
			'board3_ppkbb3cker_portal_lttorrents_display_'.$module_id,
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
