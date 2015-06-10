<?php

namespace Jrk\Admin\AtomBundle\Manager;

use Jrk\Admin\AtomBundle\Helper\Constant;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HistoryManager
{

    private static $sessionTimeout = 620;
    private $session;


    public function __construct($session) {
        $this->session = $session;
        $this->initHistory();
    }

    public function getSession() {
        return $this->session;
    }



    public function initHistory() {
        if (!$this->getSession()->has(Constant::getAtomSessionKey())) {
            $this->getSession()->set(Constant::getAtomSessionKey(), array());
        }
    }

    public function deleteHistory() {
        if ($this->getSession()->has(Constant::getAtomSessionKey())) {
            $this->getSession()->set(Constant::getAtomSessionKey(), array());
        }
    }




    public function updateHistory($newHistory, $service = null) {

        if ($service == null) {
            return $this->getSession()->set(Constant::getAtomSessionKey(), $newHistory);
        }

        $history = $this->getSession()->get(Constant::getAtomSessionKey());

        if (!isset($history[$service])) {
            $history[$service] = array();
        }

        $history[$service] = array_merge($history[$service],$newHistory);
        return $this->getSession()->set(Constant::getAtomSessionKey(), $history);
    }


    public function getHistory($service = null) {
        $session = $this->getSession();
        $history = $session->get(Constant::getAtomSessionKey());

        if ($service == null) {
            return $history;
        }

        if (!isset($history[$service])) {
            $history[$service] = array();
        }

        return $history[$service];
    }



    public function clearHistory($renderCall, $metas) {
        $deepEntitiesHistory = $this->getHistory();
        $atomSession = $this->getTimestampedHistory();
        $now = time();

        // If a "global list" page is visited, flush history
        if ($renderCall && !isset($deepEntitiesHistory['entities'])) {
            $deepEntitiesHistory['entities'][$atomSession]['root'][$metas['_uid']] = array();
            $deepEntitiesHistory['entities'][$atomSession]['mapping'][$metas['_uid']] = array();
        }


        if (isset($deepEntitiesHistory['entities'])) {
            foreach($deepEntitiesHistory['entities'] as $timestamp => $deepEntities) {
                $realTimestamp = substr($timestamp,0,-4);
                if ( ((int) $now - (int) $realTimestamp) > self::$sessionTimeout) {
                    unset($deepEntitiesHistory['entities'][$timestamp]);
                    unset($deepEntitiesHistory['base'][$realTimestamp]);
                }
            }
        }

        $this->updateHistory($deepEntitiesHistory);
    }


    public function getTimestampedHistory() {
        return isset($_GET['_atom']) ? $_GET['_atom'] : '';
    }

}
