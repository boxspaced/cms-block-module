<?php
namespace Block;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Mapper\Conditions\Conditions;
use Zend\Router\Http\Segment;
use Core\Model\RepositoryFactory;
use Account\Model\User;

return [
    'router' => [
        'routes' => [
            // LIFO
            'block' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/block[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\BlockController::class,
                        'action' => 'index',
                    ],
                ],
            ],
            // LIFO
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\BlockService::class => Service\BlockServiceFactory::class,
            Model\BlockRepository::class => RepositoryFactory::class,
            Model\BlockTemplateRepository::class => RepositoryFactory::class,
            Model\BlockTypeRepository::class => RepositoryFactory::class,
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\BlockController::class => Controller\BlockControllerFactory::class,
        ],
    ],
    'controller_plugins' => [
        'aliases' => [
            'blockWidget' => Controller\Plugin\BlockWidget::class,
        ],
        'factories' => [
            Controller\Plugin\BlockWidget::class => Controller\Plugin\BlockWidgetFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'entity_manager' => [
        'types' => [
            Model\Block::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'block',
                        'columns' => [
                            'versionOf' => 'version_of_id',
                            'type' => 'type_id',
                            'author' => 'author_id',
                            'template' => 'template_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'liveFrom' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'expiresEnd' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'workflowStage' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'status' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'authoredTime' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'lastModifiedTime' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'publishedTime' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'rollbackStopPoint' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'versionOf' => [
                            'type' => Model\Block::class,
                        ],
                        'type' => [
                            'type' => Model\BlockType::class,
                        ],
                        'author' => [
                            'type' => User::class,
                        ],
                        'template' => [
                            'type' => Model\BlockTemplate::class,
                        ],
                    ],
                    'children' => [
                        'fields' => [
                            'type' => Model\BlockField::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentBlock.id')->eq($id);
                            },
                        ],
                        'notes' => [
                            'type' => Model\BlockNote::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('parentBlock.id')->eq($id);
                            },
                        ],
                    ],
                ],
            ],
            Model\BlockType::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'block_type',
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'icon' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'description' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                    ],
                    'children' => [
                        'templates' => [
                            'type' => Model\BlockTemplate::class,
                            'conditions' => function ($id) {
                                return (new Conditions())
                                        ->field('forType.id')->eq($id);
                            },
                        ],
                    ],
                ],
            ],
            Model\BlockField::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'block_field',
                        'columns' => [
                            'parentBlock' => 'block_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'value' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'parentBlock' => [
                            'type' => Model\Block::class,
                        ],
                    ],
                ],
            ],
            Model\BlockNote::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'block_note',
                        'columns' => [
                            'parentBlock' => 'block_id',
                            'user' => 'user_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'text' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'createdTime' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'parentBlock' => [
                            'type' => Model\Block::class,
                        ],
                        'user' => [
                            'type' => User::class,
                        ],
                    ],
                ],
            ],
            Model\BlockTemplate::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'block_template',
                        'columns' => [
                            'forType' => 'for_type_id',
                        ],
                    ],
                ],
                'entity' => [
                    'fields' => [
                        'id' => [
                            'type' => AbstractEntity::TYPE_INT,
                        ],
                        'name' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'viewScript' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'description' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'forType' => [
                            'type' => Model\BlockType::class,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
