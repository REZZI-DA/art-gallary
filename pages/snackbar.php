<?php

/**
 * Displays a snackbar message.
 *
 * @param string $message The message to display.
 * @param string $type    The type of snackbar ('error', 'success', 'warning', etc.).
 * @param int    $duration Duration in milliseconds for which the snackbar should be visible.
 */
function displaySnackbar($message = '', $type = 'error', $duration = 3000) {
    if (!empty($message)) {
        // Determine the CSS class based on $type
        $class = 'snackbar snackbar-' . $type;

        // Snackbar HTML with the determined CSS class
        echo "<div id='snackbar' class='$class'>$message</div>";

        // JavaScript to show and hide the snackbar
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var snackbar = document.getElementById('snackbar');
                    
                    // Only show if snackbar is not already visible
                    if (!snackbar.classList.contains('show')) {
                        snackbar.classList.add('show'); // Show snackbar
                        
                        // Hide snackbar after the specified duration
                        setTimeout(function() {
                            snackbar.classList.remove('show'); // Hide snackbar
                        }, $duration);
                    }
                });
              </script>";

        // Clear session variables after displaying the snackbar
        $_SESSION['snackbar_message'] = null;
        $_SESSION['snackbar_type'] = null;
        $_SESSION['snackbar_duration'] = null;
    }
}

// Check if there's a snackbar message in the session and display it
if (isset($_SESSION['snackbar_message'])) {
    $message = $_SESSION['snackbar_message'];
    $type = isset($_SESSION['snackbar_type']) ? $_SESSION['snackbar_type'] : 'error';
    $duration = isset($_SESSION['snackbar_duration']) ? $_SESSION['snackbar_duration'] : 3000;
    displaySnackbar($message, $type, $duration);

    // Clear session variables after displaying the snackbar
    unset($_SESSION['snackbar_message']);
    unset($_SESSION['snackbar_type']);
    unset($_SESSION['snackbar_duration']);
}

?>


<style>
.snackbar {
    visibility: hidden;
    min-width: 250px;
    background-color: #333;
    color: #fff;
    text-align: center;
    border-radius: 8px;
    padding: 20px;
    position: fixed;
    z-index: 1;
    left: 50%;
    transform: translateX(-50%);
    bottom: 0;
    font-size: 17px;
    opacity: 0;
    max-width: 1000px;
}

/* Keyframes for fade-in animation */
@keyframes fadein {
    from { bottom: 0; opacity: 0; }
    to { bottom: 30px; opacity: 1; }
}

/* Keyframes for fade-out animation */
@keyframes fadeout {
    from { bottom: 30px; opacity: 1; }
    to { bottom: 0; opacity: 0; }
}

/* Apply animation classes dynamically */
.snackbar.show {
    visibility: visible;
    animation: fadein 1s forwards;
}


.snackbar-error {
    background-color: #d9534f; /* Red */
}

.snackbar-success {
    background-color: #5cb85c; /* Green */
}

.snackbar-info {
    background-color: #5bc0de; /* Blue */
}

.snackbar-warning {
    background-color: #f0ad4e; /* Yellow */
}
</style>
