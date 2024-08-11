<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<?php

if (isset($_SESSION['user_id'])) {
    echo '<script src="../public/js/script.js?2"></script>';
}
?>
<script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('<?=PUSHER_KEY?>', {
        cluster: '<?=PUSHER_CLUSTER?>'
    });

    var channel = pusher.subscribe('twilio');
    channel.bind('inbound-message', function(data) {
        const selectConversation = $('.conversation-card.active').data('conversation_id');
        
        console.log(parseInt(selectConversation))
        console.log(parseInt(data.conversation_id))
        console.log(parseInt(data.conversation_id) == parseInt(selectConversation))
        
        if (selectConversation !== undefined && parseInt(data.conversation_id) == parseInt(selectConversation)) {
            fetchMessagesAndDisplay(data.conversation_id);
        } else {
            fetchConversationsAndUpdateUI();
        }
    });
</script>
</body>
</html>
