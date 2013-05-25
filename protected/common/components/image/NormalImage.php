<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'Image.php';
class NormalImage extends Image
{
	public function saveImage($savePath, $imageQuality="100")
	{
		parent::saveImage($savePath, $imageQuality);
		
		return $this->getFileUrl($savePath);
	}
}

?>