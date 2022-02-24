<?php
$resolver = \Magento\TestFramework\Workaround\Override\Fixture\Resolver::getInstance();
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$admin = $objectManager->create(\Magento\User\Model\User::class)->loadByUsername('adminUser');
$collection = $objectManager->get(\Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory::class)->create();
$collection->addFieldToFilter('user_id', $admin->getId())->walk('delete');
$resolver->requireDataFixture('MageSuite_AdminGridBookmarks::Test/Integration/_files/two_users_rollback.php');
