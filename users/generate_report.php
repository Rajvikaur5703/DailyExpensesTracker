<?php
session_start();
include '../config/config.php';
require('../FPDF-master/fpdf.php');

$user = $_SESSION['user_id'];

// Fetch summary amounts
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

// Monthly budget
$monthly_budget = 50000; // ₹50,000
$balance_left = $monthly_budget - $this_month;
if($balance_left < 0) $balance_left = 0;

// Fetch total spent per category
$cat_query = $conn->query("SELECT c.category_name, SUM(e.amount) as total
                           FROM expenses e
                           JOIN categories c ON e.category_id = c.category_id
                           WHERE e.user_id='$user'
                           GROUP BY c.category_name");

$categories = [];
$totals = [];
while($row = $cat_query->fetch_assoc()){
    $categories[] = $row['category_name'];
    $totals[] = $row['total'];
}

// FPDF setup
$pdf = new FPDF();
$pdf->AddPage();

// Title
$pdf->SetFont('Arial','B',16);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(0,10,"Expense Report",0,1,'C');
$pdf->Ln(3);

// Optional: date range / generated date
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,6,"Report Generated on: ".date("d-M-Y H:i"),0,1,'C');
$pdf->Ln(8);

// Summary Section: boxes
$boxHeight = 18;
$pdf->SetFont('Arial','B',12);
$pdf->SetTextColor(0,0,0);

// Column widths
$w1 = 45; $w2 = 45; $w3 = 45; $w4 = 55;

// Labels row
$pdf->SetFillColor(230,230,230);
$pdf->Cell($w1,$boxHeight,"Today",1,0,'C',true);
$pdf->Cell($w2,$boxHeight,"This Week",1,0,'C',true);
$pdf->Cell($w3,$boxHeight,"This Month",1,0,'C',true);
$pdf->Cell($w4,$boxHeight,"Balance Left",1,1,'C',true);

// Values row
$pdf->SetFont('Arial','B',12);
$pdf->SetTextColor(255,255,255);
$pdf->SetFillColor(0,120,215);
$pdf->Cell($w1,$boxHeight,number_format($today,2),1,0,'C',true);
$pdf->Cell($w2,$boxHeight,number_format($this_week,2),1,0,'C',true);
$pdf->Cell($w3,$boxHeight,number_format($this_month,2),1,0,'C',true);

// Balance Left color
if($balance_left>0){
    $pdf->SetFillColor(76,175,80); // green
} else {
    $pdf->SetFillColor(244,67,54); // red
}
$pdf->Cell($w4,$boxHeight,number_format($balance_left,2),1,1,'C',true);
$pdf->Ln(10);

// Total Spent by Category
$pdf->SetFont('Arial','B',14);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(0,8,"Total Amount Spent by Category",0,1);
$pdf->Ln(3);

// Table header
$pdf->SetFont('Arial','B',12);
$pdf->SetFillColor(240,240,240);
$pdf->Cell(100,8,"Category",1,0,'C',true);
$pdf->Cell(80,8,"Total Amount",1,1,'C',true);

// Table data
$pdf->SetFont('Arial','',12);
$fill=false;
foreach($categories as $i=>$cat){
    $pdf->SetFillColor($fill?245:255, $fill?245:255, $fill?245:255);
    $pdf->Cell(100,8,$cat,1,0,'C',true);
    $pdf->Cell(80,8,number_format($totals[$i],2),1,1,'C',true);
    $fill = !$fill;
}

// Output PDF
$pdf->Output("I","Expense_Report.pdf");

