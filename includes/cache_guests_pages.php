<?php
/**
*
* @package cgp
* @version $Id: cache_guests_pages.php, v.1.1.2, 2013/04/05 Kot $
* @copyright (c) 2013 Vitaly Filatenko
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

if (isset($config['cgp_enabled']) && $config['cgp_enabled'])
{
	define('CGP_ENABLED', true);
}

if (defined('CGP_ENABLED') && !class_exists('CGP'))
{
	/**
	 * Helper class for providing Cache guests pages functionality
	 *
	 */
	class CGP
	{
		/**
		 * Cache class variable
		 */
		public static $cache;
		const debug_signature = '<!-- CGP DEBUG OUTPUT -->';

		/**
		 * Simulate constructor for static class, should be called after class definition
		 *
		 * @return void
		 *
		 */
		public static function init()
		{
			global $acm_type, $config, $phpbb_root_path, $phpEx;

			// check for cache type settings
			if ($acm_type != 'file' && isset($config['cgp_force_fcache']) && $config['cgp_force_fcache'])
			{
				// force file cache using
				if (!class_exists('acm_file_extended'))
				{
					include($phpbb_root_path . 'includes/acm/acm_file_extended.' . $phpEx);
				}

				self::$cache = new acm_file_extended();
			}
			else
			{
				// use configured cache type
				global $cache;
				self::$cache = &$cache;
			}
		}

		/**
		 * Indicates if user is allowed for pages caching
		 *
		 * @param user $user User class
		 * @return bool True if this type of user is allowed for pages caching
		 *
		 */
		public static function is_cacheable_user(&$user)
		{
			return $user->data['user_id'] == ANONYMOUS || $user->data['is_bot'];
		}

		/**
		 * Writes page content obtained from cached file, forces exit
		 *
		 * @param string $cache_key Name of cached file
		 * @return void
		 *
		 */
		public static function display_if_cached($cache_key)
		{
			global $db, $config, $starttime;

			// check for If-None-Match behavior
			$match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false;

			// prevent unnecessary acm_file_extended initialization
			if (!is_subclass_of(self::$cache, 'acm_file_extended', false))
			{
				//include extended cache class
				if (!class_exists('acm_file_extended'))
				{
					global $phpbb_root_path, $phpEx;
					include($phpbb_root_path . 'includes/acm/acm_file_extended.' . $phpEx);
				}
				$cache_ext = new acm_file_extended();
			}
			else
			{
				$cache_ext = self::$cache;
			}

			$cache_expired = $cache_ext->get_expiration($cache_key);

			// is matches to cached file?
			if ($match !== false && $cache_expired !== false && $match == $cache_expired)
			{
				// return not modified status
				send_status_line(304, 'Not Modified');

				self::set_cache_headers($cache_expired, $user->data['is_bot']);

				garbage_collection();
				exit_handler();
			}
			elseif (($cache_item = $cache_ext->get($cache_key)) !== false)
			{
				// gzip_compression (copied from page_header() )
				if ($config['gzip_compress'])
				{
					if (@extension_loaded('zlib') && !headers_sent() && ob_get_level() <= 1 && ob_get_length() == 0)
					{
						ob_start('ob_gzhandler');
					}
				}

				// not sure that we need for compatibility with HTTP Guest cache anymore
				// since own ETag/If-None-Match mechanism used now for cached pages
				self::set_cache_headers($cache_expired, $user->data['is_bot']);

				// echo debug information to the end of the page
				// it is not possible to use templated place, because no templates are used in cached output
				if (defined('DEBUG'))
				{
					$mtime = explode(' ', microtime());
					$totaltime = $mtime[0] + $mtime[1] - $starttime;

					$debug_output = sprintf('<br />CGP output time : %.3fs | ' . $db->sql_num_queries() . ' Queries | GZIP : ' . (($config['gzip_compress'] && @extension_loaded('zlib')) ? 'On' : 'Off'), $totaltime);

					$cache_item = str_replace(self::debug_signature, $debug_output, $cache_item);
				}

				// echo cached content
				echo $cache_item;

				unset($cache_item);

				garbage_collection();
				exit_handler();
			}

			// do nothing
		}

		/**
		 * Puts rendered page content to cache, overrides cache-related headers, flushes output buffer
		 *
		 * @param string $cache_key Name of cached file
		 * @param int $cache_ttl Cache time to live in minutes, default value equals 12 hours
		 * @return void
		 *
		 */
		public static function cache_content_page($cache_key, $cache_ttl = 720)
		{
			// prevent unnecessary acm_file_extended initialization
			if (!is_subclass_of(self::$cache, 'acm_file_extended', false))
			{
				//include extended cache class
				if (!class_exists('acm_file_extended'))
				{
					global $phpbb_root_path, $phpEx;
					include($phpbb_root_path . 'includes/acm/acm_file_extended.' . $phpEx);
				}
				$cache_ext = new acm_file_extended();
			}
			else
			{
				$cache_ext = self::$cache;
			}

			// cache page content
			$cache_item = ob_get_contents();

			$expires = $cache_ext->put($cache_key, $cache_item, $cache_ttl * 60);

			// override Cache-Control headers
			if (!headers_sent())
			{
				global $user;
				self::set_cache_headers($expires, $user->data['is_bot']);
			}

			// note sure that we need to force buffer flushing,
			// anyway it is supposed that no more content operations will be performed.
			// Uncomment string below to force output buffer flushing
			//ob_end_flush();
		}

		/**
		 * Set cache-related HTTP headers
		 *
		 * @param string $etag ETag header value
		 * @param bool $is_bot Let reverse proxies know we detected a bot
		 * @return void
		 *
		 */
		public static function set_cache_headers($etag, $is_bot = false)
		{
			// setting up page headers
			header('Content-type: text/html; charset=UTF-8');

			header('Cache-Control: public, no-cache="set-cookie"');
			header('Pragma: public');
			header('ETag: ' . $etag);

			if ($is_bot)
			{
				// Let reverse proxies know we detected a bot.
				header('X-PHPBB-IS-BOT: yes');
			}

			/* BEGIN compatibility with mod_http_guest_cache */
			global $mod_http_guest_cache;

			if (isset($mod_http_guest_cache) &&
			$mod_http_guest_cache->is_cacheable() &&
			method_exists($mod_http_guest_cache, 'set_headers'))
			{
				$mod_http_guest_cache->set_headers();
			}
			/* END compatibility with mod_http_guest_cache */
		}

		/**
		 * Returns cache key suffix based on user display preferences
		 *
		 * @param user $user Base user class
		 * @return string Cache key suffix
		 *
		 */
		public static function user_type_suffix(&$user)
		{
			global $config;

			// assumed that guests can use only one predefined forum style
			if(!$config['cgp_multi_styles'])
			{
				return '_' . ($user->data['is_bot'] ? 'bot' : 'guest');
			}
			else
			{
				return '_st' . $user->data['user_style'] . '_' . ($user->data['is_bot'] ? 'bot' : 'guest');
			}
		}

		/**
		 * Removes all cached files related to the topic
		 *
		 * @param int $topic_id Topic id
		 * @param array $topic_data $topic_data object
		 * @return bool True if success, False if fails
		 *
		 */
		public static function destroy_topic_cache($topic_id, &$topic_data = null)
		{
			global $db, $config;

			// try to obtain $topic_data if it wasn't passed by param
			if (!isset($topic_data))
			{
				// I'd prefer to use same function from mcp.php,
				// but that file contains too much trash inside ;)
				$topic_data = CGP::get_topic_data($db, $topic_id);

				// can't obtain any topic information
				if ($topic_data === false)
					return false;
			}

			// destroy cache for topic's forum and forum's parents
			CGP::destroy_forum_cache($topic_data['forum_id'], $topic_data);

			// calculate pages count
			$total_pages = ceil(($topic_data['topic_replies'] + 1) / $config['posts_per_page']);
			if($config['cgp_multi_styles'])
			{
				$styles=CGP::get_active_styles();
			}
			if(!$config['cgp_multi_styles'])
			{
				// destroy cache for all topic pages, for both guest and bot
				for ($i = 0; $i < $total_pages; $i++)
				{
					$start = $i * $config['posts_per_page'];

					self::$cache->destroy("_vt_t{$topic_id}_s{$start}_guest");
					self::$cache->destroy("_vt_t{$topic_id}_s{$start}_bot");
				}
			}
			else
			{
				// destroy cache for all topic pages, for both guest and bot
				for ($i = 0; $i < $total_pages; $i++)
				{
					$start = $i * $config['posts_per_page'];
					foreach($styles as $st)
					{
						self::$cache->destroy("_vt_t{$topic_id}_s{$start}_st{$st}_guest");
						self::$cache->destroy("_vt_t{$topic_id}_s{$start}_st{$st}_bot");
					}
				}
			}

			return true;
		}

		/**
		 * Removes all cached files related to the forum
		 *
		 * @param int $forum_id Forum id
		 * @param array $topic_data $topic_data object
		 * @return bool True if success, False if fails
		 *
		 */
		public static function destroy_forum_cache($forum_id, &$topic_data = null)
		{
			global $db, $config;

			// try to obtain $topic_data if it wasn't passed by param
			if (!isset($topic_data))
			{
				// I'd prefer to use same function from mcp.php,
				// but that file contains too much trash inside ;)
				$topic_data = CGP::get_forum_data($db, $forum_id);

				// can't obtain any topic information
				if ($topic_data === false)
					return false;
			}
			if(!$config['cgp_multi_styles'])
			{
				// destroy cache for index and portal pages
				self::$cache->destroy("_index_guest");
				self::$cache->destroy("_index_bot");
				// you can comment lines below if you don't have phpBB3 Portal installed
				self::$cache->destroy("_portal_guest");
				self::$cache->destroy("_portal_bot");
			}
			else
			{
				$styles=CGP::get_active_styles();
				foreach($styles as $st)
				{
					self::$cache->destroy("_index_st{$st}_guest");
					self::$cache->destroy("_index_st{$st}_bot");
					self::$cache->destroy("_portal_st{$st}_guest");
					self::$cache->destroy("_portal_st{$st}_bot");
				}
			}

			// calculate pages count
			$total_pages = ceil(($topic_data['forum_topics'] + 1) / $config['topics_per_page']);

			if(!$config['cgp_multi_styles'])
			{
				// destroy cache for all topic pages, for both guest and bot
				for ($i = 0; $i < $total_pages; $i++)
				{
					$start = $i * $config['topics_per_page'];
					self::$cache->destroy("_vf_f{$topic_data['forum_id']}_s{$start}_guest");
					self::$cache->destroy("_vf_f{$topic_data['forum_id']}_s{$start}_bot");
				}
			}
			else
			{
				// destroy cache for all topic pages, for both guest and bot
				for ($i = 0; $i < $total_pages; $i++)
				{
					$start = $i * $config['topics_per_page'];
					foreach($styles as $st)
					{
						self::$cache->destroy("_vf_f{$topic_data['forum_id']}_s{$start}_st{$st}_guest");
						self::$cache->destroy("_vf_f{$topic_data['forum_id']}_s{$start}_st{$st}_bot");
					}
				}
			}

			// destroy cache for forum's parents as well
			if (!function_exists('get_forum_parents'))
			{
				global $phpbb_root_path, $phpEx;
				include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
			}

			$forum_parents = get_forum_parents($topic_data);
			if(!$config['cgp_multi_styles'])
			{
				foreach ($forum_parents as $forum_id => $value)
				{
					// Honestly, we need calculate amount of pages for each of these underlying forums.
					// But I'm not sure that heap of additional sql requests are appropriated here.
					// I assume outdated information related to latest message in child forum
					// doesn't costs any additional sql request. For guests, of course. Register for realtime data, yah! :)
					self::$cache->destroy("_vf_f{$forum_id}_s0_guest");
					self::$cache->destroy("_vf_f{$forum_id}_s0_bot");
				}
			}
			else
			{
				foreach ($forum_parents as $forum_id => $value)
				{
					foreach($styles as $st)
					{
						self::$cache->destroy("_vf_f{$forum_id}_s0_st{$st}_guest");
						self::$cache->destroy("_vf_f{$forum_id}_s0_st{$st}_bot");
					}
				}
			}

			return true;
		}

		/**
		 * Obtains $topic_data object
		 *
		 * @param dbal $db Database class, passed by reference to avoid unnecessary "global" using
		 * @param int $topic_id Topic id
		 * @return array $topic_data
		 *
		 */
		private static function get_topic_data(&$db, $topic_id)
		{
			// get related forum data to clear corresponding forum caches as well
			$sql_array = array(
				'SELECT'	=> 't.*, f.*',

				'FROM'		=> array(FORUMS_TABLE => 'f'),
				);

			// Firebird handles two columns of the same name a little differently, this
			// addresses that by forcing the forum_id to come from the forums table.
			if ($db->sql_layer === 'firebird')
			{
				$sql_array['SELECT'] = 'f.forum_id AS forum_id, ' . $sql_array['SELECT'];
			}

			// Topics table need to be the last in the chain
			$sql_array['FROM'][TOPICS_TABLE] = 't';

			$sql_array['WHERE'] = "t.topic_id = $topic_id";

			$sql_array['WHERE'] .= ' AND (f.forum_id = t.forum_id';

			// If it is a global announcement make sure to set the forum id to a postable forum
			$sql_array['WHERE'] .= ' OR (t.topic_type = ' . POST_GLOBAL . '
				AND f.forum_type = ' . FORUM_POST . ')';

			$sql_array['WHERE'] .= ')';

			// Join to forum table on topic forum_id unless topic forum_id is zero
			// whereupon we join on the forum_id passed as a parameter ... this
			// is done so navigation, forum name, etc. remain consistent with where
			// user clicked to view a global topic
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			$topic_data = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if (!$topic_data)
			{
				return false;
			}
			else
			{
				return $topic_data;
			}
		}

		/**
		 * Obtains $forum_data object
		 *
		 * @param dbal $db Database class, passed by reference to avoid unnecessary "global" using
		 * @param int $forum_id Forum id
		 * @return array $forum_data
		 *
		 */
		private static function get_forum_data(&$db, $forum_id)
		{
			$sql = "SELECT *
				FROM " . FORUMS_TABLE .
				" WHERE " . $db->sql_in_set('forum_id', $forum_id);

			$result = $db->sql_query($sql);
			$forum_data = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if (!$forum_data)
			{
				return false;
			}
			else
			{
				return $forum_data;
			}
		}

		private static function get_active_styles($all=false)
		{
			global $db;

			$sql_where = (!$all) ? 'WHERE style_active = 1 ' : '';
			$sql = 'SELECT style_id
				FROM ' . STYLES_TABLE . "
				$sql_where";
			$result = $db->sql_query($sql);

			$styles = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$styles[]=$row['style_id'];
			}
			$db->sql_freeresult($result);

			return $styles;
		}
	} // class CGP

	// emulating static class constructor
	CGP::init();
}
?>
