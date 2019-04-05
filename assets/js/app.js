/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
global.$ = $;

require('bootstrap');
require('bootstrap-table');
require('popper.js');
require('toastr');
require('bootstrap-datepicker');
require('@fortawesome/fontawesome-free/js/all');


$(document).ready(function () {

    $('.js-datepicker').datepicker({
        format: "dd/mm/yyyy",
        endDate: "+0d",
        todayBtn: "linked",
        language: "fr",
        autoclose: true,
        forceParse: false,
        todayHighlight: true
    });

});

// assets/js/_main.js
const imagesContext = require.context('../img', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);