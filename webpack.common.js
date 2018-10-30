const path = require('path');
const CleanWebpackPlugin = require('clean-webpack-plugin');

module.exports = {
    entry: {
        MenuManager: './src/modules/menu-manager/menu.manager.module.js',
        MenuItemEditForm: './src/modules/menu-manager/menu.item.edit.form.module.js',
    },
    output: {
        filename: '[name].module.js',
        path: path.resolve(__dirname, 'src/bundle/Resources/public/js/modules'),
        library: ['Novactive', 'modules', '[name]'],
        libraryTarget: 'umd',
        libraryExport: 'default',
    },
    devtool: 'source-map',
    module: {
        loaders: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader',
            },
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader'],
            },
            {
                test: /\.(png|jpg|gif)$/,
                use: ['file-loader'],
            },
        ],
    },
    externals: {
        react: {
            root: 'React',
            commonjs2: 'react',
            commonjs: 'react',
            amd: 'react',
        },
        'react-dom': {
            root: 'ReactDOM',
            commonjs2: 'react-dom',
            commonjs: 'react-dom',
            amd: 'react-dom',
        },
        'prop-types': {
            root: 'PropTypes',
            commonjs2: 'prop-types',
            commonjs: 'prop-types',
            amd: 'prop-types',
        }
    },
    plugins: [new CleanWebpackPlugin(['src/bundle/Resources/public/js/modules'])],
};
