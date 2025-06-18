// Set a keep-alive interval (e.g., every 30 seconds)
const KEEP_ALIVE_INTERVAL = 30000;

function sendKeepAlive() {
    fetch('pages/keep_alive.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ action: 'keepAlive' }),
    });
}

// Start sending keep-alive pings
setInterval(sendKeepAlive, KEEP_ALIVE_INTERVAL);

