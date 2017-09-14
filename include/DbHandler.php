<?php
class DbHandler {
 
    private $conn;
 
    function __construct() {
        require_once dirname(__FILE__) . './DbConnect.php';
        require_once 'PwdHash.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
    public function createUser($username, $password) {
        require_once 'PwdHash.php';
        $response = array();
 
        // First check if user already existed in db
        
        $pwd_hash = PwdHash::hash($password);
 
            // Generating API key
             
 
            // insert query
            $stmt = $this->conn->prepare("INSERT INTO users(username, pwd_hash,password) values(?, ?,?)");
            $stmt->bind_param("sss",$username, $pwd_hash,$password);
 
            $result = $stmt->execute();
 
            $stmt->close();
 
            // Check for successful insertion
            
                // Failed to create user
            return USER_CREATED_SUCCESSFULLY;
            
        
        return $response;
    }
    public function AddTransaction($sales, $purchase,$date) {
        
        $response = array();
 
        // First check if user already existed in db
        
            
 
            // Generating API key
             
 
            // insert query
            $stmt = $this->conn->prepare("INSERT INTO transactions(sales_order, purchase_order,Date) values(?, ?,?)");
            $stmt->bind_param("sss",$sales, $purchase,$date);
 
            $result = $stmt->execute();
 
            $stmt->close();
 
            // Check for successful insertion
            
                // Failed to create user
            return USER_CREATED_SUCCESSFULLY;
            
        
        return $response;
    }
    
    
    
public function checkLogin($username, $password) {
        // fetching user by email
    $pwd_hash = PwdHash::hash($password);
        $stmt = $this->conn->prepare("SELECT pwd_hash FROM users WHERE username = ?");
 
        $stmt->bind_param("s", $username);
 
        $stmt->execute();
 
        $stmt->bind_result($pwd_hash);
 
        $stmt->store_result();
 
        if ($stmt->num_rows > 0) {
            // Found user with the email
            // Now verify the password
 
            $stmt->fetch();
 
            $stmt->close();
 
            if (PwdHash::check_password($pwd_hash, $password)) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            $stmt->close();
 
            // user not existed with the email
            return FALSE;
        }
    }
    public function getUserByEmail($username) {
        $stmt = $this->conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }
    public function updatePassword($newpassword,$password) {
        require_once 'PwdHash.php';
            $pwd_hash=PwdHash::hash($newpassword);
        $stmt = $this->conn->prepare("UPDATE users set password=?,pwd_hash=? where password=? ");
        $stmt->bind_param("sss", $newpassword,$pwd_hash,$password);
        $stmt->execute();
        $num_affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $num_affected_rows > 0;
    }
     public function getSalesOrder($start,$end) {
        $stmt = $this->conn->prepare("SELECT id,sales_order,Date FROM transactions where Date between ? and ?");
        
         $start1 = strtotime($start);
$startdate = date('Y-m-d',$start1);
         $end1 = strtotime($end);
$enddate = date('Y-m-d',$end1);

        $stmt->bind_param("ss",$startdate,$enddate);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;  
    }
    public function getPurchaseOrder($start,$end) {
        $stmt = $this->conn->prepare("SELECT id,purchase_order,Date FROM transactions where Date between ? and ?");
        
         $start1 = strtotime($start);
$startdate = date('Y-m-d',$start1);
         $end1 = strtotime($end);
$enddate = date('Y-m-d',$end1);

        $stmt->bind_param("ss",$startdate,$enddate);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;  
    }
    public function getBothOrders($start,$end) {
        $stmt = $this->conn->prepare("SELECT id,sales_order,purchase_order,Date FROM transactions where Date between ? and ?");
        
         $start1 = strtotime($start);
$startdate = date('Y-m-d',$start1);
         $end1 = strtotime($end);
$enddate = date('Y-m-d',$end1);

        $stmt->bind_param("ss",$startdate,$enddate);
        $stmt->execute();
        $tasks = $stmt->get_result();
        $stmt->close();
        return $tasks;  
    }
    public function forgotPassword($username) {
    
        $stmt = $this->conn->prepare("SELECT email FROM users where username=?");
        $stmt->bind_param("s",$username);
       if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }    
        }
    
public function MyEmail($myemail)
{
    
    require("../libs/PHPMailer-master/PHPMailerAutoload.php");
/**
 * This example shows sending a message using a local sendmail binary.
 */
//Create a new PHPMailer instance
$mail = new PHPMailer;
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 2;
//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';
//Set the hostname of the mail server
$mail->Host = "smtp.gmail.com";
//Set the SMTP port number - likely to be 25, 465 or 587
$mail->Port = 465;
$mail->SMTPSecure='ssl';
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
    $conn=mysql_connect("localhost","root","");
	if(!$conn)
	{
		die('Could not connect'.mysql_error());
	}
$six_digit_random_number = mt_rand(100000, 999999);
$pwd_hash = PwdHash::hash($six_digit_random_number);
$query="UPDATE `diotr`.`users` SET pwd_hash='$pwd_hash',password='$six_digit_random_number' WHERE email='$myemail' ;";
	mysql_query($query,$conn);	
$mail->Username = "teamresourcify@gmail.com";
//Password to use for SMTP authentication
$mail->Password = "cnpevtcfqurbhufz";
//Set who the message is to be sent from
$mail->setFrom('teamresourcify@gmail.com', 'Team Resourcify');
//Set an alternative reply-to address
$result='yashthakerlearns@gmail.com';
    $mail->addAddress($myemail);
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Temporary Password';
$mail->Body    = 'Hello,<br>Your<b>Temporary password:'.$six_digit_random_number.'</b><br>Kindly reset it after one-time use.<br><br>Regards,<br><b>Team Resourcify</b>';
    //Set the subject line
//$mail->Subject = 'Resourcify:Temporary Password';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->Body="Team Resourcify says Hello.Temporary password:$six_digit_random_number.Only use this password for once.Enjoy!!!";
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';
$mail->send();
    $maill='yay';
    return $maill;
}
}
?>