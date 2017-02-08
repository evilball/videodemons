<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_statuses.php 1.000 2011-03-06 15:56:17 PPK $
* @copyright (c) 2011 PPK
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

$user->add_lang('mods/acp/ppkbb3cker_statuses');
$user->add_lang('mods/ppkbb3cker_statuses');
$statuses_title='ACP_TRACKER_STATUSES';
$to_status=request_var('to_status', '');

if($to_status)
{
	$to_status=my_split_config($to_status, 2, array('strval', 'my_int_val'), '_');
	if(in_array($to_status[0], array('p', 'm')))
	{
		$result=$db->sql_query("SELECT status_id FROM ".TRACKER_STATUSES_TABLE." WHERE id='{$to_status[1]}'");
		$old_status=intval($db->sql_fetchfield('status_id'));
		$db->sql_freeresult($result);
		if($old_status)
		{
			$new_status=get_unassigned_statuses($to_status[0]);
			if($new_status)
			{
				$result=$db->sql_query("UPDATE ".TRACKER_TORRENTS_TABLE." SET forb='{$new_status}' WHERE forb='{$old_status}'");
				$result=$db->sql_query("UPDATE ".TRACKER_STATUSES_TABLE." SET status_id='{$new_status}' WHERE id='{$to_status[1]}'");

				$result=$db->sql_query("SELECT status_id FROM ".TRACKER_STATUSES_TABLE." WHERE def_forb!=0  LIMIT 1");
				$def_forb=intval($db->sql_fetchfield('status_id'));
				$db->sql_freeresult($result);

				$result=$db->sql_query("SELECT status_id FROM ".TRACKER_STATUSES_TABLE." WHERE def_notforb!=0 LIMIT 1");
				$def_notforb=intval($db->sql_fetchfield('status_id'));
				$db->sql_freeresult($result);

				set_tracker_config('ppkbb_tcdef_statuses', $def_forb.' '.$def_notforb);

				$sql = 'SELECT lang_iso
					FROM ' . LANG_TABLE;
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$cache->destroy("_ppkbb3cker_statuses_{$row['lang_iso']}_cache");
				}
				$db->sql_freeresult($result);

				purge_tracker_config(true);

				trigger_error(sprintf($user->lang['STATUS_SUCCESS'], append_sid("{$phpbb_admin_path}index.{$phpEx}", 'i=board&amp;mode=statuses')));
			}
		}
	}
}

if(request_var('submit', '') && @$_POST['status_reason'])
{
	$d_statuses=$author_candown=$guest_cantdown=array();

	$exchange_replace_from=request_var('exchange_replace_from', '');
	$exchange_replace_to=request_var('exchange_replace_to', 0);
	$result=false;

	if($exchange_replace_from && $exchange_replace_to)
	{
		$exchange_replace_from=my_split_config($exchange_replace_from, 2, array('strval', 'intval'), '_');
		if($exchange_replace_from[1]!=$exchange_replace_to)
		{
			if($exchange_replace_from[0]=='ex')
			{
				$exchange_replace_from[2]=get_unassigned_statuses('p');
				if($exchange_replace_from[2])
				{
					$result=$db->sql_query("UPDATE ".TRACKER_TORRENTS_TABLE." SET forb='{$exchange_replace_from[2]}' WHERE forb='{$exchange_replace_from[1]}'");
					$result=$db->sql_query("UPDATE ".TRACKER_TORRENTS_TABLE." SET forb='{$exchange_replace_from[1]}' WHERE forb='{$exchange_replace_to}'");
					$result=$db->sql_query("UPDATE ".TRACKER_TORRENTS_TABLE." SET forb='{$exchange_replace_to}' WHERE forb='{$exchange_replace_from[2]}'");
				}
			}
			else if($exchange_replace_from[0]=='re')
			{
				$result=$db->sql_query("UPDATE ".TRACKER_TORRENTS_TABLE." SET forb='{$exchange_replace_to}' WHERE forb='{$exchange_replace_from[1]}'");
			}
		}
	}

	if(!$result)
	{
		$def_forb=request_var('def_forb', 0);
		$def_notforb=request_var('def_notforb', 0);
		foreach($_POST['status_reason'] as $k=>$v)
		{
			$k=my_int_val($k);
			$_POST['status_sign'][$k]=my_split_config($_POST['status_sign'][$k], 2, array('strval', 'my_int_val'), '_');
			$_POST['status_sign'][$k]=$_POST['status_sign'][$k][0];
			if(!in_array($_POST['status_sign'][$k], array('p', 'm', 'd')))
			{
				continue;
			}
			if(@$_POST['status_delete'][$k] && $_POST['status_sign'][$k]!='d')
			{
				$d_statuses[]=$k;
			}
			else
			{
				if(STRIP)
				{
					$_POST['status_reason'][$k]=stripslashes($_POST['status_reason'][$k]);
					$_POST['status_mark'][$k]=stripslashes($_POST['status_mark'][$k]);
				}

				$_POST['status_reason'][$k]=utf8_normalize_nfc($_POST['status_reason'][$k]);
				$_POST['status_mark'][$k]=utf8_normalize_nfc($_POST['status_mark'][$k]);
				if($k==0 && @$_POST['status_reason'][$k]!=''/* && @$_POST['status_mark'][$k]!=''*/)
				{
					$status_id=get_unassigned_statuses($_POST['status_sign'][$k]);
					if(!$status_id)
					{
						trigger_error(sprintf($user->lang['STATUSES_FULL'], append_sid("{$phpbb_admin_path}index.{$phpEx}", 'i=board&amp;mode=statuses')));
					}
					$sql='INSERT INTO '.TRACKER_STATUSES_TABLE."
						(status_id, status_reason, status_mark, status_enabled)
							VALUES(
							'{$status_id}',
							'".$db->sql_escape(strip_tags($_POST['status_reason'][$k]))."',
							'".$db->sql_escape($_POST['status_mark'][$k])."',
							'".(@$_POST['status_enabled'][$k] ? 1 : 0)."')";
					$result=$db->sql_query($sql);
				}
				else if(@$_POST['status_reason'][$k]!='')
				{
					$sql='UPDATE '.TRACKER_STATUSES_TABLE." SET
						status_reason='".$db->sql_escape(strip_tags($_POST['status_reason'][$k]))."',
						status_mark='".$db->sql_escape($_POST['status_mark'][$k])."',
						status_enabled='".(@$_POST['status_enabled'][$k] ? 1 : 0)."',
						author_candown='".(@$_POST['author_candown'][$k] ? 1 : 0)."',
						guest_cantdown='".(@$_POST['guest_cantdown'][$k] ? 1 : 0)."',
						def_forb='".($def_forb==$k ? 1 : 0)."',
						def_notforb='".($def_notforb==$k ? 1 : 0)."'
							WHERE id='{$k}'";
					$result=$db->sql_query($sql);

					@$_POST['author_candown'][$k] ? $author_candown[]=$_POST['status_count'][$k] : '';
					@$_POST['guest_cantdown'][$k] ? $guest_cantdown[]=$_POST['status_count'][$k] : '';
				}
			}
		}
		set_tracker_config('ppkbb_tcdef_statuses', intval(@$_POST['status_count'][$def_forb]).' '.intval(@$_POST['status_count'][$def_notforb]));
		set_tracker_config('ppkbb_tcauthor_candown', implode(' ', $author_candown));
		set_tracker_config('ppkbb_tcguest_cantdown', implode(' ', $guest_cantdown));
		if($d_statuses)
		{
			$sql='DELETE FROM '.TRACKER_STATUSES_TABLE." WHERE id IN('".implode("', '", $d_statuses)."')";
			$result=$db->sql_query($sql);
		}
		$this->u_action=append_sid("{$phpbb_admin_path}index.{$phpEx}", 'i=board&amp;mode=statuses');

		//@unlink($phpbb_root_path.'cache/sql_'.md5('SELECT * FROM ' . TRACKER_STATUSES_TABLE." WHERE status_enabled='1'").".{$phpEx}");
		$sql = 'SELECT lang_iso
			FROM ' . LANG_TABLE;
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$cache->destroy("_ppkbb3cker_statuses_{$row['lang_iso']}_cache");
		}
		$db->sql_freeresult($result);
		purge_tracker_config(true);
		//obtain_tracker_config();
	}
}

$statuses_title='ACP_TRACKER_STATUSES';

if(request_var('add_new', ''))
{
	$template->assign_block_vars('statuses', array(
		'STATUS' => get_statuses_sign(),
		'COUNT'	=> 0,
		'ID'	=> '',
		'REASON'	=> '',
		'MARK' => '',
		'ENABLED' => '',
		'FORB' => '',
		'NOTFORB' => '',
		)
	);
	$template->assign_vars(array(
		'S_NEW_STATUS'	=> true,
		'S_HIDDEN_FIELDS'=>'<input type="hidden" name="view_statuses" value="1" >',
		)
	);
}
else
{
	$statuses_descr=0;
	$status_sign='';
	$forb_sel=array();
	$sql="SELECT * FROM ".TRACKER_STATUSES_TABLE." ORDER BY status_id DESC";
	$result=$db->sql_query($sql);
	while($row=$db->sql_fetchrow($result))
	{
		$statuses_descr=false;
		if($row['status_id'] < 0 && $row['status_id'] > -50 && @!$forb_sel[-1])
		{
			$statuses_descr=$user->lang['STATUSES_SIGN']['m'];
			$forb_sel[-1]=1;
			$status_sign='m';
		}
		if($row['status_id'] > 0 && @!$forb_sel[1])
		{
			$statuses_descr=$user->lang['STATUSES_SIGN']['p'];
			$forb_sel[1]=1;
			$status_sign='p';
		}
		if($row['status_id'] == 0 && @!$forb_sel[0])
		{
			$statuses_descr=$user->lang['STATUSES_SIGN']['d'];
			$forb_sel[0]=1;
			$status_sign='d';
		}

		$exchange_replace_from='';

		$template->assign_block_vars('statuses', array(
			'STATUS' => get_statuses_sign($row['id'], $status_sign),
			'COUNT' => $row['id'],
			'ID' => $row['status_id'],
			'REASON' => htmlspecialchars($row['status_reason']),
			'MARK' => htmlspecialchars($row['status_mark']),
			'LANG_REASON' => isset($user->lang[$row['status_reason']]) ? $user->lang[$row['status_reason']] : $row['status_reason'],
			'LANG_MARK' => isset($user->lang[$row['status_mark']]) ? $user->lang[$row['status_mark']] : $row['status_mark'],
			'ENABLED' => $row['status_enabled'] ? ' checked="checked"' : '',
			'CANDOWN' => $row['author_candown'] ? ' checked="checked"' : '',
			'CANTDOWN' => $row['guest_cantdown'] ? ' checked="checked"' : '',
			'FORB' => $row['def_forb'] ? ' checked="checked"' : '',
			'NOTFORB' => $row['def_notforb'] ? ' checked="checked"' : '',
			'CANDOWN_OPT' => $row['status_id'] > 0 ? true : false,
			'CANTDOWN_OPT' => $row['status_id'] < 0 ? true : false,
			'DESCR' => $statuses_descr,
			'EXCHANGE_FROM' => '<input class="radio" type="radio" name="exchange_replace_from" value="ex_'.$row['status_id'].'">',
			'REPLACE_FROM' => '<input class="radio" type="radio" name="exchange_replace_from" value="re_'.$row['status_id'].'">',
			'EXCHANGE_REPLACE_TO' => '<input class="radio" type="radio" name="exchange_replace_to" value="'.$row['status_id'].'">',
			)
		);
	}
	$db->sql_freeresult($result);

	$template->assign_vars(array(
		'S_VIEW_STATUS'	=> true,
		'S_HIDDEN_FIELDS'=>'<input type="hidden" name="view_statuses" value="1" >',
		)
	);
}

$template->assign_vars(array(
	'S_STATUSES_INC'	=> true,
	)
);

$display_vars = array(
	'title'	=> $statuses_title,
	'vars'	=> array(
		'legend1'				=> 'ACP_TRACKER_STATUSES_SETTINGS',
	)
);

//##############################################################################
function get_unassigned_statuses($sign)
{
	global $assigned_statuses, $db;

	$signs=array('p'=>array(99, 1), 'm'=>array(-1, -49), 'd'=>array(0, 0));

	if(!isset($signs[$sign]))
	{
		return false;
	}

	if(!isset($assigned_statuses[$sign]))
	{
		$sql="SELECT id, status_id FROM ".TRACKER_STATUSES_TABLE." WHERE status_id<='{$signs[$sign][0]}' AND status_id>='{$signs[$sign][1]}'";
		$result=$db->sql_query($sql);
		while($row=$db->sql_fetchrow($result))
		{
			$assigned_statuses[$sign][$row['id']]=$row['status_id'];
		}
		$db->sql_freeresult($result);
	}

	if(sizeof($assigned_statuses[$sign]))
	{
		for($i=$signs[$sign][0];$i>=$signs[$sign][1];$i--)
		{
			if(!in_array($i, $assigned_statuses[$sign]))
			{
				return $i;
			}
		}
	}
	else
	{
		return $signs[$sign][0];
	}

	return false;

}

function get_statuses_sign($i=0, $c='')
{
	global $user;

	$statuses='<select name="status_sign['.$i.']" style="width:125px;"'.($i==0 ? '' : ' onchange="if(this.options[this.selectedIndex].value != \'d\'){if(confirm(\''.$user->lang['STATUS_CHANGE_SIGN'].'\')){window.location.href=window.location+\'&amp;to_status=\'+this.options[this.selectedIndex].value;} }"').'>';
	foreach($user->lang['STATUSES_SIGN'] as $k => $v)
	{
		$statuses.='<option value="'.$k.'_'.$i.'"'.(($k=='d' && $c!='d') || ($c=='d' && $c!=$k) ? ' disabled="disabled"' : '').($c && $c==$k ? ' selected="selected"' : '').'>'.$v.'</option>';
	}
	$statuses.='</select>';

	return $statuses;
}
?>
