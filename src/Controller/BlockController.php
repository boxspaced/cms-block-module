<?php
namespace Boxspaced\CmsBlockModule\Controller;

use DateTime;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Log\Logger;
use Boxspaced\CmsBlockModule\Service;
use Boxspaced\CmsBlockModule\Exception;
use Zend\Paginator;
use Boxspaced\CmsBlockModule\Form;
use Boxspaced\CmsAccountModule\Service\AccountService;
use Boxspaced\CmsWorkflowModule\Service\WorkflowService;

class BlockController extends AbstractActionController
{

    /**
     * @var Service\BlockService
     */
    protected $blockService;

    /**
     * @var WorkflowService
     */
    protected $workflowService;

    /**
     * @var AccountService
     */
    protected $accountService;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ViewModel
     */
    protected $view;

    /**
     * @param Service\BlockService $blockService
     * @param WorkflowService $workflowService
     * @param AccountService $accountService
     * @param Logger $logger
     * @param array $config
     */
    public function __construct(
        Service\BlockService $blockService,
        WorkflowService $workflowService,
        AccountService $accountService,
        Logger $logger,
        array $config
    )
    {
        $this->blockService = $blockService;
        $this->workflowService = $workflowService;
        $this->accountService = $accountService;
        $this->logger = $logger;
        $this->config = $config;

        $this->view = new ViewModel();
        $this->view->setTerminal(true);
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        $adminNavigation = $this->adminNavigationWidget();
        if (null !== $adminNavigation) {
            $this->view->addChild($adminNavigation, 'adminNavigation');
        }

        $adapter = new Paginator\Adapter\Callback(
            function ($offset, $itemCountPerPage) {
                return $this->blockService->getPublishedBlocks($offset, $itemCountPerPage);
            },
            function () {
                return $this->blockService->countPublishedBlocks();
            }
        );

        $paginator = new Paginator\Paginator($adapter);
        $paginator->setCurrentPageNumber($this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage($this->config['core']['admin_show_per_page']);

        $this->view->paginator = $paginator;

        $blockItems = [];

        foreach ($paginator as $block) {

            $blockMeta = $this->blockService->getBlockMeta($block->id);
            $publishingOptions = $this->blockService->getCurrentPublishingOptions($block->id);

            // @todo remove, should be view helpers
            $lifespanState = $this->itemAdminWidget()->calcLifeSpanState($publishingOptions->liveFrom, $publishingOptions->expiresEnd);
            $lifespanTitle = $this->itemAdminWidget()->calcLifeSpanTitle($publishingOptions->liveFrom, $publishingOptions->expiresEnd);

            $blockItems[] = array(
                'typeIcon' => $blockMeta->typeIcon,
                'typeName' => $blockMeta->typeName,
                'name' => $blockMeta->name,
                'id' => $block->id,
                'lifespanState' => $lifespanState,
                'lifespanTitle' => $lifespanTitle,
                'allowEdit' => $this->accountService->isAllowed(get_class(), 'edit'),
                'allowPublish' => $this->accountService->isAllowed(get_class(), 'publish'),
                'allowDelete' => $this->accountService->isAllowed(get_class(), 'delete'),
            );
        }

        $this->view->blockItems = $blockItems;

        $this->view->allowCreate = $this->accountService->isAllowed(get_class(), 'create');

        return $this->view;
    }

    /**
     * @return void
     */
    public function createAction()
    {
        $form = new Form\BlockCreateForm($this->blockService);

        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {
            return $this->view;
        }

        $form->setData($this->getRequest()->getPost());

        if (!$form->isValid()) {

            $this->flashMessenger()->addErrorMessage('Validation failed.');
            return $this->view;
        }

        $values = $form->getData();

        $blockId = $this->blockService->createDraft($values['name'], $values['typeId']);

        $this->flashMessenger()->addSuccessMessage('Create successful, add content below.');

        return $this->redirect()->toRoute('block', [
            'action' => 'edit',
            'id' => $blockId,
        ]);
    }

    /**
     * @return void
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id');
        $blockMeta = $this->blockService->getBlockMeta($id);
        $block = $this->blockService->getBlock($id);
        $identity = $this->accountService->getIdentity();

        if (
            $this->workflowService->getStatus(Service\BlockService::MODULE_NAME, $id) !== WorkflowService::WORKFLOW_STATUS_CURRENT
            && $blockMeta->authorId != $identity->id
        ) {
            throw new Exception\RuntimeException('User has not authored this draft/revision');
        }

        $this->view->titleSuffix = '';
        if ($this->workflowService->getStatus(Service\BlockService::MODULE_NAME, $id) !== WorkflowService::WORKFLOW_STATUS_CURRENT) {
            $this->view->titleSuffix = $this->workflowService->getStatus(Service\BlockService::MODULE_NAME, $id);
        }

        $this->view->typeName = $blockMeta->typeName;
        $this->view->blockName = $blockMeta->name;
        $this->view->blockNotes = $blockMeta->notes;

        $form = new Form\BlockEditForm($blockMeta->typeName);
        $form->get('id')->setValue($id);

        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {

            $form->populateFromBlock($block);
            return $this->view;
        }

        $form->setData($this->getRequest()->getPost());

        if ($this->params()->fromPost('partial')) {

            $form->get('partial')->setValue(false);
            return $this->view;
        }

        if (!$form->isValid()) {

            $this->flashMessenger()->addErrorMessage('Validation failed.');
            return $this->view;
        }

        $values = $form->getData();

        $block->fields = [];

        foreach (isset($values['fields']) ? $values['fields'] : [] as $name => $value) {

            $field = new Service\BlockField();
            $field->name = $name;
            $field->value = $value;

            $block->fields[] = $field;
        }

        if (null !== $values['save']) {

            $editId = $id;

            if ($this->workflowService->getStatus(Service\BlockService::MODULE_NAME, $id) === WorkflowService::WORKFLOW_STATUS_CURRENT) {
                $editId = $this->blockService->createRevision($id);
            }

            $this->blockService->edit($editId, $block, $values['note']);

            $this->flashMessenger()->addSuccessMessage('Save successful.');

            return $this->redirect()->toRoute('workflow', [
                'action' => 'authoring',
            ]);
        }

        if (null !== $values['publish']) {

            $canPublish = $this->accountService->isAllowed(get_class(), 'publish');

            if (!$canPublish) {

                $editId = $id;

                if ($this->workflowService->getStatus(Service\BlockService::MODULE_NAME, $id) === WorkflowService::WORKFLOW_STATUS_CURRENT) {
                    $editId = $this->blockService->createRevision($id);
                }

                $this->blockService->edit($editId, $block, $values['note']);
                $this->workflowService->moveToPublishing(Service\BlockService::MODULE_NAME, $editId);

                $this->flashMessenger()->addSuccessMessage('Save successful, content moved to publishing for approval.');

                return $this->redirect()->toRoute('workflow', [
                    'action' => 'authoring',
                ]);
            }

            switch ($this->workflowService->getStatus(Service\BlockService::MODULE_NAME, $id)) {

                case WorkflowService::WORKFLOW_STATUS_CURRENT:

                    $revisionId = $this->blockService->createRevision($id);

                    $this->blockService->edit($revisionId, $block, $values['note']);
                    $this->blockService->publish($revisionId);

                    $this->flashMessenger()->addSuccessMessage('Update successful.');

                    return $this->redirect()->toRoute('block');
                    break;

                case WorkflowService::WORKFLOW_STATUS_UPDATE:

                    $this->blockService->edit($id, $block, $values['note']);
                    $this->blockService->publish($id);

                    $this->flashMessenger()->addSuccessMessage('Update successful.');

                    return $this->redirect()->toRoute('workflow', [
                        'action' => 'authoring',
                    ]);
                    break;

                case WorkflowService::WORKFLOW_STATUS_NEW:

                    $this->blockService->edit($id, $block, $values['note']);
                    $this->workflowService->moveToPublishing(Service\BlockService::MODULE_NAME, $id);

                    $this->flashMessenger()->addSuccessMessage('Save successful, please set options below to complete publishing process.');

                    return $this->redirect()->toRoute('block', [
                        'action' => 'publish',
                        'id' => $id,
                    ]);
                    break;

                default:
                    throw new Exception\UnexpectedValueException('Workflow status unknown');
            }
        }
    }

    /**
     * @return void
     */
    public function publishAction()
    {
        $id = $this->params()->fromRoute('id');
        $blockMeta = $this->blockService->getBlockMeta($id);
        $type = $this->blockService->getType($blockMeta->typeId);

        $publishingOptions = null;
        if ($this->workflowService->getStatus(Service\BlockService::MODULE_NAME, $id) === WorkflowService::WORKFLOW_STATUS_CURRENT) {
            $publishingOptions = $this->blockService->getCurrentPublishingOptions($id);
        }

        $this->view->typeName = $blockMeta->typeName;
        $this->view->blockName = $blockMeta->name;
        $this->view->blockNotes = $blockMeta->notes;

        foreach ($type->templates as $template) {

            if ($template->id == $this->params()->fromPost('templateId')) {
                $this->view->templateDescription = $template->description;
            }
        }

        $form = new Form\BlockPublishForm($id, $this->blockService);
        $form->get('id')->setValue($id);
        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {

            $form->populateValues(array(
                'name' => $blockMeta->name,
            ));

            if ($publishingOptions) {
                // Already published, editing
                $form->populateFromPublishingOptions($publishingOptions);
            }

            return $this->view;
        }

        $form->setData($this->getRequest()->getPost());

        if ($this->params()->fromPost('partial')) {

            $form->get('partial')->setValue(false);
            return $this->view;
        }

        if (!$form->isValid()) {

            $this->flashMessenger()->addErrorMessage('Validation failed.');
            return $this->view;
        }

        $values = $form->getData();

        if (null === $publishingOptions) {
            $publishingOptions = new Service\PublishingOptions();
        }

        $publishingOptions->name = $values['name'];
        $publishingOptions->liveFrom = new DateTime($values['liveFrom']);
        $publishingOptions->expiresEnd = new DateTime($values['expiresEnd']);
        $publishingOptions->templateId = $values['templateId'];

        $this->blockService->publish($id, $publishingOptions);

        $this->flashMessenger()->addSuccessMessage('Publishing successful.');

        return $this->redirect()->toRoute('block');
    }

    /**
     * @return void
     */
    public function deleteAction()
    {
        $form = new Form\ConfirmForm();
        $form->get('id')->setValue($this->params()->fromRoute('id'));
        $form->get('confirm')->setValue('Confirm delete');

        $this->view->form = $form;
        $this->view->setTemplate('block/block/confirm.phtml');

        if (!$this->getRequest()->isPost()) {
            return $this->view;
        }

        $form->setData($this->getRequest()->getPost());

        if (!$form->isValid()) {

            $this->flashMessenger()->addErrorMessage('Validation failed.');
            return $this->view;
        }

        $values = $form->getData();

        $this->blockService->delete($values['id']);

        $this->flashMessenger()->addSuccessMessage('Delete successful.');

        return $this->redirect()->toRoute('block');
    }

    /**
     * @return void
     */
    public function publishUpdateAction()
    {
        $form = new Form\ConfirmForm($this->getRequest());
        $form->get('id')->setValue($this->params()->fromRoute('id'));
        $form->get('confirm')->setValue('Confirm update');

        $this->view->form = $form;
        $this->view->setTemplate('block/block/confirm.phtml');

        if (!$this->getRequest()->isPost()) {
            return $this->view;
        }

        $form->setData($this->getRequest()->getPost());

        if (!$form->isValid()) {

            $this->flashMessenger()->addErrorMessage('Validation failed.');
            return $this->view;
        }

        $values = $form->getData();

        $this->blockService->publish($values['id']);

        $this->flashMessenger()->addSuccessMessage('Update successful.');

        return $this->redirect()->toRoute('workflow', [
            'action' => 'publishing',
        ]);
    }

}
