const path = require('path')

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-udw-js',
        newItems: [
            path.resolve(__dirname, '../public/js/modules/universal-discovery/standalone.content.edit.tab.module.js'),
            path.resolve(__dirname, '../public/js/modules/universal-discovery/content.edit.module.js'),
        ]
    })
}
