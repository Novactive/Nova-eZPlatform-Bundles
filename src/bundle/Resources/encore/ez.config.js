/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    florian
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

const path = require('path')

module.exports = (Encore) => {
  Encore.addEntry('ezplatform-admin-ui-modules-menu-manager-js', [
    path.resolve(__dirname, '../../../modules/menu-manager/menu.manager.renderer.js')
  ])
}
