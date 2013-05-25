<?php

class SAEKVCache extends CCache
{
	private $_cache = null;
	
	public function init()
	{
		parent::init();
		$cache = $this->getKVCache();
	}
	
	public function getKVCache()
	{
		if($this->_cache!==null)
			return $this->_cache;
		else
		{
			$this->_cache = new SaeKV();
			$this->_cache->init();
			return $this->_cache;
		}
	}
	
	protected function getValue($key)
	{
		return $this->_cache->get($key);
	}
	
	protected function getValues($keys)
	{
		return $this->_cache->mget($keys);
	}
	
	protected function setValue($key, $value, $expire)
	{
		return $this->_cache->set($key, $value);
	}
	
	protected function addValue($key,$value,$expire)
	{
		return $this->_cache->add($key,$value);
	}
	
	protected function deleteValue($key)
	{
		return $this->_cache->delete($key);
	}
	
	protected function flushValues()
	{
		//return $this->_cache->flush();
	}
}
