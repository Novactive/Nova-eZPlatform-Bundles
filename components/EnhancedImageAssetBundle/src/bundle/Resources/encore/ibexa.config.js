const path = require('path');

module.exports = (Encore) => {
    Encore
    .addEntry('enhancedimage-js', [
        path.resolve(__dirname, '../public/js/enhancedimage.js'),
    ])
    .addEntry('enhancedimage-css', [
        path.resolve(__dirname, '../public/css/enhancedimage.scss'),
    ])
};
