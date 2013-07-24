<?php
class Coffeesh0p_Payment_Block_Custom_Form_Five extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('coffeesh0p/payment/custom/form/five.phtml');
        parent::_construct();
    }

    public function getInformationBlock()
    {
        return Mage::getStoreConfig('payment/custom_five/payment_information');
    }
}