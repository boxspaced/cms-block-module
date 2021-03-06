<?php
namespace Boxspaced\CmsBlockModule\Model;

use DateTime;
use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\CmsAccountModule\Model\User;

class BlockNote extends AbstractEntity
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
     * @return BlockNote
     */
    public function setId($id)
    {
        $this->set('id', $id);
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
     * @return BlockNote
     */
    public function setParentBlock(Block $parentBlock)
    {
        $this->set('parent_block', $parentBlock);
		return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->get('text');
    }

    /**
     * @param string $text
     * @return BlockNote
     */
    public function setText($text)
    {
        $this->set('text', $text);
		return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->get('user');
    }

    /**
     * @param User $user
     * @return BlockNote
     */
    public function setUser(User $user)
    {
        $this->set('user', $user);
		return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedTime()
    {
        return $this->get('created_time');
    }

    /**
     * @param DateTime $createdTime
     * @return BlockNote
     */
    public function setCreatedTime(DateTime $createdTime = null)
    {
        $this->set('created_time', $createdTime);
		return $this;
    }

}
