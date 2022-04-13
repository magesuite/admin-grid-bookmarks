<?php
declare(strict_types=1);

namespace MageSuite\AdminGridBookmarks\Model;

class ConfigApplier
{
    public const IDENTIFIER_PREFIX = 'magesuite_view';

    public const BOOKMARK_TITLE = 'MageSuite View';

    protected $adminId;

    protected $bookmarkIdentifier;

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
     */
    public function execute(string $namespace, int $adminId): void
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
        $identifier = $this->getBookmarkIdentifier();
        $bookmark->setIdentifier($identifier);
        $bookmark->setCurrent(false);
        $bookmark->setTitle(self::BOOKMARK_TITLE);
        $config = $bookmark->getConfig();
        $viewData = array_pop($config['views']);
        $newConfig = [
            'views' => [
                $identifier => [
                    'label' => self::BOOKMARK_TITLE,
                    'index' => $identifier,
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
            $bookmark->setId(null);
            $bookmark->setUserId($adminUserId);
            $conditions = [
                'namespace = ?' => $bookmark->getNamespace(),
                'user_id = ?' => $adminUserId,
                'identifier LIKE ?' => self::IDENTIFIER_PREFIX . '%'
            ];
            $userCollection->getConnection()->delete(
                $bookmark->getResource()->getMainTable(),
                $conditions
            );
            $this->bookmarkRepository->save($bookmark);
        }
    }

    protected function getBookmarkIdentifier(): string
    {
        if (!$this->bookmarkIdentifier) {
            $this->bookmarkIdentifier = sprintf('%s_%s', self::IDENTIFIER_PREFIX, time());
        }

        return $this->bookmarkIdentifier;
    }
}
