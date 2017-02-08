<?php
/*
 * OpenTracker, TorrentPier
 * revised 10-Sep-2004
 * revised 4-Mar-2008: fixed null tests (thanks to Kevin Dion)
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

function bdecode_f($filename, $filesize)
{
	if (!$filesize || !$fp = fopen($filename, 'rb'))
	{
		return null;
	}
	$fc = fread($fp, $filesize);
	fclose($fp);

	return bdecode($fc);
}

function bdecode($str)
{
	$pos = 0;
	return bdecode_r($str, $pos);
}

function bdecode_r($str, &$pos)
{
	$strlen = strlen($str);

	if (($pos < 0) || ($pos >= $strlen))
	{
		return NULL;
	}
	else if ($str[$pos] == 'i')
	{
		$pos++;
		$numlen = strspn($str, '-0123456789', $pos);
		$spos = $pos;
		$pos += $numlen;

		if (($pos >= $strlen) || ($str[$pos] != 'e'))
		{
			return NULL;
		}
		else
		{
			$pos++;
			return floatval(substr($str, $spos, $numlen));
		}
	}
	else if ($str[$pos] == 'd')
	{
		$pos++;
		$ret = array();

		while ($pos < $strlen)
		{
			if ($str[$pos] == 'e')
			{
				$pos++;
				return $ret;
			}
			else
			{
				$key = bdecode_r($str, $pos);

				if ($key === NULL)
				{
					return NULL;
				}
				else
				{
					$val = bdecode_r($str, $pos);

					if ($val === NULL)
					{
						return NULL;
					}
					else if (!is_array($key))
					{
						$ret[$key] = $val;
					}
				}
			}
		}
		return NULL;
	}
	else if ($str[$pos] == 'l')
	{
		$pos++;
		$ret = array();

		while ($pos < $strlen)
		{
			if ($str[$pos] == 'e')
			{
				$pos++;
				return $ret;
			}
			else
			{
				$val = bdecode_r($str, $pos);

				if ($val === NULL)
				{
					return NULL;
				}
				else
				{
					$ret[] = $val;
				}
			}
		}
		return NULL;
	}
	else
	{
		$numlen = strspn($str, '0123456789', $pos);
		$spos = $pos;
		$pos += $numlen;

		if (($pos >= $strlen) || ($str[$pos] != ':'))
		{
			return NULL;
		}
		else
		{
			$vallen = my_int_val(substr($str, $spos, $numlen));
			$pos++;
			$val = substr($str, $pos, $vallen);

			if (strlen($val) != $vallen)
			{
				return NULL;
			}
			else
			{
				$pos += $vallen;
				return $val;
			}
		}
	}
}

function bencode($var)
{
	if (is_int($var))
	{
		return 'i'. $var .'e';
	}
	else if (is_float($var))
	{
		return 'i'. sprintf('%.0f', $var) .'e';
	}
	else if (is_array($var))
	{
		if (count($var) == 0)
		{
			return 'de';
		}
		else
		{
			$assoc = false;

			foreach ($var as $key => $val)
			{
				if (!is_int($key) && !is_float($var))
				{
					$assoc = true;
					break;
				}
			}

			if ($assoc)
			{
				//ksort($var, SORT_REGULAR);
				$ret = 'd';

				foreach ($var as $key => $val)
				{
					$ret .= bencode($key) . bencode($val);
				}
				return $ret .'e';
			}
			else
			{
				$ret = 'l';

				foreach ($var as $val)
				{
					$ret .= bencode($val);
				}
				return $ret .'e';
			}
		}
	}
	else
	{
		return strlen($var) .':'. $var;
	}
}
?>
