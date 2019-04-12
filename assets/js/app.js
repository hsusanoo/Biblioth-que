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
require('bootstrap-table/dist/locale/bootstrap-table-fr-FR');
require('tableexport.jquery.plugin/libs/FileSaver/FileSaver.min');
require('xlsx/dist/xlsx.core.min');
var jsPDF = require('jspdf');
require('jspdf-autotable');
require('tableexport.jquery.plugin/tableExport.min');
require('bootstrap-table/dist/extensions/export/bootstrap-table-export');
require('bootstrap-table/dist/extensions/cookie/bootstrap-table-cookie');
require('bootstrap-table/dist/extensions/accent-neutralise/bootstrap-table-accent-neutralise');
require('bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile');
require('popper.js');
require('toastr');
require('bootstrap-datepicker');
require('bootstrap-datepicker/js/locales/bootstrap-datepicker.fr-CH');
require('@fortawesome/fontawesome-free/js/all');
require('select2/dist/js/select2.full');
require('select2/dist/js/i18n/fr');


$(document).ready(function () {

    $('.js-datepicker').datepicker({
        format: "dd/mm/yyyy",
        endDate: "+0d",
        todayBtn: "linked",
        language: "fr",
        forceParse: false,
        autoclose: true,
        todayHighlight: true
    });

    $('.select-two-multiple').select2({
        language: 'fr',
        tags: true,
        tokenSeparators: [','],
        placeholder: "Clé1,Clé2,.."
    });
    $('.select-two').select2({
        language: 'fr'
    })

});

// assets/js/_main.js
const imagesContext = require.context('../img', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);