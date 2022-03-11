<?php

namespace Qlicks\Updateproducts\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    protected $_scopeConfig;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        
    }

    public function getConfig($configPath)
    {
        return $this->_scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getBaseUrl(){
        return $this->storeManager->getStore()->getBaseUrl();
    }

    public function getMediaUrl(){
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
  
}