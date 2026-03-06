/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    florian
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */

const path = require('path')

module.exports = (ibexaConfig, ibexaConfigManager) => {
  // Check if the config and required methods exist (Ibexa 5 compatibility)
  if (!ibexaConfigManager || typeof ibexaConfigManager.add !== 'function') {
    console.warn('Menu Manager Bundle: Skipping frontend config - incompatible webpack config manager');
    return;
  }

  try {
    ibexaConfigManager.add({
      eZConfig: ibexaConfig, // Keep backward compatibility by passing as eZConfig
      entryName: 'ibexa-admin-ui-content-edit-parts-js',
      newItems: [
        path.resolve(__dirname, '../../../modules/menu-manager/menu.manager.renderer.js'),
        path.resolve(__dirname, '../public/js/scripts/fieldType/menuitem.js')
      ]
    })
  } catch (error) {
    console.error('Menu Manager Bundle: Failed to add webpack config:', error.message);
  }
}