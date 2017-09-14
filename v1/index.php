<?php
 
require_once '../include/DbHandler.php';
require_once '../include/PwdHash.php';
require '.././libs/Slim/Slim.php';
 
\Slim\Slim::registerAutoloader();
 
$app = new \Slim\Slim();
 
// User id from db - Global Variable
$user_id = NULL;
 
/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
 
    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}
 
/**
 * Validating email address
 */

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);
 
    // setting response content type to json
    $app->contentType('application/json');
 
    echo json_encode($response);
}
$app->post('/register', function() use ($app) {
            // check for required params
            verifyRequiredParams(array( 'username', 'password'));
 
            $response = array();
 
            // reading post params
            
            $username = $app->request->post('username');
            $password = $app->request->post('password');
 
            // validating email address
            
 
            $db = new DbHandler();
            $res = $db->createUser( $username, $password);
 
            if ($res == USER_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You are successfully registered";
                echoRespnse(201, $response);
            } else if ($res == USER_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while registereing";
                echoRespnse(200, $response);
            } else if ($res == USER_ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this email already existed";
                echoRespnse(200, $response);
            }
        });
$app->post('/add', function() use ($app) {
            // check for required params
            verifyRequiredParams(array( 'sales', 'purchase','date'));
 
            $response = array();
 
            // reading post params
            
            $sales  = $app->request->post('sales');
            $purchase = $app->request->post('purchase');
           $date=$app->request->post('date');
            // validating email address
            
 
            $db = new DbHandler();
            $res = $db->AddTransaction( $sales, $purchase,$date);
 
            if ($res == USER_CREATED_SUCCESSFULLY) {
                $response["error"] = false;
                $response["message"] = "You are successfully registered";
                echoRespnse(201, $response);
            } else if ($res == USER_CREATE_FAILED) {
                $response["error"] = true;
                $response["message"] = "Oops! An error occurred while registereing";
                echoRespnse(200, $response);
            } else if ($res == USER_ALREADY_EXISTED) {
                $response["error"] = true;
                $response["message"] = "Sorry, this email already existed";
                echoRespnse(200, $response);
            }
        });
 

/**
 * User Login
 * url - /login
 * method - POST
 * params - email, password
 */
$app->post('/login', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('username', 'password'));
 
            // reading post params
            $username = $app->request()->post('username');
            $password = $app->request()->post('password');
            $response = array();
 
            $db = new DbHandler();
            // check for correct email and password
            if ($db->checkLogin($username, $password)) {
                // get the user by email
                $user = $db->getUserByEmail($username);
 
                if ($user != NULL) {
                    $response["error"] = false;
                    
                $response['username'] = $user['username'];
                                    } else {
                    // unknown error occurred
                    $response['error'] = true;
                    $response['message'] = "An error occurred. Please try again";
                }
            } else {
                // user credentials are wrong
                $response['error'] = true;
                $response['message'] = 'Login failed. Incorrect credentials';
            }
 
            echoRespnse(200, $response);
        });
$app->post('/getSales', function() use ($app)  {
        $response = array();
            $db = new DbHandler();
 $start = $app->request()->post('start');
            $end = $app->request()->post('end');
          
            // fetch task
            $result = $db->getSalesOrder($start,$end);
 
 
            $response["error"] = false;
            $response["tasks"] = array();
 
            // looping through result and preparing tasks array
            while ($task = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $task["id"];
                $tmp["sales_order"] = $task["sales_order"];
                $tmp["Date"] = $task["Date"];
                array_push($response["tasks"], $tmp);
            }
 
            echoRespnse(200, $response);
            });
$app->post('/getPurchase', function() use ($app)  {
        $response = array();
            $db = new DbHandler();
 $start = $app->request()->post('start');
            $end = $app->request()->post('end');
          
            // fetch task
            $result = $db->getPurchaseOrder($start,$end);
 
 
            $response["error"] = false;
            $response["tasks"] = array();
 
            // looping through result and preparing tasks array
            while ($task = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $task["id"];
                $tmp["purchase_order"] = $task["purchase_order"];
                $tmp["Date"] = $task["Date"];
                
                array_push($response["tasks"], $tmp);
            }
 
            echoRespnse(200, $response);
            });

$app->post('/getBoth', function() use ($app)  {
        $response = array();
            $db = new DbHandler();
 $start = $app->request()->post('start');
            $end = $app->request()->post('end');
          
            // fetch task
            $result = $db->getBothOrders($start,$end);
 
 
            $response["error"] = false;
            $response["tasks"] = array();
 
            // looping through result and preparing tasks array
            while ($task = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $task["id"];
                $tmp["sales_order"] = $task["sales_order"];
                $tmp["purchase_order"] = $task["purchase_order"];
                $tmp["Date"] = $task["Date"];
                
                array_push($response["tasks"], $tmp);
            }
 
            echoRespnse(200, $response);
            });

$app->put('/change', function() use ($app) {
            // check for required params
            verifyRequiredParams(array('newpassword', 'password'));
 
            // reading post params
            $newpassword = $app->request()->post('newpassword');
            $password = $app->request()->post('password');
            $response = array();
 
            $db = new DbHandler();
            // check for correct email and password
            $result = $db->updatePassword($newpassword,$password);
            if ($result) {
                // task updated successfully
                $response["error"] = false;
                $response["message"] = "Task updated successfully";
            } else {
                // task failed to update
                $response["error"] = true;
                $response["message"] = "Task failed to update. Please try again!";
            }
            echoRespnse(200, $response);
});
$app->post('/forgot', function() use ($app) {
$myemail = $app->request()->post('email');
     $response=array();
$db=new DbHandler();
$yash='yashthakerlearns@gmail.com';
    $result=$db->MyEmail($myemail);
//Create a new PHPMailer instance
   
//Attach an image file
if ($result) {
                // task updated successfully
                $response["error"] = false;
                $response["message"] = "Task updated successfully";
            } else {
                // task failed to update
                $response["error"] = true;
                $response["message"] = "Task failed to update. Please try again!";
            }
//send the message, check for errors

echoRespnse(200,$response);
});

$app->post('/getEmail', function() use ($app) {
$myemail = $app->request()->post('username');
     $response=array();
$db=new DbHandler();
    $result=$db->forgotPassword($myemail);
//Create a new PHPMailer instance
   
//Attach an image file
if ($result) {
                // task updated successfully
                $response["error"] = false;
                $response["email"] = $result["email"];
            } else {
                // task failed to update
                $response["error"] = true;
                $response["message"] = "Task failed to update. Please try again!";
            }
//send the message, check for errors

echoRespnse(200,$response);
});
$app->run();

?>