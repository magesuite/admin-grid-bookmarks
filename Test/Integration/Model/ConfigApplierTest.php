<?php
declare(strict_types=1);

namespace MageSuite\AdminGridBookmarks\Test\Integration\Model;

class ConfigApplierTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \MageSuite\AdminGridBookmarks\Model\ConfigApplier
     */
    protected $configApplier;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->userFactory = $objectManager->get(\Magento\User\Model\UserFactory::class);
        $this->collectionFactory = $objectManager->get(\Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory::class);
        $this->configApplier = $objectManager->get(\MageSuite\AdminGridBookmarks\Model\ConfigApplier::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture MageSuite_AdminGridBookmarks::Test/Integration/_files/bookmark.php
     */
    public function testIfConfigIsAppliedToAdminUser()
    {
        $admin = $this->userFactory->create()->loadByUsername('adminUser');
        $adminSecond = $this->userFactory->create()->loadByUsername('adminUserSecond');

        $this->assertEquals(0, $this->getBookmarkList($adminSecond->getId())->getSize());
        $this->configApplier->execute('product_listing', (int)$admin->getId());
        $this->assertEquals(1, $this->getBookmarkList($adminSecond->getId())->getSize());
    }

    protected function getBookmarkList($adminId): \Magento\Ui\Model\ResourceModel\Bookmark\Collection
    {
        /** @var \Magento\Ui\Model\ResourceModel\Bookmark\Collection $collection */
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter(
                \MageSuite\UiBookmarkCleaner\Controller\Adminhtml\Reset\Index::UI_BOOKMARK_USER_ID_FIELD,
                ['eq' => $adminId]
            );

        return $collection;
    }
}
