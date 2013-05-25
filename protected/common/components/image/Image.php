<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'magicia'.DIRECTORY_SEPARATOR.'imageLib.php';
class Image extends imageLib
{
	public function getFileUrl($filename)
	{
        return $this->getFile()->getFileUrl($filename);
	}
    
    public function upload($src, $dest)
    {
      return $this->getFile()->upload($src, $dest);
    }
    
    public function getFile()
    {
      static $instance = null;
      if(empty($instance))
        $instance = Yii::app()->getModule('filemanager')->getFile();
      return $instance;
    }
}