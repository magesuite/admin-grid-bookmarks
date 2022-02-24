<?php
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$adminUsers = [
    'adminUser' => 'adminUser@example.com',
    'adminUserSecond' => 'adminUserSecond@example.com',
];

foreach ($adminUsers as $adminUsername => $adminEmail) {
    /** @var $model \Magento\User\Model\User */
    $model = $objectManager->create(\Magento\User\Model\User::class);
    $model->setFirstname("John")
        ->setLastname("Doe")
        ->setUsername($adminUsername)
        ->setPassword(\Magento\TestFramework\Bootstrap::ADMIN_PASSWORD)
        ->setEmail($adminEmail)
        ->setRoleType('G')
        ->setResourceId('Magento_Backend::all')
        ->setPrivileges("")
        ->setAssertId(0)
        ->setRoleId(1)
        ->setPermission('allow');
    $model->save();
}
