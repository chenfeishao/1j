<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'AFile.php';
class SaeFile extends AFile
{
    /**
     * 存储已经解析过的路径
     * @var array
     */
    private static $_path = array();
    
    private $storage;
	
    public function init()
    {
      parent::init();
      $this->storage = new SaeStorage();
    }
    
    /**
     * 创建多级目录
     * @param string $dir
     * @param int $mode
     * @return boolean
     */
    public function mkdirs($dir, $mode = 0777)
    {
      $dir = rtrim($dir, '/');
      list($domain, $path) = $this->explodePath2SAE($dir);
      $folderName = basename($dir);
      $r = $this->storage->write($domain, $path.'/'.$folderName, ' ');
      return ($r == false) ? false : true;
    }
	
    /**
     * 删除目录
     * @param string $dir
     * @param bool $rec
     * @return bool
     */
    public function deleteFolder($dir, $rec = false)
    {
      $ret = $this->getListByPath($dir);
      list($domain, $path) = $this->explodePath2SAE($dir);
      if (array_key_exists('files', $ret))
      {
        $files = $ret['files'];
        foreach ($files as $file)
        {
          $this->deleteFile($domain.DIRECTORY_SEPARATOR.$file['fullName']);
        }
        unset($files);
      }
      if (array_key_exists('dirs', $ret)) 
      {
        $directories = $ret['dirs'];
        unset($ret);
        foreach ($directories as $directory) 
        {
          $this->deleteFolder($domain.DIRECTORY_SEPARATOR.$directory['fullName']);
        }
        unset($directories);
      }
      
      return ($this->errno() == 0) ? true : false;
    }
    
    /**
     * 删除文件
     * @param string $filename 文件名
     * @return bool
     */
    public function deleteFile($filename)
    {
      list($domain, $path) = $this->explodePath2SAE($filename);
      return $this->storage->delete($domain, $path);
    }
	
    /**
     * 检查文件是否存在
     * @param string $filename
     * @return bool
     */
    public function fileExists($filename)
    {
      return $this->isFile($filename) || $this->isDir($filename);
    }

    /**
     * 判断是否是目录
     * @param string $path
     * @return boolean
     */
    public function isDir($filename)
    {
      $filename = rtrim($filename, '/');
      $pathinfo = pathinfo($filename);
      if(isset($pathinfo['extension']))
        return false;
      
      list($domain, $path) = $this->explodePath2SAE($filename.'/'.$pathinfo['basename']);
      return $this->storage->fileExists($domain, $path);
    }

    /**
     * 判断是否是文件
     * @param string $path
     * @return boolean
     */
    public function isFile($filename)
    {
      list($domain, $path) = $this->explodePath2SAE($filename);
      return $this->storage->fileExists($domain, $path);
    }

    public function fileSize($filename)
    {
      list($domain, $path) = $this->explodePath2SAE($filename);
      $attr = $this->storage->getAttr($domain, $path);
      
      /**
       * $attr结构
       * array(
       *  'fileName' => 'image/1.jpg', //文件名
       *  'datetime' => 1368008932, //时间戳
       *  'length' => 76541, //文件大小 单位:字节
       *  'md5sum' => '93d80cf11b3e4e57ad7e0a09e7ec8a74'
       * )
       */
      if($this->errno() != 0)
        return false;
      return $attr['length'];
    }
    
    public function fileMTime($filename)
    {
      list($domain, $path) = $this->explodePath2SAE($filename);
      $attr = $this->storage->getAttr($domain, $path);
      
      if($this->errno() != 0)
        return false;
      return $attr['datetime'];
    }
	
    /**
     * 获取文件的URL地址
     * @param string $filename
     * @return string | bool
     */
    public function getFileUrl($filename)
    {
      list($domain, $path) = $this->explodePath2SAE($filename);
      $url = $this->storage->getUrl($domain, $path);
      
      //if($this->errno() != 0)
        //return false;
      return str_replace('\\', '/', $url);
    }
    
    /**
     * 读取文件内容
     * @param string $filename
     * @return mix
     */
    public function read($filename)
    {
      list($domain, $path) = $this->explodePath2SAE($filename);
      $content = $this->storage->read($domain, $path);
      
      if($this->errno() != 0)
        return false;
      return $content;
    }
    
    /**
     * 向文件写入数据
     * @param type $filename
     * @param type $data
     * @return string
     */
    public function write($filename, $data)
    {
      list($domain, $path) = $this->explodePath2SAE($filename);
      $r = $this->storage->write($domain, $path, $data);
      return ($r != false) ? true : false;
    }
    
    /**
     * 上传文件
     * @param type $filename
     * @param type $destination
     * @return bool
     */
    public function upload($filename, $destination)
    {
      list($domain, $path) = $this->explodePath2SAE($destination);
      $r = $this->storage->upload($domain, $path, $filename);
      
      return ($r != false) ? true : false;
    }
    
    /**
     * 重命名一个文件或目录
     * @param string $oldname
     * @param string $newname
     * @return bool
     */
    public function rename($oldname, $newname)
    {
        return $this->copy($oldname, $newname, true);
    }
    
    /**
     * 复制文件或目录
     * @param string $source
     * @param string $dest
     * @param boolean $deleteOld
     * @return boolean
     */
    public function copy($source, $dest, $deleteOld=false)
    {
      list($sourceDomain, $sourcePath) = $this->explodePath2SAE($source);
      list($destDomain, $destPath) = $this->explodePath2SAE($dest);
      
      if($this->isFile($source) && !$this->storage->fileExists($sourceDomain, $sourcePath))
        return false;
      
      if($this->isDir($source) && $this->fileExists($source))
      {
        $ret = $this->getListByPath($source);
        foreach($ret['files'] as $file)
        {
           if($this->storage->fileExists($sourceDomain, $file['fullName']))
           {
              $this->copy($sourceDomain.DIRECTORY_SEPARATOR.$file['fullName'], $destDomain.DIRECTORY_SEPARATOR.$destPath.DIRECTORY_SEPARATOR.$file['Name'], $deleteOld);
           }
        }
        //将原目录中和目录名一样的文件，改成和目标目录一样的名称
        $s = $destDomain.DIRECTORY_SEPARATOR.$destPath.DIRECTORY_SEPARATOR.basename($sourcePath);
        $d = $destDomain.DIRECTORY_SEPARATOR.$destPath.DIRECTORY_SEPARATOR.basename($destPath);
        $this->copy($s, $d, true);
        if($deleteOld)
          $this->deleteFolder($source);
      }
      
      $content = $this->storage->read($sourceDomain, $sourcePath);
      $this->storage->write($destDomain, $destPath, $content);
      if($deleteOld)
        $this->deleteFile($source);
      unset($content);
      
      return true;
    }

    /**
     * 移动文件或文件夹
     * @param string $source
     * @param string $dest
     */
    public function move($source, $dest)
    {
      $this->copy($source, $dest, true);
    }

    public function glob($pattern, $flags = 1)
    {
      $result = null;
      switch($flags)
      {
        //仅返回与模式匹配的目录项
        case GLOB_ONLYDIR:
          $result = $this->getDirectory($pattern);
          break;
        default:
          $result = $this->getFiles($pattern);
      }
      
      return $result;
    }
    
    private function getDirectory($pattern)
    {
      $pattern = rtrim($pattern, '*');
      $attr = $this->getListByPath($pattern);
      list($domain, $path) = $this->explodePath2SAE($pattern);
      $result = null;
      foreach($attr['dirs'] as $dir)
      {
        $result[] = $domain.'/'.$dir['fullName'];
      }
      
      return $result;
    }
    
    private function getFiles($pattern)
    {
      list($domain, $path) = $this->explodePath2SAE($pattern);
      
      $result = null;
      $num = 0;
      //$ret = $this->storage->getList($domain, '*'/*basename($path)*/, 100, $num);
      $path = rtrim($path, '*');
      while ($ret = $this->storage->getList($domain, $path, 100, $num))
      {
         foreach($ret as $file)
         {
             $num ++;
             $pathinfo = pathinfo($file);
             if(!isset($pathinfo['extension'])) continue;
             if(strpos(str_replace($path,'',$file), '/') !== false) continue;
             
             $result[] = $domain.'/'.$file;
             
         }
     }
      
      return $result;
    }
	
    public function explodePath2SAE($path)
    {
      $md5Path = md5($path);
      if(key_exists($md5Path, self::$_path))
         return self::$_path[$md5Path];
      
      $path = $this->normalizePath($path);
      $domainPath = $this->getDomainPath($path);
      $firstDSpos = strpos($domainPath, DIRECTORY_SEPARATOR);
      $result = array();
      // domain
      $result[] = substr($domainPath, 0, $firstDSpos);
      // path
      $result[] = substr($domainPath, $firstDSpos + 1);
      
      self::$_path[$md5Path] = $result;
      return $result;
    }

    /**
     * 获取目录中文件列表
     * @param string $path
     * @param int $limit
     * @param int $offset
     * @param bool $fold
     * @return array
     */
    public function getListByPath($path, $limit=1000, $offset=0, $fold=true)
    {
      list($domain, $path) = $this->explodePath2SAE($path);
      return $this->storage->getListByPath($domain, $path, $limit, $offset, $fold);
    }
    
    public function errmsg()
    {
      return $this->storage->errmsg();
    }
    
    public function errno()
    {
      return $this->storage->errno();
    }
    
    private function getDomainPath($path)
    {
      $replacePath = $path;
      if($this->isAbsolutePath($path))
      {
        $replacePath = $this->getRelativePath($path);
      }
      return trim($replacePath, DIRECTORY_SEPARATOR);
    }
}
?>