<?php

namespace Qlicks\Systemconfig\Model\Config\Source;
 
class Custom implements \Magento\Framework\Option\ArrayInterface
{
    protected $collectionFactory;
 
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }
 
    public function toOptionArray() 
    {

     
// return [
// ['value' => 0, 'label' => __('First')],
// ['value' => 1, 'label' => __('Second')],
// ['value' => 2, 'label' => __('Third')],
// ['value' => 3, 'label' => __('Fourth')]];

        $collection = $this->collectionFactory->create();
        $collection
         ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name');
 
        $ret        = [];
        foreach ($collection as $product) {
            $ret[] = [
                'value' => $product->getSku(),
                'label' => $product->getName(),
            ];
        }
        return $ret;
    }
}
