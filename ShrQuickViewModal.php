<?php declare(strict_types=1);

namespace ShrQuickViewModal;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class ShrQuickViewModal extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
    }

    public function uninstall(UninstallContext $context): void
    {
        parent::uninstall($context);
        
        if ($context->keepUserData()) {
            return;
        }
        
        // Clean up logic if user data should not be kept
    }
}