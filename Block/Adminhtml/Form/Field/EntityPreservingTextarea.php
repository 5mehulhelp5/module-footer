<?php
declare(strict_types=1);

namespace Panth\Footer\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class EntityPreservingTextarea extends Field
{
    protected function _getElementHtml(AbstractElement $element): string
    {
        $value = $element->getValue();
        if (is_string($value) && $value !== '') {
            $element->setValue(str_replace('&', '&amp;', $value));
        }

        return parent::_getElementHtml($element);
    }
}
