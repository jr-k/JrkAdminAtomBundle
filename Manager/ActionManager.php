<?php

namespace Jrk\Admin\AtomBundle\Manager;

use Jrk\Admin\AtomBundle\Helper\Constant;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ActionManager
{

    private $historyManager;
    private $entityManager;
    private $subscriptionEntityManager;

    public function __construct($historyManager, $subscriptionEntityManager, $entityManager) {
        $this->historyManager = $historyManager;
        $this->entityManager = $entityManager;
        $this->subscriptionEntityManager = $subscriptionEntityManager;
    }


    public function mapParentEntityForListAction($queryBuilder, $metas, $rootAlias = null) {
        $entity = null;
        $rootAlias = $rootAlias == null ? 'e' : $rootAlias;

        if (($deepEntitiesHistory = $this->historyManager->getHistory('entities')) !== null) {
            $atomSessionId = $this->historyManager->getTimestampedHistory();

            if (isset($deepEntitiesHistory[$atomSessionId])) {
                $timestampedHistory = $deepEntitiesHistory[$atomSessionId];
                $mapping = $timestampedHistory['mapping'];

                if (isset($mapping[$metas['_uid']]['mappedBy'])) {
                    if (null !== $mapping[$metas['_uid']]['entity']->getId()) {
                        $entity = $mapping[$metas['_uid']]['entity'];
                        $attr = $mapping[$metas['_uid']]['mappedBy'];
                        $alias = 'a_'.$attr;
                        $queryBuilder->leftJoin($rootAlias.'.'.$attr,$alias)->addSelect($alias);
                        $queryBuilder->andWhere($alias.' = :'.$alias)->setParameter($alias, $entity);
                        $queryBuilder->andWhere($queryBuilder->expr()->isNotNull($alias));
                    }
                }
            }
        }

        return $entity;
    }



    public function mapParentEntityForEditAction($entity, $metas, $children) {
        $parent = array('exists' => false);

        if (($deepEntitiesHistory = $this->historyManager->getHistory('entities')) !== null) {
            $atomSessionId = $this->historyManager->getTimestampedHistory();
            $timestampedHistory = &$deepEntitiesHistory[$atomSessionId];
            $mapping = &$timestampedHistory['mapping'];

            if (isset($mapping[$metas['_uid']]['entity'])) {
                $parent = $mapping[$metas['_uid']];
                $parentMeta = $parent['metas'];
                $parentEntity = $this->entityManager->getRepository($parentMeta['_uid'])->find($parent['entity']->getId());
                $parent['exists'] = true;
                $entity->{'set'.ucFirst($parent['mappedBy'])}($parentEntity);
            }

            foreach($children as $subentity) {
                $timestampedHistory['root'][$metas['_uid']] = $subentity;
                $timestampedHistory['root'][$metas['_uid']]['entity'] = $entity;
                $timestampedHistory['mapping'][$subentity['targetEntity']] = &$timestampedHistory['root'][$metas['_uid']];
            }

            $this->historyManager->updateHistory($deepEntitiesHistory,'entities');
        }

        return $parent;
    }



}
