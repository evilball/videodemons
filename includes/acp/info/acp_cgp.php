<?php
/**
*
* @package cgp
* @version $Id: acp_cgp.php, v.1.1.2, 2013/04/05 Kot $
* @copyright (c) 2013 Vitaly Filatenko
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_cgp_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_cgp',
			'title'		=> 'CGP_CAT_NAME',
			'version'	=> '1.1.2',
			'modes'		=> array(
				'main'		=> array(
					'title' => 'CGP_MOD_NAME',
					'auth'	=> 'acl_a_server',
					'cat' 	=> array('CGP_CAT_NAME'),
					),
				),
			);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}
?>