<?php
declare(strict_types=1);

namespace Panth\Footer\Test\Unit\Block\Adminhtml\Form\Field;

use Panth\Footer\Block\Adminhtml\Form\Field\EntityPreservingTextarea;

class EntityPreservingTextareaTest extends FieldBlockTestCase
{
    public function testAmpersandsAreDoubleEncodedSoEntitiesSurviveTheTextarea(): void
    {
        $element = $this->element('&copy; 2026 Tom &amp; Co');

        $html = $this->render($this->block(EntityPreservingTextarea::class), $element);

        $this->assertSame('&amp;copy; 2026 Tom &amp;amp; Co', $element->getData('value'));
        $this->assertSame('<textarea>&amp;copy; 2026 Tom &amp;amp; Co</textarea>', $html);
    }

    public function testValueWithoutAmpersandIsUnchanged(): void
    {
        $element = $this->element('Plain text');

        $this->render($this->block(EntityPreservingTextarea::class), $element);

        $this->assertSame('Plain text', $element->getData('value'));
    }

    public function testEmptyAndNonStringValuesAreLeftAlone(): void
    {
        $empty = $this->element('');
        $array = $this->element(['a&b']);

        $this->render($this->block(EntityPreservingTextarea::class), $empty);
        $this->render($this->block(EntityPreservingTextarea::class), $array);

        $this->assertSame('', $empty->getData('value'));
        $this->assertSame(['a&b'], $array->getData('value'));
    }
}
