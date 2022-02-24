<?php
declare(strict_types=1);

namespace MageSuite\AdminGridBookmarks\Model;

class ConfigApplier
{
    public const IDENTIFIER = 'magesuite_view';

    public const BOOKMARK_TITLE = 'MageSuite View';

    protected $adminId;

    protected \Magento\Ui\Api\BookmarkRepositoryInterface $bookmarkRepository;

    protected \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory $collectionFactory;

    protected \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory;

    protected \Magento\Framework\Serialize\SerializerInterface $serializer;

    public function __construct(
        \Magento\Ui\Api\BookmarkRepositoryInterface $bookmarkRepository,
        \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory $collectionFactory,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->bookmarkRepository = $bookmarkRepository;
        $this->collectionFactory = $collectionFactory;
        $this->userCollectionFactory = $userCollectionFactory;
        $this->serializer = $serializer;
    }

    /**
     * Save grid configuration for all admin users
     *
     * @param string $namespace
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(string $namespace, int $adminId)
    {
        $this->adminId = $adminId;
        /** @var \Magento\Ui\Model\Bookmark $bookmark */
        $bookmark = $this->getCurrentBookmark($namespace);

        if ($bookmark->getIdentifier() == 'default') {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('You can\'t save grid configuration for Default View.')
            );
        }

        $this->modifyConfiguration($bookmark);
        $this->applyToAdminUsers($bookmark);
    }

    protected function getCurrentBookmark(string $namespace): ?\Magento\Ui\Model\Bookmark
    {
        return $this->collectionFactory->create()
            ->addFieldToFilter('namespace', $namespace)
            ->addFieldToFilter('user_id', $this->adminId)
            ->addFieldToFilter('current', 1)
            ->getFirstItem();
    }

    public function modifyConfiguration(\Magento\Ui\Model\Bookmark $bookmark): void
    {
        $bookmark->setId(null);
        $bookmark->setIdentifier(self::IDENTIFIER);
        $bookmark->setCurrent(false);
        $bookmark->setTitle(self::BOOKMARK_TITLE);
        $config = $bookmark->getConfig();
        $viewData = array_pop($config['views']);
        $newConfig = [
            'views' => [
                self::IDENTIFIER => [
                    'label' => self::BOOKMARK_TITLE,
                    'index' => self::IDENTIFIER,
                    'editable' => true,
                    'data' => $viewData['data'],
                    'value' => self::BOOKMARK_TITLE
                ]
            ]
        ];
        $bookmark->setConfig($this->serializer->serialize($newConfig));
    }

    protected function applyToAdminUsers(\Magento\Ui\Model\Bookmark $bookmark): void
    {
        $userCollection = $this->userCollectionFactory->create()
            ->addFieldToFilter('user_id', ['nin' => $this->adminId]);

        foreach ($userCollection->getAllIds() as $adminUserId) {
            $existingBookmark = $this->collectionFactory->create()
                ->addFieldToFilter('namespace', $bookmark->getNamespace())
                ->addFieldToFilter('user_id', $adminUserId)
                ->addFieldToFilter('identifier', self::IDENTIFIER)
                ->getFirstItem();

            $bookmark->setId($existingBookmark->getId());
            $bookmark->setUserId($adminUserId);
            $this->bookmarkRepository->save($bookmark);
        }
    }
}
