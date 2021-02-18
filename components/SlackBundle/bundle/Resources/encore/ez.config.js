const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry('nova_ezslack', [
        path.resolve(__dirname, '../public/admin/css/ezslack.css'),
        path.resolve(__dirname, '../public/admin/js/ezslack.js')
    ]);
};
