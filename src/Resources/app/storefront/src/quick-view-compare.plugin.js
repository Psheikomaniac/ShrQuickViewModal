import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import TranslationHelper from './helper/translation.helper';

export default class ShrQuickViewComparePlugin extends Plugin {
    static options = {
        compareEnabled: false,
        compareSelector: '.shr-product-compare',
        compareStorageKey: 'shr-compare-products',
        maxCompareProducts: 4
    };

    init() {
        if (!this.options.compareEnabled) {
            return;
        }

        this._registerEvents();
        this._initCompareButtons();
    }

    _registerEvents() {
        const compareButton = DomAccess.querySelector(this.el, this.options.compareSelector, false);
        if (!compareButton) {
            return;
        }

        compareButton.addEventListener('click', this._onCompareButtonClick.bind(this));
    }

    _initCompareButtons() {
        const compareList = this._getCompareList();
        const compareButton = DomAccess.querySelector(this.el, this.options.compareSelector, false);
        
        if (compareButton && compareList.includes(compareButton.dataset.productId)) {
            compareButton.classList.add('active');
        }
    }

    _onCompareButtonClick(event) {
        event.preventDefault();
        
        const button = event.currentTarget;
        const productId = button.dataset.productId;
        if (!productId) return;
        
        let compareList = this._getCompareList();
        
        if (compareList.includes(productId)) {
            compareList = compareList.filter(id => id !== productId);
            button.classList.remove('active');
            this._showNotification('success', this._translateCompareRemoved());
        } else {
            if (compareList.length >= this.options.maxCompareProducts) {
                this._showNotification('error', this._translateCompareMaxReached());
                return;
            }
            compareList.push(productId);
            button.classList.add('active');
            this._showNotification('success', this._translateCompareAdded());
        }
        
        this._saveCompareList(compareList);
        this._updateCompareCounter(compareList.length);
    }

    _getCompareList() {
        return JSON.parse(localStorage.getItem(this.options.compareStorageKey) || '[]');
    }

    _saveCompareList(list) {
        localStorage.setItem(this.options.compareStorageKey, JSON.stringify(list));
    }

    _updateCompareCounter(count) {
        const counter = document.querySelector('.shr-compare-counter');
        if (counter) {
            counter.textContent = count;
            counter.classList.toggle('d-none', count === 0);
        }
    }

    _showNotification(type, message) {
        const event = new CustomEvent('showNotification', {
            detail: {
                type: type,
                message: message
            }
        });
        document.dispatchEvent(event);
    }

    _translateCompareAdded() {
        return TranslationHelper.translate('compareAdded');
    }

    _translateCompareRemoved() {
        return TranslationHelper.translate('compareRemoved');
    }

    _translateCompareMaxReached() {
        return TranslationHelper.translate('compareMaxReached');
    }
}