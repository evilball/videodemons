<?php
/**
*
* @package ppkBB3cker
* @version $Id: rannfunc.php 1.000 2010-05-07 11:25:00 PPK $
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

function remote_announce($url, $info_hash, $scrape=false)
{
	global $phpEx, $phpbb_root_path, $config;

	if(ini_get('allow_url_fopen')!=1 || (!function_exists('file_get_contents') && !function_exists('fsockopen') && !function_exists('curl_version')))
	{
		return array('a_message' => "Remote announce: functions 'file_get_contents/fsockopen/curl_version' or 'allow_url_fopen' disabled");
	}

	$a_url = @parse_url($url);
	$scheme = @$a_url['scheme'];
	$host = @$a_url['host'];
	$port = isset($a_url['port']) ? $a_url['port'] : '';
	$path = @$a_url['path'];

	$q_string = isset($a_url['query']) ? explode('&', $a_url['query']) : array();
	$get_opt=array();

	foreach($q_string as $value)
	{
		@list($q_key, $q_value)=explode('=', $value);
		$get_opt[$q_key] = $q_value;
	}

	$get_opt = array_merge($get_opt, array(
		'peer_id' => urlencode($config['ppkbb_tcrannounces_options'][10].(substr(md5(date('d-m-Y')), 0, 12))),
		'port' => rand(1024, 65535),
		'uploaded' => 0,
		'downloaded' => 0,
		'left' => 1,
		'corrupt' => 0,
		'key' => substr(md5(date('Y-m-d')), 0, 8),
		'event' => 'started',
		'supportcrypto' => 1,
		'numwant' => $config['ppkbb_tcrannounces_options'][2] ? $config['ppkbb_tcrannounces_options'][2] : 50,
		'compact' => 1,
		'no_peer_id' => 1,
		'info_hash' => rawurlencode($info_hash),
		)
	);

	//$query = http_build_query($get_opt);
	$query='';
	$i=0;
	$q=sizeof($get_opt);
	foreach($get_opt as $k => $v)
	{
		$i+=1;
		$query.="{$k}={$v}".($i<$q ? '&' : '');
	}

	$timeout=$config['ppkbb_tcrannounces_options'][4] ? $config['ppkbb_tcrannounces_options'][4] : 5;
	$result=array();
	$error='';

	if(in_array($scheme, array('http')))
	{
		if(function_exists('file_get_contents'))
		{
			$opt = array('http' =>
				array(
					'method' => 'GET',
					'header' => "User-Agent: {$config['ppkbb_tcrannounces_options'][9]}\r\n".
							"Host: {$host}\r\n".
							"Connection: close\r\n",
					'timeout' => $timeout
				)
			);

			$context = stream_context_create($opt);
			$url='http://'.$host.($port ? ':'.$port : '').$path.($query ? '?'.$query : '');
			$result = @file_get_contents($url, false, $context);
		}
		else if(function_exists('curl_version'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://'.$host.($port ? ':'.$port : '').$path.($query ? '?'.$query : ''));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, $config['ppkbb_tcrannounces_options'][9]);
			$result = curl_exec($ch);

			curl_close ($ch);
		}
		else if(function_exists('fsockopen'))
		{
			$fp=@fsockopen($host, ($port ? $port : 80), $errno, $errstr, $timeout);
			if(!$fp)
			{
				$error=$errstr.($errno ? " ({$errno})" : '');
			}
			else
			{
				$request="GET {$path}".($query ? '?'.$query : '')." HTTP/1.1\r\n";
				$request.="Host: $host\r\n";
				$request.="User-Agent: {$config['ppkbb_tcrannounces_options'][9]}\r\n";
				$request.="Connection: close\r\n\r\n";
				fputs($fp, $request);
				//stream_set_timeout($fp, $timeout);
				$result='';
				while(!feof($fp))
				{
					$line=fgets($fp, 4096);
					if($line=="\r\n" && !isset($header))
					{
						$header=true;
					}
					if(isset($header))
					{
						$result.=$line;
					}
				}
				fclose($fp);
				isset($header) ? $result=trim($result) : '';
			}
		}
	}
	else if($scheme=='udp')
	{
		include_once("{$phpbb_root_path}tracker/include/udptscraper.{$phpEx}");
		try
		{
			$scraper = new udptscraper($timeout);
			$info_hash2=bin2hex($info_hash);
			$url="udp://".$host.($port ? ':'.$port : '').$path.($query ? '?'.$query : '');

			$ret = $scraper->scrape($url, array($info_hash2));

			$result=array(
				'interval' => 0,
				'seeders' => my_int_val(@$ret[$info_hash2]['seeders'], 10000, true),
				'leechers' => my_int_val(@$ret[$info_hash2]['leechers'], 10000, true),
				'times_completed' => my_int_val(@$ret[$info_hash2]['completed'], 100000, true),
				'peers' => my_int_val(@$ret[$info_hash2]['seeders']+@$ret[$info_hash2]['leechers'], 20000, true),
				'a_message'=> '',
			);

			return $result;
		}
		catch(ScraperException $e)
		{
			return array('a_message' => 'UDP announce error: '.$e->getMessage().($e->isConnectionError() ? ' (connection error)' : ''));
		}
		//return array('a_message' => 'UDP announce not supported');
	}

	if(!$result)
	{
		return array('a_message' => ($result===false ? 'Query timeout' : ($error ? $error : 'Empty result')));
	}

	include_once($phpbb_root_path.'tracker/include/bencoding.'.$phpEx);
	$result = bdecode($result);

	if(!is_array($result))
	{
		return array('a_message' => 'BDecoding error');
	}

	$complete=my_int_val(@$result['complete']);
	$incomplete=my_int_val(@$result['incomplete']);
	$downloaded=my_int_val(@$result['downloaded']);
	$peers=@$result['peers'];
	if(!is_numeric($peers))
	{
		$bin_peers=@unpack('N*', $peers);
		if(is_array($bin_peers))
		{
			$peers=sizeof($bin_peers);
		}
		else
		{
			$peers=my_int_val(strlen($peers)/6);
		}
	}

	if($peers && $complete+$incomplete==0)
	{
		!$config['ppkbb_tcrannounces_options'][3] || $config['ppkbb_tcrannounces_options'][3] > 100 ? $config['ppkbb_tcrannounces_options'][3]=100 : '';
		$complete=my_int_val($config['ppkbb_tcrannounces_options'][3]*$peers/100);
		$incomplete=$peers-$complete;
	}
	else
	{
		$peers=$complete+$incomplete;
	}

	$return=array(
		'interval' => my_int_val(@$result['interval'], 10000, true),
		'seeders' => my_int_val($complete, 10000, true),
		'leechers' => my_int_val($incomplete, 10000, true),
		'times_completed' => my_int_val($downloaded, 100000, true),
		'peers' => my_int_val($peers, 100000, true),
		'a_message'=> @$result['failure reason'] ? my_utf8_convert_message($result['failure reason']) : '',
	);
	$scrape ? $return['s_message']='Scrape not supported' : '';

	return $return;

}

function remote_scrape($url, $info_hash)
{
	global $phpEx, $phpbb_root_path, $config;

	if(ini_get('allow_url_fopen')!=1 || (!function_exists('file_get_contents') && !function_exists('fsockopen')) && !function_exists('curl_version'))
	{
		return array('a_message' => "Remote announce: functions 'file_get_contents/fsockopen/curl_version' or 'allow_url_fopen' disabled");
	}

	$url_scrape=str_replace('announce', 'scrape', $url);

	if($config['ppkbb_tcrannounces_options'][7] || $url_scrape==$url)
	{
		return remote_announce($url, $info_hash, true);
	}
	else
	{
		$url=$url_scrape;

		$a_url = @parse_url($url);
		$scheme = @$a_url['scheme'];
		$host = @$a_url['host'];
		$port = isset($a_url['port']) ? $a_url['port'] : '';
		$path = @$a_url['path'];

		$q_string = isset($a_url['query']) ? explode('&', $a_url['query']) : array();
		$get_opt=array();

		foreach($q_string as $value)
		{
			@list($q_key, $q_value)=explode('=', $value);
			$get_opt[$q_key] = $q_value;
		}

		$get_opt = array_merge($get_opt, array(
			/*'peer_id' => urlencode($config['ppkbb_tcrannounces_options'][10].(substr(md5(date('d-m-Y')), 0, 12))),
			'port' => rand(1024, 65535),
			'uploaded' => 0,
			'downloaded' => 0,
			'left' => 1,*/
			'info_hash' => rawurlencode($info_hash),
			)
		);

		//$query = http_build_query($get_opt);
		$query='';
		$i=0;
		$q=sizeof($get_opt);
		foreach($get_opt as $k => $v)
		{
			$i+=1;
			$query.="{$k}={$v}".($i<$q ? '&' : '');
		}

		$timeout=$config['ppkbb_tcrannounces_options'][4] ? $config['ppkbb_tcrannounces_options'][4] : 5;
		$result=array();
		$error='';

		if(in_array($scheme, array('http')))
		{
			if(function_exists('file_get_contents'))
			{
				$opt = array('http' =>
					array(
						'method' => 'GET',
						'header' => "User-Agent: {$config['ppkbb_tcrannounces_options'][9]}\r\n".
								"Host: {$host}\r\n".
								"Connection: close\r\n",
						'timeout' => $timeout
					)
				);

				$context = @stream_context_create($opt);
				$url='http://'.$host.($port ? ':'.$port : '').$path.($query ? '?'.$query : '');
				$result = @file_get_contents($url, false, $context);
			}
			else if(function_exists('curl_version'))
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.$host.($port ? ':'.$port : '').$path.($query ? '?'.$query : ''));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_USERAGENT, $config['ppkbb_tcrannounces_options'][9]);
				$result = curl_exec($ch);

				curl_close ($ch);
			}
			else if(function_exists('fsockopen'))
			{
				$fp=@fsockopen($host, ($port ? $port : 80), $errno, $errstr, $timeout);
				if(!$fp)
				{
					$error=$errstr.($errno ? " ({$errno})" : '');
				}
				else
				{
					$request="GET {$path}".($query ? '?'.$query : '')." HTTP/1.1\r\n";
					$request.="Host: $host\r\n";
					$request.="User-Agent: {$config['ppkbb_tcrannounces_options'][9]}\r\n";
					$request.="Connection: close\r\n\r\n";
					fputs($fp, $request);
					//stream_set_timeout($fp, $timeout);
					$result='';
					while(!feof($fp))
					{
						$line=fgets($fp, 4096);
						if($line=="\r\n" && !isset($header))
						{
							$header=true;
						}
						if(isset($header))
						{
							$result.=$line;
						}
					}
					fclose($fp);
					isset($header) ? $result=trim($result) : '';
				}
			}
		}
		else if($scheme=='udp')
		{
			include_once("{$phpbb_root_path}tracker/include/udptscraper.{$phpEx}");
			try
			{
				$scraper = new udptscraper($timeout);
				$info_hash2=bin2hex($info_hash);
				$url="udp://".$host.$port.$path.($query ? '?'.$query : '');

				$ret = $scraper->scrape($url, array($info_hash2));

				$result=array(
					'interval' => 0,
					'seeders' => my_int_val(@$ret[$info_hash2]['seeders'], 10000, true),
					'leechers' => my_int_val(@$ret[$info_hash2]['leechers'], 10000, true),
					'times_completed' => my_int_val(@$ret[$info_hash2]['completed'], 100000, true),
					'peers' => my_int_val(@$ret[$info_hash2]['seeders']+@$ret[$info_hash2]['leechers'], 20000, true),
					's_message'=> '',
				);

				return $result;
			}
			catch(ScraperException $e)
			{
				return array('s_message' => 'UDP scrape error: '.$e->getMessage().($e->isConnectionError() ? ' (connection error)' : ''));
			}
		}

		if(!$result)
		{
			return array('s_message' => ($result===false ? 'Query timeout' : ($error ? $error : 'Empty result')));
		}

		include_once($phpbb_root_path.'tracker/include/bencoding.'.$phpEx);
		$result = bdecode($result);

		if(!is_array($result))
		{
			return array('s_message' => 'BDecoding error');
		}

		if(!@$result['failure reason'] && !isset($result['files'][$info_hash]['complete']) && !isset($result['files'][$info_hash]['incomplete']) && !isset($result['files'][$info_hash]['downloaded']))
		{
			return array('s_message' => 'Empty result');
		}

		$return=array(
			'interval' => my_int_val(@$result['flags']['min_request_interval'], 10000, true),
			'seeders' => my_int_val(@$result['files'][$info_hash]['complete'], 10000, true),
			'leechers' => my_int_val(@$result['files'][$info_hash]['incomplete'], 10000, true),
			'times_completed' => my_int_val(@$result['files'][$info_hash]['downloaded'], 100000, true),
			'peers' => my_int_val(@$result['files'][$info_hash]['complete']+@$result['files'][$info_hash]['incomplete'], 20000, true),
			's_message'=> @$result['failure reason'] ? my_utf8_convert_message($result['failure reason']) : '',
		);

		return $return;
	}
}

function my_utf8_convert_message($message)
{
	if (!preg_match('/[\x80-\xFF]/', $message))
	{
		return htmlspecialchars($message, ENT_COMPAT, 'UTF-8');
	}

	return htmlspecialchars($message);
}

?>
