/*
 * Nova-eZPlatform-Bundles.
 *
 * @package   Nova-eZPlatform-Bundles
 *
 * @author    florian
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaHtmlIntegrationBundle/blob/master/LICENSE
 */

const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        eZConfig: ibexaConfig,
        entryName: 'ezplatform-admin-ui-location-view-css',
        newItems: [path.resolve(__dirname, '../public/css/focuspoint.scss')]
    });
    ibexaConfigManager.add({
        eZConfig: ibexaConfig,
        entryName: 'ezplatform-admin-ui-content-edit-parts-css',
        newItems: [
            path.resolve(__dirname, '../public/css/focuspoint.scss'),
            path.resolve(__dirname, '../public/css/enhancedimage-field.scss'),
            path.resolve(__dirname, '../public/css/enhancedimage.scss')
        ]
    });
    ibexaConfigManager.add({
        eZConfig: ibexaConfig,
        entryName: 'ezplatform-admin-ui-content-edit-parts-js',
        newItems: [
            path.resolve(__dirname, '../public/js/enhancedimage.js'),
            path.resolve(
                __dirname,
                '../public/js/scripts/fieldType/enhancedimage.js'
            )
        ]
    });
};
