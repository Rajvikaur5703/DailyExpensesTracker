<?php
session_start();
include '../config/config.php';
include '../includes/session_check.php';
require('../FPDF-master/fpdf.php');

$user = $_SESSION['user_id'];

// ===== Fetch User Info =====
$user_query = $conn->query("SELECT name FROM users WHERE user_id='$user'");
$user_data = $user_query->fetch_assoc();
$username = $user_data['name'] ?? "Unknown";

// ===== Fetch Summary =====
$today = $conn->query("SELECT SUM(amount) AS total FROM expenses 
                       WHERE user_id='$user' AND DATE(expense_date)=CURDATE()")
                       ->fetch_assoc()['total'] ?? 0;

$this_week = $conn->query("SELECT SUM(amount) AS total FROM expenses 
                           WHERE user_id='$user' AND YEARWEEK(expense_date,1)=YEARWEEK(CURDATE(),1)")
                           ->fetch_assoc()['total'] ?? 0;

$this_month = $conn->query("SELECT SUM(amount) AS total FROM expenses 
                            WHERE user_id='$user' AND MONTH(expense_date)=MONTH(CURDATE()) 
                            AND YEAR(expense_date)=YEAR(CURDATE())")
                            ->fetch_assoc()['total'] ?? 0;

// ===== Fetch Category-wise =====
$result_cat = $conn->query("SELECT c.category_name, SUM(e.amount) as total
                            FROM expenses e
                            JOIN categories c ON e.category_id = c.category_id
                            WHERE e.user_id='$user'
                            GROUP BY c.category_name");

// ===== Custom PDF Class =====
class PDF extends FPDF {
    public $username;
    public $userid;

    function Header() {
        // Title
        $this->SetFont('Arial','B',16);
        $this->Cell(190,10,'Expense Report',0,1,'C');
        $this->Ln(3);

        // User info
        $this->SetFont('Arial','I',11);
        $this->Cell(95,8,"User ID: ".$this->userid,0,0,'L');
        $this->Cell(95,8,"Username: ".$this->username,0,1,'R');
        $this->Ln(5);

        // Line
        $this->SetDrawColor(50,50,50);
        $this->Line(10,35,200,35);
        $this->Ln(10);
    }

    function Footer() {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        $this->SetFont('Arial','I',9);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->username = $username;
$pdf->userid   = $user;
$pdf->AliasNbPages();
$pdf->AddPage();

// ===== Summary =====
$pdf->SetFont('Arial','B',13);
$pdf->Cell(190,10,'Summary Overview',0,1,'L');
$pdf->Ln(2);

$pdf->SetFont('Arial','',12);
$pdf->Cell(60,8,"Today: Rs. ".number_format($today,2),0,1);
$pdf->Cell(60,8,"This Week: Rs. ".number_format($this_week,2),0,1);
$pdf->Cell(60,8,"This Month: Rs. ".number_format($this_month,2),0,1);
$pdf->Ln(8);

// ===== Category Table =====
$pdf->SetFont('Arial','B',13);
$pdf->Cell(190,10,'Category-wise Expenses',0,1,'L');
$pdf->Ln(2);

$pdf->SetFont('Arial','B',12);
$pdf->SetFillColor(200,220,255);
$pdf->Cell(95,10,'Category',1,0,'C',true);
$pdf->Cell(95,10,'Total (Rs.)',1,1,'C',true);

$pdf->SetFont('Arial','',12);
if ($result_cat && $result_cat->num_rows > 0) {
    while ($row = $result_cat->fetch_assoc()) {
        $pdf->Cell(95,8,$row['category_name'],1,0,'C');
        $pdf->Cell(95,8,number_format($row['total'],2),1,1,'C');
    }
} else {
    $pdf->Cell(190,8,'No expenses found.',1,1,'C');
}

$pdf->Output("D","Expense_Report.pdf");
?>
