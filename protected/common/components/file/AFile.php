<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'IFile.php';
abstract class AFile extends CApplicationComponent implements IFile
{
  	/**
   	 * 格式化路径，将路径中的/和\统一成当前平台支持的分隔符
   	 *
   	 * @param string $path
   	 * @return string
   	 */
	public function normalizePath($path)
	{
		return str_replace(array("/", "\\"),DIRECTORY_SEPARATOR, $path);
	}

	/**
	 * 判断给定文件名是否是绝对路径
	 *
	 * @param string $path 文件名
	 * @return bool 是绝对路径返回true,否则返回false
	 */
	public function isAbsolutePath($path)
    {
        $yiiPath = Yii::app()->basePath.'/../';
        if(strpos($path, $yiiPath) !== false)
            return true;
        return false;
    }

    /**
     * 获取文件名的相对路径
     *
     * @param string $path 文件名
     * @return string 返回相对路径
     */
    public function getRelativePath($path)
    {
    	$yiiPath = Yii::app()->basePath.'/../';
        return str_replace($yiiPath, '', $path);
    }
}
