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
}).trigger('change');

$('#statut').change(function () {
    console.log("Selected "+$(this).val());
    if ($(this).val() !== '') {
        $('#table').bootstrapTable('filterBy',{
            statut: $(this).val()
        })
    }
});

