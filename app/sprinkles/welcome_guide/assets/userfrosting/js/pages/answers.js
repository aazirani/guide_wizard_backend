/**
 * This script depends on uf-table.js, moment.js, handlebars-helpers.js
 *
 * Target page: /answers
 */
 $(document).ready(function() {
    // Set up table of answers
    $("#widget-answers").ufTable({
        dataUrl: site.uri.public + "/api/answers",
        useLoadingTransition: site.uf_table.use_loading_transition
    });

    // Bind table buttons
    $("#widget-answers").on("pagerComplete.ufTable", function () {
		$(".js-displayForm-table").formGenerator();
		$(".js-displayConfirm-table").formGenerator('confirm');
        //bindAnswerButtons($(this));
    });
});