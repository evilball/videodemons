<?php
/**
*
* @package ppkBB3cker
* @version $Id: cvarchchat.php 1.000 2010-03-14 13:24:00 PPK $
* @copyright (c) 2010 PPK
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

function view_archive($text)
{
	global $lang;

	echo '
		<table width="100%" id="chat_archive">
			<tr>
				<th>'.$lang['CHAT_ARCHIVE'].'</th>
			</tr>
			<tbody id="chat_archive">
			<tr>
				<td valign="top">
					<div id="chat_window" style="height:300px;overflow:auto;">
					'.($text ? $text : $lang['CHAT_ARCH_EMPTY']).'
					</div>
				</td>
			</tr>
			</tbody>
		</table>
	';
}
?>
