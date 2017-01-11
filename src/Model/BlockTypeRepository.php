<?php
namespace Boxspaced\CmsBlockModule\Model;

use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Collection\Collection;

class BlockTypeRepository
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
     * @return BlockType
     */
    public function getById($id)
    {
        return $this->entityManager->find(BlockType::class, $id);
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        return $this->entityManager->findAll(BlockType::class);
    }

    /**
     * @param BlockType $entity
     * @return BlockTypeRepository
     */
    public function delete(BlockType $entity)
    {
        $this->entityManager->delete($entity);
        return $this;
    }

}
