<?php
declare(strict_types=1);

namespace Panth\Footer\Test\Unit\Model\Config\Source;

use Panth\Footer\Model\Config\Source\Layout;
use Panth\Footer\Model\Config\Source\Position;
use PHPUnit\Framework\TestCase;

class SourceModelsTest extends TestCase
{
    public function testLayoutOffersTwoToFourColumns(): void
    {
        $options = (new Layout())->toOptionArray();

        $this->assertSame(['2', '3', '4'], array_column($options, 'value'));
        $this->assertSame('3 Columns', (string)$options[1]['label']);
    }

    public function testPositionOffersBothCornersAndLegacyValue(): void
    {
        $options = (new Position())->toOptionArray();

        $this->assertSame(['bottom-right', 'bottom-left', '1'], array_column($options, 'value'));
        $this->assertSame('Bottom Left', (string)$options[1]['label']);
        $this->assertStringContainsString('legacy', (string)$options[2]['label']);
    }
}
