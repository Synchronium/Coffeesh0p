<?php
class Coffeesh0p_Payment_Block_Custom_Info_Four extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/info/default.phtml');
    }

    protected function _getInstructions()
    {
        return Mage::getStoreConfig($this->__('payment/%s/payment_information', $this->getInfo()->getMethodInstance()->getCode()));
    }

    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $info = $this->getInfo();
        $transport = new Varien_Object(array(Mage::helper('payment')->__('Instructions') => $this->_getInstructions(),));
        return $transport;
    }

    public function getSpecificInformation()
    {
        return $this->_prepareSpecificInformation()->getData();
    }
}