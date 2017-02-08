<?php
/**
*
* @package cgp
* @version $Id: hook_cgp.php, v.1.1.2, 2013/04/05 Kot $
* @copyright (c) 2013 Vitaly Filatenko
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

if (!class_exists('CGP'))
{
	include($phpbb_root_path . 'includes/cache_guests_pages.' . $phpEx);
}

if (defined('CGP_ENABLED'))
{
	/**
	 * Disables adding &sid= for cached guests pages
	 *
	 */
	function cgp_clear_guest_sid(&$hook, $url, $params = false, $is_amp = true, $session_id = false)
	{
		global $config, $user;
		
		if (isset($config['cgp_remove_sid']) && $config['cgp_remove_sid'] && CGP::is_cacheable_user($user))
		{
			global $SID, $_SID;
			$SID = $_SID = '';
		}
	}

	// Register hook
	$phpbb_hook->register('append_sid', 'cgp_clear_guest_sid');
}

?>