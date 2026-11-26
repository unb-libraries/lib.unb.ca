/**
 * @file
 * Builds the guides CKEditor 5 plugins against Drupal core's CKEditor 5 DLL.
 *
 * Usage: npm ci && npm run build
 * Output: js/build/<plugin-dir>.js
 */

const path = require('path');
const fs = require('fs');
const webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');

const pluginsDir = path.resolve(__dirname, 'js/ckeditor5_plugins');

module.exports = fs
  .readdirSync(pluginsDir)
  .filter((dir) => fs.statSync(path.join(pluginsDir, dir)).isDirectory())
  .map((dir) => ({
    mode: 'production',
    optimization: {
      minimize: true,
      minimizer: [
        new TerserPlugin({
          terserOptions: { format: { comments: false } },
          test: /\.js(\?.*)?$/i,
          extractComments: false,
        }),
      ],
      moduleIds: 'named',
    },
    entry: { path: path.join(pluginsDir, dir, 'src/index.js') },
    output: {
      path: path.resolve(__dirname, 'js/build'),
      filename: `${dir}.js`,
      library: ['CKEditor5', dir],
      libraryTarget: 'umd',
      libraryExport: 'default',
    },
    plugins: [
      new webpack.DllReferencePlugin({
        // eslint-disable-next-line global-require, import/no-unresolved
        manifest: require('./node_modules/ckeditor5/build/ckeditor5-dll.manifest.json'),
        scope: 'ckeditor5/src',
        name: 'CKEditor5.dll',
      }),
    ],
    module: {
      rules: [{ test: /\.svg$/, use: 'raw-loader' }],
    },
  }));
