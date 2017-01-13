<?php
namespace Boxspaced\CmsBlockModule\Controller\Plugin;

use DateTime;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Boxspaced\CmsBlockModule\Service;
use Zend\View\Model\ViewModel;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\CamelCaseToDash;

class BlockWidget extends AbstractPlugin
{

    /**
     * @var Service\BlockService
     */
    protected $blockService;

    /**
     * @param Service\BlockService $blockService
     */
    public function __construct(Service\BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * @param int $id
     * @param string $placeholder
     * @return ViewModel|null
     */
    public function __invoke($id, $placeholder)
    {
        if (!$id || !$placeholder) {
            return null;
        }

        $block = $this->blockService->getCacheControlledBlock($id);
        $blockMeta = $this->blockService->getCacheControlledBlockMeta($id);
        $publishingOptions = $this->blockService->getCurrentPublishingOptions($id);

        $now = new DateTime();

        if ($publishingOptions->liveFrom > $now || $publishingOptions->expiresEnd < $now) {
            return null;
        }

        $blockType = $this->blockService->getType($blockMeta->typeId);

        foreach ($blockType->templates as $template) {

            if ($template->id == $publishingOptions->templateId) {
                $blockTemplate = $template;
                break;
            }
        }

        if (!isset($blockTemplate)) {
            return null;
        }

        $values = [];

        $values['groupClass'] = sprintf(
            '%s-block',
            strtolower(StaticFilter::execute($placeholder, CamelCaseToDash::class))
        );

        foreach ($block->fields as $blockField) {
            $values[$blockField->name] = $blockField->value;
        }

        return (new ViewModel($values))->setTemplate(sprintf(
            'boxspaced/cms-block-module/block/%s.phtml',
            str_replace('_', '', $blockTemplate->viewScript)
        ));
    }

}
