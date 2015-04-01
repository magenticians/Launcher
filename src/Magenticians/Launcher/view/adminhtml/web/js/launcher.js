require([
    'jquery',
    'jquery/ui'
], function($) {
    $(function() {
        $('#magenticians-launcher-link').click(function () {
            $('#magenticians-launcher-dialog').dialog({
                //autoOpen: true,
                modal: true,
                open: function() {
                    $(this).closest('.ui-dialog').addClass('ui-dialog-active');

                    //var topMargin = $(this).closest('.ui-dialog').children('.ui-dialog-titlebar').outerHeight() + 10;
                    //$(this).closest('.ui-dialog').css('margin-top', topMargin);
                }
            });
        });

        $('#magenticians-launcher-input').autocomplete({
            source: launcher_items,

            // On selecting an entry, we point the document to the location attached to it
            select: function (event, ui) {
                event.preventDefault();
                document.location = $(this).attr('data-target');
            },

            // When an entry receives focus we display the label in the input and store the URL in "data-target"
            focus: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
                $(this).attr('data-target', ui.item.value);
            },

            // When the autocomplete widget is initialized, we quickly remove the accessible helper as we don't need it
            create: function (event) {
                $(this).next('.ui-helper-hidden-accessible').remove();
            }
        });
    });
});