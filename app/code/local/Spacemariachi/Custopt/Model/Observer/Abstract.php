<?php

class Spacemariachi_Custopt_Model_Observer_Abstract extends Varien_Object
{
    private $_productInfoBlock, $_editCustomOptionsBlockInstance, $_editCustomOptionsButtonBlockInstance;
    private $_editCustomOptionsButtonBlock = 'spacemariachi_custopt/adminhtml_button';

    protected function _addEditButton()
    {
        $this->_getProductInfoBlock()->setTemplate('sales/items/column/nameplus.phtml')->append(
            $this->_getCustomOptionsButtonInstance()->setItem($this->_getItem()),
            'edit_button' . $this->_getItemId()
        );
    }

    private function _getCustomOptionsButtonInstance()
    {
        if (empty($this->_editCustomOptionsBlockInstance)) {
            $this->_setCustomOptionsButtonInstance();
        }
        return $this->_editCustomOptionsButtonBlockInstance;
    }

    private function _setCustomOptionsButtonInstance()
    {
        $this->_editCustomOptionsButtonBlockInstance = $this->_getProductInfoBlock()->getLayout()->createBlock(
            $this->_getEditCustomOptionsButtonBlock(),
            'order_items_option_button',
            array('template' => 'catalog/product/view/options/button.phtml',
                'item' => $this->_getItem()
            ));
        return $this;
    }

    private function _getEditCustomOptionsButtonBlock()
    {
        return $this->_editCustomOptionsButtonBlock;
    }

    private function _getItemId()
    {
        return $this->_getItem()->getId();
    }

    /**
     * @return Mage_Sales_Model_Order_Item
     */
    private function _getItem()
    {
        return $this->_getProductInfoBlock()->getItem();
    }


    protected function _canEditOptions()
    {
        return $this->_hasCustomProductOptions();
    }

    private function _hasCustomProductOptions()
    {
        return array_key_exists('options', $this->_getProductInfoBlock()->getItem()->getProductOptions());
    }

    private function _getProductInfoBlock()
    {
        return $this->_productInfoBlock;
    }

    protected function _setObserverCurrentBlock($block)
    {
        if (!($block instanceof Mage_Adminhtml_Block_Sales_Items_Column_Name
            OR $block instanceof Mage_Adminhtml_Block_Sales_Items_Column_Name_Grouped)
        ) return false;
        $this->_productInfoBlock = $block;
        return $this;
    }
}