<?php
declare(strict_types=1);

namespace Panth\Footer\Test\Unit\Observer;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\View\LayoutInterface;
use Panth\Footer\Helper\Data as FooterHelper;
use Panth\Footer\Observer\AddEnabledHandle;
use PHPUnit\Framework\TestCase;

class AddEnabledHandleTest extends TestCase
{
    private function observerFor(mixed $layout): Observer
    {
        $event = new Event(['layout' => $layout]);
        return new Observer(['event' => $event]);
    }

    private function helper(bool $enabled): FooterHelper
    {
        $helper = $this->createStub(FooterHelper::class);
        $helper->method('isEnabled')->willReturn($enabled);
        return $helper;
    }

    public function testHandleNameMatchesLayoutFile(): void
    {
        $this->assertSame('panth_footer_enabled', AddEnabledHandle::HANDLE);
    }

    public function testAddsHandleWhenFooterIsEnabled(): void
    {
        $update = $this->createMock(ProcessorInterface::class);
        $update->expects($this->once())->method('addHandle')->with('panth_footer_enabled');
        $layout = $this->createStub(LayoutInterface::class);
        $layout->method('getUpdate')->willReturn($update);

        (new AddEnabledHandle($this->helper(true)))->execute($this->observerFor($layout));
    }

    public function testDoesNotAddHandleWhenFooterIsDisabled(): void
    {
        $update = $this->createMock(ProcessorInterface::class);
        $update->expects($this->never())->method('addHandle');
        $layout = $this->createStub(LayoutInterface::class);
        $layout->method('getUpdate')->willReturn($update);

        (new AddEnabledHandle($this->helper(false)))->execute($this->observerFor($layout));
    }

    public function testIgnoresEventWithoutLayoutAndSkipsConfigLookup(): void
    {
        $helper = $this->createMock(FooterHelper::class);
        $helper->expects($this->never())->method('isEnabled');

        $observer = new AddEnabledHandle($helper);
        $observer->execute($this->observerFor(null));
        $observer->execute($this->observerFor(new \stdClass()));
    }
}
