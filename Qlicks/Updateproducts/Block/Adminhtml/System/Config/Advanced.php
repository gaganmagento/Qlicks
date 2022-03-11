<?php

namespace Qlicks\Updateproducts\Block\Adminhtml\System\Config;

class Advanced extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'system/config/custom.phtml';

   protected $_configPath = 'Qlicks/uploadconfig/custom_field'; //replace your config path here

    protected $_groupName = 'uploadconfig'; // replace this with your group name

    protected $_fieldName = 'custom_field'; // replace this with your field name

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_decorateRowHtml($element,"<td colspan='3'><div class='custom-slice-container'>".$this->toHtml()."</div></td>");
    }

    public function getConfigPath(){
        return $this->_configPath;
    }

    public function getGroupName(){
        return $this->_groupName;
    }

    public function getFieldName(){
        return $this->_fieldName;
    }
}