<?php
/**
 * Copyright (c) Panth Infotech. All rights reserved.
 *
 * JSON Beautifier form field for admin config.
 */
declare(strict_types=1);

namespace Panth\Footer\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class JsonBeautifier extends Field
{
    /**
     * Render element HTML with JSON formatting controls
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $element->setClass('json-beautifier-field');
        $element->setRows(10);
        $element->setStyle('font-family: monospace; width: 100%;');

        $htmlId = $this->escapeHtmlAttr($element->getHtmlId());

        $html = '<div class="json-beautifier-wrapper">';
        $html .= '<textarea'
            . ' id="' . $htmlId . '"'
            . ' name="' . $this->escapeHtmlAttr($element->getName()) . '"'
            . ' class="' . $this->escapeHtmlAttr($element->getClass()) . '"'
            . ' style="' . $this->escapeHtmlAttr($element->getStyle()) . '"'
            . ' rows="' . (int)$element->getRows() . '"'
            . '>' . $element->getEscapedValue() . '</textarea>';

        $html .= '<div class="json-beautifier-controls" style="margin-top: 10px;">';
        $html .= '<button type="button" class="action-default" '
            . 'data-action="beautify" data-target="' . $htmlId . '">'
            . '<span>' . $this->escapeHtml(__('Beautify JSON')) . '</span></button>';
        $html .= '<button type="button" class="action-default" style="margin-left: 10px;" '
            . 'data-action="minify" data-target="' . $htmlId . '">'
            . '<span>' . $this->escapeHtml(__('Minify JSON')) . '</span></button>';
        $html .= '<button type="button" class="action-default" style="margin-left: 10px;" '
            . 'data-action="validate" data-target="' . $htmlId . '">'
            . '<span>' . $this->escapeHtml(__('Validate JSON')) . '</span></button>';
        $html .= '<span id="' . $htmlId . '_status" '
            . 'style="margin-left: 15px; font-weight: bold;"></span>';
        $html .= '</div></div>';

        $html .= $this->getJsonScript();

        return $html;
    }

    /**
     * Get scoped JavaScript for JSON beautification
     *
     * @return string
     */
    private function getJsonScript(): string
    {
        return <<<'SCRIPT'
<script>
    require(['jquery', 'domReady!'], function($) {
        'use strict';

        function showStatus(statusEl, message, isValid) {
            statusEl.text(message);
            statusEl.css('color', isValid ? '#3d9970' : '#d32f2f');
            if (isValid) {
                setTimeout(function() { statusEl.text(''); }, 3000);
            }
        }

        $(document).on('click', '[data-action="beautify"]', function() {
            var targetId = $(this).data('target');
            var $el = $('#' + targetId);
            var $status = $('#' + targetId + '_status');
            try {
                var json = JSON.parse($el.val());
                $el.val(JSON.stringify(json, null, 4));
                showStatus($status, 'Valid JSON', true);
            } catch (e) {
                showStatus($status, 'Invalid JSON: ' + e.message, false);
            }
        });

        $(document).on('click', '[data-action="minify"]', function() {
            var targetId = $(this).data('target');
            var $el = $('#' + targetId);
            var $status = $('#' + targetId + '_status');
            try {
                var json = JSON.parse($el.val());
                $el.val(JSON.stringify(json));
                showStatus($status, 'JSON Minified', true);
            } catch (e) {
                showStatus($status, 'Invalid JSON: ' + e.message, false);
            }
        });

        $(document).on('click', '[data-action="validate"]', function() {
            var targetId = $(this).data('target');
            var $el = $('#' + targetId);
            var $status = $('#' + targetId + '_status');
            try {
                JSON.parse($el.val());
                showStatus($status, 'Valid JSON', true);
            } catch (e) {
                showStatus($status, 'Invalid JSON: ' + e.message, false);
            }
        });

        // Auto-beautify on load
        $('.json-beautifier-field').each(function() {
            if ($.trim($(this).val())) {
                try {
                    var json = JSON.parse($(this).val());
                    $(this).val(JSON.stringify(json, null, 4));
                } catch (e) {
                    // Invalid JSON, leave as is
                }
            }
        });
    });
</script>
SCRIPT;
    }
}
