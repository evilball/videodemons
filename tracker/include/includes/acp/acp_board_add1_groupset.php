<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_groupset.php 1.000 2014-09-29 14:18:51 PPK $
* @copyright (c) 2014 PPK
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

$user->add_lang('mods/acp/ppkbb3cker_groupset');
$gs_select=request_var('gs_select', '');
$groupset_title='ACP_TRACKER_GROUPSET';

if($gs_select && isset($user->lang['GROUPSET'][strtoupper($gs_select)]))
{
	$gs_forum_options=array(
		'addf' => array(1, 1, 0, 1),
		'statuses' => array(1, 1, 1),
		'lsic' => array(0, 0, 0, 1),
		'ftype' => array(0, 0, 0, 1),
		'fpep' => array(0, 0, 0, 1),
		'free' => array(1, 1, 1, 0),
		'reqratio' => array(1, 1, 1, 0),
		'requpload' => array(1, 1, 1, 0),
		'qreply' => array(0, 0, 0, 1),
	);

	$gs_name=$gs_action=$gs_addit='';

	$gs_value=request_var('gs_value', '');
	$gs_submit=request_var('gs_submit', 0);
	$gs_options=request_var('gs_options', '');
	in_array($gs_options, array('all', 'selected', 'not_selected')) ? '' : $gs_options='all';
	$gs_forums=request_var('gs_forums', array(0=>''));
	$view_curr=request_var('view_curr', 0);

	$sql_from=$gs_curr=$gs_curr_name=array();
	$sql_forums='';
	if($gs_submit)
	{
		if($gs_forum_options[$gs_select][0]==1)
		{
			$sql_from[]="forumas='1'";
		}
		else if($gs_forum_options[$gs_select][0]==2)
		{
			$sql_from[]="forumas='2'";
		}
		if($gs_forum_options[$gs_select][1])
		{
			$sql_from[]="forumas!='2'";
		}

		$gs_options=='all' ? $gs_forums=array() : '';
		if($gs_forums)
		{
			$gs_forums=array_map('my_int_val', $gs_forums);
			$sql_forums="forum_id ".($gs_options=='selected' ? '' : 'NOT ')."IN('".implode("', '", $gs_forums)."')";
		}
		else if($gs_options!='all')
		{
			$sql_forums="forum_id=0";
		}
		$this->u_action=append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=groupset&amp;gs_select='.$gs_select);

		if(isset($gs_forum_options[$gs_select][2]) && $gs_forum_options[$gs_select][2] && $sql_from)
		{
			$sql="SELECT forum_id FROM ".FORUMS_TABLE." WHERE ".implode(' AND ', $sql_from);
			$result=$db->sql_query($sql);
			$sql_from=array();
			while($row=$db->sql_fetchrow($result))
			{
				$sql_from[]=$row['forum_id'];
			}
			$db->sql_freeresult($result);
		}
	}

	switch($gs_select)
	{
		case 'addf':
			if($view_curr)
			{
				$sql="SELECT forum_addfields, forum_id FROM ".FORUMS_TABLE." WHERE forum_type='" . FORUM_POST ."'";
				$result=$db->sql_query($sql);
				while($row=$db->sql_fetchrow($result))
				{
					$gs_curr[$row['forum_id']]=$row['forum_addfields'];
				}
				$db->sql_freeresult($result);
			}
			$af_selects='';
			$sql="SELECT * FROM ".TRACKER_ADDFIELDS_TPL."";
			$result=$db->sql_query($sql);
			while($row=$db->sql_fetchrow($result))
			{
				$view_curr ? $gs_curr_name[$row['id']]=$row['af_name'] : '';
				$af_selects.='<option value="'.$row['id'].'">'.$row['af_name'].'</option>';
			}
			$db->sql_freeresult($result);

			$gs_name=$user->lang['GS_TORRENT_ADDF'];
			$gs_action='<select name="gs_value"><option value="0">'.$user->lang['GS_TORRENT_ADDF_WITHOUT'].'</option>'.$af_selects.'</select>';
			$gs_addit='';

			if($gs_submit)
			{
				$gs_value=my_int_val($gs_value);
				$sql="UPDATE ".FORUMS_TABLE." SET forum_addfields='{$gs_value}'".
					($sql_from || $sql_forums ? ' WHERE ' : '').
						($sql_from ? implode(' AND ', $sql_from) : '').
							($sql_forums ? ($sql_from ? ' AND ' : '').$sql_forums : '');
				$result=$db->sql_query($sql);
			}
		break;

		case 'lsic':
			if($view_curr)
			{
				$sql="SELECT forum_subforumslist_type, forum_id FROM ".FORUMS_TABLE." WHERE forum_type='" . FORUM_POST ."'";
				$result=$db->sql_query($sql);
				while($row=$db->sql_fetchrow($result))
				{
					$gs_curr[$row['forum_id']]=$row['forum_subforumslist_type'];
					$gs_curr_name[$row['forum_subforumslist_type']]=$row['forum_subforumslist_type'];
				}
				$db->sql_freeresult($result);

			}
			$gs_name=$user->lang['GS_TORRENT_LSIC'];
			$gs_action='<input type="text" name="gs_value" value="" size="3" maxlength="3" />';
			$gs_addit=$user->lang['GS_TORRENT_LSIC_ADDIT'];

			if($gs_submit)
			{
				$gs_value=my_int_val($gs_value);
				$sql="UPDATE ".FORUMS_TABLE." SET forum_subforumslist_type='{$gs_value}'".
					($sql_from || $sql_forums ? ' WHERE ' : '').
						($sql_from ? implode(' AND ', $sql_from) : '').
							($sql_forums ? ($sql_from ? ' AND ' : '').$sql_forums : '');
				$result=$db->sql_query($sql);
			}
		break;

		case 'free':
			$gs_name=$user->lang['GS_TORRENT_FREE'];
			$gs_action='<input type="text" name="gs_value" value="" size="3" maxlength="3" />&nbsp;%';

			if($gs_submit)
			{
				$gs_value=my_int_val($gs_value);
				$gs_value > 100 || $gs_value < 0 ? $gs_value=0 : '';
				$sql="UPDATE ".TRACKER_TORRENTS_TABLE." SET free='{$gs_value}'".
					($sql_from || $sql_forums ? ' WHERE ' : '').
						($sql_from ? " forum_id IN('".implode("', '", $sql_from)."')" : '').
							($sql_forums ? ($sql_from ? ' AND ' : '').$sql_forums : '');
				$result=$db->sql_query($sql);
			}
		break;

		case 'reqratio':
			$gs_name=$user->lang['GS_TORRENT_REQRATIO'];
			$gs_action='<input type="text" name="gs_value" value="" size="7" maxlength="7" />';
			$gs_addit=$user->lang['GS_TORRENT_REQRATIO_ADDIT'];

			if($gs_submit)
			{
				$gs_value=my_float_val($gs_value);
				$gs_value > 999.999 || $gs_value < 0.000 ? $gs_value=0.000 : '';
				$sql="UPDATE ".TRACKER_TORRENTS_TABLE." SET req_ratio='{$gs_value}'".
					($sql_from || $sql_forums ? ' WHERE ' : '').
						($sql_from ? " forum_id IN('".implode("', '", $sql_from)."')" : '').
							($sql_forums ? ($sql_from ? ' AND ' : '').$sql_forums : '');
				$result=$db->sql_query($sql);
			}
		break;

		case 'requpload':
			$gs_name=$user->lang['GS_TORRENT_REQUPLOAD'];
			$gs_action='<input type="text" name="gs_value" value="" size="16" maxlength="32" />';
			$gs_addit='<select name="requploadv">'.select_size_value().'</select>&nbsp;[<strong>'.$user->lang['BYTES'].'</strong>]';

			if($gs_submit)
			{
				$gs_value=substr(my_int_val($gs_value), 0, 20);
				$sql="UPDATE ".TRACKER_TORRENTS_TABLE." SET req_upload='".get_size_value(request_var('requploadv', 'b'), $gs_value)."'".
					($sql_from || $sql_forums ? ' WHERE ' : '').
						($sql_from ? " forum_id IN('".implode("', '", $sql_from)."')" : '').
							($sql_forums ? ($sql_from ? ' AND ' : '').$sql_forums : '');
				$result=$db->sql_query($sql);
			}
		break;

		case 'ftype':
			if($view_curr)
			{
				$sql="SELECT forumas, forum_id FROM ".FORUMS_TABLE." WHERE forum_type='" . FORUM_POST ."'";
				$result=$db->sql_query($sql);
				while($row=$db->sql_fetchrow($result))
				{
					$gs_curr[$row['forum_id']]=$row['forumas'];
				}
				$db->sql_freeresult($result);

				$gs_curr_name=array(
					0 => $user->lang['GS_TORRENT_FTYPE_FORUM'],
					1 => $user->lang['GS_TORRENT_FTYPE_TRACKER'],
					2 => $user->lang['GS_TORRENT_FTYPE_CHAT'],
				);
			}
			$gs_name=$user->lang['GS_TORRENT_FTYPE'];
			$gs_action='<select name="gs_value"><option value="0">'.$user->lang['GS_TORRENT_FTYPE_FORUM'].'</option><option value="1">'.$user->lang['GS_TORRENT_FTYPE_TRACKER'].'</option><option value="2">'.$user->lang['GS_TORRENT_FTYPE_CHAT'].'</option></select>';
			$gs_addit='';

			if($gs_submit)
			{
				in_array($gs_value, array(0, 1, 2)) ? '' : $gs_value=0;
				$sql="UPDATE ".FORUMS_TABLE." SET forumas='{$gs_value}'".
					($sql_from || $sql_forums ? ' WHERE ' : '').
						($sql_from ? implode(' AND ', $sql_from) : '').
							($sql_forums ? ($sql_from ? ' AND ' : '').$sql_forums : '');
				$result=$db->sql_query($sql);
			}
		break;

		case 'fpep':
			if($view_curr)
			{
				$sql="SELECT forum_first_post_show, forum_id FROM ".FORUMS_TABLE." WHERE forum_type='" . FORUM_POST ."'";
				$result=$db->sql_query($sql);
				while($row=$db->sql_fetchrow($result))
				{
					$gs_curr[$row['forum_id']]=$row['forum_first_post_show'];
				}
				$db->sql_freeresult($result);

				$gs_curr_name=array(
					0 => $user->lang['GS_TORRENT_FPEP_F'],
					1 => $user->lang['GS_TORRENT_FPEP_N'],
					2 => $user->lang['GS_TORRENT_FPEP_M'],
				);
			}
			$gs_name=$user->lang['GS_TORRENT_FPEP'];
			$gs_action='<select name="gs_value"><option value="0">'.$user->lang['GS_TORRENT_FPEP_OFF'].'</option><option value="1">'.$user->lang['GS_TORRENT_FPEP_ON'].'</option><option value="2">'.$user->lang['GS_TORRENT_FPEP_MESS'].'</option></select>';
			$gs_addit='';

			if($gs_submit)
			{
				in_array($gs_value, array(0, 1, 2)) ? '' : $gs_value=0;
				$sql="UPDATE ".FORUMS_TABLE." SET forum_first_post_show='{$gs_value}'".
					($sql_from || $sql_forums ? ' WHERE ' : '').
						($sql_from ? implode(' AND ', $sql_from) : '').
							($sql_forums ? ($sql_from ? ' AND ' : '').$sql_forums : '');
				$result=$db->sql_query($sql);
			}
		break;

		case 'statuses':
			$torrent_statuses=get_torrent_statuses();
			$form_forb='';
			if(sizeof($torrent_statuses['TRACKER_FORB_REASON']))
			{
				ksort($torrent_statuses['TRACKER_FORB_REASON']);
				$forb_sel=array();
				foreach($torrent_statuses['TRACKER_FORB_REASON'] as $rk => $rv)
				{
					if($rk < 0 && $rk > -50 && @!$forb_sel[-1])
					{
						$form_forb.='<option disabled="disabled">'.$user->lang['TRACKER_FORB_MREASON'].'</option>';
						$forb_sel[-1]=1;
					}
					if($rk > 0 && @!$forb_sel[1])
					{
						$form_forb.='<option disabled="disabled">'.$user->lang['TRACKER_FORB_PREASON'].'</option>';
						$forb_sel[1]=1;
					}
					if($rk == 0 && @!$forb_sel[0])
					{
						$form_forb.='<option disabled="disabled">'.$user->lang['TRACKER_FORB_UREASON'].'</option>';
						$forb_sel[0]=1;
					}
					$form_forb.='<option value="'.$rk.'">'.$rv.'</option>';
				}
			}

			$gs_name=$user->lang['GS_TORRENT_STATUS'];
			$gs_action='<select name="gs_value">'.$form_forb.'</select>';
			$gs_addit='';

			if($gs_submit)
			{
				$gs_value=intval($gs_value);
				isset($torrent_statuses['TRACKER_FORB_REASON'][$gs_value]) ? '' : $gs_value=0;
				$sql="UPDATE ".TRACKER_TORRENTS_TABLE." SET forb='{$gs_value}'".
					($sql_from || $sql_forums ? ' WHERE ' : '').
						($sql_from ? " forum_id IN('".implode("', '", $sql_from)."')" : '').
							($sql_forums ? ($sql_from ? ' AND ' : '').$sql_forums : '');
				$result=$db->sql_query($sql);
			}
		break;
		case 'qreply':
			//SELECT * FROM  FORUMS_TABLE WHERE (forum_flags & FORUM_FLAG_QUICK_REPLY)=0
			//UPDATE phpbb_forums SET `forum_flags`=`forum_flags` | 64 where forum_id=3
			//PDATE phpbb_forums SET `forum_flags`=`forum_flags` &~ 64 where forum_id=3

			if($view_curr)
			{
				$sql="SELECT forum_flags, forum_id FROM ".FORUMS_TABLE." WHERE forum_type='" . FORUM_POST ."'";
				$result=$db->sql_query($sql);
				while($row=$db->sql_fetchrow($result))
				{
					$gs_curr[$row['forum_id']]=($row['forum_flags'] & FORUM_FLAG_QUICK_REPLY) ? 1 : 0;
				}
				$db->sql_freeresult($result);

				$gs_curr_name=array(
					0 => $user->lang['NO'],
					1 => $user->lang['YES'],
				);
			}
			$gs_name=$user->lang['GS_TORRENT_QUICKREPLY'];
			$gs_action='<label><input type="radio" name="gs_value" value="1" class="radio" /> - '.$user->lang['YES'].'</label><label><input type="radio" name="gs_value" value="0" class="radio" checked="checked" /> - '.$user->lang['NO'].'</label>';
			$gs_addit='';

			if($gs_submit)
			{
				in_array($gs_value, array(0, 1)) ? '' : $gs_value=0;
				$sql="UPDATE ".FORUMS_TABLE." SET forum_flags=forum_flags ".($gs_value ? '|' : '&~')." ".FORUM_FLAG_QUICK_REPLY.
					($sql_from || $sql_forums ? ' WHERE ' : '').
						($sql_from ? implode(' AND ', $sql_from) : '').
							($sql_forums ? ($sql_from ? ' AND ' : '').$sql_forums : '');
				$result=$db->sql_query($sql);
			}
		break;
	}

	$template->assign_vars(array(
		'S_VIEW_GSS'	=> true,
		'GS_FORUMS'	=> select_gs_forums('gs_forums', $gs_forum_options[$gs_select][0], $gs_forum_options[$gs_select][1], $gs_curr, $gs_curr_name, $view_curr),
		'GS_DESCR' => $user->lang['GROUPSET'][strtoupper($gs_select)],
		'GS_NAME' => $gs_name,
		'GS_ACTION' => $gs_action,
		'GS_ADDIT' => $gs_addit,
		'S_HIDDEN_FIELDS'=>'
			<input type="hidden" name="gs_submit" value="1" >
			<input type="hidden" name="gs_select" value="'.$gs_select.'" >
			',
		'GS_VIEW' => isset($gs_forum_options[$gs_select][3]) && $gs_forum_options[$gs_select][3] ? $this->u_action.="&amp;gs_select={$gs_select}&amp;view_curr=1" : false,
		)
	);
}
else
{
	if(isset($user->lang['GROUPSET']) && sizeof($user->lang['GROUPSET']))
	{
		foreach($user->lang['GROUPSET'] as $k=>$v)
		{
			$template->assign_block_vars('groupsets', array(
				'NAME'	=> $v,
				'DESCR'	=> isset($user->lang['GROUPSET_DESCR'][$k]) && $user->lang['GROUPSET_DESCR'][$k] ? $user->lang['GROUPSET_DESCR'][$k] : '',
				'URL'	=> append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=groupset&amp;gs_select='.strtolower($k)),
				)
			);
		}
	}

	$template->assign_vars(array(
		'S_VIEW_GROUPSET'	=> true,
		'S_TRACKER_NOBUTT' => true,
		)
	);
}

$display_vars = array(
	'title'	=> $groupset_title,
	'vars'	=> array(
		'legend1'				=> 'ACP_TRACKER_GROUPSET_SETTINGS',
	)
);

$template->assign_vars(array(
	'S_GROUPSET_INC'	=> true,
	)
);

//##############################################################################
function select_gs_forums($key, $only_trackers=1, $exclude_chat=1, $gs_curr=array(), $gs_curr_name=array(), $view_curr=0)
{
	global $user;

	$forum_list = make_forum_select(false, false, true, true, true, false, true, $only_trackers, $exclude_chat);

	// Build forum options
	$s_forum_options = '<select id="' . $key . '" name="' . $key . '[]" multiple="multiple" size="8">';
	foreach ($forum_list as $f_id => $f_row)
	{
		$s_forum_options .= '<option value="' . $f_id . '"' . ($f_row['disabled'] ? ' disabled="disabled" class="disabled-option"' : '') . '>' . $f_row['padding'] . $f_row['forum_name'].($view_curr && isset($gs_curr[$f_id]) && isset($gs_curr_name[$gs_curr[$f_id]]) ? ' ('.$gs_curr_name[$gs_curr[$f_id]].')' : '') . '</option>';
	}
	$s_forum_options .= '</select>';

	return $s_forum_options;

}
?>
