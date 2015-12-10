<?php
namespace Extensions\UpLoad;

use Illuminate\Contracts\Filesystem\Factory as FactoryContract;
use Extensions\UpLoad\FastDFS as fastdfs;
use League\Flysystem\Config;
use League\Flysystem\Adapter\AbstractAdapter;
/**
 * 提供FastDFS下的文件上传和删除功能
 * 
 * 
 * @copyright  Copyright (c) 2014-2018 jkpt
 * @author limingyao 10301139@qq.com
 * @version 0.1    2014-10-27
 *
 */
class FastDfsAdapter extends  AbstractAdapter{

    protected   $group;
    protected   $dfs;
    protected   $url;

    public  function __construct()
    {
        $this->dfs=new fastdfs();
        $this->group='group1';
        $this->url='';
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function put($path,$ext=null)
    {
        return self::write($path,$ext,new Config());
    }


    /**
     * Write a new file.
     *
     * @param string $path 含扩展名
     * @param string $ext 原始文件扩展名
     * @param Config $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $ext, Config $config){
        $this->url='';
        $tracker = $this->dfs->tracker_get_connection();
        $location = "";
        $sizes=array();

        if($this->dfs->active_test($tracker)){
            $storaged = $this->dfs->tracker_query_storage_store($this->group,$tracker);

            if(!empty($sizes)){
                $count = 0;
                $filename = $this->dfs->storage_upload_by_filename($path);
                if(isset($filename['group_name'])&&isset($filename['filename'])){
                    $location =$filename['group_name']."/".$filename['filename'];
                }
                foreach($sizes as $key=>$val){
                    $snapshot_file_info =$this->dfs->storage_upload_slave_by_filename($val,$this->group,$filename['filename'],$key);
                    if($snapshot_file_info){
                        $count++;
                    }
                }
                if($count <> count($sizes)){
                    $location = "";
                }
            }else{
                $filename = $this->dfs->storage_upload_by_filename($path,$ext);
                if(isset($filename['group_name'])&&isset($filename['filename'])){
                    $location =$filename['group_name']."/".$filename['filename'];
                }
            }
        }

        $this->url=$location;
        return $location;

    }

    /**
     * Delete a file.
     *
     * @param string $path 文件路径；"M00/00/01/wKgB_FRHVAGAZaRhAAAJNyOUaAY429.png"
     *
     * @return bool
     */
    public function delete($path){

        $tracker = $this->dfs->tracker_get_connection();

        if($this->dfs->active_test($tracker)){
            $storaged = $this->dfs->tracker_query_storage_store("group1",$tracker);
            return $this->dfs->storage_delete_file($this->group, $path, $tracker, $storaged);
        }
        else
        {
            throw new Exception("服务器连接失败");
        }

        return false;
    }

    /**
     * Write a new file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config){}

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents, Config $config){}

    /**
     * Update a file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config   Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource, Config $config){}

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath){}

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath){}



    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname){}

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config){}

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility){}

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path){}

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path){}

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path){}

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false){}

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path){}

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path){}

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path){}

    /**
     * Get the timestamp of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path){}

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path){}

}