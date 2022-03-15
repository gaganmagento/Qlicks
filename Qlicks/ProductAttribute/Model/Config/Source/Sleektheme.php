<?php
/**
 * My own options
 *
 */
namespace Qlicks\ProductAttribute\Model\Config\Source;

class Sleektheme implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'BelowCart', 'label' => __('Below Cart')],
            ['value' => 'BelowImage', 'label' => __('Below Image')],
            ['value' => 'BelowProduct', 'label' => __('Below Product')]
        ];
    }
}

?>