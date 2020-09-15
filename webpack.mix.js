require('dotenv').config();
const mix = require('laravel-mix');
let webpack = require('webpack');

const env = process.env.APP_ENV;

if (env === 'production' || env === 'dev') {
    mix
        .js('resources/js/app.js', 'public/js')
        .js('resources/js/snake.js', 'public/js')
        .sass('resources/sass/app.scss', 'public/css')
        .sass('resources/sass/snake.scss', 'public/css')
        .options({
            extractVueStyles: false,
            globalVueStyles: 'resources/sass/vue_components.scss'
        })
        .version();
// env === local
} else {
    mix
        .setPublicPath('public/build')
        .setResourceRoot('/build/')
        .js('resources/js/app.js', 'js')
        .js('resources/js/snake.js', 'js')
        .sass('resources/sass/app.scss', 'css')
        .sass('resources/sass/snake.scss', 'css')
        .options({
            extractVueStyles: false,
            globalVueStyles: 'resources/sass/vue_components.scss'
        })
        .version();
}

mix.webpackConfig(
    {
        plugins: [
            new webpack.IgnorePlugin(/^codemirror$/)
        ]
    }
);
