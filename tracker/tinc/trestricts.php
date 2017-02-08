<?php
/**
*
* @package ppkBB3cker
* @version $Id: trestricts.php 1.000 2009-03-02 19:43:00 PPK $
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

$userratio=get_ratio($user['user_uploaded'], $user['user_downloaded'], $config['ppkbb_tcratio_start'], $user['user_bonus']);

//From includes/functions.php
function get_ratio($up, $down, $skip=0, $bonus=0.000)
{
	global $config;

	$config['ppkbb_tcbonus_value'][0] < 0 ? $bonus=0.000 : '';
	//settype($up, 'integer');//Byte
	//settype($down, 'integer');//Byte
	settype($skip, 'integer');//Gb

	$ratio=0;

	if($skip && $down < $skip)
	{
		$ratio='None.';
	}
	else if(!$up && !$down)
	{
		$ratio='Inf.';
	}
	else if(!$up && $down)
	{
		$ratio='Leech.';
	}
	else if(!$down && $up)
	{
		$ratio='Seed.';
	}
	else
	{
		$ratio=number_format($up / $down, 3, '.', '');
	}
	if($bonus!=0.000 && !in_array($ratio, array('Inf.', 'Seed.', 'Leech.', 'None.')))
	{
		settype($bonus, 'float');
		$ratio=number_format($ratio + $bonus, 3, '.', '');
	}

	return $ratio;
}

//From includes/functions.php
function get_trestricts($uploaded, $downloaded, $ratio, $ppkbb_tcwait_time, $t='down')
{

	//settype($uploaded, 'integer');//Byte
	//settype($downloaded, 'integer');//Byte

	$uploaded = intval($uploaded/1024/1024/1024);//Gb
	$downloaded = intval($downloaded/1024/1024/1024);//Gb

	$lines=explode("\n", trim($ppkbb_tcwait_time));

	if($lines)
	{
		foreach($lines as $line)
		{
			@list($ratios, $updown, $timetorrent)=explode('|', $line);
			if(in_array($ratio, array('Inf.', 'Leech.', 'Seed.', 'None.')))
			{
				if($ratios==$ratio)
				{
					if($t=='up')
					{
						if($updown && $uploaded < $updown)
						{
							return intval($timetorrent);
						}
						else
						{
							return intval($timetorrent);
						}
					}
					else
					{
						if($updown && $downloaded > $updown)
						{
							return intval($timetorrent);
						}
						else
						{
							return intval($timetorrent);
						}
					}
				}
			}
			else
			{
				if(!in_array($ratios, array('Inf.', 'Leech.', 'Seed.', 'None.')))
				{
					$ratios=floatval($ratios);
					$updown=intval($updown);

					if($t=='up')
					{
						if($ratios && !$updown)
						{
							if($ratio < $ratios)
							{
								return intval($timetorrent);
							}
						}
						else if(!$ratios && $updown)
						{
							if($uploaded < $updown)
							{
								return intval($timetorrent);
							}
						}
						else
						{
							if($ratio < $ratios && $uploaded < $updown)
							{
								return intval($timetorrent);
							}
						}
					}
					else
					{
						if($ratios && !$updown)
						{
							if($ratio < $ratios)
							{
								return intval($timetorrent);
							}
						}
						else if(!$ratios && $updown)
						{
							if($downloaded > $updown)
							{
								return intval($timetorrent);
							}
						}
						else
						{
							if($ratio < $ratios && $downloaded > $updown)
							{
								return intval($timetorrent);
							}
						}
					}
				}
			}
		}
	}

	return -1;
}
?>
