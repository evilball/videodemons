<?php
/**
*
* @package cgp
* @version $Id: acp_cgp.php, v.1.1.2, 2013/04/05 Kot $
* @copyright (c) 2013 Vitaly Filatenko
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_PHPBB') || !defined('ADMIN_START'))
{
	exit;
}

class acp_cgp
{
	var $u_action;
	var $new_config = array();
	private static $version = '1.1.2';

	function main($id, $mode)
	{
		global $db, $user, $template, $acm_type;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$action = request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;

		$this->new_config = $config;

		// Ensure settings are initialised to defaults
		$this->new_config['cgp_enabled'] = isset($config['cgp_enabled']) ? $config['cgp_enabled'] : false;
		$this->new_config['cgp_ttl'] = isset($config['cgp_ttl']) ? $config['cgp_ttl'] : 720;
		$this->new_config['cgp_version'] = isset($config['cgp_version']) ? $config['cgp_version'] : self::$version;
		$this->new_config['cgp_remove_sid'] = isset($config['cgp_remove_sid']) ? $config['cgp_remove_sid'] : true;
		$this->new_config['cgp_force_fcache'] = isset($config['cgp_force_fcache']) ? $config['cgp_force_fcache'] : true;

		// calculate cache size
		if (!class_exists('acm_file_extended'))
		{
			include($phpbb_root_path . 'includes/acm/acm_file_extended.' . $phpEx);
		}

		$cache_ext = new acm_file_extended();

		if ($cache_ext->number_files() !== false && $cache_ext->dir_size() !== false)
		{
			$cache_size_msg = $user->lang('CGP_CACHE_INFO',
				number_format($cache_ext->number_files(), 0, '.', $user->lang('CGP_TH_SEP')),
				get_formatted_filesize($cache_ext->dir_size()));
		}
		else
		{
			$cache_size_msg = $user->lang('CGP_CACHE_INFO_ERROR');
		}

		$error = array();

		// check for cache type settings
		if ($acm_type != 'file' && (!isset($config['cgp_force_fcache']) || !$config['cgp_force_fcache']))
		{
			$error[] = $user->lang('CGP_CACHE_TYPE_ERROR', $acm_type);
		}

		/**
		*	Validation types are:
		*		string, int, bool,
		*		script_path (absolute path in url - beginning with / and no trailing slash),
		*		rpath (relative), rwpath (realtive, writeable), path (relative path, but able to escape the root), wpath (writeable)
		*/
		switch ($mode)
		{
			case 'main':
				$display_vars = array(
					'title' => 'CGP_MOD_NAME',
					'vars'	=> array(
							'legend1'			=> 'CGP_GENERAL_SETTINGS',
							'cgp_enabled'		=> array('lang' => 'CGP_ENABLED', 'validate' => 'int:0', 'type' => 'text:1:1', 'explain' => true),
							'cgp_ttl'			=> array('lang' => 'CGP_TTL', 'validate' => 'int', 'type' => 'text:5:5', 'explain' => true),
							'cgp_remove_sid'	=> array('lang' => 'CGP_REMOVE_SID', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
							'cgp_force_fcache'	=> array('lang' => 'CGP_FORCE_FCACHE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),

							'cgp_multi_styles'	=> array('lang' => 'CGP_MULTI_STYLES', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
							'cgp_places'	=> array('lang' => 'CGP_PLACES', 'validate' => 'string', 'type' => 'text:7:7', 'explain' => true),

							'legend2'			=> 'CGP_ADDITIONAL_INFO',
							'custom1'			=> array('lang' => 'CGP_VERSION', 'type' => $this->new_config['cgp_version'], 'explain' => false),
							'custom2'			=> array('lang' => 'CGP_CACHE_SIZE', 'type' => $cache_size_msg, 'explain' => true),
							'legend3'			=> 'ACP_SUBMIT_CHANGES',
							)
				);
				break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
				break;
		}

		if (isset($display_vars['lang']))
		{
			$user->add_lang($display_vars['lang']);
		}

		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;

		// We validate the complete config if whished
		if(isset($display_vars) && sizeof($display_vars) > 0)
		{
			validate_config_vars($display_vars['vars'], $cfg_array, $error_validate);

			// Do not write values if there is an error
			if (sizeof($error_validate))
			{
				$submit = false;
				$error = array_merge($error, $error_validate);
			}

			// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
			foreach ($display_vars['vars'] as $config_name => $null)
			{
				if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend'))
				{
					continue;
				}

				$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

				if ($submit)
				{
					set_config($config_name, $config_value);

					if($config_name=='cgp_places')
					{
						set_tracker_config('ppkbb_cgp_places', $config_value);
						$config_value=my_split_config($config_value, 4, 'my_int_val');
						array_sum($config_value)!=4 ? '' : set_config('cgp_enabled', '0');
					}

				}
			}
		}

		if ($submit)
		{

			purge_tracker_config(true);

			add_log('admin', 'LOG_CGP_UPDATED');
			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->tpl_name = 'acp_board';
		$this->page_title = $display_vars['title'];

		$title_explain = $user->lang[$display_vars['title'] . '_EXPLAIN'];

		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang[$display_vars['title']],
			'L_TITLE_EXPLAIN'	=> $title_explain,

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action)
		);

		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
				);

				continue;
			}

			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain']))
			{
				$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			}
			else if ($vars['explain'])
			{
				$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}

			if (strpos($config_key, 'custom') === false)
			{
				$template->assign_block_vars('options', array(
					'KEY'			=> $config_key,
					'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
					'S_EXPLAIN'		=> $vars['explain'],
					'TITLE_EXPLAIN'	=> $l_explain,
					'CONTENT'		=> build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars),
					)
				);
			}
			else
			{
				// display non-config variable
				$template->assign_block_vars('options', array(
					'KEY'			=> $config_key,
					'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
					'S_EXPLAIN'		=> $vars['explain'],
					'TITLE_EXPLAIN'	=> $l_explain,
					'CONTENT'		=> $vars['type'],
					)
				);
			}

			unset($display_vars['vars'][$config_key]);
		}
	}
}
