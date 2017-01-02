<?php
namespace Block\Service;

use Block\Model\Block as BlockEntity;

class Block
{

    /**
     *
     * @var int
     */
    public $id;

    /**
     *
     * @var BlockField[]
     */
    public $fields = [];

    /**
     * @param BlockEntity $entity
     * @return Block
     */
    public static function createFromEntity(BlockEntity $entity)
    {
        $block = new static();
        $block->id = $entity->getId();

        foreach ($entity->getFields() as $field) {
            $block->fields[] = BlockField::createFromEntity($field);
        }

        return $block;
    }

}
