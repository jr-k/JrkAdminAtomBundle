<?php

namespace Jrk\Admin\AtomBundle\Controller\Inside;

use Jrk\Admin\AtomBundle\Controller\FullAtomController;
use Jrk\Admin\AtomBundle\Helper\Constant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugController extends FullAtomController {

    public function historyAction(Request $request) {

        $historySessionKey = Constant::getAtomSessionKey();
        $session = $request->getSession();

        if ($session->has($historySessionKey)) {
            $history = $session->get($historySessionKey);
            echo "<hr><pre>";
            var_dump($history);die();
            $ctime = '14335095614738';
            var_dump($history['entities'][$ctime]['root']);
            echo "<hr>";
            echo "<hr>";
            echo "<hr>";
            var_dump($history['entities'][$ctime]['mapping']);
        }

        die('<hr>');
    }



}
