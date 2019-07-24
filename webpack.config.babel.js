const Encore = require('@symfony/webpack-encore');
const path = require('path');

Encore.setOutputPath(path.resolve(__dirname, 'src/bundle/Resources/public/js/modules'))
    .setPublicPath('/bundles/ezmenumanager/js/modules/')
    .setManifestKeyPrefix('modules')
    .addEntry('menu-manager', './src/modules/menu-manager/menu.manager.module.js')
    .addEntry('menu-manager-renderer', './src/modules/menu-manager/menu.manager.renderer.js')
    .addEntry('menu-container-menuitem-type', './src/modules/menu-manager/type/container.menu.item.type.module.js')
    .addEntry('menu-content-menuitem-type', './src/modules/menu-manager/type/content.menu.item.type.module.js')
    .addEntry('menu-default-menuitem-type', './src/modules/menu-manager/type/default.menu.item.type.module.js')
    .enableReactPreset()
    // .enableSingleRuntimeChunk()
    .splitEntryChunks()
    .configureTerserPlugin((options) => {
         options.terserOptions = {
             output: {
                 comments: false
             }
         }
     })

let config = Encore.getWebpackConfig();
config.name = "menu_manager";
module.exports = config;
