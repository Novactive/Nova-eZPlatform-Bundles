const path = require('path');

module.exports = (Encore) => {
    Encore.addAliases({
        '@ibexa-richtext': path.resolve('./vendor/ibexa/fieldtype-richtext'),
    });
};
