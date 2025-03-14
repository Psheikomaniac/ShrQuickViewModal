import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import DomAccess from 'src/helper/dom-access.helper';
import AjaxModal from 'src/plugin/modal/ajax-modal.plugin';

export default class ShrQuickViewModalPlugin extends Plugin {
    static options = {
        /**
         * The selector for the quick view button
         */
        quickViewButtonSelector: '.shr-quick-view-button',
        
        /**
         * The selector for the modal component
         */
        modalSelector: '#shr-quick-view-modal',
        
        /**
         * The URL to load product data
         */
        quickViewUrl: window.router['frontend.shr.quick-view.modal'],
        
        /**
         * Show button only on hover
         */
        showOnHoverOnly: true,
        
        /**
         * Button icon class
         */
        buttonIcon: 'eye',

        /**
         * Enhanced accessibility enabled
         */
        improvedA11y: true
    };

    init() {
        this._client = new HttpClient();
        this._registerEvents();
        
        if (this.options.showOnHoverOnly) {
            this._initHoverEffect();
        }
        
        if (this.options.improvedA11y) {
            this._initKeyboardNavigation();
        }
    }

    /**
     * Register all needed events
     * @private
     */
    _registerEvents() {
        const quickViewButtons = DomAccess.querySelectorAll(document, this.options.quickViewButtonSelector);
        
        if (quickViewButtons) {
            quickViewButtons.forEach((button) => {
                button.addEventListener('click', this._onQuickViewClick.bind(this));
                
                // Add ARIA attributes for accessibility
                if (this.options.improvedA11y) {
                    button.setAttribute('role', 'button');
                    button.setAttribute('aria-label', button.title);
                    button.setAttribute('tabindex', '0');
                }
            });
        }
    }

    /**
     * Initialize keyboard navigation
     * @private
     */
    _initKeyboardNavigation() {
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && this._currentModal) {
                this._currentModal.close();
            }
        });

        const quickViewButtons = DomAccess.querySelectorAll(document, this.options.quickViewButtonSelector);
        quickViewButtons.forEach(button => {
            button.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    this._onQuickViewClick(event);
                }
            });
        });
    }

    /**
     * Initialize hover effect
     * @private
     */
    _initHoverEffect() {
        const productBoxes = DomAccess.querySelectorAll(document, '.product-box');
        
        productBoxes.forEach((box) => {
            const button = box.querySelector(this.options.quickViewButtonSelector);
            if (button) {
                box.addEventListener('mouseenter', () => {
                    button.classList.add('visible');
                });
                
                box.addEventListener('mouseleave', () => {
                    button.classList.remove('visible');
                });
            }
        });
    }

    /**
     * Handle click on quick view button
     * @param {Event} event
     * @private
     */
    _onQuickViewClick(event) {
        event.preventDefault();
        
        const button = event.currentTarget;
        const productId = button.dataset.productId;
        
        if (!productId) {
            return;
        }
        
        this._trackQuickViewOpen(productId);
        this._openModal(productId);
    }

    /**
     * Track quick view opens in analytics
     * @param {string} productId
     * @private
     */
    _trackQuickViewOpen(productId) {
        if (window.dataLayer) {
            window.dataLayer.push({
                'event': 'quickView',
                'productId': productId,
                'feature': 'ShrQuickViewModal'
            });
        }
    }

    /**
     * Open modal with product data
     * @param {string} productId
     * @private
     */
    _openModal(productId) {
        const url = this.options.quickViewUrl.replace('__PRODUCT_ID__', productId);
        
        // Create a new AjaxModal instance with loading indicator
        const modal = new AjaxModal({
            remoteUrl: url,
            ajaxContainerSelector: '.shr-quick-view-modal-content',
            modalClass: 'shr-quick-view-modal',
            delay: 150,
            initCallback: (modal) => {
                modal._content.innerHTML = '<div class="modal-loader"><div class="spinner-border"></div></div>';
                this._currentModal = modal;
                
                // Set focus trap for accessibility
                if (this.options.improvedA11y) {
                    this._initModalFocusTrap(modal);
                }
            }
        });
        
        modal.open();
    }

    /**
     * Initialize focus trap for modal accessibility
     * @param {Object} modal
     * @private
     */
    _initModalFocusTrap(modal) {
        const focusableElements = modal._modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length > 0) {
            const firstFocusable = focusableElements[0];
            const lastFocusable = focusableElements[focusableElements.length - 1];
            
            // Focus first element
            firstFocusable.focus();
            
            modal._modal.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === firstFocusable) {
                            e.preventDefault();
                            lastFocusable.focus();
                        }
                    } else {
                        if (document.activeElement === lastFocusable) {
                            e.preventDefault();
                            firstFocusable.focus();
                        }
                    }
                }
            });
        }
    }
}