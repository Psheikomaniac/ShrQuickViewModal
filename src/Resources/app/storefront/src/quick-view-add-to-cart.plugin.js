import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';

export default class ShrQuickViewAddToCartPlugin extends Plugin {
    static options = {
        addToCartButtonSelector: '.btn-buy',
        quantitySelectSelector: '.quantity-select',
        addToCartUrl: window.router['frontend.checkout.line-item.add'],
        csrfToken: ''
    };

    init() {
        this._client = new HttpClient();
        this._registerEvents();
        this._getCsrfToken();
    }

    /**
     * Register all needed events
     * @private
     */
    _registerEvents() {
        const addToCartButton = DomAccess.querySelector(this.el, this.options.addToCartButtonSelector);
        addToCartButton.addEventListener('click', this._onAddToCartClick.bind(this));
    }

    /**
     * Get CSRF token from the DOM
     * @private
     */
    _getCsrfToken() {
        const csrfElement = document.querySelector('meta[name="csrf-token"]');
        if (csrfElement) {
            this.options.csrfToken = csrfElement.content;
        }
    }

    /**
     * Handle add to cart button click
     * @param {Event} event
     * @private
     */
    _onAddToCartClick(event) {
        event.preventDefault();
        
        const button = event.currentTarget;
        const productId = button.dataset.productId;
        const quantitySelect = DomAccess.querySelector(this.el, this.options.quantitySelectSelector);
        const quantity = parseInt(quantitySelect.value, 10);
        
        if (!productId || isNaN(quantity)) {
            return;
        }
        
        this._addToCart(productId, quantity);
    }

    /**
     * Add product to cart via AJAX
     * @param {string} productId
     * @param {number} quantity
     * @private
     */
    _addToCart(productId, quantity) {
        const data = JSON.stringify({
            lineItems: [
                {
                    id: productId,
                    quantity: quantity,
                    type: 'product'
                }
            ]
        });
        
        const headers = {
            'Content-Type': 'application/json',
            'X-CSRF-Token': this.options.csrfToken
        };
        
        // Show loading indicator or disable button
        this._client.post(this.options.addToCartUrl, data, (response) => {
            if (response) {
                // Product added successfully
                // Close modal and update mini cart
                const closeEvent = new CustomEvent('closeModal');
                document.dispatchEvent(closeEvent);
                
                // Trigger mini cart update
                const updateCartEvent = new CustomEvent('updateMiniCart');
                document.dispatchEvent(updateCartEvent);
            }
        }, headers);
    }
}