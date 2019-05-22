
require('../css/search.scss');

var result = [];
$.get('/search/get', function (data, status) {
    // converting to array
    $.each(data.results, function (index, value) {
        result.push(value);
    });
    var newResult = result;
    $('.ui.search')
        .search({
            apiSettings: {
                'response': function (e) {
                    let searchTerm = e.urlData.query;
                    let result = newResult.map(function (cat) {

                        let items = cat.results.filter(function (item) {
                            return item.title.toLowerCase().includes(searchTerm.toLowerCase());
                        });

                        if (items !== null) {
                            let category = {'name': cat.name};
                            category.results = items;
                            return category;
                        }
                    });

                    return {'results': result};
                }
            },
            type: 'category',
            fullTextSearch: false,
        })
    ;

});