/*
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

const common = require('./webpack.common');
const merge = require('webpack-merge');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');

module.exports = merge(common, {
    mode: 'production',
    // plugins: [
    //     new UglifyJSPlugin({
    //         sourceMap: true,
    //         uglifyOptions: {
    //             ecma: 6,
    //         },
    //     }),
    // ],
    optimization: {
        minimize: true,
        mangleWasmImports: true,
        concatenateModules: true
    }
});
