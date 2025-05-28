<?php
require('FPDF-master/fpdf.php');

// Function to clean special characters
function cleanText($text) {
    return str_replace(
        ['–', '—', '“', '”', '‘', '’', '•', '₱'],
        ['-', '-', '"', '"', "'", "'", '*', 'PHP '],
        $text
    );
}

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rentsystem";

try {
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) throw new Exception("Database connection failed");

    mysqli_set_charset($conn, "utf8mb4");

    $tenant_id = filter_input(INPUT_GET, 'tenant_id', FILTER_VALIDATE_INT);
    if (!$tenant_id || $tenant_id < 1) throw new Exception("Invalid tenant ID");

    $sql = "SELECT t.*, u.user_name, u.user_address, u.user_email, u.user_phone_number, 
                   p.property_name, p.property_location, p.property_rental_price, 
                   DATE_FORMAT(t.tenant_date_created, '%M %d, %Y') AS formatted_lease_start
            FROM tenants t
            JOIN users u ON t.user_id = u.user_id
            JOIN properties p ON t.property_id = p.property_id
            WHERE t.tenant_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) throw new Exception("Failed to prepare SQL statement");

    mysqli_stmt_bind_param($stmt, "i", $tenant_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if (!$data) throw new Exception("Tenant record not found");

    class LeaseAgreementPDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, 'LEASE AGREEMENT', 0, 1, 'C');
            $this->Ln(3);
            $this->SetFont('Arial', '', 10);
            $this->Cell(0, 5, 'Confidential Document - For Authorized Use Only', 0, 1, 'C');
            $this->Ln(10);
        }

        function Footer() {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }
    }

    $pdf = new LeaseAgreementPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetAutoPageBreak(true, 20);

    // Format values
    $date = date('F d, Y');
    $tenantName = cleanText($data['user_name']);
    $tenantAddress = cleanText($data['user_address']);
    $propertyName = cleanText($data['property_name']);
    $propertyAddress = cleanText($data['property_location']);
    $monthlyRent = number_format($data['property_rental_price'], 2);
    $leaseStart = $data['formatted_lease_start'];
    $landlord = "RentMaster Landlord";

    // Start PDF content
    $pdf->MultiCell(0, 7, cleanText("$date\n"));
    $pdf->Ln(3);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, "PARTIES", 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, cleanText(
        "This Lease Agreement (\"Agreement\") is made between:\n\n" .
        "LANDLORD: $landlord\n\n" .
        "and\n\n" .
        "TENANT: $tenantName\n" .
        "Address: $tenantAddress\n" .
        "Phone: {$data['user_phone_number']}\n" .
        "Email: {$data['user_email']}"
    ));
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, "PROPERTY", 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, cleanText(
        "Property Name: $propertyName\n" .
        "Address: $propertyAddress"
    ));
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, "TERM OF LEASE", 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, cleanText(
        "The lease shall commence on $leaseStart and shall remain in effect unless earlier terminated in accordance with this Agreement."
    ));
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, "RENT", 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, cleanText(
        "The tenant agrees to pay a monthly rental fee of PHP $monthlyRent. Payment is due on the 1st of each month and shall be paid directly to the landlord or the authorized property manager.\n\n" .
        "A reminder will be issued five (5) days before the due date."
    ));
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, "RESPONSIBILITIES OF TENANT", 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, cleanText(
        "1. Maintain cleanliness and avoid damage to the leased premises.\n" .
        "2. Promptly report any maintenance issues.\n" .
        "3. Use the premises solely for residential purposes.\n" .
        "4. Abide by all house rules set by the landlord."
    ));
    $pdf->Ln(5);

   $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, "TERMINATION", 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, 
        "The tenancy shall be automatically terminated if the tenant fails to pay rent by the due date. " .
        "Additionally, the landlord reserves the right to terminate this lease at any time for reasonable cause, " .
        "with proper notice as required by law. Failure to comply with any terms herein may result in immediate eviction."
    );
    $pdf->Ln(5);


    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, "LEGAL BASIS", 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, cleanText(
        "This Agreement is subject to and governed by the Philippine Rent Control Act of 2009 (RA 9653). Any dispute arising from this lease shall be resolved under Philippine law."
    ));
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 7, "ACKNOWLEDGEMENT", 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 7, cleanText(
        "This document serves as formal proof of tenancy. This Lease Agreement is considered binding and enforceable upon issuance, even without the physical signatures of both parties, as both have agreed to the terms electronically or by implied consent through occupancy and payment."
    ));
    $pdf->Ln(8);

    $pdf->SetFont('Arial', 'I', 11);
    $pdf->MultiCell(0, 7, cleanText(
        "By accepting the terms herein and taking possession of the leased property, the Tenant acknowledges and agrees to all provisions of this Agreement."
    ));

    $pdf->Output('D', "Lease_Agreement_{$tenantName}_{$tenant_id}.pdf");

} catch (Exception $e) {
    error_log("PDF Error: " . $e->getMessage());
    header('Content-Type: text/html');
    die("<h3>Error Generating Document</h3><p>" . htmlspecialchars($e->getMessage()) . "</p>");
} finally {
    if (isset($conn)) mysqli_close($conn);
}
?>
