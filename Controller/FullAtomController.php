<?php

namespace Jrk\Admin\AtomBundle\Controller;


class FullAtomController extends BasicAtomController
{

    public function getParameter($str){
        return $this->container->getParameter($str);
    }

    protected function trans($path, $array = null, $file = 'G361MupBackBundle') {
        if ($array == null)
            $array = array();
        return $this->get('translator')->trans($path, $array, $file);
    }

    protected function getEntityManager() {
        return $this->getDoctrine()->getManager();
    }

    protected function getSecuredUser() {
        return $this->container->get('security.context')->getToken()->getUser();
    }

    protected function getSecurity() {
        return $this->get('security.context');
    }

    protected function isGranted($state) {
        return $this->get('security.context')->isGranted($state);
    }

    protected function getRepository($class, $path) {
        return $this->getDoctrine()->getManager()->getRepository($path . ":" . $class);
    }

    protected function addFlash($type, $text, $clear = false) {
        if ($clear) {
            $this->get('session')->getFlashBag()->clear();
        }

        $this->get('session')->getFlashBag()->add($type, $text);
    }

    protected function startsWith($hay, $needle) {
        return substr($hay, 0, strlen($needle."")) === $needle."";
    }

    protected function endsWith($hay, $needle) {
        return substr($hay, -strlen($needle."")) === $needle."";
    }


    protected function guessExtension($file) {
        $extension = $file->guessExtension();

        if (!$extension) {
            $extension = 'bin';
        }

        return $extension;
    }


    public function getSession() {
        return $this->container->get('session');
    }

    public function getEntitiesByIdentifiers($identifiers,$repository) {
        $queryBuilder = $repository->createQueryBuilder('e');
        $queryBuilder->where($queryBuilder->expr()->in('e.id',$identifiers));
        return $queryBuilder->getQuery()->getResult();
    }

}
