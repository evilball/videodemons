<?php
/**
*
* @package ppkBB3cker
* @version $Id: cmgurl.php 1.000 2012-04-11 14:05:14 PPK $
* @copyright (c) 2012 PPK
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

//From /includes/functions_content.php
function make_clickable_callback($type, $whitespace, $url, $relative_url, $class)
{
	$orig_url		= $url;
	$orig_relative	= $relative_url;
	$append			= '';
	$url			= htmlspecialchars_decode($url);
	$relative_url	= htmlspecialchars_decode($relative_url);

	// make sure no HTML entities were matched
	$chars = array('<', '>', '"');
	$split = false;

	foreach ($chars as $char)
	{
		$next_split = strpos($url, $char);
		if ($next_split !== false)
		{
			$split = ($split !== false) ? min($split, $next_split) : $next_split;
		}
	}

	if ($split !== false)
	{
		// an HTML entity was found, so the URL has to end before it
		$append			= substr($url, $split) . $relative_url;
		$url			= substr($url, 0, $split);
		$relative_url	= '';
	}
	else if ($relative_url)
	{
		// same for $relative_url
		$split = false;
		foreach ($chars as $char)
		{
			$next_split = strpos($relative_url, $char);
			if ($next_split !== false)
			{
				$split = ($split !== false) ? min($split, $next_split) : $next_split;
			}
		}

		if ($split !== false)
		{
			$append			= substr($relative_url, $split);
			$relative_url	= substr($relative_url, 0, $split);
		}
	}

	// if the last character of the url is a punctuation mark, exclude it from the url
	$last_char = ($relative_url) ? $relative_url[strlen($relative_url) - 1] : $url[strlen($url) - 1];

	switch ($last_char)
	{
		case '.':
		case '?':
		case '!':
		case ':':
		case ',':
			$append = $last_char;
			if ($relative_url)
			{
				$relative_url = substr($relative_url, 0, -1);
			}
			else
			{
				$url = substr($url, 0, -1);
			}
		break;

		// set last_char to empty here, so the variable can be used later to
		// check whether a character was removed
		default:
			$last_char = '';
		break;
	}

	$short_url = (utf8_strlen($url) > 55) ? utf8_substr($url, 0, 39) . ' ... ' . utf8_substr($url, -10) : $url;

	switch ($type)
	{
		case MAGIC_URL_FULL:
			$tag	= 'm';
			$text	= $short_url;
		break;

		case MAGIC_URL_WWW:
			$tag	= 'w';
			$url	= 'http://' . $url;
			$text	= $short_url;
		break;

		case MAGIC_URL_EMAIL:
			$tag	= 'e';
			$text	= $short_url;
			$url	= 'mailto:' . $url;
		break;
	}

	$url	= htmlspecialchars($url);
	$text	= htmlspecialchars($text);
	$append	= htmlspecialchars($append);

	$html	= "$whitespace<!-- $tag --><a onclick=\"window.open(this.href);return false;\"$class href=\"$url\">$text</a><!-- $tag -->$append";

	return $html;
}

//From /includes/functions_content.php
function make_clickable($text, $server_url = false, $class = 'postlink')
{

	static $magic_url_match;
	static $magic_url_replace;
	static $static_class;

	if (!is_array($magic_url_match) || $static_class != $class)
	{
		$static_class = $class;
		$class = ($static_class) ? ' class="' . $static_class . '"' : '';
		$local_class = ($static_class) ? ' class="' . $static_class . '-local"' : '';

		$magic_url_match = $magic_url_replace = array();
		// Be sure to not let the matches cross over. ;)

		// matches a xxxx://aaaaa.bbb.cccc. ...
		$magic_url_match[] = '#(^|[\n\t (>.])(' . get_preg_expression('url_inline') . ')#ieu';
		$magic_url_replace[] = "make_clickable_callback(MAGIC_URL_FULL, '\$1', '\$2', '', '$class')";

		// matches a "www.xxxx.yyyy[/zzzz]" kinda lazy URL thing
		$magic_url_match[] = '#(^|[\n\t (>])(' . get_preg_expression('www_url_inline') . ')#ieu';
		$magic_url_replace[] = "make_clickable_callback(MAGIC_URL_WWW, '\$1', '\$2', '', '$class')";

		// matches an email@domain type address at the start of a line, or after a space or after what might be a BBCode.
		$magic_url_match[] = '/(^|[\n\t (>])(' . get_preg_expression('email') . ')/ie';
		$magic_url_replace[] = "make_clickable_callback(MAGIC_URL_EMAIL, '\$1', '\$2', '', '')";
	}

	return @preg_replace($magic_url_match, $magic_url_replace, $text);
}

//From /includes/functions.php
function get_preg_expression($mode)
{
	switch ($mode)
	{
		case 'email':
			// Regex written by James Watts and Francisco Jose Martin Moreno
			// http://fightingforalostcause.net/misc/2006/compare-email-regex.php
			return '([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*(?:[\w\!\#$\%\'\*\+\-\/\=\?\^\`{\|\}\~]|&amp;)+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)';
		break;

		case 'url':
		case 'url_inline':
			$inline = ($mode == 'url') ? ')' : '';
			$scheme = ($mode == 'url') ? '[a-z\d+\-.]' : '[a-z\d+]'; // avoid automatic parsing of "word" in "last word.http://..."
			// generated with regex generation file in the develop folder
			return "[a-z]$scheme*:/{2}(?:(?:[\pLa-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})+|[0-9.]+|\[[\pLa-z0-9.]+:[\pLa-z0-9.]+:[\pLa-z0-9.:]+\])(?::\d*)?(?:/(?:[\pLa-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[\pLa-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[\pLa-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?";
		break;

		case 'www_url':
		case 'www_url_inline':
			$inline = ($mode == 'www_url') ? ')' : '';
			return "www\.(?:[\pLa-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})+(?::\d*)?(?:/(?:[\pLa-z0-9\-._~!$&'($inline*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[\pLa-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[\pLa-z0-9\-._~!$&'($inline*+,;=:@/?|]+|%[\dA-F]{2})*)?";
		break;

	}

	return '';
}

//From includes/utf/utf_tools.php
if(!function_exists('utf8_substr'))
{
	function utf8_substr($str, $offset, $length = NULL)
	{
		// generates E_NOTICE
		// for PHP4 objects, but not PHP5 objects
		$str = (string) $str;
		$offset = (int) $offset;
		if (!is_null($length))
		{
			$length = (int) $length;
		}

		// handle trivial cases
		if ($length === 0 || ($offset < 0 && $length < 0 && $length < $offset))
		{
			return '';
		}

		// normalise negative offsets (we could use a tail
		// anchored pattern, but they are horribly slow!)
		if ($offset < 0)
		{
			// see notes
			$strlen = utf8_strlen($str);
			$offset = $strlen + $offset;
			if ($offset < 0)
			{
				$offset = 0;
			}
		}

		$op = '';
		$lp = '';

		// establish a pattern for offset, a
		// non-captured group equal in length to offset
		if ($offset > 0)
		{
			$ox = (int) ($offset / 65535);
			$oy = $offset % 65535;

			if ($ox)
			{
				$op = '(?:.{65535}){' . $ox . '}';
			}

			$op = '^(?:' . $op . '.{' . $oy . '})';
		}
		else
		{
			// offset == 0; just anchor the pattern
			$op = '^';
		}

		// establish a pattern for length
		if (is_null($length))
		{
			// the rest of the string
			$lp = '(.*)$';
		}
		else
		{
			if (!isset($strlen))
			{
				// see notes
				$strlen = utf8_strlen($str);
			}

			// another trivial case
			if ($offset > $strlen)
			{
				return '';
			}

			if ($length > 0)
			{
				// reduce any length that would
				// go passed the end of the string
				$length = min($strlen - $offset, $length);

				$lx = (int) ($length / 65535);
				$ly = $length % 65535;

				// negative length requires a captured group
				// of length characters
				if ($lx)
				{
					$lp = '(?:.{65535}){' . $lx . '}';
				}
				$lp = '(' . $lp . '.{'. $ly . '})';
			}
			else if ($length < 0)
			{
				if ($length < ($offset - $strlen))
				{
					return '';
				}

				$lx = (int)((-$length) / 65535);
				$ly = (-$length) % 65535;

				// negative length requires ... capture everything
				// except a group of  -length characters
				// anchored at the tail-end of the string
				if ($lx)
				{
					$lp = '(?:.{65535}){' . $lx . '}';
				}
				$lp = '(.*)(?:' . $lp . '.{' . $ly . '})$';
			}
		}

		if (!preg_match('#' . $op . $lp . '#us', $str, $match))
		{
			return '';
		}

		return $match[1];
	}
}
?>
