<?php
namespace Block\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Form\Element;
use Zend\Filter;

class HtmlBlockFieldset extends Fieldset implements InputFilterProviderInterface
{

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $element = new Element\Textarea('html');
        $element->setLabel('Html');
        $element->setAttributes([
            'class' => 'wysiwyg',
            'rows' => 4,
            'cols' => 60,
        ]);
        $this->add($element);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'html' => [
                'allow_empty' => true,
                'filters' => [
                    ['name' => Filter\StringTrim::class],
                ],
            ],
        ];
    }

}
