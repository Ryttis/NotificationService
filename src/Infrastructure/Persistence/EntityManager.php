<?php

namespace App\Infrastructure\Persistence;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Cache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;

class EntityManager implements EntityManagerInterface
{
    private $wrapped;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->wrapped = $entityManager;
    }

    public function getRepository($entityName): EntityRepository
    {
        return $this->wrapped->getRepository($entityName);
    }

    public function getCache(): ?Cache
    {
        return $this->wrapped->getCache();
    }

    public function getConnection(): Connection
    {
        return $this->wrapped->getConnection();
    }

    public function getExpressionBuilder(): Expr
    {
        return $this->wrapped->getExpressionBuilder();
    }

    public function beginTransaction(): void
    {
        $this->wrapped->beginTransaction();
    }

    public function transactional($func): void
    {
        $this->wrapped->transactional($func);
    }

    public function commit(): void
    {
        $this->wrapped->commit();
    }

    public function rollback(): void
    {
        $this->wrapped->rollback();
    }

    public function createQuery($dql = ''): Query
    {
        return $this->wrapped->createQuery($dql);
    }

    public function createNativeQuery($sql, ResultSetMapping $rsm): NativeQuery
    {
        return $this->wrapped->createNativeQuery($sql, $rsm);
    }

    public function createNamedNativeQuery($name): Query
    {
        return $this->wrapped->createNamedNativeQuery($name);
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->wrapped->createQueryBuilder();
    }

    public function find($className, $id, $lockMode = null, $lockVersion = null): ?object
    {
        return $this->wrapped->find($className, $id, $lockMode, $lockVersion);
    }

    public function flush($entity = null): void
    {
        $this->wrapped->flush($entity);
    }

    public function getReference($entityName, $id): ?object
    {
        return $this->wrapped->getReference($entityName, $id);
    }

    public function getPartialReference($entityName, $identifier)
    {
        return $this->wrapped->getPartialReference($entityName, $identifier);
    }

    public function clear($entityName = null): void
    {
        $this->wrapped->clear($entityName);
    }

    public function close(): void
    {
        $this->wrapped->close();
    }

    public function lock($entity, $lockMode, $lockVersion = null): void
    {
        $this->wrapped->lock($entity, $lockMode, $lockRegexData = null);
    }

    public function getEventManager(): EventManager
    {
        return $this->wrapped->getEventManager();
    }

    public function getConfiguration(): Configuration
    {
        return $this->wrapped->getConfiguration();
    }

    public function isOpen(): bool
    {
        return $this->wrapped->isOpen();
    }

    public function getUnitOfWork(): UnitOfWork
    {
        return $this->wrapped->getUnitOfWork();
    }

    public function getHydrator($hydrationMode)
    {
        return $this->wrapped->getHydrator($hydrationMode);
    }

    public function newHydrator($hydrationMode): AbstractHydrator
    {
        throw new \LogicException('Method not implemented.');
    }

    public function getProxyFactory(): ProxyFactory
    {
        return $this->wrapped->getProxyFactory();
    }

    public function getFilters(): FilterCollection
    {
        return $this->wrapped->getFilters();
    }

    public function isFiltersStateClean(): bool
    {
        return $this->wrapped->isFiltersStateClean();
    }

    public function hasFilters(): bool
    {
        return $this->wrapped->hasFilters();
    }

    public function persist($entity): void
    {
        $this->wrapped->persist($entity);
    }

    public function remove($entity): void
    {
        $this->wrapped->remove($entity);
    }

    public function detach($entity): void
    {
        $this->wrapped->detach($entity);
    }

    public function merge($entity)
    {
        return $this->wrapped->merge($entity);
    }

    public function copy($entity, $deep = false)
    {
        return $this->wrapped->copy($entity, $deep);
    }

    public function contains($entity): bool
    {
        return $this->wrapped->contains($entity);
    }

    public function getMetadataFactory(): ClassMetadataFactory
    {
        return $this->wrapped->getMetadataFactory();
    }

    public function initializeObject($obj): void
    {
        $this->wrapped->initializeObject($obj);
    }

    public function getClassMetadata($className): ClassMetadata
    {
        return $this->wrapped->getClassMetadata($className);
    }

    public function wrapInTransaction(callable $func): mixed
    {
        // TODO: Implement wrapInTransaction() method.

        return null;
    }

    public function refresh(object $object, int|LockMode|null $lockMode = null): void
    {
        // TODO: Implement refresh() method.
    }
}
