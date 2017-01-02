<?php
namespace Block\Model;

use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Collection\Collection;

class BlockTemplateRepository
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
     * @return BlockTemplate
     */
    public function getById($id)
    {
        return $this->entityManager->find(BlockTemplate::class, $id);
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        return $this->entityManager->findAll(BlockTemplate::class);
    }

    /**
     * @param BlockTemplate $entity
     * @return BlockTemplateRepository
     */
    public function delete(BlockTemplate $entity)
    {
        $this->entityManager->delete($entity);
        return $this;
    }

}
