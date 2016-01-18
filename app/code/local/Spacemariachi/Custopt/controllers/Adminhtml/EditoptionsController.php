<?php

class Spacemariachi_Custopt_Adminhtml_EditoptionsController extends Mage_Adminhtml_Controller_Action
{
    const OPTION_PREFIX = 'option_';

    public function viewformAction()
    {

        // Prepare data
        $configureResult = new Varien_Object();
        try {
            $orderItemId = (int)$this->getRequest()->getParam('item_id');
            if (!$orderItemId) {
                Mage::throwException($this->__('Quote item id is not received.'));
            }

            $orderItem = Mage::getModel('sales/order_item')->load($orderItemId);
            if (!$orderItem->getId()) {
                Mage::throwException($this->__('Quote item is not loaded.'));
            }

            $configureResult->setOk(true);

            $configureResult->setBuyRequest($orderItem->getBuyRequest());
            $configureResult->setCurrentStoreId($orderItem->getStoreId());
            $configureResult->setProductId($orderItem->getProductId());

        } catch (Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        // Render page
        /* @var $helper Mage_Adminhtml_Helper_Catalog_Product_Composite */
        $helper = Mage::helper('spacemariachi_custopt');
        $helper->renderConfigureResult($this, $configureResult);

        return $this;
    }

    public function confirmAction()
    {

        $updateResult = new Varien_Object();
        try {
            $orderItemId = (int)$this->getRequest()->getParam('item_id');
            $options = $this->getRequest()->getParam('options');
            $orderItem = Mage::getModel('sales/order_item')->load($orderItemId);
            $quote = Mage::getModel('sales/quote')->load($orderItem->getOrder()->getQuoteId());
            $quoteItem = Mage::getModel('sales/quote_item')->load($orderItem->getQuoteItemId())->setQuote($quote);
            $buyRequest = $orderItem->getBuyRequest();
            $buyRequest->setOptions($options);

            $products = $orderItem->getProduct()->getTypeInstance(true)->prepareForCartAdvanced($buyRequest, $orderItem->getProduct(), 'lite');
            $quoteItem->setOptions($products[0]->getCustomOptions());
            $quoteItem->setBuyRequest($buyRequest);
            $productOptions = $orderItem->getProductOptions();
            $productOptions['info_buyRequest'] = $buyRequest->getData();
            $productOptions['options'] = Mage::helper('catalog/product_configuration')->getCustomOptions($quoteItem);
            $orderItem->setProductOptions($productOptions);
            Mage::dispatchEvent('sales_convert_quote_item_to_order_item',
                array('order_item' => $orderItem, 'item' => $quoteItem)
            );

            $quoteItem->save();
            $orderItem->save();


            $updateResult->setOk(true);
        } catch (Exception $e) {
            $updateResult->setError(true);
            $updateResult->setMessage($e->getMessage());
        }

        $updateResult->setJsVarName($this->getRequest()->getParam('as_js_varname'));
        Mage::getSingleton('adminhtml/session')->setCompositeProductResult($updateResult);
        $this->_redirect('*/catalog_product/showUpdateResult');

        return $this;


    }

    public function getOrderOptions($optionsArr, $product)
    {
        $optionArr = array();

        foreach ($optionsArr as $optionId => $value) {
            if ($option = $product->getOptionById($optionId)) {

                $confItemOption = $product->getCustomOption(self::OPTION_PREFIX . $option->getId());

                $group = $option->groupFactory($option->getType())
                    ->setOption($option)
                    ->setProduct($product)
                    ->setConfigurationItemOption($confItemOption);

                $optionArr['options'][] = array(
                    'label' => $option->getTitle(),
                    'value' => $group->getFormattedOptionValue($confItemOption->getValue()),
                    'print_value' => $group->getPrintableOptionValue($confItemOption->getValue()),
                    'option_id' => $option->getId(),
                    'option_type' => $option->getType(),
                    'option_value' => $confItemOption->getValue(),
                    'custom_view' => $group->isCustomizedView()
                );
            }
        }


        return $optionArr;
    }

    protected function _initData()
    {

        $quoteItemId = (int)$this->getRequest()->getParam('id');

        $this->_quote = Mage::getModel('sales/quote')
            ->setWebsite(Mage::app()->getWebsite($websiteId))
            ->loadByCustomer($this->_customer);

        $this->_quoteItem = $this->_quote->getItemById($quoteItemId);
        if (!$this->_quoteItem) {
            Mage::throwException($this->__('Wrong quote item.'));
        }

        return $this;
    }

}