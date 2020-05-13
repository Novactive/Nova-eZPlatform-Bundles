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
const CleanWebpackPlugin = require('clean-webpack-plugin')

module.exports = {
  entry: {
    SolrManager: './src/bundle/Ressources/public/js/ezsolrconfig.js'
  },
  output: {
    filename: '[name].module.js',
    path: path.resolve(__dirname, 'src/bundle/Resources/public/js'),
    library: ['eZ', 'modules', '[name]'],
    libraryTarget: 'umd',
    libraryExport: 'default'
  },
  devtool: 'source-map',
  module: {
    loaders: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        loader: 'babel-loader'
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader']
      }
    ]
  },
  externals: {
    jquery: {
      root: 'jQuery',
      commonjs2: 'jquery',
      commonjs: 'jquery',
      amd: 'jquery'
    }
  },
  plugins: [new CleanWebpackPlugin(['Resources/public/js/'])],
  resolve: {
    extensions: ['.js']
  }
}
