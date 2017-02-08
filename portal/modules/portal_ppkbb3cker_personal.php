<?php
/**
*
* @package ppkBB3cker
* @copyright (c) PPK 2011
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
* @package Custom
*/
class portal_ppkbb3cker_personal_module
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
	public $name = 'PORTAL_PPKBB3CKER_PERSONAL';

	/**
	* Default module-image:
	* file must be in "{T_THEME_PATH}/images/portal/"
	*/
	public $image_src = 'portal_custom.png';

	/**
	* module-language file
	* file must be in "language/{$user->lang}/mods/portal/"
	*/
	public $language = 'portal_ppkbb3cker_personal_module';

	/**
	* personal acp template
	* file must be in "adm/style/portal/"
	*/
	public $custom_acp_tpl = 'acp_portal_personal';

	public function get_template_center($module_id)
	{
		global $config, $template, $portal_config, $user, $db, $cache, $phpEx, $phpbb_root_path, $auth, $table_prefix;

		$config['board3_personal_' . $module_id . '_htmltpl']=basename($config['board3_personal_' . $module_id . '_htmltpl']);

		if($config['board3_personal_' . $module_id . '_htmltpl']=='' || !file_exists("{$phpbb_root_path}styles/{$user->theme['theme_path']}/template/portal/modules/{$config['board3_personal_' . $module_id . '_htmltpl']}.html"))
		{
			return false;
		}

		if($config['board3_personal_' . $module_id . '_langtpl']!='')
		{
			$user->add_lang("mods/portal/".basename($config['board3_personal_' . $module_id . '_langtpl']));
		}

		if($config['board3_personal_' . $module_id . '_phptpl']!='')
		{
			@include_once("{$phpbb_root_path}portal/modules/".basename($config['board3_personal_' . $module_id . '_phptpl']).".{$phpEx}");
		}

		return $config['board3_personal_' . $module_id . '_htmltpl'].'.html';
	}

	public function get_template_side($module_id)
	{
		global $config, $template, $portal_config, $user, $db, $cache, $phpEx, $phpbb_root_path, $auth;

		$config['board3_personal_' . $module_id . '_htmltpl']=basename($config['board3_personal_' . $module_id . '_htmltpl']);

		if($config['board3_personal_' . $module_id . '_htmltpl']=='' || !file_exists("{$phpbb_root_path}styles/{$user->theme['theme_path']}/template/portal/modules/{$config['board3_personal_' . $module_id . '_htmltpl']}.html"))
		{
			return false;
		}

		if($config['board3_personal_' . $module_id . '_langtpl']!='')
		{
			$user->add_lang("mods/portal/".basename($config['board3_personal_' . $module_id . '_langtpl']));
		}

		if($config['board3_personal_' . $module_id . '_phptpl']!='')
		{
			@include_once("{$phpbb_root_path}portal/modules/".basename($config['board3_personal_' . $module_id . '_phptpl']).".{$phpEx}");
		}

		return $config['board3_personal_' . $module_id . '_htmltpl'].'.html';
	}

	public function get_template_acp($module_id)
	{
		return array(
			'title'	=> 'PORTAL_PPKBB3CKER_PERSONAL',
			'vars'	=> array(
				'legend1'								=> 'PORTAL_PPKBB3CKER_PERSONAL',
				//'board3_personal_' . $module_id . '_phptpl'	=> array('lang' => 'PORTAL_PERSONAL_PHPTPL',		'validate' => 'string',	'type' => 'personal', 'method' => 'manage_personal', 'submit' => 'update_personal', 'explain' => true),
				'board3_personal_' . $module_id . '_htmltpl'	=> array('lang' => 'PORTAL_PERSONAL_HTMLTPL',		'validate' => 'string',	'type' => 'personal', 'method' => 'manage_personal', 'submit' => 'update_personal', 'explain' => true),
				//'board3_personal_' . $module_id . '_langtpl'	=> array('lang' => 'PORTAL_PERSONAL_LANGTPL',		'validate' => 'string',	'type' => 'personal',  'method' => 'manage_personal', 'submit' => 'update_personal', 'explain' => true),
			),
		);
	}

	/**
	* API functions
	*/
	public function install($module_id)
	{
		set_config('board3_personal_' . $module_id . '_phptpl', '');
		set_config('board3_personal_' . $module_id . '_htmltpl', '');
		set_config('board3_personal_' . $module_id . '_langtpl', '');
		set_config('board3_personal_' . $module_id . '_title', '');
		set_config('board3_personal_' . $module_id . '_image_src', '');
		set_config('board3_personal_' . $module_id . '_permission', '');
		return true;
	}

	public function uninstall($module_id)
	{
		global $db;

		$del_config = array(
			'board3_personal_' . $module_id . '_phptpl',
			'board3_personal_' . $module_id . '_htmltpl',
			'board3_personal_' . $module_id . '_langtpl',
			'board3_personal_' . $module_id . '_title',
			'board3_personal_' . $module_id . '_image_src',
			'board3_personal_' . $module_id . '_permission',
		);
		$sql = 'DELETE FROM ' . CONFIG_TABLE . '
			WHERE ' . $db->sql_in_set('config_name', $del_config);
		return (isset($check) ? $check : $db->sql_query($sql)); // if something went wrong, make sure we are aware of the first query
	}

	public function manage_personal($value, $key, $module_id)
	{
		global $db, $portal_config, $config, $template, $user, $phpEx, $phpbb_admin_path, $phpbb_root_path;

		$u_action = append_sid($phpbb_admin_path . 'index.' . $phpEx, 'i=portal&amp;mode=config&amp;module_id=' . $module_id);

		$action = (isset($_POST['reset'])) ? 'reset' : '';
		$action = (isset($_POST['submit'])) ? 'save' : $action;

		switch($action)
		{
			// Save changes
			case 'save':

				if (!check_form_key('acp_portal'))
				{
					trigger_error($user->lang['FORM_INVALID']. adm_back_link($u_action), E_USER_WARNING);
				}

				$personal_permission = request_var('permission-setting', array(0 => ''));
				$personal_title = utf8_normalize_nfc(request_var('module_name', '', true));
				$personal_image_src = utf8_normalize_nfc(request_var('module_image', ''));
				$groups_ary = array();

				// get groups and check if the selected groups actually exist
				$sql = 'SELECT group_id
						FROM ' . GROUPS_TABLE . '
						ORDER BY group_id ASC';
				$result = $db->sql_query($sql);
				while($row = $db->sql_fetchrow($result))
				{
					$groups_ary[] = $row['group_id'];
				}
				$db->sql_freeresult($result);

				$personal_permission = array_intersect($personal_permission, $groups_ary);
				$personal_permission = implode(',', $personal_permission);

				$personal_langtpl=request_var('personal_langtpl', '');
				$personal_phptpl=request_var('personal_phptpl', '');
				$personal_htmltpl=request_var('personal_htmltpl', '');

				if($personal_langtpl!='')
				{
					if(!file_exists("{$phpbb_root_path}language/{$user->data['user_lang']}/mods/portal/".basename($personal_langtpl).".{$phpEx}"))
					{
						$personal_langtpl='';
					}
				}
				if($personal_phptpl!='')
				{
					if(!file_exists("{$phpbb_root_path}portal/modules/".basename($personal_phptpl).".{$phpEx}"))
					{
						$personal_phptpl='';
					}
				}
				if($personal_htmltpl!='')
				{
					if(!file_exists("{$phpbb_root_path}styles/{$user->theme['theme_path']}/template/portal/modules/".basename($personal_htmltpl).".html"))
					{
						trigger_error($user->lang['ACP_PORTAL_PERSONAL_HTMLTPL_NOTEXISTS']. adm_back_link($u_action), E_USER_WARNING);
					}
				}
				else
				{
					trigger_error($user->lang['ACP_PORTAL_PERSONAL_HTMLTPL_EMPTY']. adm_back_link($u_action), E_USER_WARNING);
				}

				set_config('board3_personal_' . $module_id . '_title', $personal_title);
				set_config('board3_personal_' . $module_id . '_image_src', $personal_image_src);
				set_config('board3_personal_' . $module_id . '_permission', $personal_permission);
				set_config('board3_personal_' . $module_id . '_phptpl', $personal_phptpl);
				set_config('board3_personal_' . $module_id . '_htmltpl', $personal_htmltpl);
				set_config('board3_personal_' . $module_id . '_langtpl', $personal_langtpl);
			break;

			case'reset':
			default:
				$template->assign_vars(array(
					'PERSONAL_HTMLTPL' => $config['board3_personal_' . $module_id . '_htmltpl'],
					'PERSONAL_PHPTPL' => $config['board3_personal_' . $module_id . '_phptpl'],
					'PERSONAL_LANGTPL' => $config['board3_personal_' . $module_id . '_langtpl'],
				));

				$groups_ary = (isset($config['board3_personal_' . $module_id . '_permission'])) ? explode(',', $config['board3_personal_' . $module_id . '_permission']) : array();

				// get group info from database and assign the block vars
				$sql = 'SELECT group_id, group_name
						FROM ' . GROUPS_TABLE . '
						ORDER BY group_id ASC';
				$result = $db->sql_query($sql);
				while($row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('permission_setting_personal', array(
						'SELECTED'		=> (in_array($row['group_id'], $groups_ary)) ? true : false,
						'GROUP_NAME'	=> (isset($user->lang['G_' . $row['group_name']])) ? $user->lang['G_' . $row['group_name']] : $row['group_name'],
						'GROUP_ID'		=> $row['group_id'],
					));
				}
				$db->sql_freeresult($result);
			break;
		}

	}

	public function update_personal($key, $module_id)
	{
		$this->manage_personal('', $key, $module_id);
	}
}
