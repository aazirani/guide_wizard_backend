/**
 * This script depends on uf-table.js, moment.js, handlebars-helpers.js
 *
 * Target page: /questions
 */
 $(document).ready(function() {
    // Set up table of questions
    $("#widget-steps").ufTable({
        dataUrl: site.uri.public + "/api/steps",
        useLoadingTransition: site.uf_table.use_loading_transition
    });

    // Bind table buttons
    $("#widget-steps").on("pagerComplete.ufTable", function () {
		$(".js-displayForm-table").formGenerator();
		$(".js-displayConfirm-table").formGenerator('confirm');
        //bindStepButtons($(this));
    });
});