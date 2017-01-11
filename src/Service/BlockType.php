<?php
namespace Boxspaced\CmsBlockModule\Service;

use Boxspaced\CmsBlockModule\Model\BlockType as BlockTypeEntity;

class BlockType
{

    /**
     *
     * @var int
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var BlockTemplate[]
     */
    public $templates = [];

    /**
     * @param BlockTypeEntity $entity
     * @return BlockType
     */
    public static function createFromEntity(BlockTypeEntity $entity)
    {
        $type = new static();

        $type->id = $entity->getId();
        $type->name = $entity->getName();

        foreach ($entity->getTemplates() as $template) {
            $type->templates[] = BlockTemplate::createFromEntity($template);
        }

        return $type;
    }

}
