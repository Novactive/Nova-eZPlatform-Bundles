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
const fs = require('fs');

const getVendorFilePath = (filename) => {
  const realFilePath = path.resolve(__dirname, `../../../../../../${filename}`);
  if (fs.existsSync(realFilePath)) {
    return realFilePath;
  }
  return path.resolve(__dirname, `../../../../../../ezplatform/vendor/${filename}`)
}


module.exports = (Encore) => {
  Encore.addEntry('ezplatform-admin-ui-modules-menu-manager-js', [
    path.resolve(__dirname, '../../../modules/menu-manager/menu.manager.renderer.js'),
    getVendorFilePath('ezsystems/ezplatform-admin-ui/src/bundle/Resources/public/js/scripts/button.state.toggle.js')
  ])

  Encore.addEntry('ezplatform-admin-ui-modules-menu-manager-css', [
    path.resolve(__dirname, '../public/css/open-iconic-bootstrap.min.css'),
    path.resolve(__dirname, '../public/css/jstree.css'),
    path.resolve(__dirname, '../public/css/menu-manager.css')
  ])

  Encore.copyFiles({
    from: path.resolve(__dirname, '../public/images/'),
    to: '../images/[path][name].[ext]',
  })
  Encore.copyFiles({
    from: path.resolve(__dirname, '../public/fonts/'),
    to: '../fonts/[path][name].[ext]',
  })
}
