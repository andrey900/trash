<?php
//StorageSingleton
class CacheEngineMemcache
{
	protected static $_instance;

	private static $obMemcache = null;
	private static $isConnected = false;

	private static $baseDirVersion = array();
	private static $sid = '';
	private static $index = Array();

	//cache stats
	private $written = false;
	private $read = false;
	// unfortunately is not available for memcache...

	private function __construct()
	{
	}


    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;

		self::$obMemcache = new Memcache;
		$port = BX_MEMCACHE_PORT;
		if (self::$obMemcache->connect(BX_MEMCACHE_HOST, $port))
		{
			self::$isConnected = true;
			register_shutdown_function(array("CacheEngineMemcache", "close"));
		}
		self::$sid = '';
		
        }
        return self::$_instance;
    }


	function getMemCacheObj() {
		return self::$obMemcache;
	}

	function stat() {
	}
    
	function read($key)
	{
	
		return self::$obMemcache->get(self::$sid.'|'.$key);
	}

	function write($key, $arAllVars )
	{

		return self::$obMemcache->set(self::$sid.'|'.$key, $arAllVars, 0, 0);
	}

	function increment($key, $count = 1)
	{

		if (!self::$obMemcache->increment(self::$sid.'|'.$key,$count) ) {
			return self::write($key,1);
		}
		return true;
	}

	function replace($key, $val)
	{

		return self::$obMemcache->replace(self::$sid.'|'.$key,$val, 0, 0);
	}




	function isCacheExpired($path)
	{
		return false;
	}

    private function __clone() {
    }

    private function __wakeup() {
    }



	function close()
	{
		if(self::$obMemcache != null)
		{
			self::$obMemcache->close();
			self::$obMemcache = null;
		}
	}

	function isAvailable()
	{
		return self::$isConnected;
	}

	function clean($baseDir, $initDir = false, $filename = false)
	{
		if(is_object(self::$obMemcache))
		{
			if(strlen($filename))
			{
				if(!isset(self::$baseDirVersion[$baseDir]))
					self::$baseDirVersion[$baseDir] = self::$obMemcache->get(self::$sid.$baseDir);

				if(self::$baseDirVersion[$baseDir] === false || self::$baseDirVersion[$baseDir] === '')
					return true;

				if($initDir !== false)
				{
					$initDirVersion = self::$obMemcache->get(self::$baseDirVersion[$baseDir]."|".$initDir);
					if($initDirVersion === false || $initDirVersion === '')
						return true;
				}
				else
				{
					$initDirVersion = "";
				}

				self::$obMemcache->replace(self::$baseDirVersion[$baseDir]."|".$initDirVersion."|/".$filename, "", 0, 1);
			}
			else
			{
				if(strlen($initDir))
				{
					if(!isset(self::$baseDirVersion[$baseDir]))
						self::$baseDirVersion[$baseDir] = self::$obMemcache->get(self::$sid.$baseDir);

					if(self::$baseDirVersion[$baseDir] === false || self::$baseDirVersion[$baseDir] === '')
						return true;

					self::$obMemcache->replace(self::$baseDirVersion[$baseDir]."|".$initDir, "", 0, 1);
				}
				else
				{
					if(isset(self::$baseDirVersion[$baseDir]))
						unset(self::$baseDirVersion[$baseDir]);

					self::$obMemcache->replace(self::$sid.$baseDir, "", 0, 1);
				}
			}
			return true;
		}

		return false;
	}


}
