<?php
/**
*
* @package ppkBB3cker
* @version $Id: tauth.php 1.000 2008-10-30 11:49:00 PPK $
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

define('ACL_NEVER', 0);
define('ACL_YES', 1);
define('ACL_NO', -1);

/**
* Cache data to user_permissions row
*/
function acl_cache($userdata, $this_acl_options)
{
	global $c;

	// Empty user_permissions
	$userdata['user_permissions'] = '';

	$hold_ary = acl_raw_data_single_user($userdata['user_id']);

	// Key 0 in $hold_ary are global options, all others are forum_ids

	// If this user is founder we're going to force fill the admin options ...
	if ($userdata['user_type'] == USER_FOUNDER)
	{
		foreach ($this_acl_options['global'] as $opt => $id)
		{
			if (strpos($opt, 'a_') === 0)
			{
				$hold_ary[0][$this_acl_options['id'][$opt]] = ACL_YES;
			}
		}
	}

	$hold_str = build_bitstring($hold_ary, $this_acl_options);

	if ($hold_str)
	{
		$userdata['user_permissions'] = $hold_str;

		$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_permissions = '" . mysql_real_escape_string($userdata['user_permissions'], $c) . "',
				user_perm_from = 0
			WHERE user_id = " . $userdata['user_id'];
		my_sql_query($sql);
	}

	return $userdata['user_permissions'];
}

/**
* Build bitstring from permission set
*/
function build_bitstring(&$hold_ary, $this_acl_options)
{
	$hold_str = '';

	if (sizeof($hold_ary))
	{
		ksort($hold_ary);

		$last_f = 0;

		foreach ($hold_ary as $f => $auth_ary)
		{
			$ary_key = (!$f) ? 'global' : 'local';

			$bitstring = array();
			foreach ($this_acl_options[$ary_key] as $opt => $id)
			{
				if (isset($auth_ary[$this_acl_options['id'][$opt]]))
				{
					$bitstring[$id] = $auth_ary[$this_acl_options['id'][$opt]];

					$option_key = substr($opt, 0, strpos($opt, '_') + 1);

					// If one option is allowed, the global permission for this option has to be allowed too
					// example: if the user has the a_ permission this means he has one or more a_* permissions
					if ($auth_ary[$this_acl_options['id'][$opt]] == ACL_YES && (!isset($bitstring[$this_acl_options[$ary_key][$option_key]]) || $bitstring[$this_acl_options[$ary_key][$option_key]] == ACL_NEVER))
					{
						$bitstring[$this_acl_options[$ary_key][$option_key]] = ACL_YES;
					}
				}
				else
				{
					$bitstring[$id] = ACL_NEVER;
				}
			}

			// Now this bitstring defines the permission setting for the current forum $f (or global setting)
			$bitstring = implode('', $bitstring);

			// The line number indicates the id, therefore we have to add empty lines for those ids not present
			$hold_str .= str_repeat("\n", $f - $last_f);

			// Convert bitstring for storage - we do not use binary/bytes because PHP's string functions are not fully binary safe
			for ($i = 0, $bit_length = strlen($bitstring); $i < $bit_length; $i += 31)
			{
				$hold_str .= str_pad(base_convert(str_pad(substr($bitstring, $i, 31), 31, 0, STR_PAD_RIGHT), 2, 36), 6, 0, STR_PAD_LEFT);
			}

			$last_f = $f;
		}
		unset($bitstring);

		$hold_str = rtrim($hold_str);
	}

	return $hold_str;
}

/**
* Get raw acl data based on user for caching user_permissions
* This function returns the same data as acl_raw_data(), but without the user id as the first key within the array.
*/
function acl_raw_data_single_user($user_id)
{
	$this_role_cache = array();

	// We pre-fetch roles
	$sql = 'SELECT *
		FROM ' . ACL_ROLES_DATA_TABLE . '
		ORDER BY role_id ASC';
	$result = my_sql_query($sql);

	while ($row = mysql_fetch_array($result))
	{
		$this_role_cache[$row['role_id']][$row['auth_option_id']] = (int) $row['auth_setting'];
	}
	mysql_free_result($result);

	foreach ($this_role_cache as $role_id => $role_options)
	{
		$this_role_cache[$role_id] = serialize($role_options);
	}

	$hold_ary = array();

	// Grab user-specific permission settings
	$sql = 'SELECT forum_id, auth_option_id, auth_role_id, auth_setting
		FROM ' . ACL_USERS_TABLE . '
		WHERE user_id = ' . $user_id;
	$result = my_sql_query($sql);

	while ($row = mysql_fetch_array($result))
	{
		// If a role is assigned, assign all options included within this role. Else, only set this one option.
		if ($row['auth_role_id'])
		{
			$hold_ary[$row['forum_id']] = (empty($hold_ary[$row['forum_id']])) ? unserialize($this_role_cache[$row['auth_role_id']]) : $hold_ary[$row['forum_id']] + unserialize($this_role_cache[$row['auth_role_id']]);
		}
		else
		{
			$hold_ary[$row['forum_id']][$row['auth_option_id']] = $row['auth_setting'];
		}
	}
	mysql_free_result($result);

	// Now grab group-specific permission settings
	$sql = 'SELECT a.forum_id, a.auth_option_id, a.auth_role_id, a.auth_setting
		FROM ' . ACL_GROUPS_TABLE . ' a, ' . USER_GROUP_TABLE . ' ug
		WHERE a.group_id = ug.group_id
			AND ug.user_pending = 0
			AND ug.user_id = ' . $user_id;
	$result = my_sql_query($sql);

	while ($row = mysql_fetch_array($result))
	{
		if (!$row['auth_role_id'])
		{
			_set_group_hold_ary($hold_ary[$row['forum_id']], $row['auth_option_id'], $row['auth_setting']);
		}
		else if (!empty($this_role_cache[$row['auth_role_id']]))
		{
			foreach (unserialize($this_role_cache[$row['auth_role_id']]) as $option_id => $setting)
			{
				_set_group_hold_ary($hold_ary[$row['forum_id']], $option_id, $setting);
			}
		}
	}
	mysql_free_result($result);

	return $hold_ary;
}

/**
* Private function snippet for setting a specific piece of the hold_ary
*/
function _set_group_hold_ary(&$hold_ary, $option_id, $setting)
{
	global $this_acl_options;

	if (!isset($hold_ary[$option_id]) || (isset($hold_ary[$option_id]) && $hold_ary[$option_id] != ACL_NEVER))
	{
		$hold_ary[$option_id] = $setting;

		// If we detect ACL_NEVER, we will unset the flag option (within building the bitstring it is correctly set again)
		if ($setting == ACL_NEVER)
		{
			$flag = substr($this_acl_options['option'][$option_id], 0, strpos($this_acl_options['option'][$option_id], '_') + 1);
			$flag = (int) $this_acl_options['id'][$flag];

			if (isset($hold_ary[$flag]) && $hold_ary[$flag] == ACL_YES)
			{
				unset($hold_ary[$flag]);

/*					This is uncommented, because i suspect this being slightly wrong due to mixed permission classes being possible
				if (in_array(ACL_YES, $hold_ary))
				{
					$hold_ary[$flag] = ACL_YES;
				}*/
			}
		}
	}
}

?>
