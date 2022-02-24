<?php
declare(strict_types=1);

namespace MageSuite\AdminGridBookmarks\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_BOOKMARK_GENERAL_USER_LIST = 'bookmark/general/user_list';

    public function getBookmarkUserList(): array
    {
        $allowedUsers = (string)$this->scopeConfig->getValue(self::XML_PATH_BOOKMARK_GENERAL_USER_LIST);

        if (empty($allowedUsers)) {
            return [];
        }

        return explode(',', $allowedUsers);
    }
}
