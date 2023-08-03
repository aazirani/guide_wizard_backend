// Function to update the order of tasks in the hidden input field
function updateOrder() {
    let order = Array.from(document.getElementById('list-of-elements').children).map(element => element.id).join(",");
    document.getElementById('field_subTasksOrder').value = order;
}

// Configure Sortable for list-of-elements
new Sortable(document.getElementById('list-of-elements'), {
    handle: '.handle', // handle's class
    animation: 150,
    onEnd: function (/**Event*/evt) {
        updateOrder();
    },
});

// Initially set the order
updateOrder();