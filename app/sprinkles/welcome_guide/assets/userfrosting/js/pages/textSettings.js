/**
 * This script depends on uf-table.js, moment.js, handlebars-helpers.js
 *
 * Target page: /textSettings
 */
$(document).ready(function () {
    // Set up table of questions
    $("#widget-textSettings").ufTable({
        dataUrl: site.uri.public + "/api/textSettings",
        useLoadingTransition: site.uf_table.use_loading_transition
    });

    // Bind table buttons
    $("#widget-textSettings").on("pagerComplete.ufTable", function () {
        $(".js-displayForm-table").formGenerator();
        $(".js-displayConfirm-table").formGenerator('confirm');
        //bindTextSettingButtons($(this));
    });
});