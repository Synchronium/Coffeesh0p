<?php
/**
 * Coder: Adam Paterson (JH)
 * URL: http://www.wearejh.com/
 * Date: 12/09/2012
 * Time: 20:48
 */

$installer = $this;
$installer->startSetup();

$entityTypeId       =   $installer->getEntityTypeId('catalog_category');
$attributeSetId     =   $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId   =   $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('catalog_category', 'bottom_description', array(
    'type'              => 'text',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Bottom Description',
    'input'             => 'textarea',
    'class'             => '',
    'source'            => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => true,
    'unique'            => false,
    'wysiwyg_enabled'   => 1,
    'is_html_allowed_on_front' => true
));

$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'bottom_description',
   '11'
);

$installer->endSetup();