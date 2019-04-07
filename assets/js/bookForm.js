var collectionHolder;
var addNewSample = $('<input type="button" class="form-control btn btn-info" value="Ajouter un Exemplaire">');

$(document).ready(function () {

    // get collection
    collectionHolder = $('#exemplaires');

    // add remove button
    collectionHolder.find('.form-row').each(function (item) {
        addRemoveButton($(this));
    });

});

// Adding remove button
function addRemoveButton(item) {

    // Creating remove button
    let removeButton = $('<div class="col-1">' +
        '<input type="button" class="btn btn-danger" value="―">' +
        '</div>');

    // Handling click event
    removeButton.click(function (e) {
        console.log(e.target);
    });

    // Append button to row
    item.append(removeButton);

}