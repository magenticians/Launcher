jQuery(document).ready(function($) {
    $('#magenticians-launcher-link').click(function() {
        $('#magenticians-launcher-dialog').dialog({
            width: 500
        });
    });

    $('#magenticians-launcher-input').autocomplete({
        source: launcher_items,
        select: function(event, ui) {
            event.preventDefault();
            document.location = $(this).attr('data-target');
        },
        focus: function(event, ui) {
            event.preventDefault();
            $(this).val(ui.item.label);
            $(this).attr('data-target', ui.item.value);
        },
        create: function (event) {
            $(this).next('.ui-helper-hidden-accessible').remove();
        }
    });
});