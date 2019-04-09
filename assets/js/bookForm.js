var $collectionHolder;
var $addNewSample = $('<input type="button" class="form-control btn btn-info" value="Ajouter un Exemplaire">');
var $exmCardBody = $('#exemplaires div.card-body');
var index;

$(document).ready(function () {

    // get collection
    $collectionHolder = $('#exemplaires');

    // setting current index
    index = $collectionHolder.find('.form-row').length;


    // add remove button foreach form-row
    // $collectionHolder.find('.form-row').each(function () {
    //     addRemoveButton($(this));
    // });

    // add "Ajouter un exemplaire" button
    $('#exemplaires div.card-footer').append($addNewSample);

    //Handle adding a new sample button
    $addNewSample.click(function (e) {

        e.preventDefault();

        addNewRow();
    })

});

// Adding new sample row
function addNewRow(){

    // Getting the prototype
    let prototype = $collectionHolder.data('prototype');

    // Creating new form row
    let newForm = prototype;
    newForm = newForm.replace(/__name__/g,index++);


    // creating form
    let $formRow    = $('<div class="form-row"></div>');
    let $nInentaire = $('<div class="col col-sm-6 col-md-6 col-6 col-lg-6"></div>');
    let $cote       = $('<div class="col col-sm-4 col-md-4 col-4 col-lg-4"></div>');

    $nInentaire.append($(newForm).find('div.form-group:first'));
    $cote.append($(newForm).find('div.form-group:last'));
    $formRow.append($nInentaire);
    $formRow.append($cote);
    addRemoveButton($($formRow));


    //Removing labels + adding placeholders instead
    $($formRow).find('label').remove();
    $($formRow).find('input:first').attr("placeholder","N° Inventaire");
    $($formRow).find('input:last').attr("placeholder","Cote");

    //Appending form to card
    $exmCardBody.append($formRow);

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