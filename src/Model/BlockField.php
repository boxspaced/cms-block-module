<?php
namespace Block\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;

class BlockField extends AbstractEntity
{

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return BlockField
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * @param string $name
     * @return BlockField
     */
    public function setName($name)
    {
        $this->set('name', $name);
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->get('value');
    }

    /**
     * @param string $value
     * @return BlockField
     */
    public function setValue($value)
    {
        $this->set('value', $value);
        return $this;
    }

    /**
     * @return Block
     */
    public function getParentBlock()
    {
        return $this->get('parent_block');
    }

    /**
     * @param Block $parentBlock
     * @return BlockField
     */
    public function setParentBlock(Block $parentBlock)
    {
        $this->set('parent_block', $parentBlock);
        return $this;
    }

}
