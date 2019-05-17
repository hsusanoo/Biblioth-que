//TODO: domain select2 bug always focusing

$('#exportType').change(function () {
    $('#table').bootstrapTable('refreshOptions', {
        exportTypes: ['csv', 'sql', 'excel'],
        exportDataType: $(this).val(),
        columns: [
            {
                field: 'state',
                checkbox: true,
                visible: $(this).val() === 'selected'
            }
        ]
    })
});

$('#exportType').val('selected').trigger('change');

$.get("/admin/books/getcat",function (data, status) {

    $('#domaine').select2({
        data: data.results,
        language: 'fr',
        width: '100%'
    });
});

