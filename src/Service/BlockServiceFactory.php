<?php
namespace Block\Service;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Zend\Log\Logger;
use Zend\Authentication\AuthenticationService;
use Boxspaced\EntityManager\EntityManager;
use Block\Model;
use Account\Model\UserRepository;
use Versioning\Model\VersioningService;
use Workflow\Model\WorkflowService;
use Core\Model\EntityFactory;

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
