<?php
declare(strict_types=1);

namespace Panth\Footer\Test\Unit\Block\Adminhtml\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\AbstractBlock;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

abstract class FieldBlockTestCase extends TestCase
{
    /**
     * Build a block without its template context and give it a plain escaper.
     *
     * @param string $class
     * @return object
     */
    protected function block(string $class): object
    {
        $block = (new ReflectionClass($class))->newInstanceWithoutConstructor();

        $escaper = $this->createStub(Escaper::class);
        $escaper->method('escapeHtmlAttr')->willReturnCallback(
            static fn($value) => htmlspecialchars((string)$value, ENT_QUOTES)
        );
        $escaper->method('escapeHtml')->willReturnCallback(
            static fn($value) => htmlspecialchars((string)$value, ENT_QUOTES)
        );

        (new ReflectionProperty(AbstractBlock::class, '_escaper'))->setValue($block, $escaper);

        return $block;
    }

    /**
     * Call the protected _getElementHtml() method.
     *
     * @param object $block
     * @param AbstractElement $element
     * @return string
     */
    protected function render(object $block, AbstractElement $element): string
    {
        return (new ReflectionMethod($block, '_getElementHtml'))->invoke($block, $element);
    }

    /**
     * Form element double with real data storage and a fixed id and name.
     *
     * @param mixed $value
     * @return AbstractElement
     */
    protected function element($value): AbstractElement
    {
        $element = new FormElementDouble();
        $element->setData('value', $value);

        return $element;
    }
}
