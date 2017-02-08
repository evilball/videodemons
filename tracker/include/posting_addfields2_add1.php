<?php
/**
*
* @package ppkBB3cker
* @version $Id: posting_addfields2_add1.php 1.000 2009-03-25 11:39:00 PPK $
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

$post_addfields='';

foreach($array_addfields as $k)
{
	$v='';
	$c=intval(@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_COUNT'][$k]);
	$cf_bbcodes=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_BBCODES'][$k];
	$addfields_skip=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_SKIP'][$k];
	if($c > 1)
	{
		$tmp_addfields=array();
		for($i=1;$i<=$c;$i++)
		{
			$v=@$_POST[$k.$i];
			if($v)
			{
				STRIP ? $v=stripslashes($v) : '';
				if(!$addfields_skip)
				{
					if($cf_bbcodes)
					{
						$tmp_addfields[]=sprintf($cf_bbcodes, $v);
					}
					else
					{
						$tmp_addfields[]="[b]{$torrent_addfield['TRACKER_FORUM_ADDFIELDS'][$k]} #{$i}[/b]: {$v}";
					}
				}
			}
		}
		if(sizeof($tmp_addfields))
		{
			$br_addfields=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_BR'][$k] ? $torrent_addfield['TRACKER_FORUM_ADDFIELDS_BR'][$k] : '';
			$br_addfields2=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_BR2'][$k] ? $torrent_addfield['TRACKER_FORUM_ADDFIELDS_BR2'][$k] : '';
			$post_addfields ? $post_addfields.=$br_addfields."\r\n" : '';
			$cf_bbcode=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_BBCODE'][$k];
			if($cf_bbcode)
			{
				$post_addfields.=sprintf($cf_bbcode, implode((@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_SPLIT'][$k] ? $torrent_addfield['TRACKER_FORUM_ADDFIELDS_SPLIT'][$k] : "\r\n"), $tmp_addfields));
			}
			else
			{
				$post_addfields.=implode("\r\n", $tmp_addfields);
			}
			$post_addfields ? $post_addfields.=$br_addfields2 : '';
		}
	}
	else if($c)
	{
		if(!$addfields_skip)
		{
			$v=@$_POST[$k];
			$tmp_addfields=array();
			$br_addfields=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_BR'][$k] ? $torrent_addfield['TRACKER_FORUM_ADDFIELDS_BR'][$k] : '';
			$br_addfields2=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_BR2'][$k] ? $torrent_addfield['TRACKER_FORUM_ADDFIELDS_BR2'][$k] : '';
			$v ? $post_addfields.=$br_addfields : '';
			$post_addfields && $v ? $post_addfields.="\r\n" : '';
			if($v)
			{
				if(is_array($v) && @$torrent_addfield['TRACKER_FORUM_ADDFIELDS_MULTI'][$k])
				{
					$v=implode(', ', $v);
				}
				STRIP ? $v=stripslashes($v) : '';
				if($preview && @$torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING'])
				{
					$torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']=str_replace("#{$k}#", $v, $torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']);
				}
				$cf_bbcodes=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_BBCODES'][$k];
				if($cf_bbcodes)
				{
					$tmp_addfields[]=sprintf($cf_bbcodes, $v);
				}
				else if(@$torrent_addfield['TRACKER_FORUM_ADDFIELDS'][$k])
				{
					$tmp_addfields[]="[b]{$torrent_addfield['TRACKER_FORUM_ADDFIELDS'][$k]}[/b]: {$v}";
				}
				$cf_bbcode=@$torrent_addfield['TRACKER_FORUM_ADDFIELDS_BBCODE'][$k];
				if(sizeof($tmp_addfields))
				{
					if($cf_bbcode)
					{
						$post_addfields.=sprintf($cf_bbcode, implode("\r\n", $tmp_addfields));
					}
					else
					{
						$post_addfields.=implode("\r\n", $tmp_addfields);
					}
				}
			}
			$v ? $post_addfields.=$br_addfields2 : '';
		}
		else
		{
			$v=@$_POST[$k];
			if($v)
			{
				if(is_array($v) && @$torrent_addfield['TRACKER_FORUM_ADDFIELDS_MULTI'][$k])
				{
					$v=implode(', ', $v);
				}
				STRIP ? $v=stripslashes($v) : '';
				if($preview && @$torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING'])
				{
					$torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']=str_replace("#{$k}#", $v, $torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']);
				}
			}
		}
	}
}
if(!$submit && $mode=='post' && (request_var('addf_switch', 0) || !request_var('message', '', true)) && @$torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING'])
{
	$torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']=preg_replace('/{[^{]*#\w+#[^{]*}/', '', $torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']);
	$torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']=preg_replace('/#\w+#/', '', $torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']);
	$torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']=str_replace(array('{', '}'), '', $torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']);
	$torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']=preg_replace('/\s+/', ' ', $torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']);
	trim($torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING']) ? $_REQUEST['subject']=$torrent_addfield['TRACKER_FORUM_ADDFIELDS_SUBJSTRING'] : '';
}
$request_message=isset($_REQUEST['message']) ? $_REQUEST['message'] : '';
$post_addfields ? $_REQUEST['message']=(!$config['ppkbb_addfields_type'][3] ? $post_addfields."\r\n".$request_message : $request_message."\r\n".$post_addfields) : '';
?>
