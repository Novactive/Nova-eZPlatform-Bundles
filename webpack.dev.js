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

module.exports = merge(common, {
    mode: 'development',
    devtool: 'source-map'
});
