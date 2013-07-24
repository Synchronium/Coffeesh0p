<?php
class Coffeesh0p_Payment_Block_Custom_Form_One extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('coffeesh0p/payment/custom/form/one.phtml');
        parent::_construct();
    }

    public function getInformationBlock()
    {
        return Mage::getStoreConfig('payment/custom_one/payment_information');
    }
}