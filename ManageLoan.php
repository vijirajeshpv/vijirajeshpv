<?php
$filepath = realpath(dirname(__FILE__));
include_once ($filepath."/../libs/CrudOperation.php");
include_once ($filepath."/../helpers/Format.php");

/**
* Sample Class for photo uploading, insert data, update data and others.
*/
class ManageLoan
{
	private $db;
	private $fm;
	function __construct()
	{
		$this->db = new CrudOperation();
		$this->fm = new Format();
	}

	function showPath(){
		return realpath(dirname(__FILE__));
	}
	function dbcon(){
		return $this->db->link;
	}

	//loan application
	public function applyForLoan($data, $file)
	{
			// 	//validation of borrower data
		$b_id = $this->fm->validation($data['b_id']);

		$borrower_name = $this->fm->validation($data['borrower_name']);
                
                 $item_description = $_POST['item_description'];
$item_description1 = $this->fm->validation(implode(", ", $item_description));

                
	$loan_amount = $this->fm->validation($data['loan_amount']);

	$gross_weight = $this->fm->validation($data['gross_weight']);

		$stone_weight = $data['stone_weight'];

	$net_weight = $this->fm->validation($data['net_weight']);

	$market_value = $this->fm->validation($data['market_value']);
                 $currentDate = date("Y-m-d");
                 $status=0;

		//take image information using super global variable $_FILES[];
		$permited  = array('doc', 'docx', 'pdf');
		$file_name = $file['borrower_files']['name'];
		$file_size = $file['borrower_files']['size'];
		$file_temp = $file['borrower_files']['tmp_name'];

		
		if (empty($b_id) or empty($borrower_name) or empty($item_description1) or empty($gross_weight) or empty($stone_weight) or empty($market_value) or empty($net_weight) or empty($loan_amount)  or empty($file_name))
		{
			$msg = "<span class='error'>Fields must not be empty!</span>";
			return $msg;
		}else{
			//validate uploaded images
			$div = explode('.', $file_name);
			$file_ext = strtolower(end($div));
			$unique_image = substr(md5(time()), 0, 10).'.'.$file_ext;
			$uploaded_file = "admin/uploads/documents/".$unique_image;
			
			if ($file_size >10048567) {
				$msg = "<span class='error'>Borrower not found !</span>";
				return $msg;
			} elseif (in_array($file_ext, $permited) === false) {
				echo "<span class='error'>You can upload only:-"
				.implode(', ', $permited)."</span>";
			}else{
				move_uploaded_file($file_temp, $uploaded_file);
				
				$query = "INSERT INTO tbl_gold_loan(`item_description`, `gross_weight`, `stone_weight`, `net_weight`, `market_value`, `loan_amnt`, `b_id`, `name`, `date`, `status`, `file`) 
				VALUES('$item_description1','$gross_weight','$stone_weight','$net_weight','$market_value','$loan_amount','$b_id','$borrower_name','$currentDate','$status','$uploaded_file')";

$inserted = $this->db->insert($query);
if ($inserted) {
	$msg = "<span class='success'>Loan Application submitted successfully.</span>";
	return $msg;
} else {
	$msg = "<span class='error'>Failed to submit.</span>";
	return $msg;

}
		 	}

		}

	}

	public function viewLoanApplication()
	{
		//get all borrower data
		$sql = "SELECT tbl_borrower.*, tbl_loan_application.*
			    FROM tbl_borrower
				INNER JOIN tbl_loan_application
				ON tbl_borrower.id = tbl_loan_application.b_id
		 		ORDER BY tbl_loan_application.id DESC";
		$result = $this->db->select($sql);
		return $result;
	}

	public function viewLoanApplicationNotVerified()
	{
		//get all borrower data
		$sql = "SELECT tbl_borrower.*, tbl_loan_application.*
			    FROM tbl_borrower
				INNER JOIN tbl_loan_application
				ON tbl_borrower.id = tbl_loan_application.b_id
				WHERE tbl_loan_application.status != 3
		 		ORDER BY tbl_loan_application.id";
		$result = $this->db->select($sql);
		return $result;
	}

	public function getLoanById($loan_id)
	{
		$sql = "SELECT * FROM tbl_loan_application WHERE id='$loan_id' ";
		$result = $this->db->select($sql);
		return $result;	
	}


	public function getLoanVerificationStatus($loan_id, $role_id)
	{	
		if ($role_id == 1) {
			$sql = "UPDATE tbl_loan_application SET status = 1 WHERE id = '$loan_id' ";

		}else if($role_id == 2){
			$sql = "UPDATE tbl_loan_application SET status = 2 WHERE id = '$loan_id' ";
			
		}else{
			$sql = "UPDATE tbl_loan_application SET status = 3 WHERE id = '$loan_id' ";
		}
		
		$updated = $this->db->update($sql);
		if ($updated) {
			$msg = "<span style='color:green'>Successfully verified!</span>";
			return $msg;
		}else{
			$msg = "<span style='color:red'>Failed to verify!</span>";
			return $msg;
		}
	}


	public function getApprovedLoan($b_id)
	{
		//get all borrower data
		$sql = "SELECT tbl_borrower.*, tbl_loan_application.*
			    FROM tbl_borrower
				INNER JOIN tbl_loan_application
				ON tbl_borrower.id = tbl_loan_application.b_id
				WHERE tbl_loan_application.status = 3 AND tbl_loan_application.b_id = '$b_id'
		 		ORDER BY tbl_loan_application.id DESC";
		$result = $this->db->select($sql);
		return $result;
	}

	//get upapproved loan
	public function getNotApproveLoan()
	{
		$sql = "SELECT * FROM tbl_loan_application WHERE status != 3 ";
		$result = $this->db->select($sql);
		if ($result) {
			$result = $result->num_rows;
			return $result;
		}else{
			return 0;
		}
		
			
	}

	public function getAllApproveLoan()
	{
		$sql = "SELECT * FROM tbl_loan_application WHERE status = 3 ";
		$result = $this->db->select($sql);
		if ($result) {
			$result = $result->num_rows;
			return $result;
		}else{
			return 0;
		}
			
	}


	public function totalPaidLoanAmount()
	{
		$sql = "SELECT SUM(amount_paid) as total_money FROM tbl_loan_application";
		$result = $this->db->select($sql);
			
		return $result;
	}

	//get loan not paid
	public function getApprovedLoanNotPaid($b_id)
	{
		//get all borrower data
		$sql = "SELECT tbl_borrower.*, tbl_loan_application.*
			    FROM tbl_borrower
				INNER JOIN tbl_loan_application
				ON tbl_borrower.id = tbl_loan_application.b_id
				WHERE tbl_loan_application.status = 3 AND tbl_loan_application.b_id = '$b_id' AND tbl_loan_application.total_loan > tbl_loan_application.amount_paid
		 		ORDER BY tbl_loan_application.id DESC";
		$result = $this->db->select($sql);
		return $result;	
	}

 // get opening date
public function getOpeningDate($gl_no)
{

$sql = "SELECT tbl_borrower.*, tbl_gold_loan.*
			    FROM tbl_borrower
				INNER JOIN tbl_gold_loan
				ON tbl_borrower.id = tbl_gold_loan.b_id
				WHERE tbl_gold_loan.status = 0 ";

		$result = $this->db->select($sql);			
 

     if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Fetch the data as an associative array.
        return $row['date']; // Return the specific value you need.
    }

    // Handle the case when no rows are returned or other errors.
    return null;
}
// get gold loan details
public function getGoldLoanDetails($gl_no)
{
   // $query = "SELECT * FROM tbl_gold_loan WHERE gl_no = '$gl_no'";
$query="SELECT tbl_borrower.*, tbl_gold_loan.*
			    FROM tbl_borrower
				INNER JOIN tbl_gold_loan
				ON tbl_borrower.id = tbl_gold_loan.b_id
				WHERE tbl_gold_loan.status = 0";
		
     $result = $this->db->select($query);

        return $result;
    
}

//  Function to update gold loan status to "closed"
function closeGoldLoan($gl_no) {
 $query = "UPDATE tbl_gold_loan SET closing_date = NOW(), status= 1 WHERE gl_no = $gl_no'";
$updated = $this->db->update($query);

        return $updated;

}

	// pay loan
	public function payLoan($data)
	{
		
                $gl_no = $this->fm->validation($data['gl_no']);
		$b_id = $this->fm->validation($data['b_id']);
				
		$net_weight = $this->fm->validation($data['net_weight']);

		$interest = $this->fm->validation($data['interest']);

		$pay_date = $this->fm->validation($data['pay_date']);

		$loan_amnt = $this->fm->validation($data['loan_amnt']);
		
		$total = $this->fm->validation($data['total']);
	
		//$paid_amount = $this->fm->validation($data['paid_amount']);

		//$remain_amount = $data['total_amount'] -$data['paid_amount'];
		
		//$fine = 0;
		//fine calculation needed field
		//if (isset($data['fine_amount'])) {
		//	$fine = $data['fine_amount'];
		//}

		$next_date = '0000-00-00';

		if (isset($data['next_date'])) {
			$next_date = $data['next_date'];
		}else{

			$next_date = strtotime('+30 days',strtotime($data['pay_date']));
			$next_date = date('Y-m-d', $next_date);
			var_dump($next_date);
		}

		if (empty($b_id) or empty($gl_no) or empty($loan_amnt) or empty($pay_date) or empty($total))
		{
			$msg = "<span style='color:red'>Error....!</span>";
			return $msg;
		}else{

			$query = "DELETE FROM tbl_gold_loan WHERE gl_no = '$gl_no' AND loan_id = '$loan_id'";

$deleted = $this->db->delete($query);
			
		}
		
	}

	//send notification if not paying for 3 month 
	public function getNotification3monthNotPaying()
	{	

		$sql = "SELECT tbl_borrower.*, tbl_loan_application.*
				FROM tbl_borrower
				INNER JOIN tbl_loan_application
				ON tbl_borrower.id = tbl_loan_application.b_id
			 	WHERE tbl_loan_application.status = 3 ";
		$result = $this->db->select($sql);
		return $result;
	}
	//find payment info

	public function findPayment($b_id, $loan_id)
	{
		//get all borrower data by nid
		$sql = "SELECT * FROM tbl_payment WHERE b_id='$b_id' AND loan_id ='$loan_id' ";
		$result = $this->db->select($sql);
		return $result;
	}

	//generate payment report
	public function paymentReport($loan_id, $pay_id, $b_id)
	{
		$sql = "SELECT tbl_payment.*, tbl_loan_application.*
		    FROM tbl_payment
			INNER JOIN tbl_loan_application
			ON tbl_payment.loan_id = tbl_loan_application.id
			WHERE tbl_payment.b_id = '$b_id' AND tbl_payment.loan_id = '$loan_id' AND tbl_payment.id = '$pay_id' ";
		$result = $this->db->select($sql);
		return $result;	
	}

	//property sell details
	public function propertySellDetails($data)
	{
		$b_id = $this->fm->validation($data['b_id']);
		
		$loan_id = $this->fm->validation($data['loan_id']);

		$property_name = $this->fm->validation($data['property_name']);

		$property_details = $this->fm->validation($data['property_details']);
		
		$price = $this->fm->validation($data['price']);

		$pay_remaining_loan = $this->fm->validation($data['pay_remaining_loan']);
		
		$return_money = $price - $pay_remaining_loan;

		$amount_paid = $this->fm->validation($data['amount_paid']);

		$amount_paid = $amount_paid + $pay_remaining_loan;

		if (empty($price) or empty($property_name) or empty($property_details) or empty($pay_remaining_loan) or empty($amount_paid) )
		{
			$msg = "<span style='color:red'>Empty field !</span>";
			return $msg;
		}else{
			$query = "DELETE FROM tbl_gold_loan WHERE gl_no = '$gl_no' AND loan_id = '$loan_id'";

$deleted = $this->db->delete($query);

if ($deleted) {
    $updateSql = "UPDATE tbl_loan_application SET amount_paid = '$amount_paid', amount_remain = 0 WHERE id = '$loan_id'";

    $up = $this->db->update($updateSql);

    $msg = "<span class='success'>Due loan paid and property selling details saved!</span>";
    return $msg;
} else {
    $msg = "<span class='error'>Failed to delete.</span>";
    return $msg;
}
}
}	

	//view liabiility details

	public function viewLiabilityDetails()
	{
		//get all borrower data
		$sql = "SELECT tbl_borrower.*, tbl_liability.*
			    FROM tbl_borrower
				INNER JOIN tbl_liability
				ON tbl_borrower.id = tbl_liability.b_id
		 		ORDER BY tbl_liability.id DESC";
		$result = $this->db->select($sql);
		return $result;
	}


//end of ManageLoan class
}
?>