<?php
require '../../FPDF-master/fpdf.php';
require '../../PHPMailer-master/PHPMailer.php';
require '../../PHPMailer-master/SMTP.php';
require '../../PHPMailer-master/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendTenantDecisionEmail($tenant_email, $action, $tenantDetails)
{
    $pdfFilePath = generateLeaseAgreementPDF($tenantDetails);

    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mikogapasan04@gmail.com';
        $mail->Password = 'izjv khci zwjg pufd';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Sender & Receiver
        $mail->setFrom('mikogapasan04@gmail.com', 'Rent Master Website');
        $mail->addAddress($tenant_email);
        $mail->addReplyTo('mikogapasan04@gmail.com', 'Rent Master Admin');

        // Email Content
        $mail->isHTML(true);
        $subjectStatus = ucfirst($action);
        $mail->Subject = "Rental Request $subjectStatus";

        $bodyMsg = ($action === 'approve') ?
            "<p>Dear {$tenantDetails['name']},</p>
            <p>Your request to rent the property <strong>{$tenantDetails['property_name']}</strong> has been <strong style='color:green;'>approved</strong>.</p>
            <p>Please find the attached lease agreement for your review.</p>" :
            "<p>Dear {$tenantDetails['name']},</p>
            <p>We regret to inform you that your request to rent <strong>{$tenantDetails['property_name']}</strong> has been <strong style='color:red;'>rejected</strong>.</p>
            <p>For further inquiries, contact our office.</p>";

        $mail->Body = "
            <div style='font-family: Arial, sans-serif; color: #333;'>
                $bodyMsg
                <p>Thank you,<br>Rent Master Team</p>
            </div>
        ";
        $mail->AltBody = strip_tags($bodyMsg);

        // Attach Lease PDF if approved
        if ($action === 'approve' && file_exists($pdfFilePath)) {
            $mail->addAttachment($pdfFilePath, 'Lease_Agreement.pdf');
        }

        $mail->send();
        // 🔥 Delete the PDF file after sending
        if ($action === 'approve' && file_exists($pdfFilePath)) {
            unlink($pdfFilePath);
        }
        if ($action === 'approve') {
            $message = $tenant_email . " request has been successfully approved.";
            header("Location: /rent-master2/admin/?page=maintenance/index&success=" . urlencode($message));
            exit();
        } else {
            $message = $tenant_email . " request has been rejected.";
            header("Location: /rent-master2/admin/?page=maintenance/index&error=" . urlencode($message));
            exit();
        }
    } catch (Exception $e) {
        echo "❌ Failed to send email. Error: {$mail->ErrorInfo}";
    }
}

function generateLeaseAgreementPDF($tenant)
{
    $pdf = new FPDF();
    $pdf->AddPage();

    // Header
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'LEASE AGREEMENT', 0, 1, 'C');
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, 'Confidential Document - For Authorized Use Only', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, date('F j, Y'), 0, 1, 'C');
    $pdf->Ln(10);

    // 1. Parties Section
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, 'PARTIES', 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 7, 'This Lease Agreement ("Agreement") is made between:');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 7, 'LANDLORD: RentMaster Landlord', 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 7, 'and', 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 7, 'TENANT: ' . $tenant['name'], 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 7, "Address: {$tenant['address']}\nPhone: {$tenant['phone']}\nEmail: {$tenant['email']}");
    $pdf->Ln(10);

    // 2. Property Section
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, 'PROPERTY', 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 7, "Property Name: {$tenant['property_name']}");
    $pdf->MultiCell(0, 7, "Address: {$tenant['property_address']}");
    $pdf->Ln(10);

    // 3. Term Section
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, 'TERM OF LEASE', 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 7, "The lease shall commence on {$tenant['lease_start']} and shall remain in effect unless earlier terminated in accordance with this Agreement.");
    $pdf->Ln(10);

    // 4. Rent Section
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, 'RENT', 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 7, "The tenant agrees to pay a monthly rental fee of PHP {$tenant['monthly_rent']}. Payment is due on the 1st of each month and shall be paid directly to the landlord or the authorized property manager.");
    $pdf->MultiCell(0, 7, "A reminder will be issued five (5) days before the due date.");
    $pdf->Ln(10);

    // 5. Responsibilities
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, 'RESPONSIBILITIES OF TENANT', 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 7, "1. Maintain cleanliness and avoid damage to the leased premises.");
    $pdf->MultiCell(0, 7, "2. Promptly report any maintenance issues.");
    $pdf->MultiCell(0, 7, "3. Use the premises solely for residential purposes.");
    $pdf->MultiCell(0, 7, "4. Abide by all house rules set by the landlord.");
    $pdf->Ln(10);

    // 6. Termination
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, 'TERMINATION', 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 7, "The tenancy shall be automatically terminated if the tenant fails to pay rent by the due date. Additionally, the landlord reserves the right to terminate this lease at any time for reasonable cause, with proper notice as required by law. Failure to comply with any terms herein may result in immediate eviction.");
    $pdf->Ln(10);

    // 7. Legal Basis
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, 'LEGAL BASIS', 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 7, "This Agreement is subject to and governed by the Philippine Rent Control Act of 2009 (RA 9653). Any dispute arising from this lease shall be resolved under Philippine law.");
    $pdf->Ln(10);

    // 8. Acknowledgement
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, 'ACKNOWLEDGEMENT', 0, 1);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 7, "This document serves as formal proof of tenancy. This Lease Agreement is considered binding and enforceable upon issuance, even without the physical signatures of both parties, as both have agreed to the terms electronically or by implied consent through occupancy and payment.");
    $pdf->Ln(10);

    // Footer
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->MultiCell(0, 7, "By accepting the terms herein and taking possession of the leased property, the Tenant acknowledges and agrees to all provisions of this Agreement.");

    // Save the PDF
    $fileName = 'lease_agreement_' . time() . '.pdf';
    $filePath = __DIR__ . '/agreements/' . $fileName;
    if (!is_dir(__DIR__ . '/agreements')) {
        mkdir(__DIR__ . '/agreements', 0755, true);
    }
    $pdf->Output('F', $filePath);

    return $filePath;
}

// maintenance request handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit-maintenance-form'])) {
    // Sanitize and validate inputs
    $tenantEmail = filter_var(trim($_POST['tenant_email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $status = htmlspecialchars(trim($_POST['status'] ?? ''), ENT_QUOTES, 'UTF-8');
    $adminMessage = htmlspecialchars(trim($_POST['admin_message'] ?? ''), ENT_QUOTES, 'UTF-8');

    if (filter_var($tenantEmail, FILTER_VALIDATE_EMAIL)) {
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mikogapasan04@gmail.com'; // Replace with secure ENV in prod
            $mail->Password = 'izjv khci zwjg pufd';      // Replace with secure ENV in prod
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Sender and recipient
            $mail->setFrom('mikogapasan04@gmail.com', 'Rent Master Admin');
            $mail->addAddress($tenantEmail); // Tenant receives this email

            // Email content
            $mail->isHTML(true);
            $mail->Subject = "Maintenance Request - " . ucfirst($status);

            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color: #333;'>
                    <h2>🛠️ Maintenance Request {$status}</h2>
                    <p>Your maintenance request has been <strong>" . strtoupper($status) . "</strong>.</p>
                    <p><strong>Admin Response:</strong></p>
                    <div style='margin-top:10px; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #28a745;'>
                        <p>{$adminMessage}</p>
                    </div>
                    <p style='margin-top:20px;'>This is an automated message from Rent Master system.</p>
                </div>
            ";

            $mail->AltBody = "Maintenance Request {$status}\n\nAdmin Message:\n{$adminMessage}";

            $mail->send();
            // Redirect after sending email
            if ($status === 'Approved') {
                $message = $tenantEmail . " maintenance request has been successfully approved.";
                header("Location: /rent-master2/admin/?page=maintenance/index&success=" . urlencode($message));
                exit();
            } else {
                $message = $tenantEmail . " maintenance request has been rejected";
                header("Location: /rent-master2/admin/?page=maintenance/index&error=" . urlencode($message));
                exit();
            }
        } catch (Exception $e) {
            echo "❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "❌ Invalid tenant email address.";
    }
}
