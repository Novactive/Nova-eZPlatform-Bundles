const path = require('path')

module.exports = (ibexaConfig, ibexaConfigManager) => {
  ibexaConfigManager.add({
    ibexaConfig,
    entryName: 'ibexa-richtext-onlineeditor-js',
    newItems: [
      path.resolve(__dirname, '../public/js/CKEditor/embed/extraconfig.js'),
    ]
  })
}
