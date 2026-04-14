<?php
/**
 * Copyright (c) Panth Infotech. All rights reserved.
 *
 * ViewModel for footer data, replaces deprecated Helper usage in templates.
 */
declare(strict_types=1);

namespace Panth\Footer\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Panth\Footer\Helper\Data as FooterHelper;

class FooterData implements ArgumentInterface
{
    /**
     * @var FooterHelper
     */
    private FooterHelper $footerHelper;

    /**
     * @param FooterHelper $footerHelper
     */
    public function __construct(
        FooterHelper $footerHelper
    ) {
        $this->footerHelper = $footerHelper;
    }

    /**
     * Check if custom footer is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->footerHelper->isEnabled();
    }

    /**
     * Get footer layout column count
     *
     * @return int
     */
    public function getLayout(): int
    {
        return $this->footerHelper->getLayout();
    }

    /**
     * Get grid CSS classes for the layout
     *
     * @return string
     */
    public function getGridClasses(): string
    {
        return $this->footerHelper->getGridClasses();
    }

    /**
     * Get column data array
     *
     * @param int $columnNumber
     * @return array
     */
    public function getColumnData(int $columnNumber): array
    {
        return $this->footerHelper->getColumnData($columnNumber);
    }

    /**
     * Get social media links
     *
     * @return array
     */
    public function getSocialLinks(): array
    {
        return $this->footerHelper->getSocialLinks();
    }

    /**
     * Get social media icon SVG markup
     *
     * @param string $platform
     * @return string
     */
    public function getSocialIcon(string $platform): string
    {
        return $this->footerHelper->getSocialIcon($platform);
    }

    /**
     * Get copyright text with year replaced
     *
     * @return string
     */
    public function getCopyrightText(): string
    {
        return $this->footerHelper->getCopyrightText() ?? '';
    }

    /**
     * Check if payment icons should be shown
     *
     * @return bool
     */
    public function showPaymentIcons(): bool
    {
        return $this->footerHelper->showPaymentIcons();
    }

    /**
     * Check if footer bottom links should be shown
     *
     * @return bool
     */
    public function showFooterLinks(): bool
    {
        return $this->footerHelper->showFooterLinks();
    }

    /**
     * Get footer bottom links
     *
     * @return array
     */
    public function getFooterLinks(): array
    {
        return $this->footerHelper->getFooterLinks();
    }

    /**
     * Check if newsletter section is enabled
     *
     * @return bool
     */
    public function isNewsletterEnabled(): bool
    {
        return $this->footerHelper->isNewsletterEnabled();
    }

    /**
     * Get newsletter title
     *
     * @return string|null
     */
    public function getNewsletterTitle(): ?string
    {
        return $this->footerHelper->getNewsletterTitle();
    }

    /**
     * Get newsletter subtitle
     *
     * @return string|null
     */
    public function getNewsletterSubtitle(): ?string
    {
        return $this->footerHelper->getNewsletterSubtitle();
    }

    /**
     * Get newsletter benefits array
     *
     * @return array
     */
    public function getNewsletterBenefits(): array
    {
        return $this->footerHelper->getNewsletterBenefits();
    }

    /**
     * Get newsletter email placeholder text
     *
     * @return string
     */
    public function getNewsletterPlaceholder(): string
    {
        return $this->footerHelper->getNewsletterPlaceholder();
    }

    /**
     * Get newsletter button text
     *
     * @return string
     */
    public function getNewsletterButtonText(): string
    {
        return $this->footerHelper->getNewsletterButtonText();
    }

    /**
     * Check if back to top button is enabled
     *
     * @return bool
     */
    public function isBackToTopEnabled(): bool
    {
        return $this->footerHelper->isBackToTopEnabled();
    }
}
