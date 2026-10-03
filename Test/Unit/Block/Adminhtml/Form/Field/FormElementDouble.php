<?php
declare(strict_types=1);

namespace Panth\Footer\Test\Unit\Block\Adminhtml\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Element double: keeps DataObject storage but skips the factory-heavy constructor.
 * getElementHtml() echoes the stored value so tests can see what the parent rendered.
 */
class FormElementDouble extends AbstractElement
{
    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->_data = $data;
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return 'footer_col"2';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'groups[column2][fields][links][value]';
    }

    /**
     * @param string|null $index
     * @return string
     */
    public function getEscapedValue($index = null)
    {
        $value = $this->getData('value');

        return htmlspecialchars(is_scalar($value) ? (string)$value : '', ENT_QUOTES);
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $value = $this->getData('value');

        return '<textarea>' . (is_scalar($value) ? (string)$value : '') . '</textarea>';
    }
}
