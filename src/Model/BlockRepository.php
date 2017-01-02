<?php
namespace Block\Model;

use DateTime;
use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Collection\Collection;
use Versioning\Model\VersionableInterface;

class BlockRepository
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return Block
     */
    public function getById($id)
    {
        return $this->entityManager->find(Block::class, $id);
    }

    /**
     * @param string $name
     * @return Block
     */
    public function getByName($name)
    {
        $conditions = $this->entityManager->createConditions();
        $conditions->field('name')->eq($name);

        return $this->entityManager->findOne(Block::class, $conditions);
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        return $this->entityManager->findAll(Block::class);
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @param string $orderBy
     * @param string $dir
     * @return Collection
     */
    public function getAllPublished($offset = null, $showPerPage = null, $orderBy = 'name', $dir = 'ASC')
    {
        $conditions = $this->entityManager->createConditions();
        $conditions->field('status')->eq(VersionableInterface::STATUS_PUBLISHED);

        if (null !== $offset && null !== $showPerPage) {
            $conditions->paging($offset, $showPerPage);
        }

        $conditions->order($orderBy, $dir);

        return $this->entityManager->findAll(Block::class, $conditions);
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return Collection
     */
    public function getAllLive($offset = null, $showPerPage = null)
    {
        $now = new DateTime();

        $conditions = $this->entityManager->createConditions();
        $conditions->field('status')->eq(VersionableInterface::STATUS_PUBLISHED);
        $conditions->field('liveFrom')->lt($now);
        $conditions->field('expiresEnd')->gt($now);

        if (null !== $offset && null !== $showPerPage) {
            $conditions->paging($offset, $showPerPage);
        }

        return $this->entityManager->findAll(Block::class, $conditions);
    }

    /**
     * @param int $versionOfId
     * @return Collection
     */
    public function getAllVersionOf($versionOfId)
    {
        $conditions = $this->entityManager->createConditions();
        $conditions->field('versionOf.id')->eq($versionOfId);

        return $this->entityManager->findAll(Block::class, $conditions);
    }

    /**
     * @param Block $entity
     * @return BlockRepository
     */
    public function delete(Block $entity)
    {
        $this->entityManager->delete($entity);
        return $this;
    }

}
