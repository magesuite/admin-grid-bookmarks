<?php
declare(strict_types=1);

namespace MageSuite\AdminGridBookmarks\Block\Adminhtml;

class SaveConfigButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    protected \Magento\Framework\UrlInterface $urlBuilder;

    protected \Magento\Backend\Model\Auth\Session $session;

    protected \MageSuite\AdminGridBookmarks\Helper\Configuration $configuration;

    protected string $componentName;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Backend\Model\Auth\Session $session,
        \MageSuite\AdminGridBookmarks\Helper\Configuration $configuration,
        string $componentName = ''
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->session = $session;
        $this->configuration = $configuration;
        $this->componentName = $componentName;
    }

    public function getButtonData(): array
    {
        if (!$this->isVisible()) {
            return [];
        }

        return [
            'label' => __('Save Grid Configuration'),
            'url' => $this->getUrl('gridbookmarks/bookmark/apply', ['component' => $this->getComponentName()]),
            'class' => 'apply',
            'sort_order' => 90,
        ];
    }

    public function getUrl($route = '', $params = []): string
    {
        return $this->urlBuilder->getUrl($route, $params);
    }

    public function isVisible(): bool
    {
        $adminUser = $this->session->getUser()->getUserName();
        $isAllowed = in_array($adminUser, $this->configuration->getBookmarkUserList());

        return $isAllowed && !empty($this->componentName);
    }

    public function getComponentName(): string
    {
        return $this->componentName;
    }
}
