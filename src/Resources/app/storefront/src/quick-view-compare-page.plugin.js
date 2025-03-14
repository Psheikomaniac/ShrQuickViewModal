import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import NotificationHelper from './helper/notification.helper';
import TranslationHelper from './helper/translation.helper';

export default class ShrQuickViewComparePagePlugin extends Plugin {
    static options = {
        removeButtonSelector: '.remove-product',
        compareStorageKey: 'shr-compare-products'
    };

    init() {
        this._registerEvents();
        this._initRemoveButtons();
    }

    _registerEvents() {
        const removeButtons = DomAccess.querySelectorAll(document, this.options.removeButtonSelector);
        removeButtons.forEach(button => {
            button.addEventListener('click', this._onRemoveButtonClick.bind(this));
        });
    }

    _initRemoveButtons() {
        const compareList = this._getCompareList();
        const removeButtons = DomAccess.querySelectorAll(document, this.options.removeButtonSelector);
        
        removeButtons.forEach(button => {
            if (!compareList.includes(button.dataset.productId)) {
                button.closest('.compare-cell').classList.add('removed');
            }
        });
    }

    _onRemoveButtonClick(event) {
        const button = event.currentTarget;
        const productId = button.dataset.productId;
        if (!productId) return;

        let compareList = this._getCompareList();
        compareList = compareList.filter(id => id !== productId);
        this._saveCompareList(compareList);

        // Animate and remove the product cell
        const productCell = button.closest('.compare-cell');
        productCell.style.opacity = '0';
        setTimeout(() => {
            productCell.style.width = '0';
            productCell.style.padding = '0';
            productCell.style.margin = '0';
            productCell.style.overflow = 'hidden';
            
            setTimeout(() => {
                // If this was the last product, reload the page to show the empty state
                if (compareList.length === 0) {
                    window.location.reload();
                } else {
                    productCell.remove();
                }
            }, 300);
        }, 300);

        // Show notification
        NotificationHelper.createNotification('success', this._translateCompareRemoved());
    }

    _getCompareList() {
        return JSON.parse(localStorage.getItem(this.options.compareStorageKey) || '[]');
    }

    _saveCompareList(list) {
        localStorage.setItem(this.options.compareStorageKey, JSON.stringify(list));
    }

    _translateCompareRemoved() {
        return TranslationHelper.translate('compareRemoved');
    }
}