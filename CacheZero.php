<?php

/*
 * A simple caching class inspired by Cache_Lite (http://pear.php.net/manual/en/package.caching.cache-lite.php)
 *
 * @author: George Borisov <george at gir dot me dot uk>
 * @version: 20130102.02
 * @licence: LGPL (https://www.gnu.org/licenses/lgpl-3.0.txt)
 *
 */

class CacheZero {

	private $options = array(
		'cacheDir'	=> 'cache',	// directory where to put the cache files (string),
		'caching'	=> true,		// enable / disable caching (boolean),
		'fileLocking' => true,		// enable / disable fileLocking (boolean),
		'hashedDirectoryLevel' => 0,	// level of the hashed directory system (int),
		'lifeTime'	=> 3600,		// cache lifetime in seconds (int),
		'modeFile'	=> 0600,	// umask for cache file (int),
		'modeDir'	=> 0700,	// umask for group / hash directory (int),
		'protectID'	=> true, // enable / disable id protection in cache file name (boolean)			
		'protectGroup'	=> false, // enable / disable group protection in cache file name (boolean)			
		'verify' => true, // enable / disable verification of cached data (boolean)
		'verifyMethod' => 'crc32', // type of verification 'crc32', 'md5', 'strlen' (string)
	);

	private $id;
	private $group;

	function __construct($options)
	{
		if (is_array($options)) {
			foreach ($options as $k => $v) {
				if (isset($this->options[$k])) {
					$this->options[$k] = $v;
				}
			}
		}
	}

	/// PUBLIC METHODS ///

	function setOption($k, $v)
	{
		if (isset($this->options[$k])) {
			$this->options[$k] = $v;
		}
	}

	function get($id, $group = 'default', $expiredOK = false)
	{
		$data = false;
		if ($this->options['caching']) {
			$data = $this->read($id, $group);
		}
		return $data;
	}

	function save($data, $id, $group = 'default')
	{
		if ($this->options['caching']) {
			$this->write($data, $id, $group);
		}
	}

	function start($id, $group = 'default')
	{
		$this->id = $id;
		$this->group = $group;
		ob_start();
		ob_implicit_flush(false);
	}

	function finish()
	{
		$data = ob_get_contents();
		ob_end_clean();
		$this->save($data, $this->id, $this->group);
		echo $data;
	}

	/// PRIVATE METHODS ///

	private function getFilePath($id, $group)
	{
		$a = array();
		$a['id'] = 'cache_';
		$regex = '/[^\w\-\?\&\.=]/';
		if ($this->options['protectID']) {
			$a['id'] .= md5($id);
		} else {
			$a['id'] .= preg_replace($regex, '', $id);
		}
		$a['path'] = $_SERVER['DOCUMENT_ROOT'] .'/'. $this->options['cacheDir'];
		$a['prefix'] = array();
		if ($this->options['protectGroup']) {
			$a['prefix'][] = md5($group);
		} else {
			$a['prefix'][] = preg_replace($regex, '', $group);
		}
		if ($this->options['hashedDirectoryLevel']) {
			$hash = ($this->options['protectID']) ? $id : md5($id);
			for ($i=0; $i < $this->options['hashedDirectoryLevel']; ++$i) {
				$a['prefix'][] = substr($hash, 0, $i + 1);
			}
		}
		return $a;
	}

	private function createDir($path)
	{
		mkdir($path, $this->options['modeDir']);
		if (!file_exists($path) || !is_dir($path)) {
			exit("ERROR: Unable to create directory ($path)");
		}
	}

	private function read($id, $group)
	{
		$a = $this->getFilePath($id, $group);
		$path = $a['path'] .'/'. implode('/', $a['prefix']) .'/'. $a['id'];
		$result = false;
		if (file_exists($path)) {
			if ($this->options['lifeTime'] && filemtime($path) > (time() - $this->options['lifeTime'])) {
				$fh = fopen($path, 'rb');
				if ($fh) {
					if ($this->options['verify']) {
						$hash = trim(fgets($fh));
					}
					$data = '';
					while(!feof($fh)){ 
						$data .= fgets($fh);	// TODO: test with lines > 8192 (https://bugs.php.net/bug.php?id=30936)
					}
					fclose($fh);
					if ($this->options['verify']) {
						if ((string)$hash === (string)$this->hash($data)) {
							$result = $data;
						} else {
							unlink($path);
						}
					} else {
						$result = $data;
					}
				}
			}
		}
		return $result; 
	}

	private function write($data, $id, $group)
	{
		$a = $this->getFilePath($id, $group);
		$path = $a['path'];
		if (!$path || !file_exists($path)) {
			exit("ERROR: Cache directory missing ($path)");	// paranoia
		}
		while (count($a['prefix'])) {
			$path .= '/'. array_shift($a['prefix']);
			if (!file_exists($path)) {
				$this->createDir($path);
			}
		}
		$path = "$path/$a[id]";
		$fh = fopen($path, 'wb');
		if (!$fh) {
			exit("ERROR: Unable to open cache file ($path)");
		}
		if ($this->options['fileLocking']) {
			flock($fh, LOCK_EX);
		}
		if ($this->options['verify']) {
			fwrite($fh, $this->hash($data) ."\n");
		}
		fwrite($fh, $data);
		if ($this->options['fileLocking']) {
			flock($fh, LOCK_UN);
		}
		fclose($fh);
		chmod($path, $this->options['modeFile']);
	}

	private function hash($data)
	{
		$hash = FALSE;
		$method = $this->options['verifyMethod'];
		if ($method === 'crc32') {
			$hash = crc32($data);
		} elseif ($method === 'md5') {
			$hash = md5($data);
		} elseif ($method === 'strlen') {
			$hash = strlen($data);
		} else {
			exit("ERROR: Invalid verification method ($method)"); // paranoia
		}
		return $hash;
	}
}

?>
