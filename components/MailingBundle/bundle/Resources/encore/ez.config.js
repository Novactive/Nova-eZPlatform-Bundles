const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('nova_ezmailing', [
        path.resolve(__dirname, '../public/admin/css/ezmailing.scss'),
        path.resolve(__dirname, '../public/admin/css/tree.scss'),
        path.resolve(__dirname, '../public/admin/js/jquery.autocomplete.min.js'),
        path.resolve(__dirname, '../public/admin/js/jquery.peity.min.js'),
        path.resolve(__dirname, '../public/admin/js/Chart.min.js'),
        path.resolve(__dirname, '../public/admin/js/ezmailing.js'),

    ]);
};
