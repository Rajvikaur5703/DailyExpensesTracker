<?php
session_start();
include '../config/config.php';
require('../FPDF-master/fpdf.php');

// Check if admin logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get selected user ID
$user = $_POST['user_id'] ?? null;
if (!$user) {
    die("User ID not provided.");
}

/* =========================
   Fetch User Info
   ========================= */
$user_data = $conn->query("SELECT name, email FROM users WHERE user_id='$user'")->fetch_assoc();
$username = $user_data['name'] ?? 'Unknown User';
$email = $user_data['email'] ?? 'Not Available';

/* =========================
   Fetch Summary Amounts
   ========================= */
$today = $conn->query("SELECT SUM(amount) AS total FROM expenses 
                       WHERE user_id='$user' AND DATE(expense_date)=CURDATE()")
                       ->fetch_assoc()['total'] ?? 0;

$this_week = $conn->query("SELECT SUM(amount) AS total FROM expenses 
                           WHERE user_id='$user' AND YEARWEEK(expense_date,1)=YEARWEEK(CURDATE(),1)")
                           ->fetch_assoc()['total'] ?? 0;

$this_month_expenses = $conn->query("SELECT SUM(amount) AS total FROM expenses 
                                    WHERE user_id='$user' 
                                    AND MONTH(expense_date)=MONTH(CURDATE()) 
                                    AND YEAR(expense_date)=YEAR(CURDATE())")
                                    ->fetch_assoc()['total'] ?? 0;

$this_month_income = $conn->query("SELECT SUM(amount) AS total FROM income 
                                  WHERE user_id='$user' 
                                  AND MONTH(income_date)=MONTH(CURDATE()) 
                                  AND YEAR(income_date)=YEAR(CURDATE())")
                                  ->fetch_assoc()['total'] ?? 0;

$this_month_balance = $this_month_income - $this_month_expenses;
if ($this_month_balance < 0) $this_month_balance = 0;

/* =========================
   Fetch Category Totals
   ========================= */
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

/* =========================
   Generate PDF
   ========================= */
$pdf = new FPDF();
$pdf->AddPage();

// Title
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,"Expense Report",0,1,'C');
$pdf->Ln(3);

// Report metadata
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,6,"Report Generated on: ".date("d-M-Y H:i"),0,1,'C');
$pdf->Ln(8);

// User Info
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,"User Details",0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,7,"Name: ".$username,0,1);
$pdf->Cell(0,7,"Email: ".$email,0,1);
$pdf->Ln(8);

/* ===== Summary Section ===== */
$pdf->SetFont('Arial','B',12);
$w = 47;

$pdf->SetFillColor(230,230,230);
$pdf->Cell($w,14,"Today",1,0,'C',true);
$pdf->Cell($w,14,"This Week",1,0,'C',true);
$pdf->Cell($w,14,"This Month",1,0,'C',true);
$pdf->Cell($w,14,"This Month's Balance",1,1,'C',true);

$pdf->SetFont('Arial','',12);
$pdf->Cell($w,14,"Rs. ".number_format($today,2),1,0,'C');
$pdf->Cell($w,14,"Rs. ".number_format($this_week,2),1,0,'C');
$pdf->Cell($w,14,"Rs. ".number_format($this_month_expenses,2),1,0,'C');
$pdf->Cell($w,14,"Rs. ".number_format($this_month_balance,2),1,1,'C');
$pdf->Ln(10);

/* ===== Category Section ===== */
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,"Total Spent by Category",0,1);
$pdf->Ln(3);

$pdf->SetFont('Arial','B',12);
$pdf->SetFillColor(240,240,240);
$pdf->Cell(100,8,"Category",1,0,'C',true);
$pdf->Cell(80,8,"Total Amount (Rs.)",1,1,'C',true);

$pdf->SetFont('Arial','',12);
foreach($categories as $i=>$cat){
    $pdf->Cell(100,8,$cat,1,0,'C');
    $pdf->Cell(80,8,number_format($totals[$i],2),1,1,'C');
}

/* ===== Overall Totals ===== */
$pdf->Ln(10);
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,8,"Overall Summary",0,1);
$pdf->Ln(3);

// Calculate total income & expense (all-time)
$total_income = $conn->query("SELECT SUM(amount) AS total FROM income WHERE user_id='$user'")->fetch_assoc()['total'] ?? 0;
$total_expense = $conn->query("SELECT SUM(amount) AS total FROM expenses WHERE user_id='$user'")->fetch_assoc()['total'] ?? 0;
$net_balance = $total_income - $total_expense;

// Table Header
$pdf->SetFont('Arial','B',12);
$pdf->SetFillColor(240,240,240);
$pdf->Cell(60,8,"Total Income (Rs.)",1,0,'C',true);
$pdf->Cell(60,8,"Total Expenses (Rs.)",1,0,'C',true);
$pdf->Cell(60,8,"Net Balance (Rs.)",1,1,'C',true);

// Values
$pdf->SetFont('Arial','',12);
$pdf->Cell(60,8,number_format($total_income,2),1,0,'C');
$pdf->Cell(60,8,number_format($total_expense,2),1,0,'C');
$pdf->Cell(60,8,number_format($net_balance,2),1,1,'C');

$pdf->Ln(5);
$pdf->SetFont('Arial','I',11);
$pdf->Cell(0,8,"Note: Net Balance = Total Income - Total Expenses",0,1,'C');

/* ===== Output PDF (View Only) ===== */
$pdf->Output("I","Expense_Report_{$username}.pdf");
?>
