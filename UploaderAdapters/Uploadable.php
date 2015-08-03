<?php

namespace Jrk\Admin\AtomBundle\UploaderAdapters;


trait Uploadable
{


    public function getUploadDirectoryPath($create = false) {
        $dir = 'uploads/'.basename(str_replace('\\','/',strtolower(__CLASS__))).'-'.substr(md5(get_class($this)),0,6).'/'.implode('/',str_split($this->getId()));

        if (!is_dir($dir) && $create) {
            mkdir($dir,0777,true);
        }

        return $dir;
    }

    public function getResourceDirectoryPath() {
        return 'resources/'.strtolower(__CLASS__).'-'.substr(md5(get_class($this)),0,6);
    }

    public function getPicture($defaultResource = true)
    {
        $dir = $this->getUploadDirectoryPath();
        $files = array();

        if(is_dir($dir)){
            $files = scandir($dir);

            foreach($files as $imgNameKey => $imgName){
                if($imgName !== '.' && $imgName !== '..'){
                    return $dir.'/'.$imgName;
                }
            }
        }
		
		if (!$defaultResource) {
			return null;
		}

        return $this->getResourceDirectoryPath().'/picture.jpg';
    }
}
