<?php
declare(strict_types=1);

namespace MageSuite\AdminGridBookmarks\Model;

class EditableComponents
{
    protected $componentList;

    public function __construct(array $componentList = [])
    {
        $this->componentList = $componentList;
    }

    public function isEditable($name): bool
    {
        return in_array($name, $this->componentList);
    }
}
