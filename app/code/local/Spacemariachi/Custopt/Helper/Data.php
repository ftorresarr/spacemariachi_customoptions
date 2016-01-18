<?php

class Spacemariachi_Custopt_Helper_Data extends Mage_Adminhtml_Helper_Catalog_Product_Composite
{
    /**
     * Init composite product configuration layout
     *
     * $isOk - true or false, whether action was completed nicely or with some error
     * If $isOk is FALSE (some error during configuration), so $productType must be null
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     * @param bool $isOk
     * @param string $productType
     * @return Mage_Adminhtml_Helper_Catalog_Product_Composite
     */
    protected function _initConfigureResultLayout($controller, $isOk, $productType)
    {
        $update = $controller->getLayout()->getUpdate();
        if ($isOk) {
            $update->addHandle('SPACEMARIACHI_ADMINHTML_CATALOG_PRODUCT_COMPOSITE_CONFIGURE')
                ->addHandle('PRODUCT_TYPE_' . $productType);
        } else {
            $update->addHandle('ADMINHTML_CATALOG_PRODUCT_COMPOSITE_CONFIGURE_ERROR');
        }
        $controller->loadLayoutUpdates()->generateLayoutXml()->generateLayoutBlocks();
        return $this;
    }

    /**
     * Prepares and render result of composite product configuration request
     *
     * $configureResult holds either:
     *  - 'ok' = true, and 'product_id', 'buy_request', 'current_store_id', 'current_customer' or 'current_customer_id'
     *  - 'error' = true, and 'message' to show
     *
     * @param Mage_Adminhtml_Controller_Action $controller
     * @param Varien_Object $configureResult
     * @return Mage_Adminhtml_Helper_Catalog_Product_Composite
     */
    public function renderConfigureResult($controller, Varien_Object $configureResult)
    {
        try {
            if (!$configureResult->getOk()) {
                Mage::throwException($configureResult->getMessage());
            };

            $currentStoreId = (int)$configureResult->getCurrentStoreId();
            if (!$currentStoreId) {
                $currentStoreId = Mage::app()->getStore()->getId();
            }

            $product = Mage::getModel('catalog/product')
                ->setStoreId($currentStoreId)
                ->load($configureResult->getProductId());
            if (!$product->getId()) {
                Mage::throwException($this->__('Product is not loaded.'));
            }
            Mage::register('current_product', $product);
            Mage::register('product', $product);


            // Prepare buy request values
            $buyRequest = $configureResult->getBuyRequest();

            if ($buyRequest) {
                Mage::helper('catalog/product')->prepareProductOptions($product, $buyRequest);
            }

            $isOk = true;
            $productType = $product->getTypeId();
        } catch (Exception $e) {
            $isOk = false;
            $productType = null;
            Mage::register('composite_configure_result_error_message', $e->getMessage());
        }

        $this->_initConfigureResultLayout($controller, $isOk, $productType);
        $controller->renderLayout();
    }

}