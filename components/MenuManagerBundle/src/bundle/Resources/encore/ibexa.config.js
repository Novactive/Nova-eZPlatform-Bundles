/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    florian
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

const path = require('path');

module.exports = (Encore) => {
    // Menu Manager JavaScript
    Encore.addEntry('ibexa-admin-ui-modules-menu-manager-js', [
        path.resolve(__dirname, '../../../modules/menu-manager/menu.manager.renderer.js'),
        path.resolve(__dirname, '../public/js/scripts/fieldType/menuitem.js'),
        path.resolve('./public/bundles/ibexaadminui/js/scripts/button.state.toggle.js'),
    ]);

    // Menu Manager CSS
    Encore.addEntry('ibexa-admin-ui-modules-menu-manager-css', [
        path.resolve(__dirname, '../public/css/open-iconic-bootstrap.min.css'),
        path.resolve(__dirname, '../public/css/jstree.css'),
        path.resolve(__dirname, '../public/css/menu-manager.css')
    ]);

    // Copy static assets
    // Configure css-loader to process url() - this is what Ibexa 4 did automatically
    Encore.configureCssLoader((options) => {
        options.url = {
            filter: (url) => {
                // Process relative URLs (like ../images/jstree/32px.png)
                return url.startsWith('../') || url.startsWith('./');
            }
        };
        return options;
    });

    // Configure how assets are handled (mimics Ibexa 4 behavior)
    Encore.configureLoaderRule('images', (loaderRule) => {
        loaderRule.type = 'asset/resource';
        loaderRule.generator = {
            filename: 'images/bundles/novaezmenumanager/[path][name].[hash:8][ext]'
        };
    });

    Encore.configureLoaderRule('fonts', (loaderRule) => {
        loaderRule.type = 'asset/resource';
        loaderRule.generator = {
            filename: 'fonts/bundles/novaezmenumanager/[path][name].[hash:8][ext]'
        };
    });

};