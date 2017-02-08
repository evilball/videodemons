<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_addfield.php 1.000 2012-06-07 11:57:45 PPK $
* @copyright (c) 2012 PPK
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

$user->add_lang('mods/acp/ppkbb3cker_addfield');
$addfield_title='ACP_TRACKER_ADDFIELD_TPL';
//$user->add_lang('mods/ppkbb3cker_addfields');

$addf_tpl=array();
$sql='SELECT id, af_name FROM '.TRACKER_ADDFIELDS_TPL.'';
$result=$db->sql_query($sql);
while($row=$db->sql_fetchrow($result))
{
	$addf_tpl[$row['id']]=htmlspecialchars($row['af_name']);
}
$db->sql_freeresult($result);

$forb_names=array('message', 'subject', 'desc', 'topic_time_limit', 'poll_title', 'poll_length', 'poll_max_options');
if(request_var('add_addfield', 0) && @$_POST['af_name'])
{
	$d_addfield=array();

	foreach($_POST['af_name'] as $k=>$v)
	{
		if(STRIP)
		{
			$_POST['af_name'][$k]=stripslashes($_POST['af_name'][$k]);
			$_POST['af_descr'][$k]=stripslashes($_POST['af_descr'][$k]);
		}

		$_POST['af_name'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_name'][$k]));
		$_POST['af_descr'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_descr'][$k]));
		if(@$_POST['af_delete'][$k])
		{
			$d_addfield[]=$_POST['af_name'][$k];
		}
		else
		{
			$_POST['af_name'][$k]=preg_replace('/[^_a-zA-Z0-9]/', '', $_POST['af_name'][$k]);
			if(preg_match('/[a-zA-Z]+/', $_POST['af_name'][$k]) && !in_array($_POST['af_name'][$k], $forb_names))
			{
				if($k==0 && @$_POST['af_name'][$k]!='' && @$_POST['af_descr'][$k]!='')
				{
					if(STRIP)
					{
						$_POST['af_hlp'][$k]=stripslashes($_POST['af_hlp'][$k]);
						$_POST['af_def'][$k]=stripslashes($_POST['af_def'][$k]);
						$_POST['af_br'][$k]=stripslashes($_POST['af_br'][$k]);
						$_POST['af_br2'][$k]=stripslashes($_POST['af_br2'][$k]);
						$_POST['af_split'][$k]=stripslashes($_POST['af_split'][$k]);
						$_POST['af_checkas'][$k]=stripslashes($_POST['af_checkas'][$k]);
						$_POST['af_bbcode'][$k]=stripslashes($_POST['af_bbcode'][$k]);
						$_POST['af_bbcodes'][$k]=stripslashes($_POST['af_bbcodes'][$k]);
						$_POST['af_title'][$k]=stripslashes($_POST['af_title'][$k]);
						$_POST['af_alias'][$k]=stripslashes($_POST['af_alias'][$k]);
					}

					$_POST['af_hlp'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_hlp'][$k]));
					$_POST['af_def'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_def'][$k]));
					$_POST['af_br'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_br'][$k]));
					$_POST['af_br2'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_br2'][$k]));
					$_POST['af_split'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_split'][$k]));
					$_POST['af_checkas'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_checkas'][$k]));
					$_POST['af_bbcode'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_bbcode'][$k]));
					$_POST['af_bbcodes'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_bbcodes'][$k]));
					$_POST['af_title'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_title'][$k]));
					$_POST['af_alias'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_alias'][$k]));

					$sql='INSERT IGNORE INTO '.TRACKER_ADDFIELD_TPL." (addfields, addfields_hlp, addfields_def, addfields_br, addfields_br2, addfields_checkas, addfields_inall, addfields_ta, addfields_multi, addfields_bbcode, addfields_bbcodes, addfields_title, addfields_alias, addfields_skip, addfields_exists, addfields_descr, addfields_enable, addfields_split, af_id) VALUES(
						'".@$_POST['af_name'][$k]."',
						'".@$_POST['af_hlp'][$k]."',
						'".@$_POST['af_def'][$k]."',
						'".@$_POST['af_br'][$k]."',
						'".@$_POST['af_br2'][$k]."',
						'".@$_POST['af_checkas'][$k]."',
						'".(@$_POST['af_inall'][$k] ? 1 : 0)."',
						'".(@$_POST['af_ta'][$k] ? 1 : 0)."',
						'".(@$_POST['af_multi'][$k] ? 1 : 0)."',
						'".@$_POST['af_bbcode'][$k]."',
						'".@$_POST['af_bbcodes'][$k]."',
						'".@$_POST['af_title'][$k]."',
						'".@$_POST['af_alias'][$k]."',
						'".(@$_POST['af_skip'][$k] ? 1 : 0)."',
						'".(@$_POST['af_exists'][$k] ? 1 : 0)."',
						'".@$_POST['af_descr'][$k]."',
						'".(@$_POST['af_enable'][$k] ? 1 : 0)."',
						'".@$_POST['af_split'][$k]."',
						'".(isset($addf_tpl[@$_POST['af_id'][$k]]) ? $_POST['af_id'][$k] : 0)."'
					)";
					$result=$db->sql_query($sql);
					if(!$db->sql_affectedrows())
					{
						$this->u_action=append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=addfield&amp;add_addfield=1&amp;add_new=1');

						trigger_error($user->lang['ADDF_ALREADY_EXISTS'].': '.htmlspecialchars($_POST['af_name'][$k]).sprintf($user->lang['ADDF_BACK'], $this->u_action));
					}
				}
				else if(@$_POST['af_name'][$k]!='' && @$_POST['af_descr'][$k]!='')
				{
					$sql='UPDATE IGNORE '.TRACKER_ADDFIELD_TPL." SET addfields='{$_POST['af_name'][$k]}', addfields_descr='{$_POST['af_descr'][$k]}', addfields_enable='".(@$_POST['af_enable'][$k] ? 1 : 0)."', af_id='".(isset($addf_tpl[@$_POST['af_id'][$k]]) ? $_POST['af_id'][$k] : 0)."' WHERE id='".my_int_val($k)."'";
					$result=$db->sql_query($sql);
				}
			}
		}
	}
	if($d_addfield)
	{
		$sql='DELETE FROM '.TRACKER_ADDFIELD_TPL." WHERE addfields IN('".implode("', '", $d_addfield)."')";
		$result=$db->sql_query($sql);

		$sql='DELETE FROM '.TRACKER_ADDFIELDS_SETS." WHERE af_name IN('".implode("', '", $d_addfield)."')";
		$result=$db->sql_query($sql);
	}

	$cache->destroy('_ppkbb3cker_addfield_cache');
}

if(request_var('edit_sets', 0) && ($tfield=request_var('tfield', 0)))
{
	foreach(@$_POST['af_name'] as $k=>$v)
	{
		if(STRIP)
		{
			$_POST['af_name'][$k]=stripslashes($_POST['af_name'][$k]);
			$_POST['af_hlp'][$k]=stripslashes($_POST['af_hlp'][$k]);
			$_POST['af_def'][$k]=stripslashes($_POST['af_def'][$k]);
			$_POST['af_br'][$k]=stripslashes($_POST['af_br'][$k]);
			$_POST['af_br2'][$k]=stripslashes($_POST['af_br2'][$k]);
			$_POST['af_split'][$k]=stripslashes($_POST['af_split'][$k]);
			$_POST['af_checkas'][$k]=stripslashes($_POST['af_checkas'][$k]);
			$_POST['af_bbcode'][$k]=stripslashes($_POST['af_bbcode'][$k]);
			$_POST['af_bbcodes'][$k]=stripslashes($_POST['af_bbcodes'][$k]);
			$_POST['af_title'][$k]=stripslashes($_POST['af_title'][$k]);
			$_POST['af_alias'][$k]=stripslashes($_POST['af_alias'][$k]);
			$_POST['af_descr'][$k]=stripslashes($_POST['af_descr'][$k]);
		}

		$_POST['af_name'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_name'][$k]));
		$_POST['af_hlp'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_hlp'][$k]));
		$_POST['af_def'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_def'][$k]));
		$_POST['af_br'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_br'][$k]));
		$_POST['af_br2'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_br2'][$k]));
		$_POST['af_split'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_split'][$k]));
		$_POST['af_checkas'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_checkas'][$k]));
		$_POST['af_bbcode'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_bbcode'][$k]));
		$_POST['af_bbcodes'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_bbcodes'][$k]));
		$_POST['af_title'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_title'][$k]));
		$_POST['af_alias'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_alias'][$k]));
		$_POST['af_descr'][$k]=$db->sql_escape(utf8_normalize_nfc($_POST['af_descr'][$k]));

		$_POST['af_name'][$k]=preg_replace('/[^_a-zA-Z0-9]/', '', $_POST['af_name'][$k]);
		if($_POST['af_name'][$k]!='' && @$_POST['af_descr'][$k]!='' && preg_match('/[a-zA-Z0-9_]+/', $_POST['af_name'][$k]) && !in_array($_POST['af_name'][$k], $forb_names))
		{
			$sql='UPDATE '.TRACKER_ADDFIELD_TPL." SET
					addfields='".@$_POST['af_name'][$k]."',
					addfields_hlp='".@$_POST['af_hlp'][$k]."',
					addfields_def='".@$_POST['af_def'][$k]."',
					addfields_br='".@$_POST['af_br'][$k]."',
					addfields_br2='".@$_POST['af_br2'][$k]."',
					addfields_split='".@$_POST['af_split'][$k]."',
					addfields_checkas='".@$_POST['af_checkas'][$k]."',
					addfields_inall='".(@$_POST['af_inall'][$k] ? 1 : 0)."',
					addfields_ta='".(@$_POST['af_ta'][$k] ? 1 : 0)."',
					addfields_multi='".(@$_POST['af_multi'][$k] ? 1 : 0)."',
					addfields_bbcode='".@$_POST['af_bbcode'][$k]."',
					addfields_bbcodes='".@$_POST['af_bbcodes'][$k]."',
					addfields_title='".@$_POST['af_title'][$k]."',
					addfields_alias='".@$_POST['af_alias'][$k]."',
					addfields_skip='".(@$_POST['af_skip'][$k] ? 1 : 0)."',
					addfields_exists='".(@$_POST['af_exists'][$k] ? 1 : 0)."',
					addfields_descr='".@$_POST['af_descr'][$k]."',
					af_id='".(isset($addf_tpl[@$_POST['af_id'][$k]]) ? $_POST['af_id'][$k] : 0)."'
				WHERE id='".my_int_val($k)."'";
			$result=$db->sql_query($sql);
		}
	}

	$cache->destroy('_ppkbb3cker_addfield_cache');

	$this->u_action=append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=addfield&amp;view_sets=1&amp;tfield='.$tfield);
}

if(request_var('copy_sets', 0))
{
	$tfield=request_var('tfield', 0);
	if($tfield)
	{
		$sql='SELECT * FROM '.TRACKER_ADDFIELD_TPL." WHERE id='{$tfield}' LIMIT 1";
		$result=$db->sql_query($sql);
		$sets_name=$db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		if($sets_name)
		{
			$sql='INSERT INTO '.TRACKER_ADDFIELD_TPL." (addfields, addfields_hlp, addfields_def, addfields_br, addfields_br2, addfields_checkas, addfields_inall, addfields_ta, addfields_multi, addfields_bbcode, addfields_bbcodes, addfields_title, addfields_alias, addfields_skip, addfields_exists, addfields_descr, addfields_enable, addfields_split, af_id) VALUES(
				'".$db->sql_escape($sets_name['addfields'].'_'.date('ymdHis'))."',
				'".$db->sql_escape($sets_name['addfields_hlp'])."',
				'".$db->sql_escape($sets_name['addfields_def'])."',
				'".$db->sql_escape($sets_name['addfields_br'])."',
				'".$db->sql_escape($sets_name['addfields_br2'])."',
				'".$db->sql_escape($sets_name['addfields_checkas'])."',
				'".$db->sql_escape($sets_name['addfields_inall'])."',
				'".$db->sql_escape($sets_name['addfields_ta'])."',
				'".$db->sql_escape($sets_name['addfields_multi'])."',
				'".$db->sql_escape($sets_name['addfields_bbcode'])."',
				'".$db->sql_escape($sets_name['addfields_bbcodes'])."',
				'".$db->sql_escape($sets_name['addfields_title'])."',
				'".$db->sql_escape($sets_name['addfields_alias'])."',
				'".$db->sql_escape($sets_name['addfields_skip'])."',
				'".$db->sql_escape($sets_name['addfields_exists'])."',
				'".$db->sql_escape($user->lang['ADDF_COPY'].$sets_name['addfields_descr'])."',
				'".$db->sql_escape($sets_name['addfields_enable'])."',
				'".$db->sql_escape($sets_name['addfields_split'])."',
				'".$db->sql_escape($sets_name['af_id'])."'
			)";
			$result=$db->sql_query($sql);
		}
	}

	$cache->destroy('_ppkbb3cker_addfield_cache');
}

if(request_var('view_sets', 0))
{
	$torrent_addfield=get_torrent_addfield();
	$tfield=request_var('tfield', 0);
	$addfield_title='ACP_TRACKER_ADDFIELD';
	if($tfield)
	{
		$sql='SELECT * FROM '.TRACKER_ADDFIELD_TPL." WHERE id='{$tfield}'";
		$result=$db->sql_query($sql);
		$sets_name=$db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		$template->assign_block_vars('sets', array(
			'ADDF_TPL' => addf_tpl_select($addf_tpl, $sets_name['id'], $sets_name['af_id']),
			'COUNT'	=> $sets_name['id'],
			'NAME'	=> htmlspecialchars($sets_name['addfields']),
			'DESCR'	=> htmlspecialchars($sets_name['addfields_descr']),
			'BBCODE'	=> htmlspecialchars($sets_name['addfields_bbcode']),
			'BBCODES'	=> htmlspecialchars($sets_name['addfields_bbcodes']),
			'DEF'	=> htmlspecialchars($sets_name['addfields_def']),
			'BR'	=> htmlspecialchars($sets_name['addfields_br']),
			'BR2'	=> htmlspecialchars($sets_name['addfields_br2']),
			'SPLIT'	=> htmlspecialchars($sets_name['addfields_split']),
			'HLP'	=> htmlspecialchars($sets_name['addfields_hlp']),
			'ALIAS'	=> htmlspecialchars($sets_name['addfields_alias']),
			'CHECKAS'	=> htmlspecialchars($sets_name['addfields_checkas']),
			'TITLE'	=> htmlspecialchars($sets_name['addfields_title']),
			'TA'	=> $sets_name['addfields_ta'] ? ' checked="checked"' : '',
			'INALL'	=> $sets_name['addfields_inall'] ? ' checked="checked"' : '',
			'SKIP'	=> $sets_name['addfields_skip'] ? ' checked="checked"' : '',
			'EXISTS'	=> $sets_name['addfields_exists'] ? ' checked="checked"' : '',
			'MULTI'	=> $sets_name['addfields_multi'] ? ' checked="checked"' : '',
			'EXISTS'	=> $sets_name['addfields_exists'] ? ' checked="checked"' : '',

			)
		);

		$template->assign_vars(array(
			'AF_NAME' => htmlspecialchars($sets_name['addfields']),
			'S_HIDDEN_FIELDS'=>'<input type="hidden" name="edit_sets" value="1" >
						<input type="hidden" name="tfield" value="'.$tfield.'" >',
			)
		);
	}
	$template->assign_vars(array(
		'S_SET_SETS'	=> true,
		)
	);
}
else
{
	if(request_var('add_new', ''))
	{
		$template->assign_block_vars('addfield', array(
			'COUNT'	=> 0,
			'NAME'	=> '',
			'DESCR'	=> '',
			'SUBJECT'	=> '',
			'ADDF_TPL' => addf_tpl_select($addf_tpl, 0, 0),
			)
		);
		$template->assign_vars(array(
			'S_NEW_ADDFIELD'	=> true,
			'S_HIDDEN_FIELDS'=>'<input type="hidden" name="add_addfield" value="1" >',
			)
		);
	}
	else
	{
		$af_filter=request_var('filter', '');
		$addfield_filter=array(
			'addfields_ta' => $user->lang['ADDF_TA'],
			'addfields_inall' => $user->lang['ADDF_INALL'],
			'addfields_skip' => $user->lang['ADDF_SKIP'],
			'addfields_title' => $user->lang['ADDF_TITLE'],
			'addfields_bbcodes' => $user->lang['ADDF_BBCODES'],
			'addfields_bbcode' => $user->lang['ADDF_BBCODE'],
			'addfields_br' => $user->lang['ADDF_BR'],
			'addfields_br2' => $user->lang['ADDF_BR2'],
			'addfields_split' => $user->lang['ADDF_SPLIT'],
			'addfields_hlp' => $user->lang['ADDF_HLP'],
			'addfields_exists' => $user->lang['ADDF_EXISTS'],
			'addfields_multi' => $user->lang['ADDF_MULTI'],
			'addfields_def' => $user->lang['ADDF_DEF'],
			'addfields_alias' => $user->lang['ADDF_ALIAS'],
			'addfields_checkas' => $user->lang['ADDF_CHECKAS'],
		);
		$af_type_str=array('addfields_title', 'addfields_bbcodes', 'addfields_bbcode', 'addfields_br', 'addfields_br2', 'addfields_split', 'addfields_hlp', 'addfields_def', 'addfields_alias', 'addfields_checkas');
		$af_type_num=array('addfields_ta', 'addfields_inall', 'addfields_skip', 'addfields_exists', 'addfields_multi');
		$afr_filter=str_replace('-', '', $af_filter);
		$af_type=$afr_filter==$af_filter ? '!' : '';
		$sql='SELECT * FROM '.TRACKER_ADDFIELD_TPL.($af_filter && isset($addfield_filter[$afr_filter]) ? " WHERE {$af_filter}".(in_array($afr_filter, $af_type_num) ? $af_type.'=0' : $af_type."=''") : '').' ORDER BY addfields_enable DESC, addfields_descr';
		$result=$db->sql_query($sql);
		while($row=$db->sql_fetchrow($result))
		{
			$template->assign_block_vars('addfield', array(
				'ADDF_TPL' => addf_tpl_select($addf_tpl, $row['id'], $row['af_id']),
				'COUNT'	=> $row['id'],
				'NAME'	=> htmlspecialchars($row['addfields']),
				'DESCR'	=> htmlspecialchars($row['addfields_descr']),
				'ENABLED' => $row['addfields_enable'] ? ' checked="checked"' : '',
				'A_VIEW_SETS'	=> append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=addfield&amp;view_sets=1&amp;tfield='.$row['id']),
				'A_COPY_SETS'	=> append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=addfield&amp;copy_sets=1&amp;tfield='.$row['id']),
				)
			);
		}
		$db->sql_freeresult($result);
		foreach($addfield_filter as $k=>$f)
		{
			$template->assign_block_vars('addfield_filter', array(
				'FILTER' => '<option'.($af_filter==$k ? ' selected="selected"' : '').' value="'.append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=addfield&amp;filter='.$k).'">'.$f.' (+)</option>',
				'FILTER2' => '<option'.($af_filter=="-{$k}" ? ' selected="selected"' : '').' value="'.append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=addfield&amp;filter=-'.$k).'">'.$f.' (-)</option>',
				)
			);
		}
		$template->assign_vars(array(
			'S_VIEW_ADDFIELD'	=> true,
			'S_HIDDEN_FIELDS'=>'<input type="hidden" name="add_addfield" value="1" >',
			)
		);
	}

}

$display_vars = array(
	'title'	=> $addfield_title,
	'vars'	=> array(
		'legend1'				=> 'ACP_TRACKER_ADDFIELD_TPL_SETTINGS',
	)
);

$template->assign_vars(array(
	'S_ADDFIELD_INC'	=> true,
	)
);

//##############################################################################
function addf_tpl_select($addf_tpl, $i=0, $af_id=0)
{
	$addf_select='';

	if(sizeof($addf_tpl))
	{
		$addf_select='<select name="af_id['.$i.']" style="width:100px;">';
		$addf_select.='<option value="0"></option>';
		foreach($addf_tpl as $k => $v)
		{
			$addf_select.='<option value="'.$k.'"'.($af_id==$k ? ' selected="selected"' : '').'>'.$v.'</option>';
		}
		$addf_select.='</select>';
	}

	return $addf_select;
}
?>
