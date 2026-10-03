<?php
declare(strict_types=1);

namespace Panth\Footer\Test\Unit\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Panth\Footer\Model\Config\Backend\Json;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    private function backend(?string $value, array $data = []): Json
    {
        $context = $this->createStub(Context::class);
        $context->method('getEventDispatcher')->willReturn($this->createStub(ManagerInterface::class));
        $backend = new Json(
            $context,
            $this->createStub(Registry::class),
            $this->createStub(ScopeConfigInterface::class),
            $this->createStub(TypeListInterface::class),
            null,
            null,
            $data
        );
        $backend->setValue($value);
        return $backend;
    }

    public function testPrettyPrintedLinksAreStoredCompactAndUnchanged(): void
    {
        $compact = '[{"title":"New Arrivals","url":"/what-is-new.html"},{"title":"Sale","url":"/sale.html"}]';
        $pretty = json_encode(json_decode($compact, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->assertNotSame($compact, $pretty);

        $backend = $this->backend($pretty);
        $backend->beforeSave();

        $this->assertSame($compact, $backend->getValue());
    }

    public function testCompactValueSurvivesSaveByteForByte(): void
    {
        $value = '["Exclusive offers","New arrivals first","Member-only sales"]';
        $backend = $this->backend($value);
        $backend->beforeSave();

        $this->assertSame($value, $backend->getValue());
    }

    public function testSlashesAndUnicodeAreNotEscaped(): void
    {
        $backend = $this->backend("[ {\"title\": \"Caf\u{e9}\", \"url\": \"https://example.com/a/b\"} ]");
        $backend->beforeSave();

        $this->assertSame("[{\"title\":\"Caf\u{e9}\",\"url\":\"https://example.com/a/b\"}]", $backend->getValue());
    }

    public function testEscapedUnicodeIsNormalisedToUtf8(): void
    {
        $backend = $this->backend('["Caf\u00e9"]');
        $backend->beforeSave();

        $this->assertSame("[\"Caf\u{e9}\"]", $backend->getValue());
    }

    #[DataProvider('emptyValues')]
    public function testEmptyValueIsStoredAsEmptyString(?string $value): void
    {
        $backend = $this->backend($value);
        $backend->beforeSave();

        $this->assertSame('', $backend->getValue());
    }

    public static function emptyValues(): array
    {
        return [
            'null' => [null],
            'empty' => [''],
            'whitespace' => ["  \n\t "],
        ];
    }

    public function testEmptyListIsKept(): void
    {
        $backend = $this->backend('[ ]');
        $backend->beforeSave();

        $this->assertSame('[]', $backend->getValue());
    }

    public function testInvalidJsonIsRejectedWithFieldLabel(): void
    {
        $backend = $this->backend('[{"title":"Broken",}]', ['field_config' => ['label' => 'Links (JSON Format)']]);

        try {
            $backend->beforeSave();
            $this->fail('Invalid JSON must be rejected.');
        } catch (LocalizedException $e) {
            $this->assertStringContainsString('"Links (JSON Format)" must contain valid JSON', $e->getMessage());
        }
        $this->assertSame('[{"title":"Broken",}]', $backend->getValue());
    }

    #[DataProvider('scalarJson')]
    public function testScalarJsonIsRejected(string $value): void
    {
        $backend = $this->backend($value, ['path' => 'panth_footer/column2/links']);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('"panth_footer/column2/links" must contain a JSON list.');
        $backend->beforeSave();
    }

    public static function scalarJson(): array
    {
        return [
            'string' => ['"just text"'],
            'number' => ['42'],
            'boolean' => ['true'],
            'null literal' => ['null'],
        ];
    }
}
