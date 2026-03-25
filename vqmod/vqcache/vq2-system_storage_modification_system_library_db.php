<?php

			
			require_once(\VQMod::modCheck(DIR_SYSTEM.'library/cache/dbcache.php'));
			
			
class DB {
	private $adaptor;

	public function __construct($adaptor, $hostname, $username, $password, $database, $port = NULL) {
		$class = 'DB\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class($hostname, $username, $password, $database, $port);
		} else {
			throw new \Exception('Error: Could not load database adaptor ' . $adaptor . '!');
		}
	}

	public function query($sql, $params = array()) {
		
			
				if (Registry::getInstance()->get('config')->get((version_compare(VERSION, '3', '>=') ? 'module_' : '').'dbcache_status') && (stripos($_SERVER['REQUEST_URI'], '/admin') === false) && (stripos($_SERVER['REQUEST_URI'], 'api/') === false)) {
					return DbCache::processDbQuery($this, $sql);
				} else {
				 	return $this->defaultQuery($sql, $params);
				}
			}
			
           	public function defaultQuery($sql, $params = array())
           	{
				return $this->adaptor->query($sql, $params);
           
			
	}

	public function escape($value) {
		return $this->adaptor->escape($value);
	}

	public function countAffected() {
		return $this->adaptor->countAffected();
	}

	public function getLastId() {
		return $this->adaptor->getLastId();
	}
	
	public function connected() {
		return $this->adaptor->connected();
	}
}