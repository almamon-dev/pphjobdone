<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Real-time Test</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <style>
        body { font-family: sans-serif; background: #121212; color: #fff; padding: 20px; }
        #messages { border: 1px solid #333; height: 300px; overflow-y: scroll; padding: 10px; background: #1e1e1e; border-radius: 8px; }
        .msg { margin-bottom: 10px; padding: 10px; border-bottom: 1px solid #333; }
        .sender { color: #bb86fc; font-weight: bold; font-size: 0.8em; }
        .text { margin-top: 5px; }
        .status { color: #03dac6; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2>Pusher Real-time Test</h2>
    <div id="status" class="status">Connecting to Pusher (Channel: chat-debug)...</div>
    <div id="messages">
        <div class="msg">Waiting for messages...</div>
    </div>

    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}'
        });

        var channel = pusher.subscribe('chat-debug');
        
        channel.bind('message.sent', function(data) {
            var messagesDiv = document.getElementById('messages');
            var statusDiv = document.getElementById('status');
            
            if(messagesDiv.innerHTML.includes('Waiting for messages...')) {
                messagesDiv.innerHTML = '';
            }

            var messageElement = document.createElement('div');
            messageElement.className = 'msg';
            messageElement.innerHTML = `
                <div class="sender">From ID: ${data.message.sender_id} to ID: ${data.message.receiver_id}</div>
                <div class="text">${data.message.message}</div>
                <div style="font-size: 0.7em; color: #777;">Conv ID: ${data.message.conversation_id}</div>
            `;
            messagesDiv.prepend(messageElement);
            statusDiv.innerHTML = "Received a message at " + new Date().toLocaleTimeString();
        });

        pusher.connection.bind('connected', function() {
            document.getElementById('status').innerHTML = "✅ Connected to Pusher! Standing by for messages...";
        });
    </script>
</body>
</html>
