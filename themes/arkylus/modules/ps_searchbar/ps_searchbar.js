/* global $ */
$(document).ready(function () {
    var $searchWidget = $('#search_widget');
    var $searchBox    = $searchWidget.find('input[type=text]');
    var searchURL     = $searchWidget.attr('data-search-controller-url');

    $.widget('prestashop.psBlockSearchAutocomplete', $.ui.autocomplete, {

        _renderItem: function (ul, product) {
            //ul.addClass('list-group');
            return $("<li>").addClass("m-t-1")
                .append($("<a>")
                    .append($('<img />').attr('src',product.cover.small.url).width('38px').height('38px').addClass("d-inline-block"))
                    
                    //.append($("<span>").html(product.category_name).addClass("category"))
                    //.append($("<span>").html(' - ').addClass("separator d-inline-block"))                    
                    .append($("<span>").html(product.name).addClass("product d-inline-block ml-1"))
                    
                ).appendTo(ul)
            ;
        }
    });

    $searchBox.psBlockSearchAutocomplete({
        minLength: 3,
        source: function (query, response) {
            $.post(searchURL, {
                s: query.term,
                resultsPerPage: 10
            }, null, 'json')
            .then(function (resp) {
                response(resp.products);
            })
            .fail(response);
        },
        select: function (event, ui) {
            var url = ui.item.url;
            window.location.href = url;
        },
    });
});
