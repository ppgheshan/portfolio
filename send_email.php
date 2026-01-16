<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Honeypot check for spam
    if (!empty($_POST['honeypot'])) {
        die('Spam detected');
    }
    
    // Get form data and sanitize
    $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = filter_var(trim($_POST["subject"]), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST["message"]), FILTER_SANITIZE_STRING);
    
    // Validate data
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        http_response_code(400);
        echo "Please fill in all required fields.";
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Please enter a valid email address.";
        exit;
    }
    
    // Email configuration
    $to = "ppgheshan@gmail.com"; // Your email address
    $email_subject = "Portfolio Contact: $subject";
    
    // Email headers
    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Email content
    $email_content = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #7c3aed 0%, #06b6d4 100%); color: white; padding: 20px; border-radius: 10px 10px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
            .field { margin-bottom: 20px; }
            .label { font-weight: bold; color: #7c3aed; }
            .value { margin-top: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Message from Portfolio</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>From:</div>
                    <div class='value'>$name ($email)</div>
                </div>
                <div class='field'>
                    <div class='label'>Subject:</div>
                    <div class='value'>$subject</div>
                </div>
                <div class='field'>
                    <div class='label'>Message:</div>
                    <div class='value' style='white-space: pre-wrap;'>$message</div>
                </div>
                <div class='field'>
                    <div class='label'>Date:</div>
                    <div class='value'>" . date('Y-m-d H:i:s') . "</div>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Plain text version for email clients that don't support HTML
    $plain_text_content = "
    New Message from Portfolio
    ========================
    
    From: $name ($email)
    Subject: $subject
    
    Message:
    $message
    
    Date: " . date('Y-m-d H:i:s') . "
    ";
    
    // Add plain text version as alternative
    $headers .= "Content-Type: multipart/alternative; boundary=\"boundary\"\r\n";
    
    $full_email_content = "
    --boundary
    Content-Type: text/plain; charset=UTF-8
    Content-Transfer-Encoding: 7bit
    
    $plain_text_content
    
    --boundary
    Content-Type: text/html; charset=UTF-8
    Content-Transfer-Encoding: 7bit
    
    $email_content
    
    --boundary--
    ";
    
    // Send email
    if (mail($to, $email_subject, $full_email_content, $headers)) {
        // Send auto-reply to sender
        $auto_reply_subject = "Thank you for contacting Gayanthaka";
        $auto_reply_message = "
        Dear $name,
        
        Thank you for getting in touch! I have received your message and will get back to you within 24-48 hours.
        
        Best regards,
        Gayanthaka
        ICT Undergraduate | University of Vavuniya
        
        ---
        Your Message:
        Subject: $subject
        Message: $message
        ";
        
        $auto_reply_headers = "From: Gayanthaka <noreply@gayanthaka.com>\r\n";
        $auto_reply_headers .= "Reply-To: ppgheshan@gmail.com\r\n";
        
        mail($email, $auto_reply_subject, $auto_reply_message, $auto_reply_headers);
        
        // Return success response
        http_response_code(200);
        echo "success";
    } else {
        // Return error response
        http_response_code(500);
        echo "Failed to send email. Please try again later.";
    }
    
} else {
    // Not a POST request
    http_response_code(403);
    echo "There was a problem with your submission. Please try again.";
}
?>