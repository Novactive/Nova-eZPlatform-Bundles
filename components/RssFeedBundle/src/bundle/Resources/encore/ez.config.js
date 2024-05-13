const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('nova_ezrssfeed', [
        path.resolve(__dirname, '../public/admin/scss/custom.scss'),
        path.resolve(__dirname, '../public/admin/js/admin.rss.feed.state.js'),
    ]);
    Encore.addEntry('nova_ezrssfeed_edit', [
        path.resolve(__dirname, '../public/admin/scss/custom.scss'),
        path.resolve(__dirname, '../public/admin/js/collection_rss.js'),
    ]);
};
