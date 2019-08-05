/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 *
 */

const common = require('./webpack.common')
const merge = require('webpack-merge')
const TerserPlugin = require('terser-webpack-plugin-legacy')

module.exports = merge(common, {
  plugins: [
    new TerserPlugin({
      extractComments: true
    })
  ]
})
