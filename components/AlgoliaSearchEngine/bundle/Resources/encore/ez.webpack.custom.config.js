const path = require('path');
const Encore = require('@symfony/webpack-encore');

Encore.reset();
Encore.setOutputPath('public/assets/nova_ezalgolia/build')
    .setPublicPath('/assets/nova_ezalgolia/build')
    .addEntry('nova_ezalgolia', [
        path.resolve(__dirname, '../assets/js/search.jsx'),
    ])
    .enableSassLoader()
    .enableReactPreset()
    .enableSingleRuntimeChunk();

const customConfig = Encore.getWebpackConfig();

customConfig.name = 'nova_ezalgolia';

module.exports = customConfig;
