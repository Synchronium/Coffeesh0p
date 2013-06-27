<?php
/**
 * Coder: Adam Paterson (JH)
 * URL: http://www.wearejh.com/
 * Date: 12/09/2012
 * Time: 21:41
 */

$installer = $this;
$installer->startSetup();

$entityTypeId       =   $installer->getEntityTypeId('catalog_category');
$attributeSetId     =   $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId   =   $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttribute('catalog_category', 'navigation_title',  array(
    'type'     => 'text',
    'label'    => 'Navigation title',
    'input'    => 'text',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => true,
    'default'           => ''
));

$installer->addAttributeToGroup(
    $entityTypeId,
    $attributeSetId,
    $attributeGroupId,
    'navigation_title',
    '2'
);

$installer->endSetup();