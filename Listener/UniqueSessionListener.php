<?php

namespace Jrk\Admin\AtomBundle\Listener;


use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UniqueSessionListener
{

    private $request;

    public function __construct()
    {

    }


    public function updateRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $this->request = $event->getRequest();


        if ($this->isBackOffice()) {
//            $isFromRender =  $this->request->headers->has('x-forwarded-for');
//            $atomExists = isset($_GET['_atom']);
//
//            if (!$isFromRender && !$atomExists) {
//                $atom = time();
//
//                $this->request->attributes->add(array(
//                    '_atom' => $atom
//                ));
//
//            } elseif ($atomExists) {
//                $atom = $_GET['_atom'];
//
//                $this->request->attributes->add(array(
//                    '_atom' => $atom
//                ));
//            }
        }
    }


    public function isBackOffice() {
        return preg_match('#^/back/#',$this->request->getPathInfo());

    }


}