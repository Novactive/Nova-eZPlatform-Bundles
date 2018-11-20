/*
 * NovaeZRssFeedBundle.
 *
 * @package   NovaeZRssFeedBundle
 *
 * @author    Novactive
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZRssFeedBundle/blob/master/LICENSE
 *
 */

var $collectionHolder,
    containerId;

jQuery(document).ready(function () {
    $collectionHolder = containerId = $('.items-rss');
    $collectionHolder.data('index', $collectionHolder.find(':input').length);
    // for edition mode
    if (TWIG.getRssFieldsIndexes && TWIG.getRssFieldsIndexes.length) {
        $.each(TWIG.getRssFieldsIndexes, function (k, v) {
            setCTEvent(v);
        });
    }

    $('#open-child-form').on('click', function (e) {
        e.preventDefault();
        addchildForm($collectionHolder, containerId);
    });

    $(document).on('click', '.delete-rss-items', function (e) {
        e.preventDefault();
        $(this).parent().remove();
    });
});

function addchildForm($collectionHolder, containerId) {
    var prototype = $collectionHolder.data('prototype'),
        index = $collectionHolder.data('index');
    removeForm = $('<a class="btn btn-primary delete-rss-items pull-right"><svg><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/bundles/ezplatformadminui/img/ez-icons.svg#trash"></use></svg></a>');
    newForm = prototype.replace(/__name__/g, index);
    newRow = $('<section class="container mt-4">' +
        '<div class="card ez-card"><div class="card-body">' +
        newForm +
        '</div></div></section>');
    newRow.find('.card-body').prepend(removeForm);
    containerId.append(newRow);
    newIndex = index + 1;
    $collectionHolder.data('index', newIndex);
    document.dispatchEvent(new CustomEvent("rss.item.add", {"selector": newRow}));
    setCTEvent(index);
}

function setCTEvent(index) {
    $(document).on('change', '#rss_feeds_feed_items_' + index + '_contenttype_id', function (e) {
        var val = $(this).val(),
            prefixItem = "#rss_feeds_feed_items",
            selectFields = ["title", "description", "category", "media"],
            loader = $('<div class="loading-image">' +
                '<img src="' + TWIG.PathLoader + '" class="img-responsive" />' +
                '</div>');

        $(this).after(loader);
        $('#loading-image').show();
        $.ajax({
            url: TWIG.getRssFieldsPath + "?contenttype_id=" + val,
            type: 'POST',
            success: function (response) {
                $.each(selectFields, function (fieldKey, fieldName) {
                    $mainSelector = $(prefixItem + "_" + index + "_" + fieldName);
                    $mainSelector.html('');
                    $mainSelector.append($("<option value=''>[Passer]</option>")
                        .prop("selected", true));
                    $.each(response, function (k, v) {
                        $mainSelector.append($("<option></option>")
                            .attr("value", v).text(k));
                    });
                });
                loader.remove();
            }
        })
    });
}
