<?php
$resolver = \Magento\TestFramework\Workaround\Override\Fixture\Resolver::getInstance();
$resolver->requireDataFixture('MageSuite_AdminGridBookmarks::Test/Integration/_files/two_users.php');

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$admin = $objectManager->create(\Magento\User\Model\User::class)->loadByUsername('adminUser');
$bookmark = $objectManager->create(\Magento\Ui\Api\Data\BookmarkInterface::class);
$bookmarkData = [
    'user_id' => $admin->getId(),
    'namespace' => 'product_listing',
    'identifier' => \MageSuite\AdminGridBookmarks\Model\ConfigApplier::IDENTIFIER_PREFIX . '_' . time(),
    'current' => 1,
    'config' => '{"views":{"_1643708131533":{"label":"Dummy","index":"_1643708131533","editable":true,"data":[],"value":"Dummy"}}}',
    'title' => \MageSuite\AdminGridBookmarks\Model\ConfigApplier::BOOKMARK_TITLE
];
$bookmark->setData($bookmarkData)->save();
