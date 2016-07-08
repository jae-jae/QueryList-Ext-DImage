<?php
namespace QL\Ext;

/**
 * @Author: Jaeger <hj.q@qq.com>
 * @Date:   2015-07-15 23:27:52
 * @Last Modified by:   Jaeger
 * @Last Modified time: 2016-07-08 17:40:28
 * @version         1.0
 * 图片下载扩展
 */

use phpQuery;

class DImage extends AQuery
{
    public function run(array $args)
    {
        $args = array_merge(array(
            'image_path' => '/images',
            'base_url' => ''
            ),$args);
        $doc = phpQuery::newDocumentHTML($args['content']);
        $http = $this->getInstance('QL\Ext\Lib\Http');
        $imgs = pq($doc)->find('img');
        foreach ($imgs as $img) {
            $src = $args['base_url'].pq($img)->attr('src');
            $localSrc = rtrim($args['image_path'],'/').'/'.$this->makeFileName($src);
            $savePath = rtrim($args['www_root'],'/').'/'.ltrim($localSrc,'/');
            $this->mkdirs(dirname($savePath));
            $stream = $http->get($src);
            file_put_contents($savePath,$stream);
            pq($img)->attr('src',$localSrc);
        }
        return $doc->htmlOuter();
    }

    function mkdirs($dir)
    {
        if(!is_dir($dir))
        {
            if(!$this->mkdirs(dirname($dir))){
                return false;
            }
            if(!mkdir($dir,0777)){
                return false;
            }
        }
        return true;
    }

    public function makeFileName($src)
    {
        return md5($src).'.'.pathinfo($src, PATHINFO_EXTENSION);
    }
}