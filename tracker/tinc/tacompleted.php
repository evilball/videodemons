<?php
/**
*
* @package ppkBB3cker
* @version $Id: tacompleted.php 1.000 2009-08-21 18:19:00 PPK $
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

$updatesnatch[]="completedat='{$dt}'";

if($user['finished']==NULL)
{
	$snatch_add['k'][]='completedat';
	$snatch_add['k'][]='finished';
	$snatch_add['v'][]=$dt;
	$snatch_add['v'][]=1;
}
else
{
	if(!$user['finished'])
	{
		$updateset[]="times_completed=times_completed+1";
	}
	$updatesnatch[]="finished=finished+1";
}
?>
