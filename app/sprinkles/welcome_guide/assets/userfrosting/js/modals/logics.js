// Configure Sortable for list-of-operators
    new Sortable(document.getElementById('list-of-operators'), {
        group: {
            name: 'shared',
            put: false, // Disable putting items in this list
            pull: 'clone' // To clone: set pull to 'clone'
        },
        animation: 150
    });

// Configure Sortable for list-of-answers
    new Sortable(document.getElementById('list-of-answers'), {
        group: {
            name: 'shared',
            put: false, // Disable putting items in this list
            pull: 'clone', // To clone: set pull to 'clone'
        },
        animation: 150
    });

    $(document).ready(function() {
        // Handle input event on the search field
        $('#searchField').on('input', function() {
            var searchQuery = $(this).val().trim().toLowerCase();
            var selectedQuestion = $('#questionSelect').val().trim().toLowerCase();

            // Loop through each item in the list
            $('#list-of-answers .list-group-item').each(function() {
                var itemName = $(this).find('.answer-title').text().toLowerCase().trim();
                var itemQuestion = $(this).find('.question-text').text().toLowerCase().trim();
                // Check if the item name contains the search query and if the question matches the selected question
                if ((selectedQuestion === '' || itemQuestion === selectedQuestion) && (itemName.includes(searchQuery))) {
                    $(this).show(); // Show the item if it matches the search query and the selected question
                } else {
                    $(this).hide(); // Hide the item if it does not match the search query or the selected question
                }
            });
        });

        // Handle change event on the question select dropdown
        $('#questionSelect').on('change', function() {
            $('#searchField').trigger('input'); // Trigger the search again when the question selection changes
        });
    });

    // Initialize Sortable for expression list
    new Sortable(document.getElementById('expression-list'), {
        group: {
            name: 'shared',
            pull: true, // Enable pulling items from other lists
        },
        animation: 150,
        // Called by any change to the list (add / update / remove)
        onSort: function (/**Event*/evt) {
            updateExpressionInput();
            },
        // Element is removed from the list into another list
        onRemove: function (/**Event*/evt) {
            updateExpressionInput();
            },
    });

    // Function to handle removal of the clicked item
    function handleRemoveItemClick(event) {
        const removeIcon = $(event.target);
        const item = removeIcon.closest('.list-group-item');
        item.remove();
        updateExpressionInput();
    }

    // Function to handle mouseenter events on items in the expression list
    function handleItemMouseEnter(event) {
        const hoveredItem = $(event.target);
        if (hoveredItem.hasClass('list-group-item')) {
            hoveredItem.addClass('expression-list-item'); // Add the class with the cursor style
            hoveredItem.append('<span class="remove-icon">X</span>'); // Add the remove icon (X)
            // Add click event handler to the newly added X element
            hoveredItem.find('.remove-icon').on('click', handleRemoveItemClick);
        }
    }

    // Function to handle mouseleave events on items in the expression list
    function handleItemMouseLeave(event) {
        const item = $(event.target);
        if (item.hasClass('list-group-item')) {
            item.removeClass('expression-list-item'); // Remove the class with the cursor style
            item.find('.remove-icon').remove(); // Remove the remove icon
        }
    }

    // Add mouseenter and mouseleave event listeners to the expression list using event delegation
    $('#expression-list').on('mouseenter', '.list-group-item', handleItemMouseEnter);
    $('#expression-list').on('mouseleave', '.list-group-item', handleItemMouseLeave);

    // Function to update the input type text with the data from the expression list
    function updateExpressionInput() {
        const expressionItems = $('#expression-list').children('.list-group-item');
        const expressionData = expressionItems.map(function () {
            return $(this).attr('data');
        }).get().join(' '); // Join the data with a space separator

        const processedExpression = processExpression(expressionData);

        $('#field_expression').val(processedExpression);
    }

    function updateExpressionInput() {
        const expressionItems = $('#expression-list').children('.list-group-item');
        let expressionData = expressionItems.map(function () {
            return $(this).attr('data');
        }).get().join(' '); // Join the data without a space separator

        // Format the expression string
        expressionData = expressionData.replace(/!\s+/g, '!') // Remove spaces after unary "!"
                                .replace(/\s+\)/g, ')') // Remove spaces before closing parenthesis
                                .replace(/\(\s+/g, '(') // Remove spaces after opening parenthesis
                                .replace(/\s+(and|or|xor)\s+/g, ' $1 '); // Ensure spaces around binary operators

        $('#field_expression').val(expressionData);

        // Add regular expression check
        if (!isValidExpression(expressionData)) {
            $('#field_expression, #expression-list').addClass('invalid-input');
        } else {
            $('#field_expression, #expression-list').removeClass('invalid-input');
        }
    }


    // Call the updateExpressionInput function on page load to initialize the input value
    $(document).ready(function() {
        updateExpressionInput();
    });
    
    
    
    function isValidExpression(expr) {
        // Replace logical operators and operands with JavaScript-valid equivalents
        var jsExpr = expr.replace(/and/g, '&&')
                     .replace(/or/g, '||')
                     .replace(/xor/g, '^')
                     .replace(/!/g, '!')
                     .replace(/\b\d+\b/g, 'true');

        // Check if parentheses are balanced
        var depth = 0;
        for (var i = 0; i < jsExpr.length; i++) {
            if (jsExpr[i] === '(') {
                depth++;
            } else if (jsExpr[i] === ')') {
                if (depth === 0) {
                    return false;
                }
                depth--;
            }
        }
        if (depth !== 0) {
            return false;
        }

        // Try to evaluate the expression
        try {
            eval(jsExpr);
        } catch (e) {
            return false;
        }

        return true;
    }

