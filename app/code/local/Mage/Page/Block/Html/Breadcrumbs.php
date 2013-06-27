<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Page_Block_Html_Breadcrumbs extends Mage_Core_Block_Template
{
    /**
     * Array of breadcrumbs
     *
     * array(
     *  [$index] => array(
     *                  ['label']
     *                  ['title']
     *                  ['link']
     *                  ['first']
     *                  ['last']
     *              )
     * )
     *
     * @var array
     */
    protected $_crumbs = null;

    function __construct()
    {
        parent::__construct();
        $this->setTemplate('page/html/breadcrumbs.phtml');
    }

    function addCrumb($crumbName, $crumbInfo, $after = false)
    {
        $this->_prepareArray($crumbInfo, array('label', 'title', 'link', 'first', 'last', 'readonly'));
        if ((!isset($this->_crumbs[$crumbName])) || (!$this->_crumbs[$crumbName]['readonly'])) {
           $this->_crumbs[$crumbName] = $crumbInfo;
        }
        return $this;
    }

protected function _toHtml() {             

   $cat_id = "";

   if (Mage::registry('current_product')) {
      $product_id = Mage::registry('current_product')->getId();
      $obj = Mage::getModel('catalog/product');
      $_product = $obj->load($product_id); // Enter your Product Id in $product_id

      if ($product_id) {
         $categoryIds = $_product->getCategoryIds();
         $cat_id = $categoryIds[1];
      }

      $category = Mage::getModel('catalog/category')->load($cat_id);
      $cat_name = $category->getName();
      $cat_url =  $this->getBaseUrl().$category->getUrlPath();
   }

   if (is_array($this->_crumbs)) {
      reset($this->_crumbs);
      $this->_crumbs[key($this->_crumbs)]['first'] = true;
      end($this->_crumbs);
      $this->_crumbs[key($this->_crumbs)]['last'] = true;
   }

   if($cat_id) {
      $this->_crumbs['category'.$cat_id] = array('label'=>$cat_name, 'title'=>'', 'link'=>$cat_url,'first'=>'','last'=>'','readonly'=>'');
      ksort($this->_crumbs);
      $home = $this->_crumbs['home'];
      unset($this->_crumbs['home']);
      array_unshift($this->_crumbs,$home);
   }

   $this->assign('crumbs', $this->_crumbs);
   return parent::_toHtml();
}
}
