<?php
declare(strict_types=1);

namespace Panth\Footer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Data extends AbstractHelper
{
    private const XML_PATH_FOOTER = 'panth_footer/';

    private const SAFE_URL_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    private const SOCIAL_URL_SCHEMES = ['http', 'https'];

    private const LINK_TARGETS = ['_self', '_blank', '_parent', '_top'];

    private const BACK_TO_TOP_POSITIONS = ['bottom-right', 'bottom-left'];

    private const LEGACY_BACK_TO_TOP_POSITIONS = ['1' => 'bottom-right'];

    private const ALLOWED_HTML_TAGS = ['a', 'b', 'strong', 'em', 'i', 'u', 'small', 'span', 'br'];

    private Json $jsonSerializer;

    public function __construct(
        Context $context,
        Json $jsonSerializer
    ) {
        parent::__construct($context);
        $this->jsonSerializer = $jsonSerializer;
    }

    public function getConfigValue(string $field, ?int $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FOOTER . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    private function isCoreModuleEnabled(): bool
    {
        return true;
    }

    public function isEnabled(): bool
    {
        if (!$this->isCoreModuleEnabled()) {
            return false;
        }

        return (bool)$this->getConfigValue('general/enabled');
    }

    public function getLayout(): int
    {
        return (int)$this->getConfigValue('general/layout') ?: 4;
    }

    public function isColumnEnabled(int $columnNumber): bool
    {
        return (bool)$this->getConfigValue("column{$columnNumber}/enabled");
    }

    public function getColumnData(int $columnNumber): array
    {
        $data = [
            'enabled' => $this->isColumnEnabled($columnNumber),
            'title' => (string)$this->getConfigValue("column{$columnNumber}/title"),
        ];

        if ($columnNumber === 1) {
            $data['show_logo'] = (bool)$this->getConfigValue('column1/show_logo');
            $data['content'] = $this->getConfigValue('column1/content');
            $data['show_social'] = (bool)$this->getConfigValue('column1/show_social');
        } elseif ($columnNumber === 4) {
            $data['show_contact_info'] = (bool)$this->getConfigValue('column4/show_contact_info');
            $data['phone'] = $this->getConfigValue('column4/phone');
            $data['email'] = $this->getConfigValue('column4/email');
            $data['address'] = $this->getConfigValue('column4/address');
            $data['working_hours'] = $this->getConfigValue('column4/working_hours');
        } else {
            $linksJson = $this->getConfigValue("column{$columnNumber}/links");
            $data['links'] = $this->parseLinks($linksJson);
        }

        return $data;
    }

    private function parseLinks(?string $json): array
    {
        if (empty($json)) {
            return [];
        }

        try {
            $links = $this->jsonSerializer->unserialize($json);
        } catch (\InvalidArgumentException $e) {
            $this->_logger->error('Footer links JSON parse error: ' . $e->getMessage());
            return [];
        }

        return is_array($links) ? $this->normalizeLinks($links) : [];
    }

    private function normalizeLinks(array $links): array
    {
        $result = [];
        foreach ($links as $link) {
            if (!is_array($link)) {
                continue;
            }

            $title = isset($link['title']) && is_scalar($link['title']) ? trim((string)$link['title']) : '';
            if ($title === '') {
                continue;
            }

            $url = isset($link['url']) && is_scalar($link['url']) ? $this->getSafeUrl((string)$link['url']) : '';
            $target = isset($link['target']) && is_string($link['target'])
                && in_array($link['target'], self::LINK_TARGETS, true) ? $link['target'] : '';

            $result[] = [
                'title' => $title,
                'url' => $url !== '' ? $url : '#',
                'target' => $target,
            ];
        }

        return $result;
    }

    public function getSafeUrl(string $url, array $allowedSchemes = self::SAFE_URL_SCHEMES): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        $normalized = (string)preg_replace('/[\x00-\x20\x7f]+/', '', $url);
        if (preg_match('/^([a-z][a-z0-9+.\-]*):/i', $normalized, $matches)) {
            return in_array(strtolower($matches[1]), $allowedSchemes, true) ? $url : '';
        }

        return $url;
    }

    public function getAllowedHtmlTags(): array
    {
        return self::ALLOWED_HTML_TAGS;
    }

    public function getSocialLinks(): array
    {
        $social = [];
        $platforms = ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'pinterest'];

        foreach ($platforms as $platform) {
            $url = $this->getSafeUrl((string)$this->getConfigValue("social/{$platform}"), self::SOCIAL_URL_SCHEMES);
            if ($url !== '') {
                $social[$platform] = $url;
            }
        }

        return $social;
    }

    public function getCopyrightText(): string
    {
        $text = (string)$this->getConfigValue('bottom/copyright_text');
        return str_replace('{{year}}', date('Y'), $text);
    }

    public function showPaymentIcons(): bool
    {
        return (bool)$this->getConfigValue('bottom/show_payment_icons');
    }

    public function showFooterLinks(): bool
    {
        return (bool)$this->getConfigValue('bottom/show_footer_links');
    }

    public function getFooterLinks(): array
    {
        $linksJson = $this->getConfigValue('bottom/footer_links');
        return $this->parseLinks($linksJson);
    }

    public function getGridClasses(): string
    {
        $layout = $this->getLayout();
        $classes = [
            2 => 'grid-cols-1 md:grid-cols-2',
            3 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
            4 => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
        ];

        return $classes[$layout] ?? $classes[4];
    }

    public function getSocialIcon(string $platform): string
    {
        $icons = [
            'facebook' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
            'twitter' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
            'instagram' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
            'linkedin' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
            'youtube' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
            'pinterest' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/></svg>',
        ];

        return $icons[$platform] ?? '';
    }

    public function isNewsletterEnabled(): bool
    {
        return (bool)$this->getConfigValue('newsletter/enabled');
    }

    public function getNewsletterTitle(): ?string
    {
        $value = $this->getConfigValue('newsletter/title');
        return $value !== null ? (string)$value : null;
    }

    public function getNewsletterSubtitle(): ?string
    {
        $value = $this->getConfigValue('newsletter/subtitle');
        return $value !== null ? (string)$value : null;
    }

    public function getNewsletterBenefits(): array
    {
        $benefitsJson = $this->getConfigValue('newsletter/benefits');
        if (empty($benefitsJson)) {
            return [];
        }

        try {
            $benefits = $this->jsonSerializer->unserialize($benefitsJson);
        } catch (\InvalidArgumentException $e) {
            $this->_logger->error('Newsletter benefits JSON parse error: ' . $e->getMessage());
            return [];
        }

        if (!is_array($benefits)) {
            return [];
        }

        $result = [];
        foreach ($benefits as $benefit) {
            if (is_scalar($benefit) && trim((string)$benefit) !== '') {
                $result[] = (string)$benefit;
            }
        }

        return $result;
    }

    public function getNewsletterPlaceholder(): string
    {
        return (string)($this->getConfigValue('newsletter/placeholder_text') ?: 'Enter your email address');
    }

    public function getNewsletterButtonText(): string
    {
        return (string)($this->getConfigValue('newsletter/button_text') ?: 'Subscribe');
    }

    public function isBackToTopEnabled(): bool
    {
        return (bool)$this->getConfigValue('back_to_top/enabled');
    }

    public function getBackToTopPosition(): string
    {
        $position = (string)$this->getConfigValue('back_to_top/position');
        $position = self::LEGACY_BACK_TO_TOP_POSITIONS[$position] ?? $position;
        return in_array($position, self::BACK_TO_TOP_POSITIONS, true) ? $position : 'bottom-right';
    }

    public function getBackToTopBgColor(): string
    {
        return (string)($this->scopeConfig->getValue(
            'theme_customizer/back_to_top/bg_color',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ?: '#1a1a2e');
    }

    public function getBackToTopIconColor(): string
    {
        return (string)($this->scopeConfig->getValue(
            'theme_customizer/back_to_top/icon_color',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ?: '#ffffff');
    }

    public function getBackToTopHoverBgColor(): string
    {
        return (string)($this->scopeConfig->getValue(
            'theme_customizer/back_to_top/hover_bg_color',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ?: '#16213e');
    }

    public function getBackToTopHoverIconColor(): string
    {
        return (string)($this->scopeConfig->getValue(
            'theme_customizer/back_to_top/hover_icon_color',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ?: '#ffffff');
    }

    public function getBackgroundColor(): string
    {
        return (string)($this->getThemeColor('footer/bg_color') ?: '#1a1a2e');
    }

    public function getTextColor(): string
    {
        return (string)($this->getThemeColor('footer/text_color') ?: '#a0a0b0');
    }

    public function getH2Color(): string
    {
        return (string)($this->getThemeColor('footer/h2_color') ?: '#ffffff');
    }

    public function getH3Color(): string
    {
        return (string)($this->getThemeColor('footer/h3_color') ?: '#ffffff');
    }

    public function getNewsletterBackgroundColor(): string
    {
        return (string)($this->getThemeColor('newsletter/bg_color') ?: '#16213e');
    }

    public function getNewsletterTitleColor(): string
    {
        return (string)($this->getThemeColor('newsletter/title_color') ?: '#ffffff');
    }

    public function getNewsletterTextColor(): string
    {
        return (string)($this->getThemeColor('newsletter/text_color') ?: '#a0a0b0');
    }

    public function getNewsletterButtonColor(): string
    {
        return (string)($this->getThemeColor('newsletter/button_color') ?: '#e94560');
    }

    public function getNewsletterButtonTextColor(): string
    {
        return (string)($this->getThemeColor('newsletter/button_text_color') ?: '#ffffff');
    }

    public function getNewsletterButtonHoverColor(): string
    {
        return (string)($this->getThemeColor('newsletter/button_hover_color') ?: '#d63851');
    }

    public function getNewsletterInputBgColor(): string
    {
        return (string)($this->getThemeColor('newsletter/input_bg_color') ?: '#0f3460');
    }

    public function getNewsletterInputTextColor(): string
    {
        return (string)($this->getThemeColor('newsletter/input_text_color') ?: '#ffffff');
    }

    public function getNewsletterInputBorderColor(): string
    {
        return (string)($this->getThemeColor('newsletter/input_border_color') ?: '#1a4a7a');
    }

    public function getNewsletterInputFocusBorderColor(): string
    {
        return (string)($this->getThemeColor('newsletter/input_focus_border_color') ?: '#e94560');
    }

    private function getThemeColor(string $path): ?string
    {
        return $this->scopeConfig->getValue(
            'theme_customizer/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
