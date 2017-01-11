<?php
namespace Boxspaced\CmsBlockModule\Model;

use DateTime;
use Boxspaced\CmsVersioningModule\Model\VersionableInterface;
use Boxspaced\CmsAccountModule\Model\User;

class VersionableBlock implements VersionableInterface
{

    /**
     * @var Block
     */
    protected $block;

    /**
     * @param Block $block
     */
    public function __construct(Block $block)
    {
        $this->block = $block;
    }

    /**
     * @return Block
     */
    public function getAdaptee()
    {
        return $this->block;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->block->getStatus();
    }

    /**
     * @param string $status
     * @return VersionableBlock
     */
    public function setStatus($status)
    {
        $this->block->setStatus($status);
        return $this;
    }

    /**
     * @return VersionableBlock
     */
    public function getVersionOf()
    {
        if (null === $this->block->getVersionOf()) {
            return null;
        }

        return new static($this->block->getVersionOf());
    }

    /**
     * @param VersionableBlock $versionOf
     * @return VersionableBlock
     */
    public function setVersionOf(VersionableInterface $versionOf = null)
    {
        if ($versionOf instanceof $this) {
            $versionOf = $versionOf->getAdaptee();
        }

        $this->block->setVersionOf($versionOf);
        return $this;
    }

    /**
     * @return array
     */
    public function getVersioningTransferValues()
    {
        return $this->block->getVersioningTransferValues();
    }

    /**
     * @param array $values
     * @return VersionableBlock
     */
    public function setVersioningTransferValues(array $values)
    {
        $this->block->setVersioningTransferValues($values);
        return $this;
    }

    /**
     * @param User $author
     * @return VersionableBlock
     */
    public function setAuthor(User $author = null)
    {
        $this->block->setAuthor($author);
        return $this;
    }

    /**
     * @param DateTime $authoredTime
     * @return VersionableBlock
     */
    public function setAuthoredTime(DateTime $authoredTime = null)
    {
        $this->block->setAuthoredTime($authoredTime);
        return $this;
    }

    /**
     * @param DateTime $publishedTime
     * @return VersionableBlock
     */
    public function setPublishedTime(DateTime $publishedTime = null)
    {
        $this->block->setPublishedTime($publishedTime);
        return $this;
    }

    /**
     * @param DateTime $lastModifiedTime
     * @return VersionableBlock
     */
    public function setLastModifiedTime(DateTime $lastModifiedTime = null)
    {
        $this->block->setLastModifiedTime($lastModifiedTime);
        return $this;
    }

    /**
     * @param DateTime $rollbackStopPoint
     * @return VersionableBlock
     */
    public function setRollbackStopPoint(DateTime $rollbackStopPoint = null)
    {
        $this->block->setRollbackStopPoint($rollbackStopPoint);
        return $this;
    }

}
