const elixir = require('laravel-elixir');

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
    mix.less('common.less');
    mix.less('auth.less');
    mix.less('admin.less');
    mix.less('site.less');
    mix.less('test.less');
});

// ------ COMMON ------

elixir(function(mix){
    mix.scripts([
        "lib/es5-shim.js",
        "lib/polyfills.js",
        "lib/jquery-3.1.1.js",
        "lib/jquery.min.js",
        "lib/jquery-ui.min.js",
        "lib/jquery.cookie.js",
        "lib/knockout-3.4.0.debug.js",
        "lib/knockout.validation.js",
        "lib/knockout.mapping.js",
        "lib/ru-RU.js",
        "helpers/ko-postget.js",
        "lib/tooltipster.bundle.js",
        "lib/jquery.arcticmodal.js",
        "helpers/common.js",
        "helpers/ko-copy.js",
        "helpers/ko-pager.js",
        "helpers/modals.js",
        "helpers/ko-events.js",
        "helpers/tooltip.js",
        "helpers/user-info.js"
    ], "public/js/min/manager-common.js")
});
elixir(function(mix){
    mix.scripts([
        "lib/es5-shim.js",
        "lib/polyfills.js",
        "lib/jquery-3.1.1.js",
        "lib/jquery.cookie.js",
        "lib/knockout-3.4.0.debug.js",
        "lib/knockout.validation.js",
        "lib/knockout.mapping.js",
        "lib/ru-RU.js",
        "helpers/ko-postget.js",
        "lib/tooltipster.bundle.js",
        "lib/jquery.arcticmodal.js",
        "helpers/common.js",
        "helpers/ko-copy.js",
        "helpers/ko-progressbar.js",
        "helpers/modals.js",
        "helpers/ko-events.js",
        "helpers/tooltip.js",
        "helpers/user-info.js",
        "helpers/ko-pager.js"
    ], "public/js/min/student-common.js");
});
// ------ /COMMON ------


// ------ AUTH ------
elixir(function(mix){
    mix.scripts([
        "lib/jquery-3.1.1.js",
        "lib/knockout-3.4.0.debug.js",
        "lib/knockout.validation.js",
        "lib/knockout.mapping.js",
        "lib/jquery.arcticmodal.js",
        "lib/ru-RU.js",
        "lib/tooltipster.bundle.js",
        "helpers/ko-postget.js",
        "helpers/common.js",
        "helpers/ko-events.js",
        "helpers/tooltip.js",
        "helpers/modals.js",
    ], "public/js/min/auth.js");
});
elixir(function(mix){
    mix.scripts([
        "auth/login.js",
    ], "public/js/min/login.js");
});
elixir(function(mix){
    mix.scripts([
        "auth/register.js",
    ], "public/js/min/register.js");
});

// ------ /AUTH ------


// ------ STUDENT ------
elixir(function(mix){
    mix.scripts([
        "student/discipline.js"
    ], "public/js/min/student-discipline.js");
});
elixir(function(mix){
    mix.scripts([
        "student/home.js"
    ], "public/js/min/student-home.js");
});
elixir(function(mix){
    mix.scripts([
        "student/results.js"
    ], "public/js/min/student-results.js");
});
elixir(function(mix){
    mix.scripts([
        "student/media.js"
    ], "public/js/min/student-media.js");
});
elixir(function(mix){
    mix.scripts([
        "student/materials.js"
    ], "public/js/min/student-materials.js");
});
elixir(function(mix){
    mix.scripts([
        "lib/wheelzoom.js",
        "lib/ace.js",
        "lib/mode-c_cpp.js",
        "helpers/sendCode.js",
        "student/test.js"
    ], "public/js/min/student-test.js");
});
// ------ /STUDENT ------


// ------ MANAGER ------
elixir(function(mix){
    mix.scripts([
        "lib/knockout.multiselect.js",
        "admin/disciplines.js"
    ], "public/js/min/manager-disciplines.js");
});
elixir(function(mix){
    mix.scripts([
        "admin/editor.js"
    ], "public/js/min/manager-editor.js");
});
elixir(function(mix){
    mix.scripts([
        "admin/media.js"
    ], "public/js/min/manager-media.js");
});
elixir(function(mix){
    mix.scripts([
        "admin/materials.js",
        "lib/knockout.multiselect.js",
        "lib/elfinder.min.js",
        "lib/elfinder.ru.js"
    ], "public/js/min/manager-materials.js");
});
elixir(function(mix){
    mix.scripts([
        "admin/groups.js"
    ], "public/js/min/manager-groups.js");
});
elixir(function(mix){
    mix.scripts([
        "admin/performance.js"
    ], "public/js/min/manager-performance.js");
});
elixir(function(mix){
    mix.scripts([
        "lib/knockout.multiselect.js",
        "admin/lecturers.js"
    ], "public/js/min/manager-lecturers.js");
});
elixir(function(mix){
    mix.scripts([
        "admin/institutes.js"
    ], "public/js/min/manager-institutes.js");
});
elixir(function(mix){
    mix.scripts([
        "helpers/ko-progressbar.js",
        "admin/monitoring.js"
    ], "public/js/min/manager-monitoring.js");
});
elixir(function(mix){
    mix.scripts([
        "lib/bootstrap-datepicker.min.js",
        "lib/bootstrap.min.js",
        "lib/d3.v2.min.js",
        "lib/timeknots-min.js",
        "helpers/datepicker.js",
        "helpers/timeline.js",
        "admin/overall.js",
    ], "public/js/min/manager-overall.js");
});
elixir(function(mix){
    mix.scripts([
        "admin/result.js"
    ], "public/js/min/manager-result.js");
});
elixir(function(mix){
    mix.scripts([
        "admin/results.js"
    ], "public/js/min/manager-results.js");
});
elixir(function(mix){
    mix.scripts([
        "lib/bootstrap-datepicker.min.js",
        "lib/bootstrap.min.js",
        "helpers/datepicker.js",
        "admin/setting.js",
    ], "public/js/min/manager-setting.js");
});
elixir(function(mix){
    mix.scripts([
        "admin/students.js"
    ], "public/js/min/manager-students.js");
});
elixir(function(mix){
    mix.scripts([
        "admin/studyplan.js"
    ], "public/js/min/manager-studyplan.js");
});
elixir(function(mix){
    mix.scripts([
        "lib/knockout.multiselect.js",
        "admin/tests.js",
    ], "public/js/min/manager-tests.js");
});
elixir(function(mix){
    mix.scripts([
        "lib/knockout-file-bindings.js",
        "lib/ace.js",
        "lib/worker-php.js",
        "lib/mode-c_cpp.js",
        "lib/mode-php.js",
        "lib/mode-pascal.js",
        "helpers/sendCode.js",
        "lib/wheelzoom.js",
        "admin/themes.js"
    ], "public/js/min/manager-themes.js");
});
elixir(function(mix){
    mix.scripts([
        "lib/wheelzoom.js",
        "admin/help.js",
    ], "public/js/min/manager-help.js");
});
// ------/MANAGER ------
