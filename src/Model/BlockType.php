<?php
namespace Boxspaced\CmsBlockModule\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Collection\Collection;

class BlockType extends AbstractEntity
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
     * @return BlockType
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
     * @return BlockType
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->get('icon');
    }

    /**
     * @param string $icon
     * @return BlockType
     */
    public function setIcon($icon)
    {
        $this->set('icon', $icon);
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
     * @return BlockType
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
		return $this;
    }

    /**
     * @return Collection
     */
    public function getTemplates()
    {
        return $this->get('templates');
    }

    /**
     * @param BlockTemplate $template
     * @return BlockType
     */
    public function addTemplate(BlockTemplate $template)
    {
        $template->setParentType($this);
        $this->getTemplates()->add($template);
        return $this;
    }

    /**
     * @param BlockTemplate $template
     * @return BlockType
     */
    public function deleteTemplate(BlockTemplate $template)
    {
        $this->getTemplates()->delete($template);
        return $this;
    }

    /**
     * @return BlockType
     */
    public function deleteAllTemplates()
    {
        foreach ($this->getTemplates() as $template) {
            $this->deleteTemplate($template);
        }
        return $this;
    }

}
