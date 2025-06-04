<?php
// Load PHPMailer classes
require '../../PHPMailer-master/PHPMailer.php';
require '../../PHPMailer-master/SMTP.php';
require '../../PHPMailer-master/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Home form submit to send email to admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-normal-form'])) {
    // Sanitize and validate input
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mikogapasan04@gmail.com'; // Replace with env var in production
            $mail->Password = 'izjv khci zwjg pufd';      // Replace with env var in production
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Sender and receiver config
            $mail->setFrom('mikogapasan04@gmail.com', 'Rent Master Website');
            $mail->addAddress('mikogapasan04@gmail.com', 'Rent Master Owner'); // YOU receive this
            $mail->addReplyTo($email, 'Website User'); // So you can reply to user directly

            // Email format
            $mail->isHTML(true);
            $mail->Subject = "New Message from Website User";

            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color: #333;'>
                    <h2>📩 New Message from Rent Master Website</h2>
                    <p><strong>Sender Email:</strong> {$email}</p>
                    <p><strong>Message Content:</strong></p>
                    <div style='margin-top:10px; padding: 15px; background-color: #f1f1f1; border-left: 4px solid #007bff;'>
                        <p>{$message}</p>
                    </div>
                    <p style='margin-top:20px;'>This message was sent from the Rent Master website contact form.</p>
                </div>
            ";

            $mail->AltBody = "New message from Rent Master website\n\nSender Email: $email\n\nMessage:\n$message";

            $mail->send();
            header("Location: /rent-master2/client/?page=src/home&message=Message sent successfully.");
            exit();
        } catch (Exception $e) {
            echo "❌ Failed to send message. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "❌ Invalid email address.";
    }
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-property-request-form'])) {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');
    $propertyName = htmlspecialchars(trim($_POST['property_name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $propertyId = htmlspecialchars($_POST['property_id']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mail = new PHPMailer(true);

        try {
            // SMTP config
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mikogapasan04@gmail.com';  // your Gmail address
            $mail->Password = 'izjv khci zwjg pufd';       // your app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Sender/Receiver
            $mail->setFrom('mikogapasan04@gmail.com', 'Rent Master Website');
            $mail->addAddress('mikogapasan04@gmail.com', 'Rent Master Admin');
            $mail->addReplyTo($email, 'Website User'); // So you can reply directly

            // Email content
            $mail->isHTML(true);
            $mail->Subject = "New Inquiry for Property: {$propertyName}";

            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color: #333;'>
                    <h2>📬 New Property Inquiry</h2>
                    <p><strong>Property:</strong> {$propertyName}</p>
                    <p><strong>Sender Email:</strong> {$email}</p>
                    <p><strong>Message:</strong></p>
                    <div style='margin-top:10px; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #007bff;'>
                        <p>{$message}</p>
                    </div>
                    <p style='margin-top:20px;'>This message was submitted via the Rent Master website form.</p>
                </div>
            ";

            $mail->AltBody = "New Property Inquiry\nProperty: $propertyName\nSender: $email\n\nMessage:\n$message";

            $mail->send();
            header("Location: /rent-master2/client/?page=src/properties-details&property_id=" . $propertyId . "&message=✅ Message successfully sent to Rent Master Admin.");
            exit();
        } catch (Exception $e) {
            echo "❌ Failed to send message. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "❌ Invalid email address.";
    }
} 
?>
