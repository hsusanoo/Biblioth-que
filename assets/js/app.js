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
require('popper.js');
require('perfect-scrollbar');
require('@coreui/coreui');
require('bootstrap-table');
require('bootstrap-table/dist/locale/bootstrap-table-fr-FR');
require('bootstrap-table/dist/extensions/filter-control/bootstrap-table-filter-control');
require('tableexport.jquery.plugin/libs/FileSaver/FileSaver.min');
require('xlsx/dist/xlsx.core.min');
var jsPDF = require('jspdf');
require('jspdf-autotable');
require('tableexport.jquery.plugin/tableExport.min');
require('bootstrap-table/dist/extensions/export/bootstrap-table-export');
require('bootstrap-table/dist/extensions/cookie/bootstrap-table-cookie');
require('bootstrap-table/dist/extensions/accent-neutralise/bootstrap-table-accent-neutralise');
require('bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile');
require('toastr');
require('bootstrap-datepicker');
require('bootstrap-datepicker/js/locales/bootstrap-datepicker.fr-CH');
var moment = require('moment');
require('moment/locale/fr');
require('daterangepicker');
require('@fortawesome/fontawesome-free/js/all');
require('select2/dist/js/select2.full');
require('select2/dist/js/i18n/fr');
require('store');
require('jquery-resizable-columns/dist/jquery.resizableColumns.min');


// Override update method
$.fn.select2.amd.require._defined['select2/selection/search'].prototype.update = function (a, b) {
    var c = this.$search[0] == document.activeElement;
    this.$search.attr("placeholder", "");
    a.call(this, b);
    this.$selection.find(".select2-selection__rendered").append(this.$searchContainer);
    this.resizeSearch();
    if (c) {
        var self = this;
        window.setTimeout(function () {
            self.$search.focus();
        }, 0);
    }
};


$(document).ready(function () {

    // Poppers
    $(function () {
        $('[data-toggle="popover"]').popover({
            container: 'body',
            trigger: 'focus'
        })
    });

    // Datepickers
    $('.js-month-picker').datepicker({
        format: "mm/yyyy",
        startView: "months",
        minViewMode: "months",
        language: "fr",
    });

    $('.js-year-picker').datepicker({
        format: "yyyy",
        startView: "years",
        minViewMode: "years",
        language: "fr",
    });

    $('.js-datepicker').datepicker({
        format: "dd/mm/yyyy",
        endDate: "+0d",
        todayBtn: "linked",
        language: "fr",
        forceParse: false,
        autoclose: true,
        todayHighlight: true
    });

    // Select tags
    $('.select-two').select2({
        lang: 'fr',
    })

});

//Moment.JS Return Date Ranges
function getDates(startDate, endDate) {
    let dateArray = [];
    let currentDate = moment(startDate);
    let stopDate = moment(endDate);
    while (currentDate <= stopDate) {
        dateArray.push(moment(currentDate).format('DD/MM/YYYY'));
        currentDate = moment(currentDate).add(1, 'days');
    }
    return dateArray;
}

// dateRangePicker filter

var startDate;
var endDate;

$(function () {

    let min = moment().subtract(10, 'years');
    let max = moment();

    $('.date-range').daterangepicker({
        "autoApply": true,
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Appliquer",
            "cancelLabel": "Annuler",
            "fromLabel": "De",
            "toLabel": "à",
            "customRangeLabel": "Personnaliser",
            "weekLabel": "S",
            "daysOfWeek": [
                "Lun",
                "Mar",
                "Mer",
                "Jeu",
                "Ven",
                "Sam",
                "Dim"
            ],
            "monthNames": [
                "Janvier",
                "Février",
                "Mars",
                "Avril",
                "Mai",
                "Juin",
                "Juillet",
                "Aout",
                "Septembre",
                "Octobre",
                "Novembre",
                "Decembre"
            ],
            "firstDay": 0
        },
        ranges: {
            'Aujourd\'hui': [moment(), moment()],
            'Hier': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Derniers 7 jours': [moment().subtract(6, 'days'), moment()],
            'Dernier 30 jours': [moment().subtract(29, 'days'), moment()],
            'Ce mois': [moment().startOf('month'), moment().endOf('month')],
            'Mois Dernier': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Cette année': [moment().startOf('year'), moment().endOf('year')]
        },
        "minDate": min,
        "maxDate": max
    }, function (start, end, label) {
        //console.log('New date range selected: ' + start.format('DD/MM/YYYY') + ' to ' + end.format('DD/MM/YYYY') + ' (predefined range: ' + label + ')');
        startDate = start.format('DD/MM/YYYY');
        endDate = end.format('DD/MM/YYYY');
    });
});

$('#filter_button').click(function (e) {

    let statut = $('#statut').val();
    let cat = $('#domaine').val();

    $('#table').bootstrapTable('refreshOptions', {
        url: '/admin/books/get',
        queryParams: getParams(statut,cat,startDate,endDate)
    });

    function getParams(statut,cat,startDate,endDate) {

        let params = {};

        if (statut !== '')
            params.statut = statut;
        if (cat !== '')
            params.cat = cat;
        if (startDate && endDate){
            params.start = startDate;
            params.end = endDate
        }
        console.log(params);
        return params
    }

});

// assets/js/_main.js
const imagesContext = require.context('../img', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);