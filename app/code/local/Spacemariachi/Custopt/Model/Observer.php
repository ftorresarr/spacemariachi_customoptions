<?php

class Spacemariachi_Custopt_Model_Observer extends Spacemariachi_Custopt_Model_Observer_Abstract
{

    public function adminhtmlBlockHtmlBefore($observer)
    {
        if ($this->_setObserverCurrentBlock($observer->getBlock())) {
            if ($this->_canEditOptions()) {
                $this->_addEditButton();
            }
        }
    }

}