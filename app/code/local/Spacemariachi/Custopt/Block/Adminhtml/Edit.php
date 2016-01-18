<?php

class Spacemariachi_Custopt_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Options
{

    /**
     * Get option html block
     *
     * @param Mage_Catalog_Model_Product_Option $option
     */
    public function getOptionHtml(Mage_Catalog_Model_Product_Option $option)
    {
        if ($this->_canRenderOption($option)) {
            $renderer = $this->getOptionRender(
                $this->getGroupOfOption($option->getType())
            );
            if (is_null($renderer['renderer'])) {
                $renderer['renderer'] = $this->getLayout()->createBlock($renderer['block'])
                    ->setTemplate($renderer['template'])->setSkipJsReloadPrice(1);
            }
            return $renderer['renderer']
                ->setProduct($this->getProduct())
                ->setOption($option)
                ->toHtml();
        }
    }

    protected function _canRenderOption($option)
    {
        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId());

        if ($this->getGroupOfOption($option->getType()) == 'select') {
            return true;
        } else {
            if ($option->getPrice() == 0) {
                return true;
            }

            if ($option->getIsRequire() && $configValue) {
                return true;
            }
            if ($configValue) {
                $option->setIsRequire(true);
                return true;
            }
        }
        return false;
    }

}