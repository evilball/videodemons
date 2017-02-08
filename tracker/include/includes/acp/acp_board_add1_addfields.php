<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_addfields.php 1.000 2009-08-13 12:02:00 PPK $
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

$user->add_lang('mods/acp/ppkbb3cker_addfields');
//$user->add_lang('mods/ppkbb3cker_addfields');
$addfields_title='ACP_TRACKER_ADDFIELDS_TPL';
if(request_var('submit', '') && @$_POST['set_name'])
{
	$d_addfields=array();
	foreach($_POST['set_name'] as $k=>$v)
	{
		if(@$_POST['af_delete'][$k])
		{
			$d_addfields[]=my_int_val($k);
		}
		else
		{
			if(STRIP)
			{
				$_POST['af_descr'][$k]=stripslashes($_POST['af_descr'][$k]);
				$_POST['af_subject'][$k]=stripslashes($_POST['af_subject'][$k]);
				$_POST['set_name'][$k]=stripslashes($_POST['set_name'][$k]);
			}
			$_POST['af_descr'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_descr'][$k]));
			$_POST['af_subject'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_subject'][$k]));
			if($k==0 && @$_POST['set_name'][$k]!='')
			{
				$sql='INSERT INTO '.TRACKER_ADDFIELDS_TPL." (af_name, af_descr, af_subject, af_data) VALUES('".$db->sql_escape($_POST['set_name'][$k])."', '{$_POST['af_descr'][$k]}', '{$_POST['af_subject'][$k]}', '')";
				$result=$db->sql_query($sql);
			}
			else if(@$_POST['set_name'][$k]!='')
			{
				$sql='UPDATE '.TRACKER_ADDFIELDS_TPL." SET af_name='".$db->sql_escape($_POST['set_name'][$k])."', af_descr='{$_POST['af_descr'][$k]}', af_subject='{$_POST['af_subject'][$k]}' WHERE id='".my_int_val($k)."'";
				$result=$db->sql_query($sql);
			}
		}
	}
	if($d_addfields)
	{
		$sql='DELETE FROM '.TRACKER_ADDFIELDS_TPL." WHERE id IN('".implode("', '", $d_addfields)."')";
		$result=$db->sql_query($sql);

		$sql='DELETE FROM '.TRACKER_ADDFIELDS_SETS." WHERE af_id IN('".implode("', '", $d_addfields)."')";
		$result=$db->sql_query($sql);

		$sql='UPDATE '.TRACKER_ADDFIELD_TPL." SET af_id='0' WHERE af_id IN('".implode("', '", $d_addfields)."')";
		$result=$db->sql_query($sql);
	}
}

if(request_var('submit', '') && @$_POST['af_name'] && ($tfield=request_var('tfield', 0)))
{
	$d_af=array();
	foreach($_POST['af_name'] as $k=>$v)
	{
		if(@$_POST['af_delete'][$k])
		{
			$d_af[]=my_int_val($k);
		}
		else
		{
			if(STRIP)
			{
				$_POST['af_name'][$k]=stripslashes($_POST['af_name'][$k]);
			}
			$forb_names=array('message', 'subject', 'desc', 'topic_time_limit', 'poll_title', 'poll_length', 'poll_max_options');
			$_POST['af_name'][$k]=preg_replace('/[^_a-zA-Z0-9]/', '', $_POST['af_name'][$k]);
			if(preg_match('/[a-zA-Z]+/', $_POST['af_name'][$k]) && !in_array($_POST['af_name'][$k], $forb_names))
			{
				if(request_var('add_sets', 0) && @$_POST['af_name'][$k]!='' && @$_POST['af_add'][$k])
				{
					$sql='INSERT INTO '.TRACKER_ADDFIELDS_SETS."
						(af_id, af_name, af_required, af_order, af_count)
							VALUES('".my_int_val($tfield)."', '".$db->sql_escape($_POST['af_name'][$k])."', '".my_int_val(@$_POST['af_required'][$k])."', '".my_int_val($_POST['af_order'][$k])."', '".my_int_val($_POST['af_count'][$k])."')";
					$result=$db->sql_query($sql);
				}
				else if(@$_POST['af_name'][$k]!='')
				{
					$sql='UPDATE '.TRACKER_ADDFIELDS_SETS." SET af_name='".$db->sql_escape($_POST['af_name'][$k])."', af_required='".my_int_val(@$_POST['af_required'][$k])."', af_order='".my_int_val($_POST['af_order'][$k])."', af_count='".my_int_val($_POST['af_count'][$k])."' WHERE id='".my_int_val($k)."'";
					$result=$db->sql_query($sql);
				}
			}
		}
	}
	if($d_af)
	{
		$sql='DELETE FROM '.TRACKER_ADDFIELDS_SETS." WHERE id IN('".implode("', '", $d_af)."') AND af_id='{$tfield}'";
		$result=$db->sql_query($sql);
	}
	$this->u_action=append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=addfields&amp;view_sets=1&amp;tfield='.$tfield);
}

if(request_var('copy_sets', 0))
{
	$tfield=request_var('tfield', 0);
	if($tfield)
	{
		$sql='SELECT * FROM '.TRACKER_ADDFIELDS_TPL." WHERE id='{$tfield}' LIMIT 1";
		$result=$db->sql_query($sql);
		$sets_name=$db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		if($sets_name)
		{
			$sql='INSERT INTO '.TRACKER_ADDFIELDS_TPL." (af_name, af_descr, af_subject, af_data) VALUES('".$db->sql_escape($user->lang['SETS_COPY'].$sets_name['af_name'])."', '".$db->sql_escape($sets_name['af_descr'])."', '".$db->sql_escape($sets_name['af_subject'])."', '".$db->sql_escape($sets_name['af_data'])."')";
			$result=$db->sql_query($sql);
			$sets_next=$db->sql_nextid();

			$sql='SELECT af_name, af_order, af_required, af_count FROM '.TRACKER_ADDFIELDS_SETS." WHERE af_id='{$tfield}'";
			$result=$db->sql_query($sql);
			while($row=$db->sql_fetchrow($result))
			{
				$sql='INSERT INTO '.TRACKER_ADDFIELDS_SETS."
				(af_id, af_name, af_required, af_order, af_count)
					VALUES('".my_int_val($sets_next)."', '".$db->sql_escape($row['af_name'])."', '".my_int_val($row['af_required'])."', '".my_int_val($row['af_order'])."', '".my_int_val($row['af_count'])."')";
				$result2=$db->sql_query($sql);
			}
			$db->sql_freeresult($result);
		}
	}
}

if(request_var('sort_sets', 0) && isset($_GET['addf']) && sizeof($_GET['addf']))
{
	$tfield=request_var('tfield', 0);
	if($tfield)
	{
		$sql='SELECT id FROM '.TRACKER_ADDFIELDS_TPL.'';
		$result=$db->sql_query($sql);
		if($db->sql_fetchrow($result))
		{
			foreach($_GET['addf'] as $k => $v)
			{
				$k=my_int_val($k);
				$v=my_int_val($v);
				if($k && $v)
				{
					$db->sql_query("UPDATE ".TRACKER_ADDFIELDS_SETS." SET af_order='{$k}' WHERE af_id='{$tfield}' AND id='{$v}'");
				}
			}
			exit($user->lang['ADDF_SORT_SUCCESS']);
		}
	}
}
else if(request_var('view_sets', 0))
{
	$torrent_addfield=get_torrent_addfield();
	sizeof($torrent_addfield['TRACKER_FORUM_ADDFIELDS']) ? asort($torrent_addfield['TRACKER_FORUM_ADDFIELDS']) : '';
	$tfield=request_var('tfield', 0);
	$addfields_title='ACP_TRACKER_ADDFIELDS';
	if($tfield)
	{
		$addf_tpl=array();
		$sql='SELECT id, af_name FROM '.TRACKER_ADDFIELDS_TPL.'';
		$result=$db->sql_query($sql);
		while($row=$db->sql_fetchrow($result))
		{
			$addf_tpl[$row['id']]=$row;
		}
		$db->sql_freeresult($result);
		$sets_name=$addf_tpl[$tfield];
	}
	if(request_var('add_new', ''))
	{
		$sql='SELECT af_name FROM '.TRACKER_ADDFIELDS_SETS." WHERE af_id='{$tfield}'";
		$result=$db->sql_query($sql);
		$af_exists=array();
		while($row=$db->sql_fetchrow($result))
		{
			$af_exists[$row['af_name']]=1;
		}
		$db->sql_freeresult($result);

		$addf_ids=array();
		if(sizeof($addf_tpl) && sizeof($torrent_addfield['TRACKER_FORUM_ADDFIELDS']))
		{
			$sql='SELECT id, addfields, af_id FROM '.TRACKER_ADDFIELD_TPL." WHERE addfields IN('".implode("', '", array_map('addslashes', array_keys($torrent_addfield['TRACKER_FORUM_ADDFIELDS'])))."')";

			$result=$db->sql_query($sql);
			while($row=$db->sql_fetchrow($result))
			{
				$row['af_id'] ? $row['set_name']=htmlspecialchars($addf_tpl[$row['af_id']]['af_name']) : '';
				$addf_ids[$row['addfields']]=$row;
			}
			$db->sql_freeresult($result);
		}
		$i=1;
		if(sizeof($torrent_addfield['TRACKER_FORUM_ADDFIELDS']))
		{
			foreach($torrent_addfield['TRACKER_FORUM_ADDFIELDS'] as $k=>$v)
			{
				$af_disabled=@$af_exists[$k] ? true : false;
				if(!$af_disabled && $k && $v)
				{
					$template->assign_block_vars('sets', array(
						'ID'	=> $i,
						'NAME'	=> $k,
						'VALUE'	=> htmlspecialchars($v),
						'VIEW'	=> append_sid("{$phpbb_admin_path}index.{$phpEx}?i=board&amp;mode=addfield&amp;view_sets=1&tfield={$addf_ids[$k]['id']}"),
						'ORDER'	=> 450,
						'REQUIRED' => 0,
						'COUNT'	=> 1,
						'TPL'	=> @$addf_ids[$k]['set_name'] ? $addf_ids[$k]['set_name'] : false,
						)
					);
					$i+=1;
				}
			}
		}
		$template->assign_vars(array(
			'AF_NAME' => htmlspecialchars($sets_name['af_name']),
			'S_HIDDEN_FIELDS'=>'<input type="hidden" name="add_sets" value="1" >
						<input type="hidden" name="tfield" value="'.$tfield.'" >',
			)
		);
		$template->assign_vars(array(
			'S_NEW_SETS'	=> true,
			)
		);
	}
	else
	{
		if($tfield)
		{
			$sql='SELECT ads.id, ads.af_name, ads.af_order, ads.af_required, ads.af_count, adt.id id2, adst.af_name set_name, adt.addfields_multi, adt.addfields_hlp FROM '.TRACKER_ADDFIELDS_SETS." ads LEFT JOIN ".TRACKER_ADDFIELD_TPL." adt ON (adt.addfields=ads.af_name) LEFT JOIN ".TRACKER_ADDFIELDS_TPL." adst ON (adst.id=adt.af_id) WHERE ads.af_id='{$tfield}' ORDER BY ads.af_order";
			$result=$db->sql_query($sql);
			while($row=$db->sql_fetchrow($result))
			{
				$count_disabled=false;
				if($row['addfields_multi'])
				{
					$count_disabled=true;
				}
				if($row['addfields_hlp']!='')
				{
					$addfields_hlp=array_map('trim', explode("\n", $row['addfields_hlp']));
					sizeof($addfields_hlp) > 1  ? $count_disabled=true : '';
				}
				$template->assign_block_vars('sets', array(
					'ID'	=> $row['id'],
					'NAME'	=> $row['af_name'],
					'VALUE'	=> htmlspecialchars(@$torrent_addfield['TRACKER_FORUM_ADDFIELDS'][$row['af_name']]),
					'VIEW'	=> append_sid("{$phpbb_admin_path}index.{$phpEx}?i=board&amp;mode=addfield&amp;view_sets=1&tfield={$row['id2']}"),
					'ORDER'	=> $row['af_order'],
					'REQUIRED' => $row['af_required'],
					'COUNT'	=> $row['af_count'],
					'COUNT_DISABLED'	=> $count_disabled,
					'TPL'	=> $row['set_name'] ? $row['set_name'] : false,
					)
				);
			}
			$db->sql_freeresult($result);
			$template->assign_vars(array(
				'AF_NAME' => htmlspecialchars($sets_name['af_name']),
				'S_HIDDEN_FIELDS'=>'<input type="hidden" name="view_sets" value="1" >
							<input type="hidden" name="tfield" value="'.$tfield.'" >',
				)
			);
		}
		$template->assign_vars(array(
			'S_VIEW_SETS'	=> true,
			'S_SORT_SETS' => append_sid("{$phpbb_admin_path}index.$phpEx", "i=board&mode=addfields&sort_sets=1&tfield={$tfield}", false),
			)
		);
	}
}
else
{
	if(request_var('add_new', ''))
	{
		$template->assign_block_vars('addfields', array(
			'COUNT'	=> 0,
			'NAME'	=> '',
			'DESCR'	=> '',
			'SUBJECT'	=> '',
			)
		);
		$template->assign_vars(array(
			'S_NEW_ADDFIELDS'	=> true,
			'S_ADDFIELDS_TYPE' => $config['ppkbb_addfields_type'][0] ? true : false,
			'S_HIDDEN_FIELDS'=>'<input type="hidden" name="view_addfields" value="1" >',
			)
		);
	}
	else
	{
		$sql='SELECT * FROM '.TRACKER_ADDFIELDS_TPL.' ORDER BY id DESC';
		$result=$db->sql_query($sql);
		while($row=$db->sql_fetchrow($result))
		{
			$template->assign_block_vars('addfields', array(
				'COUNT'	=> $row['id'],
				'NAME'	=> htmlspecialchars($row['af_name']),
				'DESCR'	=> htmlspecialchars($row['af_descr']),
				'SUBJECT' => htmlspecialchars($row['af_subject']),
				'A_VIEW_SETS'	=> append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=addfields&amp;view_sets=1&amp;tfield='.$row['id']),
				'A_COPY_SETS'	=> append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=addfields&amp;copy_sets=1&amp;tfield='.$row['id']),
				)
			);
		}
		$db->sql_freeresult($result);
		$template->assign_vars(array(
			'S_VIEW_ADDFIELDS'	=> true,
			'S_ADDFIELDS_TYPE' => $config['ppkbb_addfields_type'][0] ? true : false,
			'S_HIDDEN_FIELDS'=>'<input type="hidden" name="view_addfields" value="1" >',
			)
		);
	}
}

$display_vars = array(
	'title'	=> $addfields_title,
	'vars'	=> array(
		'legend1'				=> 'ACP_TRACKER_ADDFIELDS_TPL_SETTINGS',
	)
);

$template->assign_vars(array(
	'S_ADDFIELDS_INC'	=> true,
	)
);
?>
