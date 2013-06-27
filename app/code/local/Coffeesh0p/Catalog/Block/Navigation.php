<?php
/**
 * Coder: Adam Paterson (JH)
 * URL: http://www.wearejh.com/
 * Date: 13/09/2012
 * Time: 16:32
 */
 class Coffeesh0p_Catalog_Block_Navigation extends Mage_Catalog_Block_Navigation
 {
     public function getName($category)
     {
         $category = Mage::getModel('catalog/category')->load($category->getId());
         if($category->getNavigationTitle()) {
             $name = $category->getNavigationTitle();
         } else {
             $name = $category->getName();
         }

         return $name;
     }
 }