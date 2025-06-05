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
        $mail->CharSet = 'UTF-8';

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
                <div style='font-family: Arial, sans-serif; color: #2c3e50; padding: 20px; background-color: #f9f9f9; border-radius: 8px;'>
                    <h2 style='color: #007bff; margin-bottom: 20px;'>📩 New Contact Message – Rent Master</h2>
                    
                    <p style='margin-bottom: 10px; font-size: 16px;'>
                        <strong>Sender Email:</strong> <a href='mailto:{$email}' style='color: #007bff;'>$email</a>
                    </p>

                    <p style='margin: 20px 0 5px; font-size: 16px;'><strong>Message:</strong></p>
                    <div style='background-color: #eef3fa; padding: 15px; border-left: 5px solid #007bff; border-radius: 5px; font-size: 15px; line-height: 1.6;'>
                        <em>{$message}</em>
                    </div>

                    <p style='margin-top: 30px; font-size: 14px; color: #7f8c8d;'>
                        — This message was sent via the <strong>Rent Master</strong> website contact form.
                    </p>
                </div>
            ";


            $mail->AltBody = "New contact message from Rent Master website

            Sender Email: $email

            Message:
            $message

            This message was sent from the Rent Master website contact form.";


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
        $mail->CharSet = 'UTF-8';

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
                <div style='font-family: Arial, sans-serif; color: #2c3e50; padding: 20px; background-color: #f9f9f9; border-radius: 8px;'>
                    <h2 style='color: #007bff; margin-bottom: 20px;'>📬 New Property Inquiry – Rent Master</h2>
                    
                    <p style='margin-bottom: 10px; font-size: 16px;'>
                        <strong>Property:</strong> {$propertyName}
                    </p>
                    
                    <p style='margin-bottom: 10px; font-size: 16px;'>
                        <strong>Sender Email:</strong> <a href='mailto:{$email}' style='color: #007bff;'>$email</a>
                    </p>

                    <p style='margin: 20px 0 5px; font-size: 16px;'><strong>Message:</strong></p>
                    <div style='background-color: #eef3fa; padding: 15px; border-left: 5px solid #007bff; border-radius: 5px; font-size: 15px; line-height: 1.6;'>
                        <em>{$message}</em>
                    </div>

                    <p style='margin-top: 30px; font-size: 14px; color: #7f8c8d;'>
                        — This message was submitted via the <strong>Rent Master</strong> website inquiry form.
                    </p>
                </div>
            ";

            $mail->AltBody = "New Property Inquiry – Rent Master

                    Property: $propertyName
                    Sender Email: $email

                    Message:
                    $message

                    This message was submitted via the Rent Master website inquiry form.";

            

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
