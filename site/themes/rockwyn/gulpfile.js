var elixir = require('laravel-elixir');
var theme = 'rockwyn';
elixir.config.assetsPath = './';
elixir.config.publicPath = './';

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Statamic theme. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.sass(theme + '.scss', 'css/' + theme + '.css');

    mix.scripts([
        'js/bootstrap.min.js',
        'vendor/collage-plus.js',
        'vendor/zoom.js',
        'jabbascripts.js'
    ], './js/rockwyn.js');
});
