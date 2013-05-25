<?php
class CacheThumbImageWidget extends CWidget
{
	public $height = null;
	public $width = null;
	/**
	 * 0/exact = 固定尺寸，图片会变形失真
	 * 1/portrait = 高不变，缩放宽
	 * 2/landscape = 宽不变，缩放高
	 * 3/auto = 自动缩放
	 * 4/crop = 缩放然后裁剪
	 */
	public $option = 'crop';
	public $sharpen = false;
	public $path = '';
	public $alt = '';
	public $class = '';
	
	public $cropImage = true;
	public $cropPos = '25x0';
	
	public $lazyLoad = false;
	
	private $baseurl = '';
	private $image = '';
	
	public function run()
	{
		$this->baseurl = Yii::app()->baseUrl;
		$this->setImage();
		$this->createThumb();
		$html = $this->buildHtml();
		$this->dumpHtml($html);
	}
	
	private function setImage()
	{
		$image = $this->path;
		if(!file_exists(Yii::app()->basePath.'/..'.$image) || empty($this->path))
		{
			$image = Yii::app()->baseUrl.($this->height > 200) ? '/img/nopic.gif' : '/img/nopic_s.gif';
			$this->path = '/img/nopic_s.gif';
			$this->image = $image;
		}
		$this->image = $this->baseurl.$image;
		
		return $this;
	}
	
	private function createThumb($image = '')
	{
		if(empty($image)) $image = Yii::app()->basePath.'/..'.$this->path;
		if(strpos($image, 'nopic_s.gif') !== false || strpos($image, 'nopic.gif') !== false)	return;
		
		$pathinfo = pathinfo($image);
		$folder = $pathinfo['dirname'].'/thumbs/';
		$savepath = $folder.md5($this->width.$this->height.$pathinfo['basename']).'.'.$pathinfo['extension'];
		$this->image = $this->baseurl.str_replace(Yii::app()->basePath.'/..', '', $savepath);
		
		if(file_exists($savepath))	return;
		if(!file_exists($folder))	FileHelper::mkdirs($folder);
		
		$config = array(
			'height' => ($this->cropImage) ? $this->height + 50 : $this->height,
			'width' => ($this->cropImage) ? $this->width + 50 : $this->width,
			'option' => $this->option,
			'sharpen' => $this->sharpen,
			'path' => $image,
			'savePath' => $savepath
		);
		$imageLib = new Image($config);
		$imageLib->executeResize();
		if($this->cropImage)
		{
			$imageLib->height = $this->height;
			$imageLib->width = $this->width;
			$imageLib->cropPos = $this->cropPos;
			$imageLib->path = $savepath;
			$imageLib->savepath = $savepath;
			$imageLib->executeCropImage();
		}
	}
	
	private function buildHtml()
	{
		if($this->lazyLoad)
		{
			$html = '<img data-original="'.$this->image.'" class="lazy '.$this->class.'" alt="'.$this->alt.'" width="'.$this->width.'" height="'.$this->height.'" />';
			$html .= '<noscript><img src="'.$this->image.'" class="'.$this->class.'" width="'.$this->width.'" heigh="'.$this->height.'"></noscript>';
			return $html;
		}
		return '<img src="'.$this->image.'" class="'.$this->class.'" alt="'.$this->alt.'" width="'.$this->width.'" height="'.$this->height.'" />';
	}
	
	private function dumpHtml($html)
	{
		echo $html;
	}
}