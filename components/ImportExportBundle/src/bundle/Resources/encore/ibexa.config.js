const path = require('path')
const fs = require('fs');

module.exports = (Encore) => {
  Encore.addEntry('ibexa-admin-ui-import-export-js', [
    path.resolve(__dirname, '../public/js/highlight.js'),
  ])
}
