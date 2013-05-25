<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'Image.php';
class SaeImage extends Image
{
    /*
	public function setFile($fileName)
	{
		parent::setFile($this->getFileUrl($fileName));
	}*/
	
	public function saveImage($savePath, $imageQuality="100")
	{
		$pathinfo = pathinfo($savePath);
		$tmp = SAE_TMP_PATH.'/'.$pathinfo['basename'];
		parent::saveImage($tmp, $imageQuality);
		$this->upload($tmp, $savePath);
		@unlink($tmp);
		
		return $this->getFileUrl($savePath);
	}
}

?>