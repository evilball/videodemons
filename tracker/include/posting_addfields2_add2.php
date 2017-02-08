<?php
/**
*
* @package ppkBB3cker
* @version $Id: posting_addfields2_add2.php 1.000 2009-03-25 11:41:00 PPK $
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

$is_submitted=$posting_page && $preview ? 1 : 0;
$return_page=$is_submitted ? '<br /><br />'.sprintf($user->lang['RETURN_PAGE'], '<a onclick="history.back(); return false;" href="#">', '</a>') : '';
$forum_addfields=0;
$addfields_inform_place=!$config['ppkbb_addfields_type'][0] && $config['ppkbb_addfields_type'][1] ? true : false;
foreach($array_addfields as $k)
{
	$v=$addfields_type='';
	$c=intval(@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_COUNT'][$k]);
	$field_name=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_REQ'][$k] && !$addfields_inform_place ? '<u>'.@$torrent_addfield['TRACKER_FORUM_ADDFIELDS'][$k].'</u>'.(($c > 1 || @$torrent_addfield['TRACKER_FORUM_ADDFIELDS_MULTI'][$k]) && $torrent_addfield['TRACKER_FORUM_ADDFIELDS_REQ'][$k] ? ' ('.$torrent_addfield['TRACKER_FORUM_ADDFIELDS_REQ'][$k].')' : '') : @$torrent_addfield['TRACKER_FORUM_ADDFIELDS'][$k];

	if($field_name && $c)
	{
		if(@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_TA'][$k])
		{
			$addfield_type='TA';
		}
		else if(@$torrent_addfield['TRACKER_FORUM_ADDFIELDS'][$k])
		{
			$addfield_type='T';
		}
		if($addfield_type)
		{
			$addfields=$c > 1 ? build_addfields_multi($addfields_type, $k, $v, $c) : build_addfields_single($addfields_type, $k, $v, $c);
			if(sizeof($addfields))
			{
				$template->assign_block_vars('addfields_option', array(
					'ADDFIELDS_DESCR'=>@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_TITLE'][$k] ? $torrent_addfield['TRACKER_FORUM_ADDFIELDS_TITLE'][$k] : '',
					'ADDFIELDS_DESCR_SRC_IMG'=>$phpbb_root_path.'images/tracker/activity.png',
					'ADDFIELDS_NAME'=>$field_name,

					'ADDFIELD_TYPE'=>isset($addfields['ADDFIELD_TYPE']) ? $addfields['ADDFIELD_TYPE'] : $addfield_type,
					'ADDFIELD_KEY'=>$addfields['ADDFIELD_KEY'] ? $addfields['ADDFIELD_KEY'] : false,
					'ADDFIELD_VALUE'=>$addfields['ADDFIELD_VALUE'] ? $addfields['ADDFIELD_VALUE'] : false,
					'ADDFIELD_HELP'=>$addfields['ADDFIELD_HELP']['VALUE'] ? $addfields['ADDFIELD_HELP']['VALUE'] : false,
					'ADDFIELD_HELP_TYPE'=>isset($addfields['ADDFIELD_HELP']['TYPE']) ? $addfields['ADDFIELD_HELP']['TYPE'] : false,
					'ADDFIELD_HELP_KEY'=>isset($addfields['ADDFIELD_HELP']['KEY']) ? $addfields['ADDFIELD_HELP']['KEY'] : false,
					)
				);
				if(is_array($addfields['ADDFIELD_VALUE']))
				{
					foreach($addfields['ADDFIELD_VALUE'] as $i => $v)
					{
						$template->assign_block_vars('addfields_option.fields', array(
							'ADDFIELD_KEY'=>$addfields['ADDFIELD_KEY'][$i] ? $addfields['ADDFIELD_KEY'][$i] : false,
							'ADDFIELD_VALUE'=>$addfields['ADDFIELD_VALUE'][$i] ? $addfields['ADDFIELD_VALUE'][$i] : false,
							'ADDFIELD_I'=>$i,
							)
						);
					}
				}
				$forum_addfields=1;
			}
		}
	}
}

if($forum_addfields)
{
	$template->assign_vars(array(
		'FORUM_ADDFIELDS'=>$forum_addfields,
		)
	);
}

if($is_submitted && !$addfields_inform_place)
{
	$addfields_error='';
	if(sizeof($addfields_count))
	{
		$addfields_error=array();
		foreach($addfields_count as $k => $v)
		{
			if($v==-1)
			{
				$addfields_error[]=$user->lang['ADDFIELDS_REQUIRED_FIELD'].' <u>'.$torrent_addfield['TRACKER_FORUM_ADDFIELDS'][$k].'</u>';
			}
			else if($v=='x')
			{
				$addfields_error[]=sprintf($user->lang['ADDFIELDS_REQUIRED_EXISTS'], $torrent_addfield['TRACKER_FORUM_ADDFIELDS'][$k]);
			}
			else if($v=='X')
			{
				$addfields_error[]=sprintf($user->lang['ADDFIELDS_CHECK_FUNCTION'], $torrent_addfield['TRACKER_FORUM_ADDFIELDS'][$k]);
			}
			else if($v)
			{
				$addfields_error[]=sprintf($user->lang['ADDFIELDS_REQUIRED_MIN'], $v, ' <u>'.$torrent_addfield['TRACKER_FORUM_ADDFIELDS'][$k]).'</u>'.(@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_EXISTS'][$k] ? ', '.sprintf($user->lang['ADDFIELDS_REQUIRED_EXISTS'], $k) : '');
			}
		}
		!$submit && $mode=='post' && (request_var('addf_switch', 0) || !request_var('message', '', true)) ? trigger_error('<b>'.$user->lang['ADDFIELDS_REQUIRED'].'</b>: '.implode(', ', $addfields_error).$return_page) : '';
	}
}

//############################################################

function build_addfields_multi($l, $k, $v, $c)
{
	global $addfields_count, $user, $is_submitted, $error, $torrent_addfield, $addfields_inform_place, $preview;

	$b_fields=array();
	$exists_check=0;

	if(intval($c) > 1)
	{
		$tmp_count=$tmp_count2=0;
		$addfields_hlp=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k];
		$addfields_checkas=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_CHECKAS'][$k];
		$addfields_req=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_REQ'][$k];
		$addfields_multi=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_MULTI'][$k];
		if($addfields_multi || is_array($addfields_hlp))
		{
			return array();
		}
		for($i=1;$i<=$c;$i++)
		{
			$v=$preview && $addfields_inform_place ? '' : utf8_normalize_nfc(@$_POST[$k.$i]);
			STRIP ? $v=stripslashes($v) : '';
			if(($v || $addfields_checkas) && $is_submitted)
			{
				if($addfields_checkas && function_exists($addfields_checkas))
				{
					$field_check=$addfields_checkas($v);
					if($field_check===false)
					{
						$addfields_count[$k]='X';
					}
					$v=$field_check;
				}
			}
			if($v)
			{
				$tmp_count+=1;
				if(is_array($addfields_hlp) && @$user->lang['ADDFIELDS_REQUIRED_EXISTS'][$k] && !in_array($v, $addfields_hlp))
				{
					$tmp_count2-=1;
				}
			}
			$b_fields['ADDFIELD_KEY'][$i]=$k.$i;
			$b_fields['ADDFIELD_VALUE'][$i]=htmlspecialchars($v);
			$b_fields['ADDFIELD_HELP']=build_hlp_addfields($k, $i);
		}
		if($addfields_req && $tmp_count < $addfields_req)
		{
			$addfields_count[$k]=$addfields_req;
		}
		else if($addfields_req && $tmp_count2 < 0)
		{
			$addfields_count[$k]='x';
		}
	}

	return $b_fields;
}

function build_addfields_single($l, $k, $v, $c)
{
	global $addfields_count, $user, $is_submitted, $error, $torrent_addfield, $addfields_inform_place, $preview, $mode;

	$b_fields=array();
	$exists_check=0;

	if(intval($c)==1)
	{
		$tmp_count=$tmp_count2=0;
		$addfields_checkas=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_CHECKAS'][$k];
		$addfields_req=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_REQ'][$k];
		$addfields_multi=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_MULTI'][$k];
		$addfields_multi ? $v=@$_POST[$k] : $v=utf8_normalize_nfc(@$_POST[$k]);
		$preview && $addfields_inform_place ? $v='' : '';
		STRIP && !is_array($v) ? $v=stripslashes($v) : '';
		if($addfields_multi && (!isset($torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k]) || !is_array($torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k])))
		{
			return array();
		}
		if(($v || $addfields_checkas) && $is_submitted && !$addfields_multi)
		{
			if($addfields_checkas && function_exists($addfields_checkas))
			{
				$field_check=$addfields_checkas($v);
				if($field_check===false)
				{
					$addfields_count[$k]='X';
				}
				$v=$field_check;
			}
		}
		if(!$addfields_multi)
		{
			if(/*$v && */@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_EXISTS'][$k])
			{
				$addfields_hlp=$torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k];
				if(is_array($addfields_hlp) && !in_array($v, $addfields_hlp))
				{
					$addfields_count[$k]='x';
				}
			}
			if(!$v && $addfields_req)
			{
				$addfields_count[$k]=-1;
			}

			if(!isset($torrent_addfield['TRACKER_FORUM_ADDFIELDS_DEF'][$k]) || (($mode=='edit' || $preview) && $addfields_inform_place))
			{
				$v='';
			}
			else
			{
				$v=isset($torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k][$torrent_addfield['TRACKER_FORUM_ADDFIELDS_DEF'][$k]-1]) ? $torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k][$torrent_addfield['TRACKER_FORUM_ADDFIELDS_DEF'][$k]-1] : $torrent_addfield['TRACKER_FORUM_ADDFIELDS_DEF'][$k];
			}

			$b_fields['ADDFIELD_KEY']=$k;
			$b_fields['ADDFIELD_VALUE']=htmlspecialchars($v);
		}
		else
		{
			is_array($v) && sizeof($v) ? '' : $v=array();
			$b_fields['ADDFIELD_KEY']=$k;
			$b_fields['ADDFIELD_VALUE']='';
			foreach($torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k] as $m)
			{
				$sel_m='';
				if(in_array($m, $v))
				{
					$sel_m=' selected="selected"';
					$addfields_req ? $tmp_count+=1 : '';
				}
				$m=htmlspecialchars($m);
				$b_fields['ADDFIELD_VALUE'].='<option value="'.$m.'"'.$sel_m.'>'.$m.'</option>';
			}
			$b_fields['ADDFIELD_TYPE']='SEL';
			if($addfields_req && $tmp_count < $addfields_req)
			{
				$addfields_count[$k]=$addfields_req;
			}
		}
		$b_fields['ADDFIELD_HELP']=build_hlp_addfields($k);
	}

	return $b_fields;
}

function build_hlp_addfields($k, $c='')
{
	global $user, $torrent_addfield;

	$hlp_addfield=array();
	$hlp_addfield['VALUE']='';
	if(@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_MULTI'][$k])
	{
		$hlp_addfield['VALUE']=$user->lang['ADDFIELDS_MULTIHLP'];
		$hlp_addfield['TYPE']='MLT';
	}
	else if(is_array(@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k]))
	{
		$hlp_addfield['KEY']=$k.$c;
		$hlp_addfield['VALUE'].='<option value="" selected="selected"></option>';
		foreach($torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k] as $k2=>$v2)
		{
			$torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k][$k2]=htmlspecialchars($torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k][$k2]);
			$hlp_addfield['VALUE'].='<option value="'.(@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_ALIAS'][$k][$k2] ? $torrent_addfield['TRACKER_FORUM_ADDFIELDS_ALIAS'][$k][$k2] : $torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k][$k2]).'">'.$torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k][$k2].'</option>';
		}
		$hlp_addfield['TYPE']='SEL';
	}
	else if(@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k])
	{
		$hlp_addfield['VALUE']=$torrent_addfield['TRACKER_FORUM_ADDFIELDS_HLP'][$k];
	}

	return $hlp_addfield;
}
?>
