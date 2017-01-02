<?php
namespace Block\Model;

use Workflow\Model\WorkflowableInterface;

class WorkflowableBlock implements WorkflowableInterface
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
     * @return string
     */
    public function getWorkflowStage()
    {
        return $this->block->getWorkflowStage();
    }

    /**
     * @param string $stage
     * @return WorkflowableInterface
     */
    public function setWorkflowStage($stage)
    {
        $this->block->setWorkflowStage($stage);
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->block->getStatus();
    }

}
