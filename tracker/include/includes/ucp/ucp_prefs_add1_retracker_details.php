<?php
/**
*
* @package ppkBB3cker
* @version $Id: ucp_prefs_add1_retracker_details.php 1.000 2009-08-19 12:22:00 PPK $
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

add_form_key('ucp_retracker');

//!$config['ppkbb_rtrack_enable'][0] ? $config['ppkbb_rtrack_enable'][1]=0 : '';

if ($submit && $config['ppkbb_rtrack_enable'][1])
{
	$message='';
	if (check_form_key('ucp_retracker'))
	{
		if(request_var('add_rtrack', 0))
		{
			$d_rtrack=$exs_rtrack=$forb_rtracks=array();

			$sql_where="(zone_id='{$user->data['user_id']}' AND rtrack_user='1') OR rtrack_forb IN(2, 3)";
			$sql='SELECT * FROM '.TRACKER_RTRACK_TABLE." WHERE {$sql_where}";
			$result=$db->sql_query($sql);
			while($row=$db->sql_fetchrow($result))
			{
				if(!$row['rtrack_forb'])
				{
					$exs_rtrack[]=$row['rtrack_url'];
				}
				else
				{
					$forb_rtracks[]=$row;
				}
			}
			$db->sql_freeresult($result);

			$v_rtrack=0;
			foreach(@$_POST['rtrack_url'] as $k=>$v)
			{
				if(@$_POST['rtrack_delete'][$k])
				{
					$d_rtrack[]=my_int_val($k);
				}
				else
				{
					if(STRIP)
					{
						$_POST['rtrack_url'][$k]=stripslashes($_POST['rtrack_url'][$k]);
					}

					$_POST['rtrack_url'][$k]=utf8_normalize_nfc($_POST['rtrack_url'][$k]);

					if(@$_POST['rtrack_url'][$k]!='' && (!preg_match('#^(http|udp):\/\/(\w+|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})#', @$_POST['rtrack_url'][$k]) || strlen(@$_POST['rtrack_url'][$k]) > 512))
					{
						$message=$user->lang['INVALID_RTRACK_URL'];
						trigger_error($message.'<br /><br />'. sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>'));
					}

					if(in_array(@$_POST['rtrack_url'][$k], $exs_rtrack) && $k==0)
					{
						continue;
					}

					$rtrack_forb=0;
					if(sizeof($forb_rtracks))
					{
						foreach($forb_rtracks as $f)
						{
							if($f['forb_type']=='s' && strstr($_POST['rtrack_url'][$k], $f['rtrack_url']))
							{
								$rtrack_forb=1;
							}
							else if($f['forb_type']=='i' && stristr($_POST['rtrack_url'][$k], $f['rtrack_url']))
							{
								$rtrack_forb=1;
							}
							else if($f['forb_type']=='r' && preg_match("{$f['rtrack_url']}", $_POST['rtrack_url'][$k]))
							{
								$rtrack_forb=1;
							}
						}
					}
					if($rtrack_forb)
					{
						$message=$user->lang['FORB_RTRACK_URL'];
						trigger_error($message.'<br /><br />'. sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>'));
					}

					if($k==0 && @$_POST['rtrack_url'][$k]!='')
					{
						$exs_rtrack[]=@$_POST['rtrack_url'][$k];
						$v_rtrack+=1;
						if($v_rtrack > $config['ppkbb_rtrack_enable'][1])
						{
							$message=sprintf($user->lang['MAXUSERS_ANNOUNCES_LIMIT'], $config['ppkbb_rtrack_enable'][1]);
							trigger_error($message.'<br /><br />'. sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>'));
						}
						$sql='INSERT INTO '.TRACKER_RTRACK_TABLE."
							(zone_id, rtrack_url, rtrack_user, rtrack_enabled)
								VALUES('{$user->data['user_id']}',
								'".$db->sql_escape($_POST['rtrack_url'][$k])."',
								'1',
								'".(@$_POST['rtrack_enabled'][$k] ? 1 : 0)."')";
						$result=$db->sql_query($sql);
					}
					else if(@$_POST['rtrack_url'][$k]!='')
					{
						$exs_rtrack[]=@$_POST['rtrack_url'][$k];
						$v_rtrack+=1;
						if($v_rtrack > $config['ppkbb_rtrack_enable'][1])
						{
							$message=sprintf($user->lang['MAXUSERS_ANNOUNCES_LIMIT'], $config['ppkbb_rtrack_enable'][1]);
							trigger_error($message.'<br /><br />'. sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>'));
						}
						$sql='UPDATE '.TRACKER_RTRACK_TABLE." SET
							zone_id='{$user->data['user_id']}',
							rtrack_url='".$db->sql_escape($_POST['rtrack_url'][$k])."',
							rtrack_user='1',
							rtrack_enabled='".(@$_POST['rtrack_enabled'][$k] ? 1 : 0)."'
								WHERE id='".my_int_val($k)."' AND zone_id='{$user->data['user_id']}' AND rtrack_user='1'";
						$result=$db->sql_query($sql);
					}
				}

			}
			if($d_rtrack)
			{
				$sql='DELETE FROM '.TRACKER_RTRACK_TABLE." WHERE id IN('".implode("', '", $d_rtrack)."') AND zone_id='{$user->data['user_id']}' AND rtrack_user='1'";
				$result=$db->sql_query($sql);
			}
			$message=$user->lang['RTRACK_SUCCESS'];
			trigger_error($message.'<br /><br />'. sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>'));
		}
		$message ? trigger_error($message.'<br /><br />'. sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>')) : '';
	}
	else
	{
		$error[] = 'FORM_INVALID';
	}
	// Replace "error" strings with their real, localised form
	$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$user->lang['\\1'])) ? \$user->lang['\\1'] : '\\1'", $error);
}

if($config['ppkbb_rtrack_enable'][1])
{
	if(request_var('add_new', ''))
	{
		$template->assign_block_vars('rtracks', array(
			'COUNT'	=> 0,
			'URL'	=> '',
			'ENABLED'	=> '',
			)
		);
		$template->assign_vars(array(
			'S_NEW_RTRACK'	=> true,
			'S_HIDDEN_FIELD' => '<input type="hidden" name="add_rtrack" value="1" />',
			)
		);
	}
	else
	{
		$sql_where="zone_id='{$user->data['user_id']}' AND rtrack_user='1'";
		$sql='SELECT * FROM '.TRACKER_RTRACK_TABLE." WHERE {$sql_where}";
		$result=$db->sql_query($sql);
		while($row=$db->sql_fetchrow($result))
		{
			$template->assign_block_vars('rtracks', array(
				'COUNT'	=> $row['id'],
				'URL'	=> htmlspecialchars($row['rtrack_url']),
				'ENABLED'	=> $row['rtrack_enabled'] ? ' checked="checked"' : '',
				)
			);
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'S_VIEW_RTRACK'	=> true,
			'S_HIDDEN_FIELD' => '<input type="hidden" name="add_rtrack" value="1" />',
			)
		);
	}
}
else
{
	$template->assign_vars(array(
		'S_VIEW_RTRACK'	=> false,
		)
	);
}

$template->assign_vars(array(
	'ERROR'			=> (sizeof($error)) ? implode('<br />', $error) : '',
));
?>
