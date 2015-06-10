<?php

namespace Jrk\Admin\AtomBundle\Helper;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class RouteGuesser
{



    public static function guess($actions = array('list','edit','delete'), $class, $namespace) {
        $routes = array();

        if (!is_array($actions)) {
            return self::guessRoute($actions, $class, $namespace);
        }

        foreach($actions as $action) {
            $routes[$action] = self::guessRoute($action, $class, $namespace);
        }


        return $routes;
    }

    public static function guessRoute($action = 'list', $class,  $namespace) {
        $fixedNamespace = str_replace("\\","/",$class);
        $className = basename($fixedNamespace);
        $entityName = strtolower(preg_replace("#Controller$#","",$className));
        $routePrefix = self::guessPrefix($namespace);
        return str_replace('#!action!#', $entityName.'_'.$action, $routePrefix);
    }

    public static function guessPrefix($namespace) {
        $path = explode('\\',$namespace);
        $bundleNameSnaked = strtolower(self::camelToSnake($path[2]));
        $vendorName = strtolower($path[0]);
        $projectName = strtolower($path[1]);
        $prefix = $vendorName.'_'.$projectName.'_back_'.$bundleNameSnaked;
        return preg_replace('#bundle$#','#!action!#',$prefix);
    }


    public static function camelToSnake($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];

        foreach ($ret as &$match) {
            $match = strtolower($match);
        }

        return implode('_', $ret);
    }



}
