var $exmcollectionHolder;
var $autCollectionHolder;
var $addNewSample = $('<button type="button" class="btn btn-dark btn-block"><i class="fas fa-plus"></i> Exemplaire</button>');
var $addNewAut = $('<button type="button" class="btn btn-dark btn-block"><i class="fas fa-plus"></i> Auteur</button>');
var $exmCardBody = $('#exemplaires div.card-body');
var $autCardBody = $('#auteurs div.card-body');
var exmindex;
var autindex;

$(document).ready(function () {

    $.get("/admin/books/gettags", function (data, status) {

        let tagString = $('.select-two-multiple:first').attr('value');

        if (tagString) {
            let tags = tagString.split(',');
            for (let i = 0; i < data.results.length; i++) {
                for (let j = 0; j < tags.length; j++) {
                    if (data.results[i].text === tags[j]) {
                        data.results[i]['selected'] = true
                    }
                }
            }
        }


        $('.select-two-multiple').select2({
            data: data.results,
            language: 'fr',
            tags: true,
            tokenSeparators: [','],
            placeholder: "Clé1,Clé2,.."
        });
    });


    // get collection
    $exmcollectionHolder = $('#exemplaires');
    $autCollectionHolder = $('#auteurs');

    // setting current exmindex
    exmindex = $exmcollectionHolder.find('.form-row').length;
    autindex = $autCollectionHolder.find('form-row').length;

    // Add buttons
    $('#exemplaires div.card-footer').append($addNewSample);
    $('#auteurs div.card-footer').append($addNewAut);

    //Handle adding a new sample button
    $addNewSample.click(function (e) {
        e.preventDefault();
        addNewSampleRow();
    });

    // Handleadding new author button
    $addNewAut.click(function (e) {
        e.preventDefault();
        addNewAuthorRow();
    });

});

// Adding new sample row
function addNewSampleRow() {

    // Getting the prototype
    let prototype = $exmcollectionHolder.data('prototype');

    // Creating new form row
    let newForm = prototype;
    newForm = newForm.replace(/__name__/g, exmindex++);


    // creating form
    let $formRow = $('<div class="form-row"></div>');
    let $nInentaire = $('<div class="col col-sm-6 col-md-6 col-6 col-lg-6"></div>');
    let $cote = $('<div class="col col-sm-4 col-md-4 col-4 col-lg-4"></div>');

    $nInentaire.append($(newForm).find('div.form-group:first'));
    $cote.append($(newForm).find('div.form-group:last'));
    $formRow.append($nInentaire);
    $formRow.append($cote);
    addRemoveButton($($formRow));


    //Removing labels + adding placeholders instead
    $($formRow).find('label').remove();
    $($formRow).find('input:first').attr("placeholder", "N° Inventaire");
    $($formRow).find('input:last').attr("placeholder", "Cote");

    //Appending form to card
    $exmCardBody.append($formRow);

}

function addNewAuthorRow() {

    // Getting the prototype
    let prototype = $autCollectionHolder.data('prototype');

    // Creating new form row
    let newForm = prototype;
    newForm = newForm.replace(/__name__/g, autindex++);

    // creating form
    let $formRow = $('<div class="form-row"></div>');
    let $nom = $('<div class="col col-sm-10 col-md-10 col-10 col-lg-10"></div>');

    $nom.append($(newForm).find('div.form-group:first'));
    $formRow.append($nom);

    addRemoveButton($($formRow));

    //Removing labels + adding placeholders instead
    $($formRow).find('label').remove();
    $($formRow).find('input').attr("placeholder", "Nom");

    //Appending form to card
    $autCardBody.append($formRow);

}

// Adding remove button
function addRemoveButton(item) {

    // Creating remove button
    let removeButton = $('<div class="col-1">' +
        '<input type="button" class="btn btn-danger" value="―">' +
        '</div>');

    // Handling click event
    removeButton.click(function (e) {

        e.preventDefault();

        $(e.target).parents('.form-row').slideUp(200, function () {
            $(this).remove();
        });
    });

    // Append button to row
    item.append(removeButton);

}


// Cover img
function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function (e) {
            $('#coverPreview').attr('src', e.target.result);
            $('#coverPreview').hide();
            $('#coverPreview').fadeIn(650);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$(".cover-upload input[type='file']").change(function () {
    readURL(this);
});