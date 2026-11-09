let mix = require('laravel-mix');
mix.webpackConfig({
    resolve: {
        symlinks: false
    },
    externals: {
        jquery: 'jQuery',
        vue: 'Vue'
    }
})

mix.options({
    processCssUrls: false
})

mix.setPublicPath('..');

mix
    .sass('assets/scss/common.scss', 'themes/xw_replica/css/common.css')
    /**
     * bootstrap-modified.css
     * This file contains old bootstrap 3 modified styles, it will be removed progressively
     * Some of its styles is important some of it conflicts with the new Bootstrap 5
     */
    //.sass('assets/scss/bootstrap-modified.scss', 'themes/xw_replica/css/bootstrap-modified.css')

/**
 * Turn off notifications
 */
mix
    .disableNotifications()
    .options({
        clearConsole: false,
    });

// Disable mix-manifest.json
// @see https://github.com/JeffreyWay/laravel-mix/issues/580
Mix.manifest.refresh = _ => void 0;
