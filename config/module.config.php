<?php
namespace Boxspaced\CmsBlockModule;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Zend\Router\Http\Segment;
use Boxspaced\CmsCoreModule\Model\RepositoryFactory;
use Boxspaced\CmsAccountModule\Model\User;
use Zend\Permissions\Acl\Acl;

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
    'acl' => [
        'resources' => [
            [
                'id' => Controller\BlockController::class,
            ],
        ],
        'rules' => [
            [
                'type' => Acl::TYPE_ALLOW,
                'roles' => 'author',
                'resources' => Controller\BlockController::class,
                'privileges' => [
                    'create',
                    'edit',
                    'index',
                ],
            ],
            [
                'type' => Acl::TYPE_ALLOW,
                'roles' => 'publisher',
                'resources' => Controller\BlockController::class,
                'privileges' => [
                    'publish',
                    'delete',
                    'publish-update',
                ],
            ],
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
                            'version_of' => 'version_of_id',
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
                        'live_from' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'expires_end' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'workflow_stage' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'status' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'authored_time' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'last_modified_time' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'published_time' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'rollback_stop_point' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'version_of' => [
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
                    'one_to_many' => [
                        'fields' => [
                            'type' => Model\BlockField::class,
                        ],
                        'notes' => [
                            'type' => Model\BlockNote::class,
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
                    'one_to_many' => [
                        'templates' => [
                            'type' => Model\BlockTemplate::class,
                        ],
                    ],
                ],
            ],
            Model\BlockField::class => [
                'mapper' => [
                    'params' => [
                        'table' => 'block_field',
                        'columns' => [
                            'parent_block' => 'block_id',
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
                        'parent_block' => [
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
                            'parent_block' => 'block_id',
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
                        'created_time' => [
                            'type' => AbstractEntity::TYPE_DATETIME,
                        ],
                        'parent_block' => [
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
                            'for_type' => 'for_type_id',
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
                        'view_script' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'description' => [
                            'type' => AbstractEntity::TYPE_STRING,
                        ],
                        'for_type' => [
                            'type' => Model\BlockType::class,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
