$('#exportType').change(function () {
    $('#table').bootstrapTable('destroy').bootstrapTable({
        exportTypes: ['csv', 'sql', 'excel'],
        exportDataType: $(this).val(),
        columns:[
            {
                field: 'state',
                checkbox: true,
                visible: $(this).val() === 'selected'
            }
        ]
    })
}).trigger('change');


