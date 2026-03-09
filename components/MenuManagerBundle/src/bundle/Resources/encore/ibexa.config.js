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

    Encore.copyFiles({
        from: path.resolve(__dirname, '../public/images/'),
        to: '../images/[path][name].[ext]',
    })
    Encore.copyFiles({
        from: path.resolve(__dirname, '../public/fonts/'),
        to: '../fonts/[path][name].[ext]',
    })
};
