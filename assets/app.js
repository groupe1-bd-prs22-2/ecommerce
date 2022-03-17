/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
// Import de TinyMCE
import tinymce from "tinymce";
import 'tinymce/themes/silver/theme';

tinymce.init({
    selector: '.tinymce'
})

// start the Stimulus application
import './bootstrap';
