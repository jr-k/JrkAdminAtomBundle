<?php

namespace Jrk\Admin\AtomBundle\Helper;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MetaGuesser
{


    public static function guess($class, $namespace, $meta = null) {
        $path = explode("\\",$namespace);
        $metadatas = self::buildMetadatas($class, $path);

        if ($meta != null) {
            if (!isset($metadatas[$meta])) {
                throw new \InvalidArgumentException(sprintf("Meta attribute '%s' for class '%s' doesn't exists", $meta, $class));    
            }
            
            return $metadatas[$meta];
        }

        return $metadatas;
    }


    public static function buildMetadatas($class, $path) {
        return array(
            "fullbundle" => self::guessFullBundle($path),
            "bundle" => self::guessBundleName($path),
            "name" => self::guessEntityName($class),
            "_uid" => self::guessUniquePath($class,$path)
        );
    }


    public static function guessFullBundle(array $path) {
        return $path[0].$path[1].$path[2];
    }
    
    public static function guessBundleName(array $path) {
        return $path[2];
    }

    public static function guessUniquePath($class, $path) {
        return self::guessFullBundle($path).":".self::guessEntityName($class);
    }
    
    public static function guessEntityName($class) {
        $fixedNamespace = str_replace("\\","/",$class);
        $className = basename($fixedNamespace);
        return preg_replace("#Controller$#","",$className);
    }

}
