<?php
namespace Boxspaced\CmsBlockModule\Service;

use Boxspaced\CmsBlockModule\Model\BlockNote as BlockNoteEntity;

class BlockNote
{

    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var string
     */
    public $time;

    /**
     *
     * @var string
     */
    public $text;

    /**
     * @param BlockNoteEntity $entity
     * @return BlockNote
     */
    public static function createFromEntity(BlockNoteEntity $entity)
    {
        $note = new static();

        $note->username = $entity->getUser()->getUsername();
        $note->text = $entity->getText();
        $note->time = $entity->getCreatedTime();

        return $note;
    }

}
