<?php
namespace Boxspaced\CmsBlockModule\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Log\Logger;
use Zend\Authentication\AuthenticationService;
use Boxspaced\EntityManager\EntityManager;
use Boxspaced\CmsBlockModule\Model;
use Boxspaced\CmsAccountModule\Model\UserRepository;
use Boxspaced\CmsVersioningModule\Model\VersioningService;
use Boxspaced\CmsWorkflowModule\Model\WorkflowService;
use Boxspaced\CmsCoreModule\Model\EntityFactory;

class BlockServiceFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new BlockService(
            $container->get('Cache\Long'),
            $container->get(Logger::class),
            $container->get(AuthenticationService::class),
            $container->get(EntityManager::class),
            $container->get(Model\BlockTypeRepository::class),
            $container->get(Model\BlockRepository::class),
            $container->get(Model\BlockTemplateRepository::class),
            $container->get(UserRepository::class),
            $container->get(VersioningService::class),
            $container->get(WorkflowService::class),
            $container->get(EntityFactory::class)
        );
    }

}
