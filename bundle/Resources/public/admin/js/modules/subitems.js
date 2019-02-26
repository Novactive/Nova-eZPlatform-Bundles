var eZMailingSubItemsModule = function () {
    var token, siteaccess;

    function _init($, $app) {
        token = document.querySelector('meta[name="CSRF-Token"]').content;
        siteaccess = document.querySelector('meta[name="SiteAccess"]').content;
        $(".ezmailing-subitem-children", $app).each(function ($container) {
            _generate($(this));
        });
    }

    function _generate($container) {
        var subitemsList = $container.data('items').SubitemsList;
        var items = subitemsList.SubitemsRow.map(function (elt) {
            return {
                content: elt.Content,
                location: elt.Location
            }
        });
        var contentTypes = $container.data('contentTypes').ContentTypeInfoList.ContentType;
        var contentTypesMap = contentTypes.reduce(function (total, item) {
            total[item._href] = item;
            return total;
        }, {});

        ReactDOM.render(React.createElement(eZ.modules.SubItems, {
            parentLocationId: $container.data('location'),
            limit: $container.data('limit'),
            items: items,
            contentTypesMap: contentTypesMap,
            totalCount: subitemsList.ChildrenCount,
            handleEditItem: function (content) {
                var contentId = content._id;
                var checkVersionDraftLink = Routing.generate('ezplatform.version_draft.has_no_conflict', {contentId: contentId});

                var submitVersionEditForm = function (content) {
                    document.querySelector('#form_subitems_content_edit_content_info').value = content._id;
                    document.querySelector('#form_subitems_content_edit_version_info_content_info').value = content._id;
                    document.querySelector('#form_subitems_content_edit_version_info_version_no').value =
                        content.CurrentVersion.Version.VersionInfo.versionNo;
                    document.querySelector('#form_subitems_content_edit_language_' + content.mainLanguageCode).checked = true;
                    document.querySelector('#form_subitems_content_edit_create').click();
                };

                var addDraft = function () {
                    submitVersionEditForm(content);
                    $('#version-draft-conflict-modal').modal('hide');
                };

                var showModal = (modalHtml) => {
                    var wrapper = document.querySelector('.ez-modal-wrapper');

                    wrapper.innerHTML = modalHtml;
                    var addDraftButton = wrapper.querySelector('.ez-btn--add-draft');
                    if (addDraftButton) {
                        addDraftButton.addEventListener('click', addDraft, false);
                    }

                    $(wrapper).find('.ez-btn--prevented').each(function (btn) {
                        btn.addEventListener('click', function (event) {
                            event.preventDefault()
                        }, false);
                    });

                    $('#version-draft-conflict-modal').modal('show');
                };

                $.ajax({
                    cache: false,
                    url: checkVersionDraftLink,
                    method: 'GET',
                    success: function () {
                        submitVersionEditForm(content);
                    },
                    error: function (data) {
                        showModal(data.responseText);
                    }
                });
            },
            generateLink: function (locationId) {
                return window.Routing.generate('_ezpublishLocation', {locationId: locationId});
            },
            restInfo: {token: token, siteaccess: siteaccess}
        }), $container.get(0));
    }

    return {init: _init};
}();
