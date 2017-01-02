<?php
namespace Block\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Block\Service;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

class BlockCreateForm extends Form
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
        parent::__construct('block-create');
        $this->blockService = $blockService;

        $this->setAttribute('method', 'post');
        $this->setAttribute('accept-charset', 'UTF-8');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * @return void
     */
    protected function addElements()
    {
        $element = new Element\Csrf('token');
        $element->setCsrfValidatorOptions([
            'timeout' => 900,
        ]);
        $this->add($element);

        $element = new Element\Text('name');
        $element->setLabel('Name');
        $element->setOption('description', 'a-z, 0-9 and hyphens only');
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Select('typeId');
        $element->setLabel('Type');
        $element->setEmptyOption('');
        $element->setValueOptions($this->getTypeValueOptions());
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Submit('create');
        $element->setValue('Create block');
        $this->add($element);
    }

    /**
     * @return BlockCreateForm
     */
    protected function addInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'name',
            'validators' => [
                [
                    'name' => Validator\Regex::class,
                    'break_chain_on_failure' => true,
                    'options' => [
                        'pattern' => '/^[a-z0-9-]+$/',
                    ],
                ],
                [
                    'name' => Validator\Callback::class,
                    'options' => [
                        'callback' => function($value, $context = []) {
                            return $this->blockService->isNameAvailable($value);
                        },
                        'messages' => [
                            Validator\Callback::INVALID_VALUE => 'The name is already in use',
                        ],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'typeId',
        ]);

        return $this->setInputFilter($inputFilter);
    }

    /**
     * @return array
     */
    protected function getTypeValueOptions()
    {
        $valueOptions = [];

        foreach ($this->blockService->getTypes() as $type) {
            $valueOptions[$type->id] = $type->name;
        }

        return $valueOptions;
    }

}
