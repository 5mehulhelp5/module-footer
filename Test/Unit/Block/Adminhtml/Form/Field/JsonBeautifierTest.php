<?php
declare(strict_types=1);

namespace Panth\Footer\Test\Unit\Block\Adminhtml\Form\Field;

use Panth\Footer\Block\Adminhtml\Form\Field\JsonBeautifier;

class JsonBeautifierTest extends FieldBlockTestCase
{
    public function testElementIsConfiguredAsMonospaceTextarea(): void
    {
        $element = $this->element('[]');

        $this->render($this->block(JsonBeautifier::class), $element);

        $this->assertSame('json-beautifier-field', $element->getData('class'));
        $this->assertSame(10, $element->getData('rows'));
        $this->assertStringContainsString('monospace', $element->getData('style'));
    }

    public function testTextareaAttributesAndValueAreEscaped(): void
    {
        $html = $this->render(
            $this->block(JsonBeautifier::class),
            $this->element('[{"title":"<b>x</b>"}]')
        );

        $this->assertStringContainsString('id="footer_col&quot;2"', $html);
        $this->assertStringContainsString('name="groups[column2][fields][links][value]"', $html);
        $this->assertStringContainsString('class="json-beautifier-field"', $html);
        $this->assertStringContainsString('rows="10"', $html);
        $this->assertStringContainsString('&lt;b&gt;x&lt;/b&gt;', $html);
        $this->assertStringNotContainsString('<b>x</b>', $html);
    }

    public function testRendersThreeActionButtonsTargetingTheField(): void
    {
        $html = $this->render($this->block(JsonBeautifier::class), $this->element(''));

        foreach (['beautify', 'minify', 'validate'] as $action) {
            $this->assertStringContainsString(
                'data-action="' . $action . '" data-target="footer_col&quot;2"',
                $html
            );
        }
        $this->assertStringContainsString('Beautify JSON', $html);
        $this->assertStringContainsString('id="footer_col&quot;2_status"', $html);
    }

    public function testAppendsClientScriptOnce(): void
    {
        $html = $this->render($this->block(JsonBeautifier::class), $this->element(''));

        $this->assertSame(1, substr_count($html, '<script>'));
        $this->assertStringContainsString('JSON.stringify(json, null, 4)', $html);
        $this->assertStringEndsWith('</script>', $html);
    }
}
