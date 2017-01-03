<?php
namespace Block\Model;

use DateTime;
use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Collection\Collection;
use Account\Model\User;

class Block extends AbstractEntity
{

    /**
     * @var string[]
     */
    protected $versioningTransferFields = array(
        'fields',
    );

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return Block
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return Block
     */
    public function getVersionOf()
    {
        return $this->get('versionOf');
    }

    /**
     * @param Block
     * @return Block
     */
    public function setVersionOf($versionOf)
    {
        $this->set('versionOf', $versionOf);
        return $this;
    }

    /**
     * @return BlockType
     */
    public function getType()
    {
        return $this->get('type');
    }

    /**
     * @param BlockType $type
     * @return Block
     */
    public function setType($type)
    {
        $this->set('type', $type);
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
     * @return Block
     */
    public function setName($name)
    {
        $this->set('name', $name);
        return $this;
    }

    /**
     * @return BlockTemplate
     */
    public function getTemplate()
    {
        return $this->get('template');
    }

    /**
     * @param BlockTemplate $template
     * @return Block
     */
    public function setTemplate($template)
    {
        $this->set('template', $template);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLiveFrom()
    {
        return $this->get('liveFrom');
    }

    /**
     * @param DateTime $liveFrom
     * @return Block
     */
    public function setLiveFrom(DateTime $liveFrom = null)
    {
        $this->set('liveFrom', $liveFrom);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpiresEnd()
    {
        return $this->get('expiresEnd');
    }

    /**
     * @param DateTime $expiresEnd
     * @return Block
     */
    public function setExpiresEnd(DateTime $expiresEnd = null)
    {
        $this->set('expiresEnd', $expiresEnd);
        return $this;
    }

    /**
     * @return string
     */
    public function getWorkflowStage()
    {
        return $this->get('workflowStage');
    }

    /**
     * @param string $workflowStage
     * @return Block
     */
    public function setWorkflowStage($workflowStage)
    {
        $this->set('workflowStage', $workflowStage);
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @param string $status
     * @return Block
     */
    public function setStatus($status)
    {
        $this->set('status', $status);
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->get('author');
    }

    /**
     *
     * @param User $author
     * @return Block
     */
    public function setAuthor(User $author = null)
    {
        $this->set('author', $author);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getAuthoredTime()
    {
        return $this->get('authoredTime');
    }

    /**
     * @param DateTime $authoredTime
     * @return Block
     */
    public function setAuthoredTime(DateTime $authoredTime = null)
    {
        $this->set('authoredTime', $authoredTime);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLastModifiedTime()
    {
        return $this->get('lastModifiedTime');
    }

    /**
     * @param DateTime $lastModifiedTime
     * @return Block
     */
    public function setLastModifiedTime(DateTime $lastModifiedTime = null)
    {
        $this->set('lastModifiedTime', $lastModifiedTime);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPublishedTime()
    {
        return $this->get('publishedTime');
    }

    /**
     * @param DateTime $publishedTime
     * @return Block
     */
    public function setPublishedTime(DateTime $publishedTime = null)
    {
        $this->set('publishedTime', $publishedTime);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRollbackStopPoint()
    {
        return $this->get('rollbackStopPoint');
    }

    /**
     * @param DateTime $rollbackStopPoint
     * @return Block
     */
    public function setRollbackStopPoint(DateTime $rollbackStopPoint = null)
    {
        $this->set('rollbackStopPoint', $rollbackStopPoint);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getFields()
    {
        return $this->get('fields');
    }

    /**
     * @param BlockField $field
     * @return Block
     */
    public function addField(BlockField $field)
    {
        $field->setParentBlock($this);
        $this->getFields()->add($field);
        return $this;
    }

    /**
     * @param BlockField $field
     * @return Block
     */
    public function deleteField(BlockField $field)
    {
        $this->getFields()->delete($field);
        return $this;
    }

    /**
     * @return Block
     */
    public function deleteAllFields()
    {
        foreach ($this->getFields() as $field) {
            $this->deleteField($field);
        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function getNotes()
    {
        return $this->get('notes');
    }

    /**
     * @param BlockNote $note
     * @return Block
     */
    public function addNote(BlockNote $note)
    {
        $note->setParentBlock($this);
        $this->getNotes()->add($note);
        return $this;
    }

    /**
     * @param BlockNote $note
     * @return Block
     */
    public function deleteNote(BlockNote $note)
    {
        $this->getNotes()->delete($note);
        return $this;
    }

    /**
     * @return Block
     */
    public function deleteAllNotes()
    {
        foreach ($this->getNotes() as $note) {
            $this->deleteNote($note);
        }
        return $this;
    }

    /**
     * @todo exchange array interface?
     * @return array
     */
    public function getVersioningTransferValues()
    {
        $values = [];

        foreach ($this->versioningTransferFields as $key) {
            $values[$key] = $this->get($key);
        }

        return $values;
    }

    /**
     * @param array $values
     * @return Block
     */
    public function setVersioningTransferValues(array $values)
    {
        foreach ($values as $field => $value) {

            if (!in_array($field, $this->versioningTransferFields)) {
                continue;
            }

            $this->set($field, $value);

            if ('fields' !== $field) {
                continue;
            }

            foreach ($value as $child) {
                $child->setParentBlock($this);
            }
        }

        return $this;
    }

}