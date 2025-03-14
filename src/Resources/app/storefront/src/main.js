import ShrQuickViewModalPlugin from './quick-view-modal.plugin';
import ShrQuickViewAddToCartPlugin from './quick-view-add-to-cart.plugin';
import ShrQuickViewComparePlugin from './quick-view-compare.plugin';
import ShrQuickViewComparePagePlugin from './quick-view-compare-page.plugin';
import NotificationHelper from './helper/notification.helper';
import TranslationHelper from './helper/translation.helper';

// Initialize notification system
NotificationHelper.init();

// Make translation helper available globally
window.ShrTranslation = TranslationHelper;

// Register the plugins via the plugin manager
const PluginManager = window.PluginManager;
PluginManager.register('ShrQuickViewModal', ShrQuickViewModalPlugin, '[data-shr-quick-view]');
PluginManager.register('ShrQuickViewAddToCart', ShrQuickViewAddToCartPlugin, '.shr-quick-view-modal-content');
PluginManager.register('ShrQuickViewCompare', ShrQuickViewComparePlugin, '.shr-quick-view-modal-content');
PluginManager.register('ShrQuickViewComparePage', ShrQuickViewComparePagePlugin, '.shr-compare-page');