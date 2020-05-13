/*
 * NovaeZSolrSearchExtraBundle.
 *
 * @package   NovaeZSolrSearchExtraBundle
 *
 * @author    Novactive
 * @copyright 2020 Novactive
 * @license   https://github.com/Novactive/NovaeZSolrSearchExtraBundle/blob/master/LICENSE
 */

const path = require('path')

module.exports = (Encore) => {
  Encore.addEntry('ezplatform-admin-ui-solr-config-js', [
    path.resolve(__dirname, '../public/js/ezsolrconfig.js')
  ])
}
