<?php
namespace Block\Controller\Plugin;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Block\Service\BlockService;

class BlockWidgetFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new BlockWidget($container->get(BlockService::class));
    }

}
