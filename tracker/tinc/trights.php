<?php
/**
*
* @package ppkBB3cker
* @version $Id: trights.php 1.000 2009-01-26 13:46:00 PPK $
* @copyright (c) 2008 PPK
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

define('ACL_OPTIONS_TABLE', $table_prefix . 'acl_options');
define('ACL_GROUPS_TABLE', $table_prefix . 'acl_groups');
define('USER_GROUP_TABLE', $table_prefix . 'user_group');

$acl=my_auth($user);

$rights=array();
if(!$unregtorr)
{
	$rights[0] = acl_get('u_download') && acl_get('f_download', $forum_id) ? 1 : 0;
	$rights[1] = acl_get('u_canusetracker') && acl_get('f_canusetracker', $forum_id) ? 1 : 0;
	$rights[2] = acl_get('u_canskiprcheck') && acl_get('f_canskiprcheck', $forum_id) ? 1 : 0;
	$rights[3] = acl_get('u_attach50down') && acl_get('f_attach50down', $forum_id) ? 1 : 0;
	$rights[4] = acl_get('u_attach100down') && acl_get('f_attach100down', $forum_id) ? 1 : 0;
	$rights[5] = acl_get('u_canusefree') && acl_get('f_canusefree', $forum_id) ? 1 : 0;
	$rights[6] = acl_get('u_canusebonus') && acl_get('f_canusebonus', $forum_id) ? 1 : 0;
	$rights[7] = acl_get('u_canskipmaxseed') ? 1 : 0;
	$rights[8] = acl_get('u_canskipmaxleech') ? 1 : 0;
	$rights[9] = acl_get('u_canskiprequpload') && acl_get('f_canskiprequpload', $forum_id) ? 1 : 0;
	$rights[10] = acl_get('u_canskipreqratio') && acl_get('f_canskipreqratio', $forum_id) ? 1 : 0;
	$rights[11] = acl_get('u_candowntorr') && acl_get('f_candowntorr', $forum_id) ? 1 : 0;
	$rights[12] = 0;
	$rights[13] = acl_get('u_canskipiprestr') ? 1 : 0;
	$rights[14] = 0;
}
else
{
	$rights[0] = acl_get('u_download') ? 1 : 0;
	$rights[1] = acl_get('u_canusetracker') ? 1 : 0;
	$rights[2] = acl_get('u_canskiprcheck') || $config['ppkbb_tcallow_unregtorr']==2 ? 1 : 0;
	$rights[3] = acl_get('u_attach50down') || $config['ppkbb_tcallow_unregtorr']==2 ? 1 : 0;
	$rights[4] = acl_get('u_attach100down') || $config['ppkbb_tcallow_unregtorr']==2 ? 1 : 0;
	$rights[5] = acl_get('u_canusefree') || $config['ppkbb_tcallow_unregtorr']==2 ? 1 : 0;
	$rights[6] = acl_get('u_canusebonus') || $config['ppkbb_tcallow_unregtorr']==2 ? 1 : 0;
	$rights[7] = acl_get('u_canskipmaxseed') || $config['ppkbb_tcallow_unregtorr']==2 ? 1 : 0;
	$rights[8] = acl_get('u_canskipmaxleech') || $config['ppkbb_tcallow_unregtorr']==2 ? 1 : 0;
	$rights[9] = acl_get('u_canskiprequpload') || $config['ppkbb_tcallow_unregtorr']==2 ? 1 : 0;
	$rights[10] = acl_get('u_canskipreqratio') || $config['ppkbb_tcallow_unregtorr']==2 ? 1 : 0;
	$rights[11] = acl_get('u_candowntorr') ? 1 : 0;
	$rights[12] = $config['ppkbb_tcallow_unregtorr']==2 ? 1 : 0;
	$rights[13] = acl_get('u_canskipiprestr') || $config['ppkbb_tcallow_unregtorr']==2 ? 1 : 0;
	$rights[14] = $config['ppkbb_tcallow_unregtorr']==2 ? 1 : 0;
}
unset($acl);

$s_rights="{$dt} {$forum_id} ".implode(' ', $rights);
$updatepeers[]="rights='{$s_rights}'";

function my_auth($userdata)
{
	global $phpbb_root_path, $phpEx, $tincludedir;

	$this_acl = $this_cache = $this_acl_options = array();

	$sql = 'SELECT auth_option_id, auth_option, is_global, is_local
		FROM ' . ACL_OPTIONS_TABLE . '
		ORDER BY auth_option_id';
	$result = my_sql_query($sql);

	$global = $local = 0;
	$this_acl_options = array();
	while ($row = mysql_fetch_array($result))
	{
		if ($row['is_global'])
		{
			$acl['this_acl_options']['global'][$row['auth_option']] = $global++;
		}

		if ($row['is_local'])
		{
			$acl['this_acl_options']['local'][$row['auth_option']] = $local++;
		}

		$acl['this_acl_options']['id'][$row['auth_option']] = (int) $row['auth_option_id'];
		$acl['this_acl_options']['option'][(int) $row['auth_option_id']] = $row['auth_option'];
	}
	mysql_free_result($result);

	if (!trim($userdata['user_permissions']))
	{
		include($tincludedir.'tauth.'.$phpEx);
		$userdata['user_permissions']=acl_cache($userdata, $acl['this_acl_options']);
	}

	$user_permissions = explode("\n", $userdata['user_permissions']);

	foreach ($user_permissions as $f => $seq)
	{
		if ($seq)
		{
			$i = 0;

			if (!isset($acl['this_acl'][$f]))
			{
				$acl['this_acl'][$f] = '';
			}

			while ($subseq = substr($seq, $i, 6))
			{
				// We put the original bitstring into the acl array
				$acl['this_acl'][$f] .= str_pad(base_convert($subseq, 36, 2), 31, 0, STR_PAD_LEFT);
				$i += 6;
			}
		}
	}

	return $acl;
}

//From includes/auth.php
function acl_get($opt, $f = 0)
{
	global $acl;

	$negate = false;

	if (strpos($opt, '!') === 0)
	{
		$negate = true;
		$opt = substr($opt, 1);
	}

	if (!isset($acl['this_cache'][$f][$opt]))
	{
		// We combine the global/local option with an OR because some options are global and local.
		// If the user has the global permission the local one is true too and vice versa
		$acl['this_cache'][$f][$opt] = false;

		// Is this option a global permission setting?
		if (isset($acl['this_acl_options']['global'][$opt]))
		{
			if (isset($acl['this_acl'][0]))
			{
				$acl['this_cache'][$f][$opt] = $acl['this_acl'][0][$acl['this_acl_options']['global'][$opt]];
			}
		}

		// Is this option a local permission setting?
		// But if we check for a global option only, we won't combine the options...
		if ($f != 0 && isset($acl['this_acl_options']['local'][$opt]))
		{
			if (isset($acl['this_acl'][$f]) && isset($acl['this_acl'][$f][$acl['this_acl_options']['local'][$opt]]))
			{
				$acl['this_cache'][$f][$opt] |= $acl['this_acl'][$f][$acl['this_acl_options']['local'][$opt]];
			}
		}
	}

	// Founder always has all global options set to true...
	return ($negate) ? !$acl['this_cache'][$f][$opt] : $acl['this_cache'][$f][$opt];

}
?>
