const path = require('path')

module.exports = (Encore) => {
  Encore.addEntry('ezplatform-admin-ui-solr-config-js', [
    path.resolve(__dirname, '../public/js/ezsolrconfig.js')
  ])
}
