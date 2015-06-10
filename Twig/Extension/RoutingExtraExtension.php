<?php

namespace Jrk\Admin\AtomBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManager;


class RoutingExtraExtension extends \Twig_Extension {

    private $container;
    private $router;
    private $request;
    private $entityManager;
    private $environment;

    /**
     * Construct RoutingExtraExtension
     *
     * @param ContainerInterface $container     Container
     * @param EntityManager      $entityManager EntityManager
     * @param \Twig_Environment  $environment   Twig
     */
    public function __construct(ContainerInterface $container, EntityManager $entityManager, \Twig_Environment $environment) {
        $this->container = $container;
        $this->router = $container->get('router');
        $this->entityManager = $entityManager;
        $this->environment = $environment;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions() {
        return array(
            'current_url' => new \Twig_Function_Method($this, 'getPathCurrent'),
            'current_route' => new \Twig_Function_Method($this, 'getRouteCurrent'),
            'path' => new \Twig_Function_Method($this, 'deepPath', array('is_safe_callback' => array($this, 'isUrlGenerationSafe'))),
        );
    }



    function deepPath($name, $parameters = array(), $relative = false) {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if (isset($_GET['_atom'])) {
            $parameters['_atom'] = $_GET['_atom'];
        } else {
            $time = time();
            $parameters['_atom'] = $time.rand(1000,9000);
            $session = $this->container->get('session');
            $historySessionKey = 'back_navigation_history';
            $deepEntitiesHistory = $session->get($historySessionKey);
            $deepEntitiesHistory['base'][$time] = $this->getPathCurrent();
            $session->set($historySessionKey, $deepEntitiesHistory);
        }
        return $this->container->get('router')->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    public function isUrlGenerationSafe(\Twig_Node $argsNode)
    {
        // support named arguments
        $paramsNode = $argsNode->hasNode('parameters') ? $argsNode->getNode('parameters') : (
        $argsNode->hasNode(1) ? $argsNode->getNode(1) : null
        );

        if (null === $paramsNode || $paramsNode instanceof \Twig_Node_Expression_Array && count($paramsNode) <= 2 &&
            (!$paramsNode->hasNode(1) || $paramsNode->getNode(1) instanceof \Twig_Node_Expression_Constant)
        ) {
            return array('html');
        }

        return array();
    }


    /**
     * Get current path
     *
     * @param array $parameters
     *
     * @return string
     */
    public function getPathCurrent($parameters = array()) {
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

            return $this->router->generate($name, $parameters, true);
        }

        return '';
    }

    /**
     * Get current path
     *
     * @param array $parameters
     *
     * @return string
     */
    public function getRouteCurrent($parameters = array()) {
        $this->request = $this->container->get('request');
        // Where is the new route ?
        $name = $this->request->attributes->get('_route');

        return $name;
    }



    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        return 'jrk_admin_atom.twig_routing_extra';
    }

}
