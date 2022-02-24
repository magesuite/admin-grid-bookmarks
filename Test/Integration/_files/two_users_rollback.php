<?php
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var $model \Magento\User\Model\User */
$model = $objectManager->create(\Magento\User\Model\User::class);
$adminUsers = [
    'adminUser',
    'adminUserSecond'
];

foreach ($adminUsers as $username) {
    $user = $model->loadByUsername($username);

    if ($user->getId()) {
        $user->delete();
    }
}
