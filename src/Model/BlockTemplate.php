<?php
namespace Block\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;

class BlockTemplate extends AbstractEntity
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
     * @return BlockTemplate
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return BlockType
     */
    public function getForType()
    {
        return $this->get('for_type');
    }

    /**
     * @param BlockType $forType
     * @return BlockTemplate
     */
    public function setForType(BlockType $forType)
    {
        $this->set('for_type', $forType);
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
     * @return BlockTemplate
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return string
     */
    public function getViewScript()
    {
        return $this->get('view_script');
    }

    /**
     * @param string $viewScript
     * @return BlockTemplate
     */
    public function setViewScript($viewScript)
    {
        $this->set('view_script', $viewScript);
		return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @param string $description
     * @return BlockTemplate
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
		return $this;
    }

}
