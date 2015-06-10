<?php

namespace Jrk\Admin\AtomBundle\Helper;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class Constant
{

    public static $ATOM_SESSION_KEY = 'back_navigation_history';


    public static function getAtomSessionKey() {
        return self::$ATOM_SESSION_KEY;
    }

}
