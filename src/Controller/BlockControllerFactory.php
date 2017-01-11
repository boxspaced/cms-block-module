<?php
namespace Boxspaced\CmsBlockModule\Controller;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Boxspaced\CmsBlockModule\Controller\BlockController;
use Boxspaced\CmsBlockModule\Service\BlockService;
use Boxspaced\CmsWorkflowModule\Service\WorkflowService;
use Boxspaced\CmsAccountModule\Service\AccountService;
use Zend\Log\Logger;
use Boxspaced\CmsCoreModule\Controller\AbstractControllerFactory;

class BlockControllerFactory extends AbstractControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controller = new BlockController(
            $container->get(BlockService::class),
            $container->get(WorkflowService::class),
            $container->get(AccountService::class),
            $container->get(Logger::class),
            $container->get('config')
        );

        return $this->forceHttps($controller, $container);
    }

}
