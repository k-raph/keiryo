<?php

namespace Simplex\DataMapper\Mapping;

use Simplex\DataMapper\EntityManager;
use Simplex\DataMapper\Proxy\Proxy;
use Simplex\DataMapper\Proxy\ProxyFactory;
use Simplex\DataMapper\Relations\Loader;

class EntityMapper
{
    /**
     * @var EntityMetadata
     */
    protected $metadata;

    /**
     * @var ProxyFactory
     */
    protected $proxyFactory;

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityMetadata $metadata, EntityManager $manager)
    {
        $this->metadata = $metadata;
        $this->em = $manager;
        $this->proxyFactory = $manager->getProxyFactory();
    }

    /**
     * Create an entity based on given data
     *
     * @param array $data
     * @return object
     */
    public function createEntity(array $data): object
    {
        $props = $this->mapToProps($data);

        $proxy = $this->createProxy();
        $proxy->hydrate($props);

        return $proxy->reveal();
    }

    /**
     * Extracts fields data from given entity
     *
     * @param object $entity
     * @return array
     */
    public function extract(object $entity): array
    {
        $proxy = $this->proxyFactory->wrap($entity);
        $props = $proxy->toArray();
        $result = [];

        foreach ($this->getMappings() as $prop => $column) {
            $result[$column] = $props[$prop];
        }

        return $result;
    }

    /**
     * Hydrates an object with provided data
     *
     * @param object $entity
     * @param array $data
     * @return void
     */
    public function hydrate(object $entity, array $data)
    {
        $proxy = $this->proxyFactory
            ->wrap($entity);

        $proxy->hydrate($this->mapToProps($data));

        return $proxy;
    }

    /**
     * Get the value of a field from provided object
     *
     * @param object $entity
     * @param string $field
     * @return mixed
     */
    public function getField(object $entity, string $field)
    {
        return $this->proxyFactory
            ->wrap($entity)
            ->getField($field);
    }

    /**
     * Loads relations and add them to entity
     *
     * @param array $entities
     * @param array $relations
     * @return array
     */
    public function loadRelations(array $entities, array $relations): array
    {
        $loader = new Loader($this->em);

        foreach ($relations as $name) {
            $relation = $this->metadata->getRelation($name);
            $relation['name'] = $name;

            $relation = $loader->build($this->metadata->getEntityClass(), $relation);
            $loaded = $relation->load($this->em, $entities);
            $entities = $relation->assign($this->em, $name, $entities, $loaded);
        }

        return $entities;
    }

    /**
     * Get mapped class metadata
     *
     * @return EntityMetadata
     */
    public function getMetadata(): EntityMetadata
    {
        return $this->metadata;
    }

    /**
     * Get fields mappings
     *
     * @return array
     */
    protected function getMappings(): array
    {
        return array_combine(
            $this->metadata->getNames(),
            $this->metadata->getSQLNames()
        );
    }

    /**
     * Maps a data array to props array
     *
     * @param array $data
     * @return array
     */
    protected function mapToProps(array $data): array
    {
        $props = [];
        $fields = $this->getMappings();

        foreach ($fields as $prop => $column) {
            if (!isset($data[$column])) {
                continue;
            }
            $props[$prop] = $data[$column];
        }

        return $props;
    }

    /**
     * Creates a proxy instance
     *
     * @return Proxy
     */
    protected function createProxy(): Proxy
    {
        return $this->proxyFactory->create($this->metadata->getEntityClass());
    }
}
