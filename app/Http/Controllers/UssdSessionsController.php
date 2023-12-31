<?php

namespace App\Http\Controllers;

use App\Models\UssdInbox;
use App\Models\CallBackReturn;
use App\Models\RegisterComplaint;
use App\Models\UssdSessions;
use App\Models\Customer;
use App\Models\Inquiries;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
// Add a function to generate the complaint number
function generateComplaintNumber() {
    return 'CMP-' . date('YmdHis') . '-' . mt_rand(1000, 9999);
}
// Function to send the confirmation message via the specified SMS API
function sendConfirmationMessage($phone_number, $message) {
    // Replace the following with your actual SMS API details
    $api_username = 'YOUR_SMS_API_USERNAME';
    $api_password = 'YOUR_SMS_API_PASSWORD';
    $api_url = 'http://www.cloudservicezm.com/smsservice/httpapi';

    // Format the message for the SMS API
    $formatted_message = "Hi, your complaint number is $message";
    $url_encoded_message = urlencode($formatted_message);

    // Construct the SMS API request URL
    $sms_api_url = $api_url . "?username=$api_username&password=$api_password&msg=$url_encoded_message.+&shortcode=2343&sender_id=Ontech&phone=$phone_number&api_key=121231313213123123";

    try {
        // Send the HTTP request to the SMS API
        $response = Http::withoutVerifying()->post($sms_api_url);

        if ($response->successful()) {
            echo "SMS sent successfully to $phone_number: $formatted_message" . PHP_EOL;
        } else {
            // Handle the case where the API request was not successful
            echo "Failed to send SMS to $phone_number: " . $response->body() . PHP_EOL;
        }
    } catch (Exception $e) {
        // Handle any exceptions that occur during the HTTP request
        error_log("Error sending SMS to $phone_number: " . $e->getMessage());
        echo "There was an error sending the SMS. Please try again later." . PHP_EOL;
    }
}


class UssdSessionsController extends Controller
{ 
    

        // Define the function to send the confirmation message via SMS
        function sendConfirmationMessage($phone_number, $message) {
            // This is a mock function for the SMS gateway, replace this with the actual SMS gateway integration
            echo "Sending SMS to $phone_number: $message" . PHP_EOL;
        }
        // Function to generate the inquiry number
        function generateInquiryNumber() {
        return 'INQ-' . date('YmdHis') . '-' . mt_rand(1000, 9999);
        }


    

    public function zicta(Request $request)
    {

        // Receiving data from the remote post method (zictaRemoteUtil.php)
        $auth = $request->get('auth');
        $data = $request->get('ussd_request');
        $api_id = "1234";
        $api_key = "1234";

        if ($auth['api_id'] == $api_id && $auth['api_key'] == $api_key) {
            // Declaration of variables to be used
            $message_string = "";
            $case_no = 0;
            $step_no = 1;
            $phone = $data['MSISDN'];
            $user_input = $data['MESSAGE'];
            $session_id = $data['SESSION_ID'];
            $lastPart = explode("*", $user_input);
            $parts = count($lastPart);
            $last_part = $lastPart[$parts - 1];
            $request_type = "2"; // continue

            // Getting last session info
            $getLastSessionInfo = UssdSessions::where('phone_number', $phone)
                ->where('session_id', $session_id)
                ->orderBy('id', 'DESC')
                ->first();

            // Checking if there is an active session or not
            if (!empty($getLastSessionInfo)) {
                $case_no = $getLastSessionInfo->case_no;
                $step_no = $getLastSessionInfo->step_no;
            } else {
                // Save new session record
                $new_session = UssdSessions::create([
                    "phone_number" => $phone,
                    "case_no" => 0,
                    "step_no" => 1,
                    "session_id" => $session_id
                ]);
                $new_session->save();
            }

            // Steps Logic
            switch ($case_no) {
                case '0': // Welcome
                    if ($case_no == 0 && $step_no == 1) {
                        $message_string = "Welcome to ZICTA. Please select from the following options:\n 1. About Us \n 2. Request Call Back \n 3. Inquiries \n 4. Register Complaints \n 5. Check Complaint Status \n 6.Report Scams ";
                        $request_type = "2";
                        // Update the session record
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                            "case_no" => 1,
                            "step_no" => 1
                        ]);
                    }
                    break;
                case '1': // About Us
                    if ($case_no == 1 && $step_no == 1 && !empty($last_part) && is_numeric($last_part)&& !empty($last_part) && is_numeric($last_part) && !empty($last_part) && is_numeric($last_part) ) { 
                        // Retrieve and display ZICTA's information
                    
                        if ($last_part == 0) {
                        $message_string = "Welcome to ZICTA. Please select from the following options:\n 1. About Us \n 2. Request Call Back \n 3. Inquiries \n 4. Register Complaints \n 5. Check Complaint Status";
                        $request_type = "2";

                        // Update the session record to go back to the main menu
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 0,
                                "step_no" => 1
                            ]);
                        
                    }
                    if ($last_part == 1) {
                        $message_string = "ZICTA (Zambia Information and Communications Technology Authority) is the regulatory body for the ICT sector in Zambia. We promote the development, provision, and use of reliable and affordable ICT services.\n or visit https://www.zicta.zm/about-us \n Press any key to return to the main menu.";
                        $request_type = "2";

                        // Update the session record to go back to the main menu
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 0,
                                "step_no" => 1
                            ]);
                        
                    }
                    if ($last_part == 2) {
                        $message_string = "Thank you for requesting a call back. We will contact you shortly. Press any key to return to the main menu.";
                        $request_type = "2";

                        // Update the session record to go back to the main menu
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 0,
                                "step_no" => 1
                            ]);
                        
                    }
                    if ($last_part == 3) {
                        $message_string = "Select from the following:\n 1. How do i get a licence? \n 2.Are Children safe Online\n 3. How do i become a registered dealer?\n Press 0 to return to the main menu.";
                        $request_type = "2";

                        // Update the session record to go back to the main menu
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 3,
                                "step_no" => 1
                            ]);
                        
                    }
                    if ($last_part == 4) {
                        $message_string = "To register a complaint, please enter a brief description of your complaint.";
                        $request_type = "2";

                        // Update the session record to go back to the main menu
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 4,
                                "step_no" => 1
                            ]);
                        
                    }
                    if ($last_part == 5) {
                        $message_string = "Please enter your complaint number:";
                        $request_type = "2";

                        // Update the session record to go back to the main menu
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 5,
                                "step_no" => 1
                            ]);
                        
                    }
                    if ($last_part == 6) {
                        $message_string = "Select one of the following: \n 1. Scamming Messages \n 2. Unsolicited Messages \n 3. Deregister SIM";
                        $request_type = "2";

                        // Update the session record to go back to the main menu
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 6,
                                "step_no" => 1
                            ]);
                        
                    }
                }   
                    break;
                case '2': // Request Call Back
                    if ($case_no == 2 && $step_no == 1 && !empty($last_part) && is_numeric($last_part)&& !empty($last_part) && is_numeric($last_part) && !empty($last_part) && is_numeric($last_part)) {
                       // Validate and process the user's input for requesting a call back
                       if($last_part== 2)
                        $message_string = "Thank you for requesting a call back. We will contact you shortly. Press 0 to return to the main menu.";
                        $request_type = "2";


                       $mobileNumber = $last_part;

                        // Perform any necessary validation on the mobile number

                        // Store the request in the database or take necessary actions
                        // For example:
                        $callbackRequest = CallBackReturn::create([
                            'mobile_number' => $mobileNumber,
                            'session_id' => $session_id
                        ]);
                        $callbackRequest->save();

                        // Generate the confirmation message
                        $message_string = "Thank you for requesting a call back. We will contact you shortly. Press 0 to return to the main menu.";
                        $request_type = "2";

                        // Update the session record
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                            "case_no" => 0,
                            "step_no" => 1
                        ]);
                    }
                    break;
                case '3': // Inquiries
                    if ($case_no == 3 && $step_no == 1 && !empty($last_part) && is_numeric($last_part)) {
                        switch ($last_part) {
                           
                            case '1': // How do I get a licence?
                                
                                $message_string = "To get a license, please follow these steps:\n Step 1. Get an application form.\n Step 2. Submit a soft and physical copy.\n Step 3. If all is well you will be issued payment fees.\n Step 4. Make payments according to the given time period.\n Press 0 to return to the main menu.";
                                $request_type = "2";
                                
                                // Update the session record
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                    "case_no" => 3,
                                    "step_no" => 4
                                ]);
                                break;
                               
                            case '2': // Are children safe online?
                                $message_string = "Yes, children can be safe online. As a parent, make sure to monitor. Press 0 to return to the main menu.";
                                $request_type = "2";
                                // Update the session record
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                    "case_no" => 3,
                                    "step_no" => 4
                                ]);
                                break;
                            case '3': // How do I become a registered dealer?
                                $message_string = "To become a registered dealer, please visit: https://www.zicta.zm/faq. Press 0 to return to the main menu.";
                                $request_type = "2";
                                // Update the session record
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                    "case_no" => 3,
                                    "step_no" => 4
                                ]);
                                break;
                            case '4': // Generate Inquiry Number
                                    // Generate a unique inquiry number using the function
                                    $inquiry_number = generateInquiryNumber();
                    
                                    // Store the inquiry number in the session for later use
                                    session(['inquiry_number' => $inquiry_number]);
                    
                                    // Store the inquiry in the database
                                    $inquiry = Inquiries::create([
                                        'inquiry_number' => $inquiry_number,
                                        'session_id' => $session_id,
                                        'status' => 'Pending', // Set the initial status
                                    ]);
                    
                                    // Generate the confirmation message
                                    $message_string = "Your inquiry number is: $inquiry_number. Please keep it for future reference. Press 0 to return to the main menu.";
                                    $request_type = "2";
                    
                                    // Update the session record
                                    $update_session = UssdSessions::where('session_id', $session_id)->update([
                                        "case_no" => 0,
                                        "step_no" => 1
                                    ]);
                            default:
                                $message_string = "Invalid option selected. Press 0 to return to the main menu.";
                                $request_type = "2";
                                break;
                        }
                    } elseif ($case_no == 3 && $step_no == 1 && $last_part == '0') {
                        $message_string = "Welcome to ZICTA.  Please select from the following options:\n 1. About Us \n 2. Request Call Back \n 3. Inquiries \n 4. Register Complaints \n 5. Check Complaint Status";
                        $request_type = "2";
                        // Update the session record
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                            "case_no" => 0,
                            "step_no" => 1
                        ]);
                    }
                    elseif ($case_no == 3 && $step_no == 2 && $last_part == '0') {
                        $message_string = "Welcome to ZICTA.  Please select from the following options:\n 1. About Us \n 2. Request Call Back \n 3. Inquiries \n 4. Register Complaints \n 5. Check Complaint Status";
                        $request_type = "2";
                        // Update the session record
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                            "case_no" => 0,
                            "step_no" => 1
                        ]);
                    }
                    break;
                    


             

            

                case '4': // Register Complaints
                    if ($case_no == 4 && $step_no == 1) {
                        if (!empty($last_part)) {
                            // Generate a unique complaint number using the function
                            $complaint_number = generateComplaintNumber();
                            // Store the complaint number in the session for later use
                           session(['complaint_number' => $complaint_number]);
                           // Store the complaint in the database or take necessary actions
                           // For example:
                           $complaint = RegisterComplaint::create([
                           'complaint_number' => $complaint_number,
                           'description' => $last_part,
                           'session_id' => $session_id,
                        "status"=> 'Pending', // Set the status to 'Pending' when registering a new complaint
                        ]);
        $complaint->save();

        // Send SMS to the customer with the complaint number
        $customer_phone_number = $phone; // Assuming the phone number is available in the $phone variable
        if ($customer_phone_number) {
            $sms_message = "Thank you for registering your complaint. Your complaint number is: $complaint_number. We will look into it. Press 0 to return to the main menu.";
            sendConfirmationMessage($customer_phone_number, $complaint_number); // Use the complaint number as the message
        } else {
            // Handle the case where you are unable to retrieve the customer's phone number.
            // For example, log an error and notify the administrator.
            error_log("Error: Unable to retrieve customer's phone number for session_id: $session_id");
            // Alternatively, you can send an error message to the user instead of an SMS.
            $sms_message = "There was an error processing your complaint. Please try again later.";
            sendConfirmationMessage($phone, $sms_message);
        }

                    
                            // Generate the confirmation message
                            $message_string = "Thank you for registering your complaint. We will look into it. Press 0 to return to the main menu.";
                            $request_type = "2";

                         } else {
                            // User entered "0" for returning to the main menu
                            $message_string = "Welcome to ZICTA. Please select from the following options:\n 1. About Us \n 2. Request Call Back \n 3. Inquiries \n 4. Register Complaints \n 5. Check Complaint Status ";
                            $request_type = "2";
                            // Reset the session to return to the main menu
                            $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 0,
                                "step_no" => 1
                            ]);
                        }

                    }    
                    break;
                    case '5': // Check Complaint Status
                        if ($case_no == 5 && $step_no == 1) {
                            if (!empty($last_part)) {
                                // Validate the user input to check if it matches the expected format 'CMP-YYYYMMDDHHmmss-XXXX'
                                $complaint_format = '/^CMP-\d{14}-\d{4}$/';
                                $complaint_number_input = trim(strtoupper($last_part));
                                
                                if ($complaint_number_input === '0') {
                                    // User entered "0" for returning to the main menu
                                    $message_string = "Welcome to ZICTA. Please select from the following options:\n 1. About Us \n 2. Request Call Back \n 3. Inquiries \n 4. Register Complaints \n 5. Check Complaint Status";
                                    $request_type = "2";
                                    // Reset the session to return to the main menu
                                    $update_session = UssdSessions::where('session_id', $session_id)->update([
                                        "case_no" => 0,
                                        "step_no" => 1
                                    ]);
                                } elseif (preg_match($complaint_format, $complaint_number_input)) {
                                    // Valid complaint number format, retrieve the status from the sample data
                    
                                    // Assuming you have a database table named "complaints" with the complaint data
                                    // Replace 'complaints' with the actual table name if different
                                    $complaint_data = RegisterComplaint::all()->pluck('status', 'complaint_number')->toArray();
                    
                                    if (array_key_exists($complaint_number_input, $complaint_data)) {
                                        $complaint_status = $complaint_data[$complaint_number_input];
                                        $message_string = "Complaint #$complaint_number_input status: $complaint_status \n press 0 to return to the main menu.";
                                        // Set the request type to "1" to end the session after displaying the status
                                        $request_type = "1";
                                    } else {
                                        // Complaint not found in the sample data, notify the user
                                        $message_string = "Complaint #$complaint_number_input not found. Please check the number and try again or press 0 to return to the main menu.";
                                        $request_type = "2"; // Request further user input
                                    }
                                } else {
                                    // Invalid complaint number format, notify the user
                                    $message_string = "Invalid complaint number format. Please enter the correct complaint number or press 0 to return to the main menu.";
                                    $request_type = "2"; // Request further user input
                                }
                            } else {
                                // User entered "0" for returning to the main menu
                                $message_string = "Welcome to ZICTA. Please select from the following options:\n 1. About Us \n 2. Request Call Back \n 3. Inquiries \n 4. Register Complaints \n 5. Check Complaint Status";
                                $request_type = "2";
                                // Reset the session to return to the main menu
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                    "case_no" => 0,
                                    "step_no" => 1
                                ]);
                            }
                        }
                        break;
                        
            case '6': // Report scams
                if ($case_no == 6 && $step_no == 1 && !empty($last_part) && is_numeric($last_part)&& !empty($last_part) && is_numeric($last_part) && !empty($last_part) && is_numeric($last_part) ) { 
                            // Scamming Messages
                            if ($last_part == 1) {
                                $message_string = "Please enter your NRC number.";
                                $request_type = "2";
        
                                // Update the session record to go back to the main menu
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                        "case_no" => 6,
                                        "step_no" => 2
                                    ]);
                                
                            }
                            // Unsolicited Messages
                            if ($last_part == 2) {
                                $message_string = "Please select the type of unsolicited message:\n 1.For Adverts\n 2.For Offensive content\n 3. For Religious content\n 4. For Hate speech\n 5. For Other unsolicited message";
                                $request_type = "2";
        
                                // Update the session record to go back to the main menu
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                        "case_no" => 6,
                                        "step_no" => 2
                                    ]);
                                
                            }
                            // Deregister SIM
                            if ($last_part == 3) {
                                $message_string = "Please enter the number you wish to deregister.";
                                $request_type = "2";
        
                                // Update the session record to go back to the main menu
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                        "case_no" => 6,
                                        "step_no" => 2
                                    ]);
                                    break;
                                
                            }
                            
                                
                    } if ($case_no == 6 && $step_no == 2 && !empty($last_part) && is_numeric($last_part)) {
                        switch ($last_part) {
                        case '1': // Scamming Messages - Type of Scam Message
                            $message_string = "Please enter the number from which you received the message.";
                            $request_type = "2";
                            // Update the session record to indicate transition to step 3
                            $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 6,
                                "step_no" => 2
                            ]);
                            break;
                        case '2': // Unsolicited Messages - Type of Message
                            $message_string = "Please enter the number of the offender.";
                            $request_type = "2";
                            // Update the session record
                            $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 6,
                                "step_no" => 2
                            ]);
                            break;
                        case '3': // Deregister SIM - NRC number
                            $message_string = "Please enter the number you wish to deregister.";
                            $request_type = "2";
                            // Update the session record
                            $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 6,
                                "step_no" => 2
                            ]);
                            break;
                        
                    }
                } else {
        // Display the message after all logic in step 2
        $message_string = "Dear customer, your request is being processed. You will receive an SMS shortly. Press 0 to return to the main menu.";
        $request_type = "2";
        // Update the session record to stay in step 2
        $update_session = UssdSessions::where('session_id', $session_id)->update([
            "case_no" => 0,
            "step_no" => 1
        ]);
    }
            break;
                    
                    
                   
                    
                  
                    
                       
                    

            }

            // Request response
            $response = [
                "ussd_response" => [
                    "USSD_BODY" => $message_string,
                    "REQUEST_TYPE" => $request_type
                ]
            ];

            return response()->json($response);
        }
   
    }

 }
