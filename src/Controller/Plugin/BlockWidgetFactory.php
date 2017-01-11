<?php
namespace Boxspaced\CmsBlockModule\Controller\Plugin;

use Zend\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;
use Boxspaced\CmsBlockModule\Service\BlockService;

class BlockWidgetFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new BlockWidget($container->get(BlockService::class));
    }

}
