let mix = require('laravel-mix');
let webpackConfig = {
    output: {
        publicPath: '/',
        chunkFilename: '[name].js'
    },
    plugins: [],
};
if (mix.inProduction()) {
    webpackConfig.output.chunkFilename = '[name].[chunkhash:12].js';
}
mix.webpackConfig(webpackConfig);

mix.js('resources/assets/js/vips.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css');

mix.extract([ 'vue']);  // 配置的源代码都会被放在 vendor.js 里
