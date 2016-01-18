<?php

class Spacemariachi_Custopt_Block_Adminhtml_Button extends Mage_Core_Block_Template
{
    /**
     * Return html button which calls configure window
     *
     * @param  Mage_Sales_Model_Quote_Item $item
     * @return string
     */
    public function getConfigureButtonHtml()
    {
        $product = $this->_getItem()->getProduct();
        $options = array('label' => Mage::helper('sales')->__('Configure'));
        if ($product->canConfigure()) {
            $options['onclick'] = "productConfigure.showItemConfiguration('item_confirm{$this->_getItemId()}', {$this->_getItemId()}) && window.productConfigure && productConfigure.onLoadIFrame()";
            $options['class'] = ' f-right';
        }

        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData($options)
            ->toHtml();
    }

    public function getButtonLink()
    {
        return Mage::helper('adminhtml')->getUrl('*/editoptions/viewform', array('item_id' => $this->_getItemId()));
    }

    public function getSubmitUrl()
    {
        return Mage::helper('adminhtml')->getUrl('*/editoptions/confirm', array('item_id' => $this->_getItemId()));
    }

    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }

    public function getProductId()
    {
        return $this->_getItem()->getProductId();
    }

    public function _getItemId()
    {
        return $this->_getItem()->getItemId();
    }

    public function setItem($item)
    {
        $this->setData('item', $item);
        return $this;
    }

    private function _getItem()
    {
        return $this->getData('item');
    }
}