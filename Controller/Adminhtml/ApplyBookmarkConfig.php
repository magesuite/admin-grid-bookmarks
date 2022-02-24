<?php
declare(strict_types=1);

namespace MageSuite\AdminGridBookmarks\Controller\Adminhtml;

class ApplyBookmarkConfig extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    protected \MageSuite\AdminGridBookmarks\Model\EditableComponents $editableComponents;

    protected \MageSuite\AdminGridBookmarks\Model\ConfigApplier $configApplier;

    protected \Magento\Authorization\Model\UserContextInterface $userContext;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Creativestyle\CustomizationIpet\Model\Ui\EditableComponents $editableComponents,
        \MageSuite\AdminGridBookmarks\Model\ConfigApplier $configApplier,
        \Magento\Authorization\Model\UserContextInterface $userContext
    ) {
        parent::__construct($context);
        $this->editableComponents = $editableComponents;
        $this->configApplier = $configApplier;
        $this->userContext = $userContext;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        $component = $this->getRequest()->getParam('component');

        if (!$this->editableComponents->isEditable($component)) {
            return $resultRedirect;
        }

        try {
            $this->configApplier->execute(
                $component,
                (int)$this->userContext->getUserId()
            );
            $this->messageManager->addSuccessMessage(__('You saved the grid configuration.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the grid configuration.'));
        }

        return $resultRedirect;
    }
}
