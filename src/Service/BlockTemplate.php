<?php
namespace Block\Service;

use Block\Model\BlockTemplate as BlockTemplateEntity;

class BlockTemplate
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
     * @var string
     */
    public $viewScript;

    /**
     *
     * @var string
     */
    public $description;

    /**
     * @param BlockTemplateEntity $entity
     * @return BlockTemplate
     */
    public static function createFromEntity(BlockTemplateEntity $entity)
    {
        $template = new static();

        $template->id = (int) $entity->getId();
        $template->name = $entity->getName();
        $template->viewScript = $entity->getViewScript();
        $template->description = $entity->getDescription();

        return $template;
    }

}
