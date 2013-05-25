<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'AFile.php';

class NormalFile extends AFile
{
	public function mkdirs($dir, $mode=0777)
	{
		if(!is_dir($dir))
	    {
	    	$ret = @mkdir($dir, $mode, true);
	    	if(!$ret)
	        throw new Exception($dir);
	    }
	    return true;
	}
	
	public function deleteFolder($dir, $rec = false)
	{
		$dir = realpath($dir);
		if ($dir == '' || $dir == '/' || (strlen($dir) == 3 && substr($dir, 1) == ':\\'))
	    {
	      // 禁止删除根目录
	      throw new Exception('禁止删除根目录：'.$dir);
	    }
	    // 遍历目录，删除所有文件和子目录
	    if(false !== ($dh = opendir($dir)))
	    {
	      while(false !== ($file = readdir($dh)))
	      {
	        if($file == '.' || $file == '..')
	        {
	          continue;
	        }
	
	        $path = $dir . DIRECTORY_SEPARATOR . $file;
	        if (is_dir($path))
	        {
	          self::rmdirs($path);
	        }
	        else
	        {
	          unlink($path);
	        }
	      }
	      closedir($dh);
	      if (!$rec && @rmdir($dir))
	      {
	        return;
	      }
	    }
	    else
	    {
	      throw new Exception('不能打开：'.$dir);
	    }
	}
	
	public function deleteFile($filename)
	{
		if(file_exists($filename))
  			@unlink($filename);
	}
    
    public function fileExists($filename)
	{
		return file_exists($filename);
	}

	public function isDir($filename)
	{
		return is_dir($filename);
	}

	public function isFile($filename)
	{
		return is_file($filename);
	}

	public function fileSize($filename)
	{
		return filesize($filename);
	}
	
	public function getFileUrl($filename)
	{
		$result = $filename;
		if($this->isAbsolutePath($filename))
		{
			$result = $this->getRelativePath($filename);
		}
		return Yii::app()->baseUrl.'/'.$result;
	}
    
    public function read($filename)
    {
      return file_get_contents($filename);
    }
    
    public function write($filename, $data)
    {
      return file_put_contents($filename, $data);
    }
    
    public function upload($filename, $destination)
    {
      return move_uploaded_file($filename, $destination);
    }
    
    public function rename($oldname, $newname)
    {
      return rename($oldname, $newname);
    }
    
    public function copy($source, $dest)
    {
      return copy($source, $dest);
    }
    
    public function move($source, $dest)
    {
      return rename($source, $dest);
    }

    public function glob($pattern, $flags)
    {
    	return glob($pattern, $flags);
    }
}