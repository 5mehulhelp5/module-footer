<?php
declare(strict_types=1);

namespace Panth\Footer\Test\Unit;

use Panth\Footer\Model\Config\Backend\Json;
use Panth\Footer\Observer\AddEnabledHandle;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ModuleConfigTest extends TestCase
{
    private static function moduleDir(): string
    {
        return dirname(__DIR__, 2);
    }

    private static function loadXml(string $relativePath): \SimpleXMLElement
    {
        $file = self::moduleDir() . '/' . $relativePath;
        self::assertFileExists($file);
        $xml = simplexml_load_string((string)file_get_contents($file));
        self::assertInstanceOf(\SimpleXMLElement::class, $xml, $relativePath . ' must be valid XML');
        return $xml;
    }

    public function testObserverIsRegisteredOnLayoutLoadBefore(): void
    {
        $xml = self::loadXml('etc/frontend/events.xml');
        $observers = $xml->xpath('//event[@name="layout_load_before"]/observer');

        $this->assertCount(1, $observers);
        $this->assertSame(AddEnabledHandle::class, (string)$observers[0]['instance']);
    }

    public function testNativeFooterBlocksAreOnlyRemovedInEnabledHandle(): void
    {
        $default = self::loadXml('view/frontend/layout/default.xml');
        $this->assertSame([], $default->xpath('//*[@remove="true"]'));
        $this->assertSame([], $default->xpath('//head'));

        $enabled = self::loadXml('view/frontend/layout/' . AddEnabledHandle::HANDLE . '.xml');
        $removed = array_map(
            static fn(\SimpleXMLElement $node) => (string)$node['name'],
            $enabled->xpath('//*[@remove="true"]')
        );

        foreach (['footer_links', 'copyright', 'form.subscribe', 'footer-content', 'footer.newsletter'] as $name) {
            $this->assertContains($name, $removed);
        }
        $this->assertCount(2, $enabled->xpath('//head/css'));
    }

    public function testFooterBlocksStayDeclaredInDefaultHandle(): void
    {
        $default = self::loadXml('view/frontend/layout/default.xml');
        foreach (['panth.footer', 'panth.footer.newsletter', 'panth.footer.logo', 'panth.footer.back_to_top'] as $name) {
            $this->assertCount(1, $default->xpath('//block[@name="' . $name . '"]'), $name);
        }
    }

    #[DataProvider('jsonFields')]
    public function testJsonFieldsUseValidatingBackend(string $group, string $field): void
    {
        $xml = self::loadXml('etc/adminhtml/system.xml');
        $nodes = $xml->xpath(
            '//section[@id="panth_footer"]/group[@id="' . $group . '"]/field[@id="' . $field . '"]/backend_model'
        );

        $this->assertCount(1, $nodes);
        $this->assertSame(Json::class, (string)$nodes[0]);
    }

    public static function jsonFields(): array
    {
        return [
            ['column2', 'links'],
            ['column3', 'links'],
            ['newsletter', 'benefits'],
            ['bottom', 'footer_links'],
        ];
    }

    #[DataProvider('defaultLinkPaths')]
    public function testDefaultLinksPointToCoreStorefrontPages(string $path): void
    {
        $xml = self::loadXml('etc/config.xml');
        $nodes = $xml->xpath('//default/panth_footer/' . $path);
        $this->assertCount(1, $nodes);

        $links = json_decode((string)$nodes[0], true, 512, JSON_THROW_ON_ERROR);
        $this->assertNotEmpty($links);

        $corePages = [
            '/contact', '/customer-service', '/about-us', '/sales/guest/form/', '/customer/account/',
            '/checkout/cart/', '/search/term/popular/', '/catalogsearch/advanced/',
            '/privacy-policy-cookie-restriction-mode', '/enable-cookies',
        ];
        foreach ($links as $link) {
            $this->assertNotSame('', trim((string)$link['title']));
            $this->assertContains($link['url'], $corePages, 'Default link must not 404 on a fresh store');
        }
    }

    public static function defaultLinkPaths(): array
    {
        return [
            ['column2/links'],
            ['column3/links'],
            ['bottom/footer_links'],
        ];
    }
}
