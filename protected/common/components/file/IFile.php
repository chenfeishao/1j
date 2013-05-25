<?php
/**
 * IFile 接口文件
 * 在此接口下的所有操作都是针对上传文件目录的操作
 * 如果在应用程序中需要用到文件操作请参考components
 * 中sae中的文件
 *
 * @author Feishao Chen <290520353@qq.com>
 * @copyright Copyright &copye; 2013 Alexander
 * @package filemanager
 * @since 1.0
 */

/**
 * IFile接口，实现跨平台(一般主机，新浪SAE)的底层文件操作接口
 *
 * @author Feishao Chen <290520353@qq.com>
 * @package filemanager
 * @since 1.0
 */
interface IFile
{
	/**
	 * 递归创建目录
	 *
	 * @param string $dir 文件路径
	 * @param int $mode 权限设置
	 * @return bool 成功返回true,否则false
	 */
	public function mkdirs($dir, $mode);
	
	/**
	 * 删除目录
	 *
	 * @param string $dir 目录
	 * @param bool $rec 是否递归操作
	 * @return bool 成功时返回true,否则false
	 */
	public function deleteFolder($dir, $rec);

	/**
	 * 删除文件
	 *
	 * @param string $filename 文件路径
	 * @return bool 成功时返回true,否则false
	 */
	public function deleteFile($filename);
	
	/**
	 * 检查文件或目录是否存在
	 *
	 * @param string $filename 文件或目录
	 * @return bool 存在返回true,否则false
	 */
	public function fileExists($filename);

	/**
	 * 判断给定文件名是否是目录
	 *
	 * @param string $filename
	 * @return bool 文件名存在并且为目录则返回true,否则返回false
	 */
	public function isDir($filename);

	/**
	 * 判断给定文件名是否为一个正常的文件
	 *
	 * @param string $filename
	 * @return bool 如果文件存在且为正常的文件则返回true
	 */
	public function isFile($filename);

	/**
	 * 取得文件大小
	 *
	 * @param string $filename
	 * @return int 返回文件大小的字节数，出错返回false
	 */
	public function fileSize($filename);
    
    /**
     * 取得文件修改时间
     * 
     * @param string $filename
     * @return int | bool 成功返回Unix时间戳，否则返回false
     */
    public function fileMTime($filename);
	
	/**
	 * 返回文件可以通过网络查看的链接地址
	 *
	 * @param string $filename 文件路径
	 * @return string | bool 文件存在放回链接，否则返回false
	 */
	public function getFileUrl($filename);

	/**
	 * 将整个文件读入一个字符串
	 *
	 * @param string $filename 文件名
	 * @return string | bool 成功时返回数据,否则返回false
	 */
	public function read($filename);

	/**
	 * 将一个字符串写入文件
	 *
	 * @param string $filename 要被写入数据的文件名
	 * @param string $data 内容
	 * @return bool
	 */
	public function write($filename, $data);

	/**
	 * 上传文件
	 *
	 * @param string $filename 文件名
	 * @param string $destination 目的文件名
	 * @param bool
	 */
	public function upload($filename, $destination);

	/**
	 * 重命名一个文件或目录
	 *
	 * @param string $oldname 原文件名
	 * @param string $newname 目标文件名
	 * @return bool 成功时返回true,否则返回false
	 */
	public function rename($oldname, $newname);

	/**
	 * 复制文件或目录
	 *
	 * @param string $source
	 * @param string $dest
	 * @return bool 成功时返回true，否则返回false
	 */
	public function copy($source, $dest);

	/**
	 * 移动文件或目录
	 *
	 * @param string $source
	 * @param string $dest
	 * @return bool 成功时返回true,否则返回false
	 */
	public function move($source, $dest);

	/**
	 * 寻找与模式匹配的文件路径，具体详情请参考PHP glob函数
	 *
	 * @param string $pattern
	 * @param int $flags
	 * @return array
	 */
	public function glob($pattern, $flags);
    
    /**
     * 返回运行过程中的错误信息
     * 
     * @return string
     */
    public function errmsg();
}
