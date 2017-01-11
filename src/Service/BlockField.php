<?php
namespace Boxspaced\CmsBlockModule\Service;

use Boxspaced\CmsBlockModule\Model\BlockField as BlockFieldEntity;

class BlockField
{

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $value;

    /**
     * @param BlockFieldEntity $entity
     * @return BlockField
     */
    public static function createFromEntity(BlockFieldEntity $entity)
    {
        $field = new static();

        $field->name = $entity->getName();
        $field->value = $entity->getValue();

        return $field;
    }

}
