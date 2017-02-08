<?php
/**
*
* @package ppkBB3cker
* @version $Id: posting_extimages_add1.php 1.000 2011-11-30 12:37:56 PPK $
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

$config['ppkbb_max_extposters'][2] ? '' : $config['ppkbb_max_extposters'][2]=3;
$config['ppkbb_max_extscreenshots'][2] ? '' : $config['ppkbb_max_extscreenshots'][2]=5;
$exs_posters=$exs_screenshots=$exs_postscr=array();
$valid_extposters=$valid_extscreenshots=0;
$is_submitted=$posting_page && $submit ? 1 : 0;

if ($submit || $preview || $refresh)
{
	$forb_extpostrscr=$config['ppkbb_forb_extpostscr'] ? explode("\n", trim($config['ppkbb_forb_extpostscr'])) : array();

	if($config['ppkbb_max_extposters'][0] && isset($_POST['extposter']) && sizeof($_POST['extposter']))
	{
		$img_link=array();
		$img_link['postscr_min_width']=$config['ppkbb_max_extposters'][3];
		$img_link['postscr_min_height']=$config['ppkbb_max_extposters'][4];
		$img_link['postscr_max_width']=$config['ppkbb_max_extposters'][5];
		$img_link['postscr_max_height']=$config['ppkbb_max_extposters'][6];
		$img_link['postscr_max_filesize']=$config['ppkbb_max_extposters'][8];
		foreach($_POST['extposter'] as $k => $v)
		{
			$p_check=md5($v);
			if(!isset($exs_postscr[$p_check]) && !empty($v) && utf8_strlen($v) < 256 && $k && $k <= $config['ppkbb_max_extposters'][2])
			{
				$iforb_check=true;
				if(sizeof($forb_extpostrscr))
				{
					foreach($forb_extpostrscr as $i)
					{
						$i=preg_quote($i);
						if(($config['ppkbb_forb_extpostscr_trueexclude'] && preg_match("#https?://(www\.)?{$i}#i", $v)) || (!$config['ppkbb_forb_extpostscr_trueexclude'] && !preg_match("#https?://(www\.)?{$i}#i", $v)))
						{
							$error['FORB_EXTPOSTSCR']=sprintf(($config['ppkbb_forb_extpostscr_trueexclude'] ? $user->lang['FORB_EXTPOSTSCR'] : $user->lang['FORB_EXTPOSTSCR_TRUEEXCLUDE']), implode(', ', $forb_extpostrscr));
							$iforb_check=false;
						}
					}
				}
				$img_link['remotelink']=$v;
				$img_link['width']=@$_POST['is_manual_p'][$k] ? intval(@$_POST['is_width_p'][$k]) : 0;
				$img_link['height']=@$_POST['is_manual_p'][$k] ? intval(@$_POST['is_height_p'][$k]) : 0;
				if($iforb_check && ($submit || !$_POST['exp'][$k] || $_POST['exp'][$k]!=$p_check))
				{
					$img_check=postscr_remote($img_link, $error, " {$user->lang['EXT_POSTERS']}: {$user->lang['EXT_POSTER']}", $k, $config['ppkbb_max_extposters'][7]);
				}
				else
				{
					$img_check=array();
					$img_check[0]=$img_link['width'];
					$img_check[1]=$img_link['height'];
					$img_check[2]='';
					$img_check[3]='';
				}
				//if($img_check)
				//{
					$exs_posters[$k]['real_filename']=$v;
					$exs_posters[$k]['i_width']=$img_check[0];
					$exs_posters[$k]['i_height']=$img_check[1];
					$exs_posters[$k]['extension']=$img_check[2];
					$exs_posters[$k]['mimetype']=$img_check[3];
					$exs_posters[$k]['i_poster']=1;
					$exs_posters[$k]['i_external']=1;
					$exs_posters[$k]['attach_comment']='';
					$exs_posters[$k]['filetime']=$dt;
					$exs_posters[$k]['is_orphan']=0;
					$torrents_attach['poster'][]=$exs_posters[$k];
					$valid_extposters+=1;
				//}
				$exs_postscr[$p_check]=1;
			}
		}
	}
	if($config['ppkbb_max_extscreenshots'][0] && isset($_POST['extscreenshot']) && sizeof($_POST['extscreenshot']))
	{
		$img_link=array();
		$img_link['postscr_min_width']=$config['ppkbb_max_extscreenshots'][3];
		$img_link['postscr_min_height']=$config['ppkbb_max_extscreenshots'][4];
		$img_link['postscr_max_width']=$config['ppkbb_max_extscreenshots'][5];
		$img_link['postscr_max_height']=$config['ppkbb_max_extscreenshots'][6];
		$img_link['postscr_max_filesize']=$config['ppkbb_max_extscreenshots'][8];
		foreach($_POST['extscreenshot'] as $k => $v)
		{
			$s_check=md5($v);
			if(!isset($exs_postscr[$s_check]) && !empty($v) && utf8_strlen($v) < 256 && $k && $k <= $config['ppkbb_max_extscreenshots'][2])
			{
				$iforb_check=true;
				if(sizeof($forb_extpostrscr))
				{
					foreach($forb_extpostrscr as $i)
					{
						$i=preg_quote($i);
						if(($config['ppkbb_forb_extpostscr_trueexclude'] && preg_match("#https?://(www\.)?{$i}#i", $v)) || (!$config['ppkbb_forb_extpostscr_trueexclude'] && !preg_match("#https?://(www\.)?{$i}#i", $v)))
						{
							$error['FORB_EXTPOSTSCR']=sprintf(($config['ppkbb_forb_extpostscr_trueexclude'] ? $user->lang['FORB_EXTPOSTSCR'] : $user->lang['FORB_EXTPOSTSCR_TRUEEXCLUDE']), implode(', ', $forb_extpostrscr));
							$iforb_check=false;
						}
					}
				}
				$img_link['remotelink']=$v;
				$img_link['width']=@$_POST['is_manual_s'][$k] ? intval(@$_POST['is_width_s'][$k]) : 0;
				$img_link['height']=@$_POST['is_manual_s'][$k] ? intval(@$_POST['is_height_s'.$k]) : 0;
				if($iforb_check && ($submit || !$_POST['exs'][$k] || $_POST['exs'][$k]!=$s_check))
				{
					$img_check=postscr_remote($img_link, $error, " {$user->lang['EXT_SCREENSHOTS']}: {$user->lang['EXT_SCREENSHOT']}", $k, $config['ppkbb_max_extscreenshots'][7]);
				}
				else
				{
					$img_check=array();
					$img_check[0]=$img_link['width'];
					$img_check[1]=$img_link['height'];
					$img_check[2]='';
					$img_check[3]='';
				}
				//if($img_check)
				//{
					$exs_screenshots[$k]['real_filename']=$v;
					$exs_screenshots[$k]['i_width']=$img_check[0];
					$exs_screenshots[$k]['i_height']=$img_check[1];
					$exs_screenshots[$k]['extension']=$img_check[2];
					$exs_screenshots[$k]['mimetype']=$img_check[3];
					$exs_screenshots[$k]['i_poster']=2;
					$exs_screenshots[$k]['i_external']=1;
					$exs_screenshots[$k]['attach_comment']='';
					$exs_screenshots[$k]['filetime']=$dt;
					$exs_screenshots[$k]['is_orphan']=0;
					$torrents_attach['screenshot'][]=$exs_screenshots[$k];
					$valid_extscreenshots+=1;
				//}
				$exs_postscr[$s_check]=1;
			}
		}
	}
	if($is_submitted && !$auth->acl_gets('a_', 'm_') && !$auth->acl_get('m_', $forum_id))
	{
		if($config['ppkbb_max_extposters'][0] && $config['ppkbb_max_extposters'][1] && $valid_extposters < $config['ppkbb_max_extposters'][1])
		{
			$error[]=sprintf($user->lang['EXTPOSTERS_REQUIRED'], $config['ppkbb_max_extposters'][1]);
		}
		if($config['ppkbb_max_extscreenshots'][0] && $config['ppkbb_max_extscreenshots'][1] && $valid_extscreenshots < $config['ppkbb_max_extscreenshots'][1])
		{
			$error[]=sprintf($user->lang['EXTSCREENSHOTS_REQUIRED'], $config['ppkbb_max_extscreenshots'][1]);
		}
	}
}
if($posting_page && $topic_id && $mode=='edit' && !$preview && !$submit)
{
	$p=$s=1;
	$exs_postscr=$message_parser->attachment_data;
	foreach($exs_postscr as $v)
	{
		if($v['i_external'] && $v['i_poster'])
		{
			if($v['i_poster']==1)
			{
				$exs_posters[$p]=$v;
				$p+=1;
			}
			else if($v['i_poster']==2)
			{
				$exs_screenshots[$s]=$v;
				$s+=1;
			}
		}
	}
}
if($config['ppkbb_max_extposters'][0])
{
	$template->assign_vars(array(
		'S_EXTPOSTERS' => true,
		)
	);
	$user->lang['MAX_EXT_POSTERS']=sprintf($user->lang['MAX_EXT_POSTERS'], $config['ppkbb_max_extposters'][2], $config['ppkbb_max_extposters'][1]);
	for($i=1;$i<$config['ppkbb_max_extposters'][2]+1;$i++)
	{
		$poster=isset($exs_posters[$i]) ? $exs_posters[$i]['real_filename'] : '';
		$width=isset($exs_posters[$i]) ? $exs_posters[$i]['i_width'] : 0;
		$height=isset($exs_posters[$i]) ? $exs_posters[$i]['i_height'] : 0;
		$template->assign_block_vars('extposters', array(
			'EXTPOSTERS_I' => $i,
			'EXTPOSTERS_MD5' => $poster ? md5($poster) : 0,
			'EXTPOSTERS_POSTER' => htmlspecialchars($poster),
			'EXTPOSTERS_WIDTH' => $width,
			'EXTPOSTERS_HEIGHT' => $height,
			)
		);
	}
}
if($config['ppkbb_max_extscreenshots'][0])
{
	$template->assign_vars(array(
		'S_EXTSCREENSHOTS' => true,
		)
	);
	$user->lang['MAX_EXT_SCREENSHOTS']=sprintf($user->lang['MAX_EXT_SCREENSHOTS'], $config['ppkbb_max_extscreenshots'][2], $config['ppkbb_max_extscreenshots'][1]);
	for($i=1;$i<$config['ppkbb_max_extscreenshots'][2]+1;$i++)
	{
		$screenshot=isset($exs_screenshots[$i]) ? $exs_screenshots[$i]['real_filename'] : '';
		$width=isset($exs_screenshots[$i]) ? $exs_screenshots[$i]['i_width'] : 0;
		$height=isset($exs_screenshots[$i]) ? $exs_screenshots[$i]['i_height'] : 0;
		$template->assign_block_vars('extscreenshots', array(
			'EXTSCREENSHOTS_I' => $i,
			'EXTSCREENSHOTS_MD5' => $screenshot ? md5($screenshot) : 0,
			'EXTSCREENSHOTS_POSTER' => htmlspecialchars($screenshot),
			'EXTSCREENSHOTS_WIDTH' => $width,
			'EXTSCREENSHOTS_HEIGHT' => $height,
			)
		);
	}
}

?>
