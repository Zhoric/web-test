const elixir = require('laravel-elixir');

require('laravel-elixir-vue');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix){
    mix.sass('app.scss').webpack('app.js');
});

elixir(function(mix){
    mix.less('common.less');
    mix.less('auth.less');
    mix.less('admin.less');
    mix.less('site.less');
    mix.less('test.less');
});

elixir(function(mix){
    mix.coffee('institutes-coffee.coffee');
});