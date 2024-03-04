const path = require('path')

module.exports = (ibexaConfig, ibexaConfigManager) => {
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-richtext-onlineeditor-js',
        newItems: [
            path.resolve(__dirname, '../public/js/CKEditor/extraconfig.js')
        ]
    })

    Object.assign(ibexaConfig.resolve.alias, {
        '@ibexa-richtext': path.resolve('./vendor/ibexa/fieldtype-richtext'),
    });
}
