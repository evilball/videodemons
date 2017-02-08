<?php
/**
*
* @package ppkBB3cker
* @version $Id: acp_board_add1_rtracker.php 1.000 2009-12-18 11:56:00 PPK $
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

$user->add_lang('mods/acp/ppkbb3cker_rtracker');
$zones_title='ACP_TRACKER_RTRACKER';

if(request_var('submit', '') && @$_POST['rtrack_url'])
{
	$this->u_action=append_sid("{$phpbb_admin_path}index.$phpEx", 'i=board&amp;mode=rtracker');

	$d_rtrack=$exs_rtrack=array();
	foreach($_POST['rtrack_url'] as $k=>$v)
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
			$_POST['rtrack_forb'][$k]=in_array($_POST['rtrack_forb'][$k], array(1, 2, 3)) ? $_POST['rtrack_forb'][$k] : 0;

			$_POST['rtrack_url'][$k]=utf8_normalize_nfc($_POST['rtrack_url'][$k]);
			if(!$_POST['rtrack_forb'][$k] && @$_POST['rtrack_url'][$k]!='' && (!preg_match('#^(http|udp):\/\/(\w+|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})#', @$_POST['rtrack_url'][$k]) || strlen(@$_POST['rtrack_url'][$k]) > 512) || in_array(@$_POST['rtrack_url'][$k], $exs_rtrack))
			{
				trigger_error($user->lang['INVALID_RTRACK_URL'].': '.htmlspecialchars($_POST['rtrack_url'][$k]).sprintf($user->lang['RTRACK_BACK'], $this->u_action));
			}

			$_POST['rtrack_remote'][$k]=$_POST['rtrack_remote'][$k]==1 ? 1 : -1;
			$_POST['forb_type'][$k]=in_array($_POST['forb_type'][$k], array('r', 's', 'i')) ? $_POST['forb_type'][$k] : 's';

			if($k==0 && @$_POST['rtrack_url'][$k]!='')
			{
				$exs_rtrack[]=@$_POST['rtrack_url'][$k];
				$sql='INSERT INTO '.TRACKER_RTRACK_TABLE."
					(rtrack_url, rtrack_remote, rtrack_enabled, rtrack_forb, forb_type)
						VALUES(
						'".$db->sql_escape($_POST['rtrack_url'][$k])."',
						'".$_POST['rtrack_remote'][$k]."',
						'".(@$_POST['rtrack_enabled'][$k] ? 1 : 0)."',
						'".$_POST['rtrack_forb'][$k]."',
						'".$_POST['forb_type'][$k]."'
						)";
				$result=$db->sql_query($sql);
			}
			else if(@$_POST['rtrack_url'][$k]!='')
			{
				$exs_rtrack[]=@$_POST['rtrack_url'][$k];
				$sql='UPDATE '.TRACKER_RTRACK_TABLE." SET
					rtrack_url='".$db->sql_escape($_POST['rtrack_url'][$k])."',
					rtrack_remote='".$_POST['rtrack_remote'][$k]."',
					rtrack_enabled='".(@$_POST['rtrack_enabled'][$k] ? 1 : 0)."',
					rtrack_forb='".$_POST['rtrack_forb'][$k]."',
					forb_type='".$_POST['forb_type'][$k]."'
					 WHERE id='".my_int_val($k)."'";
				$result=$db->sql_query($sql);
			}
		}
	}
	if($d_rtrack)
	{
		$sql='DELETE FROM '.TRACKER_RTRACK_TABLE." WHERE id IN('".implode("', '", $d_rtrack)."') AND zone_id='0' AND rtrack_user='0'";
		$result=$db->sql_query($sql);
	}
	$cache->my_remove_cache('#forb_rtrack', 'sql');
	$cache->destroy('_ppkbb3cker_forb_rtrack');
}

$zones_title='ACP_TRACKER_RTRACKER';

if(request_var('add_new', ''))
{
	$template->assign_block_vars('rtracks', array(
		'COUNT' => 0,
		'URL'	=> '',
		'TYPE'=>'
			<select name="rtrack_remote[0]">
				<option value="-1">'.$user->lang['RTRACKER_UNITED'].'</option>
				<option value="1">'.$user->lang['RTRACKER_REMOTE_UNITED'].'</option>
			</select>
		',
		'FORB'=>'
			<select name="rtrack_forb[0]" style="width:50px;">
				<option value="0">'.$user->lang['RTRACK_FORBS'][0].'</option>
				<option value="1">'.$user->lang['RTRACK_FORBS'][1].'</option>
				<option value="2">'.$user->lang['RTRACK_FORBS'][2].'</option>
				<option value="3">'.$user->lang['RTRACK_FORBS'][3].'</option>
			</select>
			<select name="forb_type[0]" style="width:80px;">
				<option value="r">'.$user->lang['RTF_TYPE']['r'].'</option>
				<option value="s" selected="selected">'.$user->lang['RTF_TYPE']['s'].'</option>
				<option value="i">'.$user->lang['RTF_TYPE']['i'].'</option>
			</select>
			',
		'ENABLED' => '',
		)
	);
	$template->assign_vars(array(
		'S_NEW_RTRACKER'	=> true,
		'S_HIDDEN_FIELDS'=>'<input type="hidden" name="add_rtrack" value="1" >',
		)
	);
}
else
{
	$rtrack_stat=$rtrack_exclude=array();
	$sql='SELECT rt.*, SUM(ra.seeders) seeders, SUM(ra.leechers) leechers, SUM(ra.seeders)+SUM(ra.leechers) peers, SUM(ra.times_completed) completed, SUM(ra.err_count) errors, SUM(1) torrents FROM '.TRACKER_RTRACK_TABLE." rt LEFT JOIN ".TRACKER_RANNOUNCES_TABLE." ra ON (rt.id=ra.tracker) WHERE rt.rtrack_enabled='1' AND rt.rtrack_forb='0' AND rt.rtrack_remote='1' AND rt.zone_id='0' AND rt.torrent='0' AND rt.rtrack_user='0' GROUP BY rt.id ORDER BY rt.rtrack_forb, rt.rtrack_remote DESC, rt.rtrack_url";
	$result=$db->sql_query($sql);
	while($row=$db->sql_fetchrow($result))
	{
		$rtrack_stat[]=$row;
		$rtrack_exclude[]=$row['id'];
	}
	$db->sql_freeresult($result);

	$sql='SELECT rt.* FROM '.TRACKER_RTRACK_TABLE." rt WHERE rt.zone_id='0' AND rt.torrent='0' AND rt.rtrack_user='0'".($rtrack_exclude ? " AND rt.id NOT IN('".implode("', '", $rtrack_exclude)."')" : '')." ORDER BY rt.rtrack_enabled DESC, rt.rtrack_forb, rt.rtrack_remote DESC, rt.rtrack_url";
	$result=$db->sql_query($sql);
	while($row=$db->sql_fetchrow($result))
	{
		$rtrack_stat[]=$row;
	}
	$db->sql_freeresult($result);

	foreach($rtrack_stat as $row)
	{
		$template->assign_block_vars('rtracks', array(
			'COUNT'	=> $row['id'],
			'URL'	=> htmlspecialchars($row['rtrack_url']),
			'TYPE'=>'
				<select name="rtrack_remote['.$row['id'].']">
					<option value="-1"'.($row['rtrack_remote']==-1 ? ' selected="selected"' : '').'>'.$user->lang['RTRACKER_UNITED'].'</option>
					<option value="1"'.($row['rtrack_remote']==1 ? ' selected="selected"' : '').'>'.$user->lang['RTRACKER_REMOTE_UNITED'].'</option>
				</select>
				',
			'FORB'=>'
				<select name="rtrack_forb['.$row['id'].']" style="width:50px;">
					<option value="0"'.(!$row['rtrack_forb'] ? ' selected="selected"' : '').'>'.$user->lang['RTRACK_FORBS'][0].'</option>
					<option value="1"'.($row['rtrack_forb']==1 ? ' selected="selected"' : '').'>'.$user->lang['RTRACK_FORBS'][1].'</option>
					<option value="2"'.($row['rtrack_forb']==2 ? ' selected="selected"' : '').'>'.$user->lang['RTRACK_FORBS'][2].'</option>
					<option value="3"'.($row['rtrack_forb']==3 ? ' selected="selected"' : '').'>'.$user->lang['RTRACK_FORBS'][3].'</option>
				</select>
				<select name="forb_type['.$row['id'].']" style="width:80px;">
					<option value="r"'.(!$row['forb_type']=='r' ? ' selected="selected"' : '').'>'.$user->lang['RTF_TYPE']['r'].'</option>
					<option value="s"'.($row['forb_type']=='s' ? ' selected="selected"' : '').'>'.$user->lang['RTF_TYPE']['s'].'</option>
					<option value="i"'.($row['forb_type']=='i' ? ' selected="selected"' : '').'>'.$user->lang['RTF_TYPE']['i'].'</option>
				</select>
				',
			'STAT' => $row['rtrack_enabled'] && $row['rtrack_remote']==1 && !$row['rtrack_forb'] ? sprintf($user->lang['RTRACKER_STAT'], $row['seeders'], $row['leechers'], $row['peers'], $row['completed'], $row['torrents'], $row['errors'], ($row['torrents'] ? $row['errors']/$row['torrents'] : 0)) : false,
			'ENABLED' => $row['rtrack_enabled'] ? ' checked="checked"' : '',
			'S_FORB' => $row['rtrack_forb'] ? true : false,
			)
		);
	}
	$template->assign_vars(array(
		'S_VIEW_RTRACKER'	=> true,
		'S_HIDDEN_FIELDS'=>'<input type="hidden" name="add_rtrack" value="1" >',
		)
	);
}

$template->assign_vars(array(
	'S_RTRACKER_INC'	=> true,
	)
);

$display_vars = array(
	'title'	=> $zones_title,
	'vars'	=> array(
		'legend1'				=> 'ACP_TRACKER_RTRACKER_SETTINGS',
	)
);

?>
