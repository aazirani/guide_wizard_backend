// Function to update the order of tasks in the hidden input field
function updateSubTaskOrder() {
    let order = Array.from(document.getElementById('list-of-elements').children).map(element => element.id).join(",");
    let element = document.getElementById('field_subTasksOrder');
    if (element) {
        element.value = order;
    }
}


$(document).ready(function () {
    //only create the sortable when we are working with subtasks
    if (document.getElementById('field_subTasksOrder')) {
        // Configure Sortable for list-of-elements
        new Sortable(document.getElementById('list-of-elements'), {
            handle: '.handle', // handle's class
            animation: 150,
            onEnd: function (/**Event*/evt) {
                updateSubTaskOrder();
            },
        });
    }
});


// Initially set the order
updateSubTaskOrder();