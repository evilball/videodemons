<?php
/**
*
* @package ppkBB3cker
* @version $Id: cnochat.php 1.000 2010-03-14 13:11:00 PPK $
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

$lang=basename($lang);
if(!file_exists("{$phpbb_root_path}language/{$lang}/mods/ppkbb3cker_chat.{$phpEx}"))
{
	err('Lang file not exists');
}
require_once("{$phpbb_root_path}language/{$lang}/mods/ppkbb3cker_chat.{$phpEx}");
$config['ppkbb_chat_murefresh']=3600;
$user_table= '';

$message_table= '<table width="100%" cellpadding="0" cellspacing="1" border="0">';
$message_table.= '
	<tr valign="top">
		<td>
			<span class="textmess">'.$lang['CHAT_LOGIN'].'</span>
		</td>
	</tr>';
$message_table.= '</table>';
?>
