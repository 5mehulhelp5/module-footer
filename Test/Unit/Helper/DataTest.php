<?php
declare(strict_types=1);

namespace Panth\Footer\Test\Unit\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Panth\Footer\Helper\Data;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DataTest extends TestCase
{
    /**
     * @param array $values config path => value
     * @param LoggerInterface|null $logger
     * @return Data
     */
    private function helper(array $values = [], ?LoggerInterface $logger = null): Data
    {
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnCallback(
            static fn(string $path) => $values[$path] ?? null
        );

        $context = $this->createStub(Context::class);
        $context->method('getScopeConfig')->willReturn($scopeConfig);
        $context->method('getLogger')->willReturn($logger ?? $this->createStub(LoggerInterface::class));

        return new Data($context, new Json());
    }

    public function testGetConfigValuePrefixesPathAndPassesStoreScope(): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('panth_footer/general/layout', ScopeInterface::SCOPE_STORE, 7)
            ->willReturn('3');
        $context = $this->createStub(Context::class);
        $context->method('getScopeConfig')->willReturn($scopeConfig);

        $helper = new Data($context, new Json());

        $this->assertSame('3', $helper->getConfigValue('general/layout', 7));
    }

    public function testIsEnabledReflectsConfigFlag(): void
    {
        $this->assertTrue($this->helper(['panth_footer/general/enabled' => '1'])->isEnabled());
        $this->assertFalse($this->helper(['panth_footer/general/enabled' => '0'])->isEnabled());
        $this->assertFalse($this->helper()->isEnabled());
    }

    public function testLayoutDefaultsToFourWhenUnsetOrZero(): void
    {
        $this->assertSame(4, $this->helper()->getLayout());
        $this->assertSame(4, $this->helper(['panth_footer/general/layout' => '0'])->getLayout());
        $this->assertSame(2, $this->helper(['panth_footer/general/layout' => '2'])->getLayout());
    }

    #[DataProvider('gridClassProvider')]
    public function testGridClassesFollowLayout(?string $layout, string $expected): void
    {
        $this->assertSame($expected, $this->helper(['panth_footer/general/layout' => $layout])->getGridClasses());
    }

    public static function gridClassProvider(): array
    {
        return [
            'two' => ['2', 'grid-cols-1 md:grid-cols-2'],
            'three' => ['3', 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'],
            'four' => ['4', 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4'],
            'unset falls back to four' => [null, 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4'],
            'unknown falls back to four' => ['7', 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4'],
        ];
    }

    public function testColumnOneDataContainsLogoContentAndSocial(): void
    {
        $data = $this->helper([
            'panth_footer/column1/enabled' => '1',
            'panth_footer/column1/title' => 'About',
            'panth_footer/column1/show_logo' => '1',
            'panth_footer/column1/content' => 'We sell things',
            'panth_footer/column1/show_social' => '0',
        ])->getColumnData(1);

        $this->assertSame([
            'enabled' => true,
            'title' => 'About',
            'show_logo' => true,
            'content' => 'We sell things',
            'show_social' => false,
        ], $data);
    }

    public function testColumnFourDataContainsContactInfo(): void
    {
        $data = $this->helper([
            'panth_footer/column4/enabled' => '0',
            'panth_footer/column4/show_contact_info' => '1',
            'panth_footer/column4/phone' => '+44 1234',
            'panth_footer/column4/email' => 'shop@example.com',
            'panth_footer/column4/address' => '1 High St',
            'panth_footer/column4/working_hours' => '9-5',
        ])->getColumnData(4);

        $this->assertFalse($data['enabled']);
        $this->assertSame('', $data['title']);
        $this->assertTrue($data['show_contact_info']);
        $this->assertSame('+44 1234', $data['phone']);
        $this->assertSame('shop@example.com', $data['email']);
        $this->assertSame('1 High St', $data['address']);
        $this->assertSame('9-5', $data['working_hours']);
        $this->assertArrayNotHasKey('links', $data);
    }

    public function testMiddleColumnLinksAreNormalized(): void
    {
        $json = json_encode([
            ['title' => ' Shipping ', 'url' => '/shipping', 'target' => '_blank'],
            ['title' => 'Bad scheme', 'url' => 'javascript:alert(1)', 'target' => 'evil'],
            ['title' => 'No url'],
            ['title' => '   ', 'url' => '/skipped'],
            ['url' => '/no-title'],
            'not-an-array',
            ['title' => ['nested'], 'url' => '/array-title'],
            ['title' => 42, 'url' => 'mailto:shop@example.com', 'target' => '_top'],
        ]);

        $data = $this->helper(['panth_footer/column2/links' => $json])->getColumnData(2);

        $this->assertSame([
            ['title' => 'Shipping', 'url' => '/shipping', 'target' => '_blank'],
            ['title' => 'Bad scheme', 'url' => '#', 'target' => ''],
            ['title' => 'No url', 'url' => '#', 'target' => ''],
            ['title' => '42', 'url' => 'mailto:shop@example.com', 'target' => '_top'],
        ], $data['links']);
        $this->assertArrayNotHasKey('show_logo', $data);
    }

    public function testEmptyLinksJsonGivesEmptyList(): void
    {
        $this->assertSame([], $this->helper()->getColumnData(3)['links']);
        $this->assertSame([], $this->helper(['panth_footer/column3/links' => ''])->getColumnData(3)['links']);
    }

    public function testInvalidLinksJsonIsLoggedAndIgnored(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with($this->stringStartsWith('Footer links JSON parse error'));

        $helper = $this->helper(['panth_footer/column2/links' => '{not json'], $logger);

        $this->assertSame([], $helper->getColumnData(2)['links']);
    }

    public function testScalarLinksJsonGivesEmptyList(): void
    {
        $helper = $this->helper(['panth_footer/bottom/footer_links' => '"just a string"']);

        $this->assertSame([], $helper->getFooterLinks());
    }

    public function testFooterLinksReadBottomSection(): void
    {
        $json = json_encode([['title' => 'Privacy', 'url' => 'https://example.com/privacy']]);

        $this->assertSame(
            [['title' => 'Privacy', 'url' => 'https://example.com/privacy', 'target' => '']],
            $this->helper(['panth_footer/bottom/footer_links' => $json])->getFooterLinks()
        );
    }

    #[DataProvider('safeUrlProvider')]
    public function testGetSafeUrl(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->helper()->getSafeUrl($input));
    }

    public static function safeUrlProvider(): array
    {
        return [
            'empty' => ['', ''],
            'whitespace only' => ['   ', ''],
            'relative path kept' => ['/about-us', '/about-us'],
            'anchor kept' => ['#top', '#top'],
            'https kept and trimmed' => ['  https://example.com/a  ', 'https://example.com/a'],
            'http kept' => ['http://example.com', 'http://example.com'],
            'mailto kept' => ['mailto:a@example.com', 'mailto:a@example.com'],
            'tel kept' => ['tel:+441234', 'tel:+441234'],
            'uppercase scheme kept' => ['HTTPS://example.com', 'HTTPS://example.com'],
            'javascript rejected' => ['javascript:alert(1)', ''],
            'mixed case javascript rejected' => ['JavaScript:alert(1)', ''],
            'data rejected' => ['data:text/html;base64,AAAA', ''],
            'tab-split scheme rejected' => ["java\tscript:alert(1)", ''],
            'newline-split scheme rejected' => ["java\nscript:alert(1)", ''],
            'protocol relative kept' => ['//cdn.example.com/x', '//cdn.example.com/x'],
        ];
    }

    public function testGetSafeUrlHonoursCustomSchemeList(): void
    {
        $helper = $this->helper();

        $this->assertSame('', $helper->getSafeUrl('mailto:a@example.com', ['http', 'https']));
        $this->assertSame('https://x.test', $helper->getSafeUrl('https://x.test', ['http', 'https']));
    }

    public function testAllowedHtmlTagsAreInlineOnly(): void
    {
        $tags = $this->helper()->getAllowedHtmlTags();

        $this->assertContains('a', $tags);
        $this->assertContains('strong', $tags);
        $this->assertContains('br', $tags);
        $this->assertNotContains('script', $tags);
        $this->assertNotContains('iframe', $tags);
    }

    public function testSocialLinksKeepOnlyHttpUrlsInPlatformOrder(): void
    {
        $links = $this->helper([
            'panth_footer/social/youtube' => 'https://youtube.com/shop',
            'panth_footer/social/facebook' => 'https://facebook.com/shop',
            'panth_footer/social/twitter' => 'mailto:x@example.com',
            'panth_footer/social/instagram' => 'javascript:alert(1)',
            'panth_footer/social/linkedin' => '',
            'panth_footer/social/pinterest' => '/relative',
        ])->getSocialLinks();

        $this->assertSame([
            'facebook' => 'https://facebook.com/shop',
            'youtube' => 'https://youtube.com/shop',
            'pinterest' => '/relative',
        ], $links);
    }

    public function testSocialLinksEmptyWhenNothingConfigured(): void
    {
        $this->assertSame([], $this->helper()->getSocialLinks());
    }

    public function testSocialIconReturnsSvgForKnownPlatformsOnly(): void
    {
        $helper = $this->helper();

        foreach (['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'pinterest'] as $platform) {
            $icon = $helper->getSocialIcon($platform);
            $this->assertStringStartsWith('<svg', $icon, $platform);
            $this->assertStringEndsWith('</svg>', $icon, $platform);
        }
        $this->assertSame('', $helper->getSocialIcon('myspace'));
        $this->assertSame('', $helper->getSocialIcon(''));
    }

    public function testCopyrightReplacesYearPlaceholder(): void
    {
        $helper = $this->helper(['panth_footer/bottom/copyright_text' => '(c) {{year}} Shop. {{year}}']);

        $year = date('Y');
        $this->assertSame('(c) ' . $year . ' Shop. ' . $year, $helper->getCopyrightText());
        $this->assertSame('', $this->helper()->getCopyrightText());
    }

    public function testBottomFlags(): void
    {
        $on = $this->helper([
            'panth_footer/bottom/show_payment_icons' => '1',
            'panth_footer/bottom/show_footer_links' => '1',
        ]);
        $off = $this->helper();

        $this->assertTrue($on->showPaymentIcons());
        $this->assertTrue($on->showFooterLinks());
        $this->assertFalse($off->showPaymentIcons());
        $this->assertFalse($off->showFooterLinks());
    }

    public function testNewsletterTextSettings(): void
    {
        $helper = $this->helper([
            'panth_footer/newsletter/enabled' => '1',
            'panth_footer/newsletter/title' => 'Join us',
            'panth_footer/newsletter/subtitle' => '',
            'panth_footer/newsletter/placeholder_text' => 'Your email',
            'panth_footer/newsletter/button_text' => 'Go',
        ]);

        $this->assertTrue($helper->isNewsletterEnabled());
        $this->assertSame('Join us', $helper->getNewsletterTitle());
        $this->assertSame('', $helper->getNewsletterSubtitle());
        $this->assertSame('Your email', $helper->getNewsletterPlaceholder());
        $this->assertSame('Go', $helper->getNewsletterButtonText());
    }

    public function testNewsletterDefaultsWhenUnset(): void
    {
        $helper = $this->helper();

        $this->assertFalse($helper->isNewsletterEnabled());
        $this->assertNull($helper->getNewsletterTitle());
        $this->assertNull($helper->getNewsletterSubtitle());
        $this->assertSame('Enter your email address', $helper->getNewsletterPlaceholder());
        $this->assertSame('Subscribe', $helper->getNewsletterButtonText());
    }

    public function testNewsletterBenefitsKeepNonEmptyScalars(): void
    {
        $json = json_encode(['Free shipping', '', '  ', 10, ['nested'], null, 'Early access']);

        $this->assertSame(
            ['Free shipping', '10', 'Early access'],
            $this->helper(['panth_footer/newsletter/benefits' => $json])->getNewsletterBenefits()
        );
    }

    public function testNewsletterBenefitsEmptyCases(): void
    {
        $this->assertSame([], $this->helper()->getNewsletterBenefits());
        $this->assertSame([], $this->helper(['panth_footer/newsletter/benefits' => '123'])->getNewsletterBenefits());
    }

    public function testInvalidNewsletterBenefitsJsonIsLogged(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with($this->stringStartsWith('Newsletter benefits JSON parse error'));

        $this->assertSame(
            [],
            $this->helper(['panth_footer/newsletter/benefits' => '[broken'], $logger)->getNewsletterBenefits()
        );
    }

    public function testBackToTopEnabledFlag(): void
    {
        $this->assertTrue($this->helper(['panth_footer/back_to_top/enabled' => '1'])->isBackToTopEnabled());
        $this->assertFalse($this->helper()->isBackToTopEnabled());
    }

    #[DataProvider('positionProvider')]
    public function testBackToTopPosition(?string $stored, string $expected): void
    {
        $this->assertSame(
            $expected,
            $this->helper(['panth_footer/back_to_top/position' => $stored])->getBackToTopPosition()
        );
    }

    public static function positionProvider(): array
    {
        return [
            'right' => ['bottom-right', 'bottom-right'],
            'left' => ['bottom-left', 'bottom-left'],
            'legacy 1 maps to right' => ['1', 'bottom-right'],
            'unset defaults to right' => [null, 'bottom-right'],
            'unknown defaults to right' => ['top-center', 'bottom-right'],
        ];
    }

    #[DataProvider('themeColorProvider')]
    public function testThemeColorsUseConfiguredValueOrDefault(string $method, string $path, string $default): void
    {
        $this->assertSame($default, $this->helper()->$method());
        $this->assertSame('#123456', $this->helper(['theme_customizer/' . $path => '#123456'])->$method());
    }

    public static function themeColorProvider(): array
    {
        return [
            ['getBackToTopBgColor', 'back_to_top/bg_color', '#1a1a2e'],
            ['getBackToTopIconColor', 'back_to_top/icon_color', '#ffffff'],
            ['getBackToTopHoverBgColor', 'back_to_top/hover_bg_color', '#16213e'],
            ['getBackToTopHoverIconColor', 'back_to_top/hover_icon_color', '#ffffff'],
            ['getBackgroundColor', 'footer/bg_color', '#1a1a2e'],
            ['getTextColor', 'footer/text_color', '#a0a0b0'],
            ['getH2Color', 'footer/h2_color', '#ffffff'],
            ['getH3Color', 'footer/h3_color', '#ffffff'],
            ['getNewsletterBackgroundColor', 'newsletter/bg_color', '#16213e'],
            ['getNewsletterTitleColor', 'newsletter/title_color', '#ffffff'],
            ['getNewsletterTextColor', 'newsletter/text_color', '#a0a0b0'],
            ['getNewsletterButtonColor', 'newsletter/button_color', '#e94560'],
            ['getNewsletterButtonTextColor', 'newsletter/button_text_color', '#ffffff'],
            ['getNewsletterButtonHoverColor', 'newsletter/button_hover_color', '#d63851'],
            ['getNewsletterInputBgColor', 'newsletter/input_bg_color', '#0f3460'],
            ['getNewsletterInputTextColor', 'newsletter/input_text_color', '#ffffff'],
            ['getNewsletterInputBorderColor', 'newsletter/input_border_color', '#1a4a7a'],
            ['getNewsletterInputFocusBorderColor', 'newsletter/input_focus_border_color', '#e94560'],
        ];
    }
}
