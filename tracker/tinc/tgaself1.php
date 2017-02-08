<?php
/**
*
* @package ppkBB3cker
* @version $Id: taself1.php 1.000 2009-02-13 12:02:00 PPK $
* @copyright (c) 2008 PPK
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

$uploaded ? $upthis=my_int_val($uploaded - $attachment['uploaded']) : $upthis=0;

$downloaded ? $downthis = my_int_val($downloaded - $attachment['downloaded']) : $downthis=0;

if($upthis)
{
	$updatesnatch[]="uploadoffset='{$upthis}'";
	$updatesnatch[]="uploaded=uploaded+{$upthis}";
	$updatepeers[]="uploaded=uploaded+{$upthis}";
}
if($downthis)
{
	$updatesnatch[]="downloadoffset='{$downthis}'";
	$updatesnatch[]="downloaded=downloaded+{$downthis}";
	$updatepeers[]="downloaded=downloaded+{$downthis}";
}

$updateuser[]="user_last_time='{$dt}'";
if(sizeof($updateuser))
{
	my_sql_query('UPDATE '. TRACKER_GUESTS_TABLE .' SET '.implode(', ', $updateuser)." WHERE user_id='{$userid}' LIMIT 1");
}

?>
