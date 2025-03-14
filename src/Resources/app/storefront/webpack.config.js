const { join, resolve } = require('path');

module.exports = () => {
    return {
        resolve: {
            alias: {
                '@shrQuickViewModal': resolve(
                    join(__dirname, '..', 'Resources', 'app', 'storefront', 'src')
                )
            }
        }
    };
};