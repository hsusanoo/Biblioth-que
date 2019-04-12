var $table = $('#table');
$(document).ready(function () {
    $table.bootstrapTable('destroy').bootstrapTable({
        classes: "table-bordered table-hover table-striped table-sm",
        exportTypes: ['csv', 'sql', 'excel', 'pdf']
    });
});

// Data formatters
function statutFormatter(value) {
    let statut, badge;
    if (value > 0) {
        statut = 'Disponible';
        badge = 'success';
    } else {
        statut = 'Indisponible';
        badge = 'light';
    }
    return '<span class="badge progress-bar-' + badge + '" style="margin-bottom: 5px">' + statut + '</span>';
}

function coverFormatter(value) {
    return '<img src="../img/livres/' + value + '" style="height: 100px; width: 70px;" ' +
        'alt="book-cover" onerror="this.onerror=null;this.src=\'images/notfound.png\'">';
}

function textFormatter(value) {
    return '<span style="display: flex; flex-direction: row; max-height: 100px; overflow: auto;">' + value + '</span>';
}

function quantiteFormatter(value) {
    return '<span class="badge progress-bar-danger ml-2">' + value + '</span>';
}