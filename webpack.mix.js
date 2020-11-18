const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/assets').version();
mix.sass('resources/sass/app.scss', 'public/assets').version();

// // Fonts
// mix.copyDirectory('node_modules/font-awesome5/webfonts', 'public/assets/fonts/fontawesome');
// mix.copyDirectory('resources/assets/fonts/FrizQuadrata', 'public/assets/fonts');

// // Image
// mix.copyDirectory('resources/assets/img/', 'public/assets/img');
// mix.copyDirectory('resources/assets/videos/', 'public/assets/videos');

// // Tinymce
// mix.copy('node_modules/tinymce/jquery.tinymce.min.js', 'public/assets/js/tinymce/jquery.tinymce.min.js');
// mix.copy('node_modules/tinymce/tinymce.min.js', 'public/assets/js/tinymce/tinymce.min.js');
// mix.copyDirectory('node_modules/tinymce/plugins', 'public/assets/js/tinymce/plugins');
// mix.copyDirectory('node_modules/tinymce/skins', 'public/assets/js/tinymce/skins');
// mix.copyDirectory('node_modules/tinymce/themes', 'public/assets/js/tinymce/themes');