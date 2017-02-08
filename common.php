<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* Minimum Requirement: PHP 4.3.3
*/

/**
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

require($phpbb_root_path . 'includes/startup.' . $phpEx);

if (file_exists($phpbb_root_path . 'config.' . $phpEx))
{
	require($phpbb_root_path . 'config.' . $phpEx);
}

if (!defined('PHPBB_INSTALLED'))
{
	// Redirect the user to the installer
	require($phpbb_root_path . 'includes/functions.' . $phpEx);
	// We have to generate a full HTTP/1.1 header here since we can't guarantee to have any of the information
	// available as used by the redirect function
	$server_name = (!empty($_SERVER['HTTP_HOST'])) ? strtolower($_SERVER['HTTP_HOST']) : ((!empty($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : getenv('SERVER_NAME'));
	$server_port = (!empty($_SERVER['SERVER_PORT'])) ? (int) $_SERVER['SERVER_PORT'] : (int) getenv('SERVER_PORT');
	$secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 1 : 0;

	$script_name = (!empty($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : getenv('PHP_SELF');
	if (!$script_name)
	{
		$script_name = (!empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : getenv('REQUEST_URI');
	}

	// $phpbb_root_path accounts for redirects from e.g. /adm
	$script_path = trim(dirname($script_name)) . '/' . $phpbb_root_path . 'install/index.' . $phpEx;
	// Replace any number of consecutive backslashes and/or slashes with a single slash
	// (could happen on some proxy setups and/or Windows servers)
	$script_path = preg_replace('#[\\\\/]{2,}#', '/', $script_path);
	// Eliminate . and .. from the path
	$script_path = phpbb_clean_path($script_path);

	$url = (($secure) ? 'https://' : 'http://') . $server_name;

	if ($server_port && (($secure && $server_port <> 443) || (!$secure && $server_port <> 80)))
	{
		// HTTP HOST can carry a port number...
		if (strpos($server_name, ':') === false)
		{
			$url .= ':' . $server_port;
		}
	}

	$url .= $script_path;
	header('Location: ' . $url);
	exit;
}

if (defined('DEBUG_EXTRA'))
{
	$base_memory_usage = 0;
	if (function_exists('memory_get_usage'))
	{
		$base_memory_usage = memory_get_usage();
	}
}

// Load Extensions
// dl() is deprecated and disabled by default as of PHP 5.3.
if (!empty($load_extensions) && function_exists('dl'))
{
	$load_extensions = explode(',', $load_extensions);

	foreach ($load_extensions as $extension)
	{
		@dl(trim($extension));
	}
}

if(@file_exists($phpbb_root_path.'cache/data_cgp_extreme.'.$phpEx) && !isset($_GET['style']) && !isset($_GET['lr']) && !isset($_GET['ap']) && !isset($_GET['np']) && !isset($_GET['tp']))
{
	$cgp_configs=@file($phpbb_root_path.'cache/data_cgp_extreme.'.$phpEx);
	if(isset($cgp_configs[3]))
	{
		$cgp_configs=unserialize($cgp_configs[3]);
		$current_page=basename($_SERVER['SCRIPT_NAME']);
		$cgp_pages=array("portal.{$phpEx}", "index.{$phpEx}", "viewforum.{$phpEx}", "viewtopic.{$phpEx}");
		$cgp_pages_alias=array("portal.{$phpEx}"=>'portal', "index.{$phpEx}"=>'index', "viewforum.{$phpEx}"=>'vf', "viewtopic.{$phpEx}"=>'vt');
		$cgp_places=explode(' ', $cgp_configs['cgp_places']);
		$cgp_place_num=array('portal' => 0, 'index' => 1, 'vf' => 2, 'vt' => 3);
		if(in_array($current_page, $cgp_pages) && !$cgp_places[$cgp_place_num[$cgp_pages_alias[$current_page]]])
		{
			if(isset($_GET['sid']))
			{
				$cgp_user='user';
			}
			else if(isset($_COOKIE[$cgp_configs['cookie_name'].'_u']))
			{
				if($_COOKIE[$cgp_configs['cookie_name'].'_u']==1)
				{
					$cgp_user='guest';
				}
				else
				{
					$cgp_user='user';
				}
			}
			else
			{
				$cookietime=time() + 86400*30;

				$name_data = rawurlencode($cgp_configs['cookie_name'].'_u') . '=' . rawurlencode(1);
				$expire = gmdate('D, d-M-Y H:i:s \\G\\M\\T', $cookietime);
				$domain = (!$cgp_configs['cookie_domain'] || $cgp_configs['cookie_domain'] == 'localhost' || $cgp_configs['cookie_domain'] == '127.0.0.1') ? '' : '; domain=' . $cgp_configs['cookie_domain'];

				header('Set-Cookie: ' . $name_data . (($cookietime) ? '; expires=' . $expire : '') . '; path=' . $cgp_configs['cookie_path'] . $domain . ((!$cgp_configs['cookie_secure']) ? '' : '; secure') . '; HttpOnly', false);
				$cgp_user='bot';
			}

			if(in_array($cgp_user, array('guest', 'bot')))
			{
				$cgp_start=isset($_GET['start']) ? intval($_GET['start']) : 0;
				$cgp_forum=isset($_GET['f']) ? intval($_GET['f']) : 0;
				$cgp_topic=isset($_GET['t']) ? intval($_GET['t']) : 0;
				if($cgp_configs['cgp_multi_styles'])
				{
					$cgp_style=isset($_COOKIE[$cgp_configs['cookie_name'].'_style']) ? intval($_COOKIE[$cgp_configs['cookie_name'].'_style']) : $cgp_configs['default_style'];
				}
				else
				{
					$cgp_style=0;
				}
				switch($cgp_pages_alias[$current_page])
				{
					case 'portal':
						$cgp_page="data_portal_".($cgp_style ? "st{$cgp_style}_" : '')."{$cgp_user}.{$phpEx}";
					break;

					case 'index':
						$cgp_page="data_index_".($cgp_style ? "st{$cgp_style}_" : '')."{$cgp_user}.{$phpEx}";
					break;

					case 'vf':
						$cgp_page="data_vf_f{$cgp_forum}_s{$cgp_start}_".($cgp_style ? "st{$cgp_style}_" : '')."{$cgp_user}.{$phpEx}";
					break;

					case 'vt':
						$cgp_page="data_vt_t{$cgp_topic}_s{$cgp_start}_".($cgp_style ? "st{$cgp_style}_" : '')."{$cgp_user}.{$phpEx}";
					break;
				}
				if(@file_exists("{$phpbb_root_path}cache/{$cgp_page}"))
				{
					$file="{$phpbb_root_path}cache/{$cgp_page}";
					if (!($handle = @fopen($file, 'rb')))
					{
						return false;
					}

					// Skip the PHP header
					fgets($handle);

					$data = false;
					$line = 0;

					while (($buffer = fgets($handle)) && !feof($handle))
					{
						$buffer = substr($buffer, 0, -1); // Remove the LF

						// $buffer is only used to read integers
						// if it is non numeric we have an invalid
						// cache file, which we will now remove.
						if (!is_numeric($buffer))
						{
							break;
						}

						if ($line == 0)
						{
							$expires = (int) $buffer;

							if (time() >= $expires)
							{
								break;
							}
						}
						else if ($line == 1)
						{
							$bytes = (int) $buffer;

							// Never should have 0 bytes
							if (!$bytes)
							{
								break;
							}

							// Grab the serialized data
							$data = fread($handle, $bytes);

							// Read 1 byte, to trigger EOF
							fread($handle, 1);

							if (!feof($handle))
							{
								// Somebody tampered with our data
								$data = false;
							}
							break;
						}
						else
						{
							// Something went wrong
							break;
						}
						$line++;
					}
					fclose($handle);
					if($data!==false)
					{
						// unserialize if we got some data
						$data = @unserialize($data);

						if (defined('DEBUG'))
						{
							$mtime = explode(' ', microtime());
							$totaltime = $mtime[0] + $mtime[1] - $starttime;

							$debug_output = sprintf('<br />CGP [Extreme] output time : %.5fs | 0 Queries | GZIP : ' . (($cgp_configs['gzip_compress'] && @extension_loaded('zlib')) ? 'On' : 'Off'), $totaltime);

							$data = str_replace('<!-- CGP DEBUG OUTPUT -->', $debug_output, $data);
						}
						header('Content-type: text/html; charset=UTF-8');

						header('Cache-Control: public, no-cache="set-cookie"');
						header('Pragma: public');
						header('ETag: ' . $expires);

						// gzip_compression (copied from page_header() )
						if ($cgp_configs['gzip_compress'])
						{
							if (@extension_loaded('zlib') && !headers_sent() && ob_get_level() <= 1 && ob_get_length() == 0)
							{
								ob_start('ob_gzhandler');
							}
						}
						echo $data;
						(ob_get_level() > 0) ? @ob_flush() : @flush();
						exit();
					}
				}
			}
		}
	}
	//include_once($phpbb_root_path.'tracker/include/cgp_extreme.'.$phpEx);
}

// Include files
require($phpbb_root_path . 'includes/acm/acm_' . $acm_type . '.' . $phpEx);
require($phpbb_root_path . 'includes/cache.' . $phpEx);
require($phpbb_root_path . 'includes/template.' . $phpEx);
require($phpbb_root_path . 'includes/session.' . $phpEx);
require($phpbb_root_path . 'includes/auth.' . $phpEx);

require($phpbb_root_path . 'includes/functions.' . $phpEx);
require($phpbb_root_path . 'includes/functions_content.' . $phpEx);

require($phpbb_root_path . 'includes/constants.' . $phpEx);
require($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);
require($phpbb_root_path . 'includes/utf/utf_tools.' . $phpEx);

// Set PHP error handler to ours
set_error_handler(defined('PHPBB_MSG_HANDLER') ? PHPBB_MSG_HANDLER : 'msg_handler');

// Instantiate some basic classes
$user		= new user();
$auth		= new auth();
$template	= new template();
$cache		= new cache();
$db			= new $sql_db();

// Connect to DB
$db->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, defined('PHPBB_DB_NEW_LINK') ? PHPBB_DB_NEW_LINK : false);

// We do not need this any longer, unset for safety purposes
unset($dbpasswd);

// Grab global variables, re-cache if necessary
$config = $cache->obtain_config();

//$db->sql_query("SET sql_mode='NO_UNSIGNED_SUBTRACTION'");
obtain_tracker_config();

// Add own hook handler
require($phpbb_root_path . 'includes/hooks/index.' . $phpEx);
$phpbb_hook = new phpbb_hook(array('exit_handler', 'phpbb_user_session_handler', 'append_sid', array('template', 'display')));

foreach ($cache->obtain_hooks() as $hook)
{
	@include($phpbb_root_path . 'includes/hooks/' . $hook . '.' . $phpEx);
}

?>
