<?php
namespace Boxspaced\CmsBlockModule\Model;

use DateTime;
use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Collection\Collection;
use Boxspaced\CmsVersioningModule\Model\VersionableInterface;

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
        $query = $this->entityManager->createQuery();
        $query->field('name')->eq($name);

        return $this->entityManager->findOne(Block::class, $query);
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
        $query = $this->entityManager->createQuery();
        $query->field('status')->eq(VersionableInterface::STATUS_PUBLISHED);

        if (null !== $offset && null !== $showPerPage) {
            $query->paging($offset, $showPerPage);
        }

        $query->order($orderBy, $dir);

        return $this->entityManager->findAll(Block::class, $query);
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return Collection
     */
    public function getAllLive($offset = null, $showPerPage = null)
    {
        $now = new DateTime();

        $query = $this->entityManager->createQuery();
        $query->field('status')->eq(VersionableInterface::STATUS_PUBLISHED);
        $query->field('live_from')->lt($now);
        $query->field('expires_end')->gt($now);

        if (null !== $offset && null !== $showPerPage) {
            $query->paging($offset, $showPerPage);
        }

        return $this->entityManager->findAll(Block::class, $query);
    }

    /**
     * @param int $versionOfId
     * @return Collection
     */
    public function getAllVersionOf($versionOfId)
    {
        $query = $this->entityManager->createQuery();
        $query->field('version_of.id')->eq($versionOfId);

        return $this->entityManager->findAll(Block::class, $query);
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
