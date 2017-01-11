<?php
namespace Boxspaced\CmsBlockModule\Form;

use Zend\Form\Form;
use Zend\Form\Element;
use Boxspaced\CmsBlockModule\Service;
use Zend\Filter\StaticFilter;
use Zend\InputFilter\InputFilter;
use Zend\Filter;

class BlockEditForm extends Form
{

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);

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
        $this->add($element);

        $element = new Element\Hidden('partial');
        $this->add($element);

        $fieldsetClass = StaticFilter::execute($this->getName(), Filter\Word\DashToCamelCase::class);
        $fieldsetClass = sprintf('Application\\Form\\%sBlockFieldset', $fieldsetClass);

        if (class_exists($fieldsetClass)) {
            $this->add(new $fieldsetClass('fields'));
        }

        $element = new Element\Textarea('note');
        $element->setLabel('Add a note');
        $element->setAttributes(array(
            'rows' => 4,
            'cols' => 60,
        ));
        $this->add($element);

        $element = new Element\Submit('save');
        $element->setValue('Save');
        $this->add($element);

        $element = new Element\Submit('publish');
        $element->setValue('Publish');
        $this->add($element);
    }

    /**
     * @return ItemEditForm
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
            'name' => 'note',
            'allow_empty' => true,
            'filters' => [
                ['name' => Filter\StringTrim::class],
                ['name' => Filter\StripTags::class],
            ],
        ]);

        return $this->setInputFilter($inputFilter);
    }

    /**
     * @param Service\Block $block
     * @return BlockEditForm
     */
    public function populateFromBlock(Service\Block $block)
    {
        $values = (array) $block;

        $fields = $values['fields'];

        $values['fields'] = [];

        foreach ($fields as $field) {
            $values['fields'][$field->name] = $field->value;
        }

        return parent::populateValues($values);
    }

}
