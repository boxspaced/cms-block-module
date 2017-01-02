<?php
namespace Block\Service;

use Block\Model\Block as BlockEntity;

class BlockMeta
{

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var int
     */
    public $typeId;

    /**
     *
     * @var string
     */
    public $typeName;

    /**
     *
     * @var string
     */
    public $typeIcon;

    /**
     *
     * @var int
     */
    public $authorId;

    /**
     *
     * @var BlockNote[]
     */
    public $notes = [];

    /**
     * @param BlockEntity $entity
     * @return BlockMeta
     */
    public static function createFromEntity(BlockEntity $entity)
    {
        $meta = new static();

        if ($entity->getVersionOf()) {
            $meta->name = $entity->getVersionOf()->getName();
        } else {
            $meta->name = $entity->getName();
        }

        $meta->authorId = $entity->getAuthor()->getId();
        $meta->typeId = (int) $entity->getType()->getId();
        $meta->typeName = $entity->getType()->getName();
        $meta->typeIcon = $entity->getType()->getIcon();

        foreach ($entity->getNotes() as $note) {
            $meta->notes[] = BlockNote::createFromEntity($note);
        }

        return $meta;
    }

}
