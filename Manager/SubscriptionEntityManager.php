<?php

namespace Jrk\Admin\AtomBundle\Manager;

use Jrk\Admin\AtomBundle\Helper\Constant;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SubscriptionEntityManager
{

    private $metas;
    private $currentUrl;
    private $entities;


    public function __construct() {
        $this->entities = array();
    }

    public function initSubscriber($metas, $currentUrl) {
        $this->metas = $metas;
        $this->currentUrl = $currentUrl;
    }
    
    public function getNameByEntityPath($entityPath) {
        $parts = explode(':',$entityPath);

        if (count($parts) != 2) {
            throw new \InvalidArgumentException(sprintf("Entity path '%s' isn't a valid entity", $entityPath));
        }

        return $parts[1];
    }

    public function addEntity($entityPath, $mappedBy, $name = null) {
        if ($name == null) {
            $name = $this->getNameByEntityPath($entityPath);
        }

        $this->entities[] = array(
            'metas' => $this->metas,
            'url' => $this->currentUrl,
            'targetEntity' => $entityPath,
            'mappedBy' => $mappedBy,
            'label' => $name
        );
    }

    public function getEntities() {
        return $this->entities;
    }
}
