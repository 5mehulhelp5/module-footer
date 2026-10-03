<?php
declare(strict_types=1);

namespace Panth\Footer\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Position implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'bottom-right', 'label' => __('Bottom Right')],
            ['value' => 'bottom-left', 'label' => __('Bottom Left')],
            ['value' => '1', 'label' => __('Bottom Right (legacy value)')],
        ];
    }
}
