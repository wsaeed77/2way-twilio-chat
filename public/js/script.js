const baseUrl = '/src/API/'; // Set the base URL for API endpoints
$(document).ready(function() {


    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    $('#composeMessageForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Show loading spinner
        $('body').append(`
        <div id="loadingSpinner" class="d-flex justify-content-center" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1051;">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);

        var toNumber = $('#toNumber').val();
        var messageText = $('#messageText').val();
        

        $.ajax({
            type: 'POST',
            url: baseUrl + 'createNewConversation.php',
            data: {
                toNumber: toNumber,
                messageBody: messageText,
                direction: 'outbound' // Assuming direction is always 'outbound' for outgoing messages
            },
            success: function(response) {
                // Hide loading spinner
                $('#loadingSpinner').remove();

                // Parse the JSON response
                var jsonResponse = JSON.parse(response);

                if(jsonResponse.success) {
                    toastr.success('Message sent successfully');
                    $('#composeModal').modal('hide'); // Hide the modal

                    // Optionally, clear the form
                    $('#composeMessageForm')[0].reset();

                    // Fetch and display the new conversation
                    fetchConversationsAndUpdateUI();
                    var conversationId = jsonResponse.conversationId;

                    // Optionally, call fetchMessagesAndDisplay for the new conversation
                    fetchMessagesAndDisplay(conversationId);
                    // makeConversationActive(conversationId);

                } else {
                    alert('Failed to send message: ' + jsonResponse.message);
                }
            },
            error: function() {
                // Hide loading spinner
                $('#loadingSpinner').remove();
                alert('Error making the request');
            }
        });
    });

    $(document).on('click', '.conversation-card', function() {
        // Remove 'active' class from all conversation cards
        $('.conversation-card').removeClass('active');

        // Add 'active' class to the clicked conversation card
        $(this).addClass('active');

        // Retrieve the conversation ID from the clicked card
        var conversationId = $(this).data('conversation_id');

        // Call fetchMessagesAndDisplay with the conversation ID
        fetchMessagesAndDisplay(conversationId);
        checkAndToggleReplyButtonState();
    });

    $(document).on('click', '.fa-trash-alt', function() {
        // Ask user for confirmation before deleting
        if (confirm('Are you sure you want to delete this conversation?')) {
            // Find the active conversation card and retrieve its conversation_id
            var activeConversationId = $('.conversation-card.active').data('conversation_id');

            console.log(activeConversationId);

            $.ajax({
                type: 'POST',
                url: baseUrl + 'deleteConversation.php',
                data: { conversationId: activeConversationId },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        // Handle success (e.g., remove the conversation from the UI)
                        toastr.success('Conversation deleted successfully');
                        // Remove the active conversation card from the UI
                        $('.conversation-card.active').remove();
                        // Clear the message container
                        $('.container.py-5').empty();
                    } else {
                        // Handle failure
                        toastr.error('Failed to delete conversation');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error: " + status + ' - ' + error);
                }
            });
        } else {
            // User cancelled the delete operation
            console.log('Conversation deletion cancelled.');
        }
    });

    $('.reply-btn').click(function() {
        var activeConversationId = $('.conversation-card.active').data('conversation_id'); // Get active conversation ID
        var messageBody = $('.fixed-at-bottom .form-control').val().trim(); // Get message body from input field and trim whitespace

        // Set direction as outbound
        var direction = 'outbound';

        // Ensure all necessary data is present
        if (!activeConversationId) {
            console.error('Missing active conversation ID.');
            return; // Stop execution if the active conversation ID is missing
        } else if (!messageBody) {
            toastr.error('Message body is required.');
            return; // Stop execution if the message body is missing or empty
        }

        // Show loading spinner
        // Disable the reply button to prevent multiple clicks
        $('.reply-btn').prop('disabled', true);

        // Clear the text inside the reply button
        $('.reply-btn').empty();

        // Append a loading spinner to the reply button
        $('.reply-btn').append(`
            <div id="loadingSpinner" class="d-inline">
                <button class="buttonload">
                    <i class="fa fa-spinner fa-spin"></i>
                </button>
            </div>
        `);

        // AJAX request to send the message
        $.ajax({
            url: baseUrl + 'createNewMessage.php',
            type: 'POST',
            data: {
                conversationId: activeConversationId,
                messageBody: messageBody,
                direction: direction
            },
            success: function(response) {
                $('#loadingSpinner').remove();
                toastr.success('Message sent successfully');
                $('.fixed-at-bottom .form-control').val(''); // Optionally clear the input field after sending
                fetchMessagesAndDisplay(activeConversationId); // Fetch and display messages after sending
                $('.reply-btn').prop('disabled', false).text('Send'); // Enable the reply button and set its text to "Send"
            },
            error: function(xhr, status, error) {
                $('#loadingSpinner').remove();
                toastr.error('Error Sending Message');
                $('.reply-btn').prop('disabled', false).text('Send'); // Enable the reply button and set its text to "Send"
            }
        });
    });

    $('.fixed-at-bottom .form-control').click(function() {
        // Assuming '.container.py-5' is the message container
        var messageContainer = $('.container.py-5.scrollable-content');
        // Scroll to the bottom of the message container
        messageContainer.scrollTop(messageContainer.prop('scrollHeight'));
    });

    $('#searchInput').on('input', function() {
        var searchQuery = $(this).val();
        $.ajax({
            url: baseUrl + 'searchConversations.php',
            type: 'GET',
            data: { query: searchQuery },
            dataType: 'json',
            success: function(response) {
                const sidebar = $('.overflow-auto');
                sidebar.empty(); // Clear existing conversations

                response.forEach(function(conversation) {
                    const card = createConversationCard(conversation);
                    sidebar.append(card);
                });
            },
            error: function() {
                alert('Error fetching search results.');
            }
        });
    });

    fetchAndPopulateNumbers();
    fetchConversationsAndUpdateUI();
    checkAndToggleReplyButtonState();
});

function fetchMessagesAndDisplay(conversationId) {
    $.ajax({
        url: baseUrl + 'fetchMessages.php',
        type: 'POST',
        data: { conversationId: conversationId },
        dataType: 'json',
        success: function(response) {
            const messageContainer = $('.container.py-5');
            messageContainer.empty();
            // Retrieve the to_number from the active conversation card
            var toNumber = $('.conversation-card.active').data('to_number');

            // Append the round pill with the to_number to the messageContainer
            messageContainer.append(`<div class="round-pill-top-right">${toNumber}</div>`);

            let lastDate = null;

            if (Array.isArray(response)) {
                response.forEach(function(message) {
                    const messageDate = new Date(message.created_at).toLocaleDateString();
                    const messageTime = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    if (messageDate !== lastDate) {
                        messageContainer.append(`<div class="date-separator text-center my-3 py-2">${messageDate}</div>`);
                        lastDate = messageDate;
                    }

                    let messageHTML = '';
                    if (message.direction === 'outbound') {
                        messageHTML = `
                            <div class="row mb-2 justify-content-end">
                                <div class="col-9 text-right d-flex align-items-center" style="justify-content: flex-end;display: flex; align-items: center;">
                                    <div class="pb-4 outboundbtn text-white rounded">
                                        ${message.message_body}
                                        <div class="message-time px-2 py-1" style="display: inline-block; margin-left: 8px; font-size: 0.8em;">${messageTime}</div>
                                    </div>
                                    <i class="fa fa-user-circle-o fa-2x" style="color: #007bff; margin-left: 8px;"></i> <!-- Round icon for outbound messages -->
                                </div>
                            </div>
                        `;
                    } else if (message.direction === 'inbound') {
                        messageHTML = `
                            <div class="row mb-2">
                                <div class="col-9 d-flex align-items-center" style=" display: flex; align-items: center; justify-content: flex-start;">
                                    <i class="fa fa-user-circle-o fa-2x" style="color: #6e6b7b; margin-right: 8px;"></i> <!-- Round icon for inbound messages -->
                                    <div class="pb-4 inboundbtn rounded">
                                        ${message.message_body}
                                        <div class="message-time text-bg-dark px-2 py-1" style="display: inline-block; margin-left: 8px; font-size: 0.8em;">${messageTime}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                        // Check if the message is unread and mark it as read
                        if (message.is_read === 0) {
                            markMessageAsRead(message.id);
                        }
                    }

                    messageContainer.append(messageHTML);
                });

                // Scroll to the bottom of the message container
                messageContainer.scrollTop(messageContainer.prop('scrollHeight'));
            } else if (response && response.error) {
                console.error("Error:", response.error);
            } else {
                console.error("Unexpected response format:", response);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching messages:", status, error);
        }
    });
}

function fetchConversationsAndUpdateUI() {
    // Target the sidebar element
    const sidebar = $('.overflow-auto');

    // Append the loading spinner to the sidebar
    sidebar.append(`
        <div id="loadingSpinner" class="d-flex justify-content-center" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);

    $.ajax({
        url: baseUrl + 'fetchConversations.php',
        type: 'GET',
        dataType: 'json',
        success: function(conversations) {
            sidebar.empty(); // Clear existing conversations and the loading spinner

            if (conversations.length === 0) {
                // If no conversations are found, display a message
                sidebar.html('<p>No Messages Available.</p>');
            } else {
                conversations.forEach(function(conversation) {
                    const card = createConversationCard(conversation);
                    sidebar.append(card);
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error fetching conversations:', textStatus, errorThrown);
            sidebar.html('<p>Error loading conversations.</p>'); // Clear the loading spinner and show error message
        }
    });
}

function createConversationCard(conversation) {
    // Assuming conversation object includes `unread_messages_count`
    let unreadBadgeHTML = '';
    if (conversation.unread_messages_count > 0) {
        unreadBadgeHTML = `<span class="badge rounded-pill bg-primary">${conversation.unread_messages_count} New</span>`;
    }

    return `
    <div class="card mb-3 conversation-card conversation_${conversation.id}" data-conversation_id="${conversation.id}" data-to_number="${conversation.to_number}">
        <div class="card-body d-flex">
            <span class="fa fa-user rounded-circle me-2" style="font-size: 50px;"></span>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between">
                    <h5 class="card-title">${conversation.to_number}</h5>
                    <small class="text-muted">${conversation.latest_message_at}</small>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <p class="card-text mb-0">${conversation.from_number}</p>
                    ${unreadBadgeHTML} <!-- Display unread message count -->
                </div>
            </div>
        </div>
    </div>
    `;
}

function fetchAndPopulateNumbers() {
    $.ajax({
        type: "GET",
        url: baseUrl + 'fetchNumbers.php',
        success: function(response) {
            var numbers = JSON.parse(response);
            var toNumberSelect = $('#toNumber');
            toNumberSelect.empty(); // Clear existing options

            numbers.forEach(function(number) {
                toNumberSelect.append($('<option>', {
                    value: number.number,
                    text: number.name + " (" + number.number + ")"
                }));
            });
        },
        error: function() {
            alert("Failed to fetch numbers.");
        }
    });
}

function markMessageAsRead(messageId) {
    $.ajax({
        url: baseUrl + 'markMessageAsRead.php',
        type: 'POST',
        data: { messageId: messageId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                console.log('Message marked as read');
                // Optionally, update the UI here
                // For example, remove a 'new' badge from the message
                $('.message_' + messageId).removeClass('unread').addClass('read');
            } else {
                console.error('Failed to mark message as read:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error marking message as read:', error);
        }
    });
}

function checkAndToggleReplyButtonState() {
    // Check if there is an active conversation
    if ($('.conversation-card.active').length) {
        $('.reply-btn').prop('disabled', false);
    } else {
        $('.reply-btn').prop('disabled', true);
    }
}
