<?php
declare(strict_types=1);

namespace Panth\Footer\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Panth\Footer\Helper\Data as FooterHelper;

class FooterData implements ArgumentInterface
{
    private FooterHelper $footerHelper;

    public function __construct(
        FooterHelper $footerHelper
    ) {
        $this->footerHelper = $footerHelper;
    }

    public function isEnabled(): bool
    {
        return $this->footerHelper->isEnabled();
    }

    public function getLayout(): int
    {
        return $this->footerHelper->getLayout();
    }

    public function getGridClasses(): string
    {
        return $this->footerHelper->getGridClasses();
    }

    public function getColumnData(int $columnNumber): array
    {
        return $this->footerHelper->getColumnData($columnNumber);
    }

    public function getSocialLinks(): array
    {
        return $this->footerHelper->getSocialLinks();
    }

    public function getSocialIcon(string $platform): string
    {
        return $this->footerHelper->getSocialIcon($platform);
    }

    public function getCopyrightText(): string
    {
        return $this->footerHelper->getCopyrightText() ?? '';
    }

    public function showPaymentIcons(): bool
    {
        return $this->footerHelper->showPaymentIcons();
    }

    public function showFooterLinks(): bool
    {
        return $this->footerHelper->showFooterLinks();
    }

    public function getFooterLinks(): array
    {
        return $this->footerHelper->getFooterLinks();
    }

    public function isNewsletterEnabled(): bool
    {
        return $this->footerHelper->isNewsletterEnabled();
    }

    public function getNewsletterTitle(): ?string
    {
        return $this->footerHelper->getNewsletterTitle();
    }

    public function getNewsletterSubtitle(): ?string
    {
        return $this->footerHelper->getNewsletterSubtitle();
    }

    public function getNewsletterBenefits(): array
    {
        return $this->footerHelper->getNewsletterBenefits();
    }

    public function getNewsletterPlaceholder(): string
    {
        return $this->footerHelper->getNewsletterPlaceholder();
    }

    public function getNewsletterButtonText(): string
    {
        return $this->footerHelper->getNewsletterButtonText();
    }

    public function isBackToTopEnabled(): bool
    {
        return $this->footerHelper->isBackToTopEnabled();
    }

    public function getBackToTopPosition(): string
    {
        return $this->footerHelper->getBackToTopPosition();
    }

    public function getAllowedHtmlTags(): array
    {
        return $this->footerHelper->getAllowedHtmlTags();
    }
}
