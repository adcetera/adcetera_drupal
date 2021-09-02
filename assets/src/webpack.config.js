const webpack = require("webpack");
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  entry: "./js/index.js",

  output: {
    path: path.resolve('../'),
    filename: "main.js",
  },

  module: {
    rules: [

      /**
       * JavaScript
       *
       * Use Babel to transpile JavaScript files.
       */
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader"
        },
      },

      /**
       * Styles
       *
       * Inject CSS into the head with source maps.
       */
      {
        test: /\.(scss|css)$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              sourceMap: true,
              importLoaders: 1,
            }
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
            }
          },
          {
            loader: 'sass-loader', // must be version 7.1.0 to use outputStyle
            options: {
              sourceMap: true,
              minimize: false,
              outputStyle: 'nested',
            }
          },
        ],
      },
      {
        test: /\.handlebars$/,
        use: [{
          loader: "handlebars-loader",
        }]
      },

      /**
       * Fonts
       *
       * Inline font files.
       */
      {
        test: /\.(woff(2)?|eot|ttf|otf)$/,
        loader: 'url-loader',
        options: {
          limit: 8192,
          name: '[name].[ext]',
        },
      },

      /**
       * Libraries
       *
       * Copy library files to dist folder.
       */
      {
        test: /\.(jpe?g|png|gif|svg)$/i,
        exclude: path.resolve(__dirname, "./src/images"),
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[path][name].[ext]', // [path] will include outer folder structures
              outputPath: 'libs', // creates folder in dist
              publicPath: '../libs', // relative to root
              context: 'node_modules/', // exclude a folder
            },
          }
        ],
      },

      /**
       * Images
       *
       * Copy image files to dist folder.
       */
      {
        test: /\.(jpe?g|png|gif|svg)$/i,
        exclude: path.resolve(__dirname, "./node_modules"),
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '[name].[ext]', // [path] not needed in this case since we created images folder
              outputPath: 'images', // creates folder in dist
              publicPath: '../images', // relative to root
            },
          },
        ],
      },
    ]
  },

  devtool: 'cheap-module-source-map',

  plugins: [
    new MiniCssExtractPlugin({
      filename: 'style.css',
    }),

    new webpack.ProvidePlugin({
      Promise: 'es6-promise-promise',
    })
  ],
};
