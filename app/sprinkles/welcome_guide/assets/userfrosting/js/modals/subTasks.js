$(document).ready(function() {
    // Define the click event handler function
    var handleSubTaskItemClick = function() {
        var itemId = $(this).attr('data');
        var selectedSubTasks = $('#field_subTasks').val() ? $('#field_subTasks').val().split(',') : [];

        // Add or remove the item from the selected subtasks
        if ($(this).hasClass('selected')) {
            $(this).removeClass('selected');
            $(this).find('.fa-check').addClass('hidden');
            selectedSubTasks = selectedSubTasks.filter(function(id) {
                return id !== itemId;
            });
        } else {
            $(this).addClass('selected');
            $(this).find('.fa-check').removeClass('hidden');
            selectedSubTasks.push(itemId);
        }

        $('#field_subTasks').val(selectedSubTasks.join(','));
    };

    // Bind the click event to the handleSubTaskItemClick function
    $('#list-of-subtasks .grid-group-item').on('click', handleSubTaskItemClick);

    // Handle input event on the subtask search field
    $('#searchFieldSubTasks').on('input', function() {
        var searchQuery = $(this).val().trim().toLowerCase();
        var selectedTask = $('#taskSelect').val().trim().toLowerCase();

        // Loop through each item in the list
        $('#list-of-subtasks .grid-group-item').each(function() {
            var itemName = $(this).find('.subtask-title').text().toLowerCase().trim();
            var itemTask = $(this).find('.task-text').text().toLowerCase().trim();
            // Check if the item name contains the search query and if the task matches the selected task
            if ((selectedTask === '' || itemTask === selectedTask) && (itemName.includes(searchQuery))) {
                $(this).show(); // Show the item if it matches the search query and the selected task
            } else {
                $(this).hide(); // Hide the item if it does not match the search query or the selected task
            }
        });
    });

    // Handle change event on the task select dropdown
    $('#taskSelect').on('change', function() {
        $('#searchFieldSubTasks').trigger('input'); // Trigger the search again when the task selection changes
    });

    // Unbind the click event
    $('#list-of-subtasks .grid-group-item').off('click', handleSubTaskItemClick);

    var selectedSubTasks = $('#field_subTasks').val().split(',');

    selectedSubTasks.forEach(function(subTaskId) {
        // Ignore empty strings
        if (subTaskId) {
            var subTaskItem = $('#list-of-subtasks .grid-group-item[data=' + subTaskId + ']');
            subTaskItem.addClass('selected');
            subTaskItem.find('.fa-check').removeClass('hidden');
        }
    });

    // Rebind the click event
    $('#list-of-subtasks .grid-group-item').on('click', handleSubTaskItemClick);
});
