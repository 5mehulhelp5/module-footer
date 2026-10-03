<?php
declare(strict_types=1);

namespace Panth\Footer\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

class Json extends Value
{
    public function beforeSave()
    {
        $value = trim((string)$this->getValue());
        if ($value === '') {
            $this->setValue('');
            return parent::beforeSave();
        }

        $label = (string)($this->getData('field_config/label') ?: $this->getPath());
        try {
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new LocalizedException(
                __('"%1" must contain valid JSON: %2', $label, $e->getMessage())
            );
        }

        if (!is_array($decoded)) {
            throw new LocalizedException(__('"%1" must contain a JSON list.', $label));
        }

        $this->setValue(json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return parent::beforeSave();
    }
}
