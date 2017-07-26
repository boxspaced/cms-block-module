<?php
namespace Boxspaced\CmsBlockModule\Service;

use DateTime;
use Zend\Cache\Storage\Adapter\AbstractAdapter as Cache;
use Zend\Log\Logger;
use Zend\Authentication\AuthenticationService;
use Boxspaced\EntityManager\EntityManager;
use Boxspaced\CmsBlockModule\Model;
use Zend\Db\Sql;
use Boxspaced\CmsBlockModule\Exception;
use Boxspaced\CmsAccountModule\Model\UserRepository;
use Boxspaced\CmsVersioningModule\Model\VersioningService;
use Boxspaced\CmsWorkflowModule\Model\WorkflowService;
use Boxspaced\CmsCoreModule\Model\EntityFactory;
use Boxspaced\CmsAccountModule\Model\User;
use Boxspaced\CmsVersioningModule\Model\VersionableInterface;

class BlockService
{

    const MODULE_NAME = 'block';
    const CURRENT_PUBLISHING_OPTIONS_CACHE_ID = 'currentPublishingOptionsBlock%d';
    const BLOCK_CACHE_ID = 'block%d';
    const BLOCK_META_CACHE_ID = 'blockMeta%d';
    const BLOCK_TYPE_CACHE_ID = 'blockType%d';

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Model\BlockTypeRepository
     */
    protected $blockTypeRepository;

    /**
     * @var Model\BlockRepository
     */
    protected $blockRepository;

    /**
     * @var Model\BlockTemplateRepository
     */
    protected $blockTemplateRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var VersioningService
     */
    protected $versioningService;

    /**
     * @var WorkflowService
     */
    protected $workflowService;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     *
     * @param Cache $cache
     * @param Logger $logger
     * @param AuthenticationService $authService
     * @param EntityManager $entityManager
     * @param Model\BlockTypeRepository $blockTypeRepository
     * @param Model\BlockRepository $blockRepository
     * @param Model\BlockTemplateRepository $blockTemplateRepository
     * @param UserRepository $userRepository
     * @param VersioningService $versioningService
     * @param WorkflowService $workflowService
     * @param EntityFactory $entityFactory
     */
    public function __construct(
        Cache $cache,
        Logger $logger,
        AuthenticationService $authService,
        EntityManager $entityManager,
        Model\BlockTypeRepository $blockTypeRepository,
        Model\BlockRepository $blockRepository,
        Model\BlockTemplateRepository $blockTemplateRepository,
        UserRepository $userRepository,
        VersioningService $versioningService,
        WorkflowService $workflowService,
        EntityFactory $entityFactory
    )
    {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->authService = $authService;
        $this->entityManager = $entityManager;
        $this->blockTypeRepository = $blockTypeRepository;
        $this->blockRepository = $blockRepository;
        $this->blockTemplateRepository = $blockTemplateRepository;
        $this->userRepository = $userRepository;
        $this->versioningService = $versioningService;
        $this->workflowService = $workflowService;
        $this->entityFactory = $entityFactory;

        if ($this->authService->hasIdentity()) {
            $identity = $authService->getIdentity();
            $this->user = $userRepository->getById($identity->id);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isNameAvailable($name)
    {
        return (null === $this->blockRepository->getByName($name));
    }

    /**
     * @return BlockType[]
     */
    public function getTypes()
    {
        $types = [];

        foreach ($this->blockTypeRepository->getAll() as $type) {

            $types[] = BlockType::createFromEntity($type);
        }

        return $types;
    }

    /**
     * @param string $query
     * @return Block[]
     */
    public function searchPublishedBlocks($query)
    {
        $blocks = [];

        foreach ($this->blockRepository->getAllPublished() as $block) {

            if (false === stripos($block->getName(), $query)) {
                continue;
            }

            $blocks[] = Block::createFromEntity($block);
        }

        return $blocks;
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return Block[]
     */
    public function getPublishedBlocks($offset = null, $showPerPage = null)
    {
        $blocks = [];

        foreach ($this->blockRepository->getAllPublished($offset, $showPerPage) as $block) {

            $blocks[] = Block::createFromEntity($block);
        }

        return $blocks;
    }

    /**
     * @todo need to find a way of using SQL_CALC_FOUND_ROWS, in mappers and returned to repository
     * @return int
     */
    public function countPublishedBlocks()
    {
        $sql = new Sql\Sql($this->entityManager->getDb());

        $select = $sql->select();
        $select->columns([
            'count' => new Sql\Expression('COUNT(*)'),
        ]);
        $select->from('block');
        $select->where([
            'status = ?' => VersionableInterface::STATUS_PUBLISHED,
        ]);

        $stmt = $sql->prepareStatementForSqlObject($select);

        return (int) $stmt->execute()->getResource()->fetchColumn();
    }

    /**
     * @return BlockType
     */
    public function getType($id)
    {
        $cacheId = sprintf(static::BLOCK_TYPE_CACHE_ID, $id);
        $cached = $this->cache->getItem($cacheId);

        if (null !== $cached) {
            return $cached;
        }

        $type = $this->blockTypeRepository->getById($id);

        if (null === $type) {
            throw new Exception\UnexpectedValueException('Unable to find type with given ID');
        }

        $blockType = BlockType::createFromEntity($type);

        $this->cache->setItem($cacheId, $blockType);

        return $blockType;
    }

    /**
     * @param int $id
     * @return Block
     */
    public function getCacheControlledBlock($id)
    {
        $cacheId = sprintf(static::BLOCK_CACHE_ID, $id);
        $cached = $this->cache->getItem($cacheId);

        if (null !== $cached) {
            return $cached;
        }

        $block = $this->getBlock($id);

        $this->cache->setItem($cacheId, $block);

        return $block;
    }

    /**
     * @param int $id
     * @return Block
     */
    public function getBlock($id)
    {
        $block = $this->blockRepository->getById($id);

        if (null === $block) {
            throw new Exception\UnexpectedValueException('Unable to find an block with given ID');
        }

        return Block::createFromEntity($block);
    }

    /**
     * @param int $id
     * @return BlockMeta
     */
    public function getCacheControlledBlockMeta($id)
    {
        $cacheId = sprintf(static::BLOCK_META_CACHE_ID, $id);
        $cached = $this->cache->getItem($cacheId);

        if (null !== $cached) {
            return $cached;
        }

        $blockMeta = $this->getBlockMeta($id);

        $this->cache->setItem($cacheId, $blockMeta);

        return $blockMeta;
    }

    /**
     * @param int $id
     * @return BlockMeta
     */
    public function getBlockMeta($id)
    {
        $block = $this->blockRepository->getById($id);

        if (null === $block) {
            throw new Exception\UnexpectedValueException('Unable to find an block with given ID');
        }

        return BlockMeta::createFromEntity($block);
    }

    /**
     * @param int $id
     * @return PublishingOptions
     */
    public function getCurrentPublishingOptions($id)
    {
        $cacheId = sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id);
        $cached = $this->cache->getItem($cacheId);

        if (null !== $cached) {
            return $cached;
        }

        $block = $this->blockRepository->getById($id);

        if (null === $block) {
            throw new Exception\UnexpectedValueException('Unable to find an block with given ID');
        }

        if ($block->getStatus() !== VersionableInterface::STATUS_PUBLISHED) {
            // @todo return null
            throw new Exception\UnexpectedValueException('Block is not published');
        }

        $publishingOptions = new PublishingOptions();
        $publishingOptions->name = $block->getName();
        $publishingOptions->templateId = $block->getTemplate()->getId();
        $publishingOptions->liveFrom = $block->getLiveFrom();
        $publishingOptions->expiresEnd = $block->getExpiresEnd();

        $this->cache->setItem($cacheId, $publishingOptions);

        return $publishingOptions;
    }

    /**
     * @param string $name
     * @param int $typeId
     * @return int
     */
    public function createDraft($name, $typeId)
    {
        $type = $this->blockTypeRepository->getById($typeId);

        if (null === $type) {
            throw new Exception\UnexpectedValueException('Unable to find type provided');
        }

        $draft = $this->entityFactory->createEntity(Model\Block::class);
        $draft->setName($name);
        $draft->setType($type);

        $versionableDraft = new Model\VersionableBlock($draft);
        $workflowableDraft = new Model\WorkflowableBlock($draft);

        $this->versioningService->createDraft($versionableDraft, $this->user);
        $this->workflowService->moveToAuthoring($workflowableDraft);

        $this->entityManager->flush();

        return $draft->getId();
    }

    /**
     * @param int $id Published block's ID
     * @return int
     */
    public function createRevision($id)
    {
        $revisionOf = $this->blockRepository->getById($id);

        if (null === $revisionOf) {
            throw new Exception\UnexpectedValueException('Unable to find an block with given ID');
        }

        if ($revisionOf->getStatus() !== VersionableInterface::STATUS_PUBLISHED) {
            throw new Exception\UnexpectedValueException('The block you are creating a revision of must be published');
        }

        $revision = $this->entityFactory->createEntity(Model\Block::class);
        $revision->setType($revisionOf->getType());

        $versionableRevision = new Model\VersionableBlock($revision);
        $versionableRevisionOf = new Model\VersionableBlock($revisionOf);
        $workflowableRevision = new Model\WorkflowableBlock($revision);

        $this->versioningService->createRevision($versionableRevision, $versionableRevisionOf, $this->user);
        $this->workflowService->moveToAuthoring($workflowableRevision);

        $this->entityManager->flush();

        return $revision->getId();
    }

    /**
     * @param int $id Draft or revision ID
     * @param Block $block
     * @param string $noteText
     * @return void
     */
    public function edit($id, Block $block, $noteText = '')
    {
        $blockEntity = $this->blockRepository->getById($id);

        if (null === $blockEntity) {
            throw new Exception\UnexpectedValueException('Unable to find block');
        }

        if (!in_array($blockEntity->getStatus(), array(
            VersionableInterface::STATUS_DRAFT,
            VersionableInterface::STATUS_REVISION,
        ))) {
            throw new Exception\UnexpectedValueException('You can only edit a draft or revision');
        }

        foreach ($block->fields as $field) {

            $fieldEntity = $this->entityFactory->createEntity(Model\BlockField::class);
            $fieldEntity->setName($field->name);
            $fieldEntity->setValue($field->value);

            $blockEntity->addField($fieldEntity);
        }

        if ($noteText) {

            $noteEntity = $this->entityFactory->createEntity(Model\BlockNote::class);
            $noteEntity->setText($noteText);
            $noteEntity->setUser($this->user);
            $noteEntity->setCreatedTime(new DateTime());

            $blockEntity->addNote($noteEntity);
        }

        $this->entityManager->flush();
    }

    /**
     * @param int $id
     * @param PublishingOptions $options
     * @return void
     */
    public function publish($id, PublishingOptions $options = null)
    {
        $block = $this->blockRepository->getById($id);

        if (null === $block) {
            throw new Exception\UnexpectedValueException('Unable to find block');
        }

        if (null === $options && in_array($block->getStatus(), array(
            VersionableInterface::STATUS_PUBLISHED,
            VersionableInterface::STATUS_DRAFT,
        ))) {
            throw new Exception\UnexpectedValueException('Block status requires publishing options');
        }

        $versionableBlock = new Model\VersionableBlock($block);
        $workflowableBlock = new Model\WorkflowableBlock($block);

        switch ($block->getStatus()) {

            case VersionableInterface::STATUS_PUBLISHED:
            case VersionableInterface::STATUS_DRAFT:

                $block->setName($options->name);
                $block->setLiveFrom($options->liveFrom);
                $block->setExpiresEnd($options->expiresEnd);

                $template = $this->blockTemplateRepository->getById($options->templateId);
                $block->setTemplate($template);

                if ($block->getStatus() === VersionableInterface::STATUS_DRAFT) {
                    $this->versioningService->publishDraft($versionableBlock);
                    $this->workflowService->removeFromWorkflow($workflowableBlock);
                }
                break;

            case VersionableInterface::STATUS_REVISION:

                $this->versioningService->publishRevision($versionableBlock);
                $this->workflowService->removeFromWorkflow($workflowableBlock);
                break;

            case VersionableInterface::STATUS_ROLLBACK:

                $this->versioningService->restoreRollback($versionableBlock);
                break;

            case VersionableInterface::STATUS_DELETED:

                $this->versioningService->restoreDeleted($versionableBlock);
                break;

            default:
                // No default
        }

        $this->entityManager->flush();

        // Clear cache
        $this->cache->removeItem(sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id));
        $versionOf = $block->getVersionOf();
        if ($versionOf) {
            $this->cache->removeItem(sprintf(static::BLOCK_CACHE_ID, $versionOf->getId()));
            $this->cache->removeItem(sprintf(static::BLOCK_META_CACHE_ID, $versionOf->getId()));
        }
    }

    /**
     * @param int $id Published block's ID
     * @return void
     */
    public function delete($id)
    {
        $block = $this->blockRepository->getById($id);

        if (null === $block) {
            throw new Exception\UnexpectedValueException('Unable to find block');
        }

        if ($block->getStatus() !== VersionableInterface::STATUS_PUBLISHED) {
            throw new Exception\UnexpectedValueException('Block must be published');
        }

        $block->setTemplate(null);
        $block->setLiveFrom(null);
        $block->setExpiresEnd(null);

        $versionableBlock = new Model\VersionableBlock($block);
        $this->versioningService->deletePublished($versionableBlock);

        $versionsOf = $this->blockRepository->getAllVersionOf($block->getId());

        foreach ($versionsOf as $versionOf) {
            $this->blockRepository->delete($versionOf);
        }

        $this->entityManager->flush();

        // Clear cache
        $this->cache->removeItem(sprintf(static::CURRENT_PUBLISHING_OPTIONS_CACHE_ID, $id));
        $this->cache->removeItem(sprintf(static::BLOCK_CACHE_ID, $id));
        $this->cache->removeItem(sprintf(static::BLOCK_META_CACHE_ID, $id));
    }

    /**
     * @return AvailableBlockTypeOption[]
     */
    public function getAvailableBlockOptions()
    {
        $blockTypes = [];

        foreach ($this->blockRepository->getAllPublished() as $block) {

            $type = $block->getType();
            $blockTypes[$type->getName()][$block->getId()] = $block->getName();
        }

        $blockOptions = [];

        foreach ($blockTypes as $name => $blocks) {

            $blockTypeOption = new AvailableBlockTypeOption();
            $blockTypeOption->name = $name;

            foreach ($blocks as $value => $label) {

                $blockOption = new AvailableBlockOption();
                $blockOption->value = $value;
                $blockOption->label = $label;

                $blockTypeOption->blockOptions[] = $blockOption;
            }

            $blockOptions[] = $blockTypeOption;
        }

        return $blockOptions;
    }

}
