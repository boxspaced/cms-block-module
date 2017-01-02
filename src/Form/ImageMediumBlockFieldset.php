<?php
namespace Block\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Filter;

class ImageMediumBlockFieldset extends Fieldset implements InputFilterProviderInterface
{

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $element = new Element\Text('src');
        $element->setLabel('Image');
        $element->setOption('description', 'Max width 280px');
        $element->setAttribute('required', true);
        $element->setAttribute('class', 'image-browser');
        $this->add($element);

        $element = new Element\Text('alt');
        $element->setLabel('Alternative text');
        $this->add($element);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'src' => [
                'filters' => [
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StripTags::class],
                ],
            ],
            'alt' => [
                'filters' => [
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StripTags::class],
                ],
            ],
        ];
    }

}
