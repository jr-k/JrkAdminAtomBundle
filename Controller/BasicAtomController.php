<?php

namespace Jrk\Admin\AtomBundle\Controller;

use Jrk\Admin\AtomBundle\Helper\RouteGuesser;
use Jrk\Admin\AtomBundle\Helper\MetaGuesser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BasicAtomController extends Controller
{
    private $oneToManyList = array();


    public function deleteHistory() {
        $this->get('jrk_admin_atom.history_manager')->deleteHistory();
    }

    public function mapFilterList($queryBuilder, $metas, $rootAlias = null) {

        if (isset($_GET['atom_delete'])) {
            $this->deleteHistory();
        }

        return $this->get('jrk_admin_atom.action_manager')->mapParentEntityForListAction($queryBuilder, $metas, $rootAlias);
    }


    public function renderList($view, $parameters, $metas = null, $response = null) {

        // Get the request
        $request = $this->container->get('request_stack')->getCurrentRequest();

        // Search for a forwarded request = twig render call
        $renderCall = $request->headers->has("x-forwarded-for");
        $this->container->get("jrk_admin_atom.history_manager")->clearHistory($renderCall, $metas);

        // Render view
        return $this->render($view, array_replace($parameters, array(
            "metas" => $metas,
            "renderCall" => $renderCall
        )), $response);
    }




    public function getSubscribedEntities() {
        return $this->container->get('jrk_admin_atom.subscription_entity_manager')->getEntities();
    }


    public function mapEdit($entity, $metas) {
        $this->oneToMany();
        return $this->get('jrk_admin_atom.action_manager')->mapParentEntityForEditAction($entity, $metas, $this->getSubscribedEntities());
    }


     public function redirect($url, $status = 302) {
        $atomSessionId = $this->get('jrk_admin_atom.history_manager')->getTimestampedHistory();

        if ($atomSessionId != "") {
            if (!preg_match("#_atom#",$url)) {
                $url = $url."?_atom=".$atomSessionId;
            }
        }

        return parent::redirect($url, $status);
    }




    public function buildMetas($class, $namespace, $meta) {
        return MetaGuesser::guess($class,$namespace,$meta);
    }
    public function buildRoutes($actions, $class,$namespace) {
        return RouteGuesser::guess($actions, $class,$namespace);
    }

    public function hasMeta($metas, $key, $default = false) {
        if (isset($metas[$key])) {
            return $metas[$key];
        }

        return $default;
    }

    
    /*
     * Build embeded list views for edit template
     */
    public function entities() {
        $children = $this->getSubscribedEntities();

        $output = array('entities' => array());

        foreach ($children as &$child) {
            $parts = explode(':',$child['targetEntity']);
            $fullbundle = $parts[0];
            $name = $parts[1];
            $child['view'] = $this->renderAtom($fullbundle.':Back/'.$name.':list');
            $output['entities'][] = $child;
        }

        return $output;
    }

    /*
     * Render embeded list views
     */
    public function renderAtom($uri) {
        $tha = $this->container->get('templating.helper.actions');
        $view = $tha->render($tha->controller($uri), array('renderCall' => true));
        $start_regexp = '<!--([\s]?)START_EMBED_LIST([\s]?)-->';
        $end_regexp = '<!--([\s]?)END_EMBED_LIST([\s]?)-->';
        $atomView = preg_replace("#(.+)$start_regexp(.+)$end_regexp(.+)#is","$4",$view);
        return $atomView;
    }


    
    

    public function getSubscriber($metas) {
        if ($metas == null && !is_array($metas)) {
            throw new \InvalidArgumentException(sprintf("You need to pass an array of 'metas' to subscriber"));
        }

        $subscriber = $this->container->get('jrk_admin_atom.subscription_entity_manager');
        $subscriber->initSubscriber($metas, $this->getCurrentUrl());

        return $subscriber;
    }



    public function getCurrentUrl($parameters = array()) {
        $this->request = $this->container->get('request');
        // Where is the new route ?
        $name = $this->request->attributes->get('_route');

        if ($name) {
            $baseParameters = $this->request->attributes->all();

            foreach ($baseParameters as $key => $value) {
                if (substr($key, 0, 1) == '_' && $key != '_locale') {
                    unset($baseParameters[$key]);
                }
            }
            $parameters = array_merge($this->request->query->all(), $baseParameters, $parameters);

            return $this->container->get('router')->generate($name, $parameters, true);
        }

        return '';
    }
    
}
