import Plugin from 'src/plugin-system/plugin.class';

/**
 * Translation manager for the ShrQuickViewModal plugin
 * Ensures proper language translation support across all languages
 */
export default class TranslationHelper {
    /**
     * Get the current locale from the document
     * 
     * @returns {string} - The current locale
     */
    static getCurrentLocale() {
        return document.documentElement.lang || 'en-GB';
    }

    /**
     * Fetch translations for the current locale
     * 
     * @returns {Promise<Object>} - The translations object
     */
    static async getTranslations() {
        const locale = this.getCurrentLocale();
        try {
            const response = await fetch(`/theme/quick-view-modal/snippets/${locale}.json`);
            if (!response.ok) {
                // Try fallback to en-GB if current locale file doesn't exist
                const fallbackResponse = await fetch('/theme/quick-view-modal/snippets/en-GB.json');
                if (!fallbackResponse.ok) {
                    throw new Error('Translation file not found');
                }
                return await fallbackResponse.json();
            }
            return await response.json();
        } catch (error) {
            console.error('Error loading translations:', error);
            return {};
        }
    }

    /**
     * Get a properly translated string by key
     * 
     * @param {string} key - The translation key to lookup
     * @param {Object} params - Optional parameters for placeholder replacement
     * @returns {Promise<string>} - The translated string
     */
    static translate(key, parameters = {}) {
        return this.getTranslations().then(translations => {
            const keys = key.split('.');
            let result = translations;

            for (const k of keys) {
                if (!result || !result[k]) {
                    return key;
                }
                result = result[k];
            }

            // Replace parameters if any
            if (Object.keys(parameters).length > 0) {
                Object.keys(parameters).forEach(param => {
                    result = result.replace(`{${param}}`, parameters[param]);
                });
            }

            return result;
        });
    }
}