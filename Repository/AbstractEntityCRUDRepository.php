<?php

namespace WeProvide\TranslationBundle\Repository;

use Doctrine\ORM\EntityRepository;

abstract class AbstractEntityCRUDRepository extends EntityRepository
{
    /**
     * Truncate table
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function truncate()
    {
        $className     = $this->getClassName();
        $entityManager = $this->getEntityManager();
        $metadata      = $entityManager->getClassMetadata($className);
        $connection    = $entityManager->getConnection();
        $dbPlatform    = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $sql = $dbPlatform->getTruncateTableSql($metadata->getTableName());
        $connection->executeUpdate($sql);
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @param $entity
     * @return mixed
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addOne($entity)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        return $entity;
    }
}