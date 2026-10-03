<?php
declare(strict_types=1);

namespace Panth\Footer\Test\Unit\ViewModel;

use Panth\Footer\Helper\Data as FooterHelper;
use Panth\Footer\ViewModel\FooterData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FooterDataTest extends TestCase
{
    /**
     * @param string $method
     * @param mixed $value
     */
    #[DataProvider('delegationProvider')]
    public function testNoArgumentMethodsReturnHelperValue(string $method, $value): void
    {
        $helper = $this->createStub(FooterHelper::class);
        $helper->method($method)->willReturn($value);

        $this->assertSame($value, (new FooterData($helper))->$method());
    }

    public static function delegationProvider(): array
    {
        return [
            ['isEnabled', true],
            ['getLayout', 3],
            ['getGridClasses', 'grid-cols-1 md:grid-cols-2'],
            ['getSocialLinks', ['facebook' => 'https://facebook.com/x']],
            ['getCopyrightText', '(c) 2026'],
            ['showPaymentIcons', true],
            ['showFooterLinks', false],
            ['getFooterLinks', [['title' => 'A', 'url' => '#', 'target' => '']]],
            ['isNewsletterEnabled', true],
            ['getNewsletterTitle', 'Join'],
            ['getNewsletterTitle', null],
            ['getNewsletterSubtitle', 'Sub'],
            ['getNewsletterBenefits', ['Free shipping']],
            ['getNewsletterPlaceholder', 'Email'],
            ['getNewsletterButtonText', 'Go'],
            ['isBackToTopEnabled', false],
            ['getBackToTopPosition', 'bottom-left'],
            ['getAllowedHtmlTags', ['a', 'b']],
        ];
    }

    public function testColumnDataPassesColumnNumber(): void
    {
        $helper = $this->createMock(FooterHelper::class);
        $helper->expects($this->once())
            ->method('getColumnData')
            ->with(2)
            ->willReturn(['enabled' => true, 'links' => []]);

        $this->assertSame(['enabled' => true, 'links' => []], (new FooterData($helper))->getColumnData(2));
    }

    public function testSocialIconPassesPlatform(): void
    {
        $helper = $this->createMock(FooterHelper::class);
        $helper->expects($this->once())
            ->method('getSocialIcon')
            ->with('youtube')
            ->willReturn('<svg></svg>');

        $this->assertSame('<svg></svg>', (new FooterData($helper))->getSocialIcon('youtube'));
    }
}
