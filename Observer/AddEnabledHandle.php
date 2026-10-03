<?php
declare(strict_types=1);

namespace Panth\Footer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;
use Panth\Footer\Helper\Data as FooterHelper;

class AddEnabledHandle implements ObserverInterface
{
    public const HANDLE = 'panth_footer_enabled';

    private FooterHelper $footerHelper;

    public function __construct(
        FooterHelper $footerHelper
    ) {
        $this->footerHelper = $footerHelper;
    }

    public function execute(Observer $observer): void
    {
        $layout = $observer->getEvent()->getData('layout');
        if (!$layout instanceof LayoutInterface || !$this->footerHelper->isEnabled()) {
            return;
        }

        $layout->getUpdate()->addHandle(self::HANDLE);
    }
}
