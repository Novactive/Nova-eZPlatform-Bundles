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

module.exports = (eZConfig, eZConfigManager) => {
  eZConfigManager.add({
    eZConfig,
    entryName: 'ezplatform-admin-ui-content-edit-parts-js',
    newItems: [
      path.resolve(__dirname, '../../../modules/menu-manager/menu.manager.renderer.js'),
      path.resolve(__dirname, '../public/js/scripts/fieldType/menuitem.js')
    ]
  })
}
