<?php
namespace Boxspaced\CmsBlockModule\Form;

use DateTime;
use Zend\Form\Form;
use Zend\Form\Element;
use Boxspaced\CmsBlockModule\Service;
use Zend\InputFilter\InputFilter;
use Zend\Validator;
use Zend\Filter;

class BlockPublishForm extends Form
{

    /**
     * @var int
     */
    protected $blockId;

    /**
     * @var Service\BlockService
     */
    protected $blockService;

    /**
     * @param int $blockId
     * @param Service\BlockService $blockService
     */
    public function __construct(
        $blockId,
        Service\BlockService $blockService
    )
    {
        parent::__construct('block-publish');
        $this->blockId = $blockId;
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

        $element = new Element\Hidden('id');
        $element->setValue($this->blockId);
        $this->add($element);

        $element = new Element\Hidden('partial');
        $this->add($element);

        $element = new Element\Text('name');
        $element->setLabel('Name');
        $element->setOption('description', 'a-z, 0-9 and hyphens only');
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Text('liveFrom');
        $element->setLabel('Live from');
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Text('expiresEnd');
        $element->setLabel('Expires end');
        $element->setAttribute('required', true);
        $this->add($element);

        $templateValueOptions = $this->getTemplateValueOptions();

        $element = new Element\Select('templateId');
        $element->setLabel('Template');
        $element->setEmptyOption('');
        $element->setValueOptions($templateValueOptions);
        if (1 === count($templateValueOptions)) {
            $element->setValue(key($templateValueOptions));
        }
        $element->setAttribute('required', true);
        $element->setOption('description', 'Template description: ');
        $this->add($element);

        $element = new Element\Submit('publish');
        $element->setValue('Publish');
        $this->add($element);
    }

    /**
     * @return BlockPublishForm
     */
    protected function addInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'filters' => [
                ['name' => Filter\ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'partial',
            'allow_empty' => true,
            'filters' => [
                ['name' => Filter\Boolean::class],
            ],
        ]);

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

                            if ($this->getCurrentName() !== $value) {
                                return $this->blockService->isNameAvailable($value);
                            }

                            return true;
                        },
                        'messages' => [
                            Validator\Callback::INVALID_VALUE => 'The name is already in use',
                        ],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'liveFrom',
            'validators' => [
                [
                    'name' => Validator\Regex::class,
                    'options' => [
                        'pattern' => '/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/',
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'expiresEnd',
            'validators' => [
                [
                    'name' => Validator\Regex::class,
                    'options' => [
                        'pattern' => '/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/',
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'templateId',
        ]);

        return $this->setInputFilter($inputFilter);
    }

    /**
     * @return Service\BlockType
     */
    protected function getBlockType()
    {
        $meta = $this->blockService->getBlockMeta($this->blockId);
        return $this->blockService->getType($meta->typeId);
    }

    /**
     * @return string
     */
    protected function getCurrentName()
    {
        $meta = $this->blockService->getBlockMeta($this->blockId);
        return $meta->name;
    }

    /**
     * @return array
     */
    protected function getTemplateValueOptions()
    {
        $type = $this->getBlockType();

        $valueOptions = [];

        foreach ($type->templates as $template) {
            $valueOptions[$template->id] = $template->name;
        }

        return $valueOptions;
    }

    /**
     * @param Service\PublishingOptions $options
     * @return BlockPublishForm
     */
    public function populateFromPublishingOptions(Service\PublishingOptions $options)
    {
        $values = (array) $options;

        $values['liveFrom'] = ($values['liveFrom'] instanceof DateTime) ? $values['liveFrom']->format('Y-m-d H:i:s') : '';
        $values['expiresEnd'] = ($values['expiresEnd'] instanceof DateTime) ? $values['expiresEnd']->format('Y-m-d H:i:s') : '';

        return parent::populateValues($values);
    }

}
