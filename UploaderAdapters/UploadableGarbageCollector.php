<?php

namespace Jrk\Admin\AtomBundle\UploaderAdapters;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class UploadableGarbageCollector implements EventSubscriber
{

    public $uploadableGetterMethodName = 'getUploadDirectoryPath';


    public function __construct()
    {

    }

    public function getSubscribedEvents()
    {
        return array(
            'preRemove',
        );
    }


    public function preRemove(LifecycleEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $classes = array();

        foreach ($metadatas as $metadata) {
            $class = $metadata->getName();
            $entity =  $args->getEntity();

            if (in_array($this->uploadableGetterMethodName,(get_class_methods($metadata->getName()))) && $entity instanceof $class) {
                $classes[] = $class;
                $this->deleteFiles($entity->{$this->uploadableGetterMethodName}());
            }
        }

    }

    public function forceRemove($entity) {
        return $this->deleteFiles($entity->getUploadDirectoryPath());
    }


    public function deleteFiles($dir)
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? self::deleteTree("$dir/$file") : unlink("$dir/$file");
            }
            rmdir($dir);
        }

    }

    public static function deleteTree($dir)
    {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? self::deleteTree("$dir/$file") : unlink("$dir/$file");
            }

            return rmdir($dir);
        }
        return null;
    }


}