<?php

namespace App\Http\Controllers;

use App\Models\UssdInbox;
use App\Models\CallBackReturn;
use App\Models\RegisterComplaint;
use App\Models\UssdSessions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UssdSessionsController extends Controller
{
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
                        $message_string = "Welcome to ZICTA. Please select from the following options:\n 1. About Us \n 2. Request Call Back \n 3. Inquiries \n 4. Register Complaints \n 5. Types of Complaints";
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
                        if($last_part== 1)
                        $message_string = "ZICTA (Zambia Information and Communications Technology Authority) is the regulatory body for the ICT sector in Zambia. We promote the development, provision, and use of reliable and affordable ICT services.\n Press 0 for main menu.";
                        if($last_part== 2)
                        $message_string = "Thank you for requesting a call back. We will contact you shortly.\n Press 0 for main menu.";
                        if($last_part== 3)
                        $message_string = "Welcome, Please select an inquiry:\n 1.How do I get a licence?\n 2.Are children safe online?\n 3.How do I become a registered dealer?\n Press 0 to for main menu.";
                        if($last_part== 4)
                        $message_string = "Thank you for registering your complaint. We will look into it.\n Press 0 for main menu.";
                        if($last_part== 5)
                        $message_string = "Welcome,Please select a complaint:\n 1.Scaming messages\n2.Unsolicited Messages\n 3.Deregister SIM";
                        if($last_part== 0)
                        $message_string = "Welcome to ZICTA. Please select from the following options:\n 1. About Us \n 2. Request Call Back \n 3. Inquiries \n 4. Register Complaints \n 5. Types of Complaints";
                        $request_type = "2";
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                            "case_no" => 0,
                            "step_no" => 1
                        ]);
                    }
                    break;
                case '2': // Request Call Back
                    if ($case_no == 2 && $step_no == 1 && !empty($last_part) && is_numeric($last_part)&& !empty($last_part) && is_numeric($last_part) && !empty($last_part) && is_numeric($last_part)) {
                        // Validate and process the user's input for requesting a call back
                        if($last_part== 0)
                        $message_string = "Welcome to ZICTA. Please select from the following options:\n 1. About Us \n 2. Request Call Back \n 3. Inquiries \n 4. Register Complaints \n 5. Types of Complaints";
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
                        $message_string = "Thank you for requesting a call back. We will contact you shortly.\n Press 0 for main menu.";
                        $request_type = "2";

                        // Update the session record
                        $update_session = UssdSessions::where('session_id', $session_id)->update([
                            "case_no" => 0,
                            "step_no" => 1
                        ]);
                    }
                    break;
                case '3': // Inquiries
                    if ($case_no == 3 && $step_no == 1 && !empty($last_part) && is_numeric($last_part)&& !empty($last_part) && is_numeric($last_part) && !empty($last_part) && is_numeric($last_part) ) {
                                if($last_part== 1) // How do I get a licence?
                                $message_string = "To get a license, please follow these steps:\n Step 1. Get an application form.\n Step 2. Submit a soft and physical copy.\n Step 3. If all is well you will be issued payment fees.\n Step 4. Make payments according to the given time period.\n Press 0 for main menu.";
                                elseif($last_part== 2) // Are children safe online?
                                $message_string = "Yes, children can be safe online. As a parent, make sure to monitor. Press 0 to return to the main menu.";
                                elseif($last_part== 3) // How do I become a registered dealer?
                                $message_string = "To become a registered dealer, please visit: https://www.zicta.zm/faq. Press 0 to return to the main menu.";
                                elseif($last_part== 0)
                                $message_string = "Welcome to ZICTA. Please select from the following options:\n 1. About Us \n 2. Request Call Back \n 3. Inquiries \n 4. Register Complaints \n 5. Types of Complaints";
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
                            // Store the complaint in the database or take necessary actions
                            // For example:
                            $complaint = RegisterComplaint::create([
                                'description' => $last_part,
                                'session_id' => $session_id
                            ]);
                            $complaint->save();

                            // Generate the confirmation message
                            $message_string = "Thank you for registering your complaint. We will look into it. Press 0 to return to the main menu.";
                            $request_type = "2";

                            // Update the session record
                            $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 0,
                                "step_no" => 1
                            ]);
                        } else {
                            $message_string = "To register a complaint, please enter a brief description of your complaint.";
                            $request_type = "2";
                        }
                    } 
                    break;
                case '5': // Types of Complaints 
                    if ($case_no == 5 && $step_no == 1 && !empty($last_part) && is_numeric($last_part)) {
                        switch ($last_part) {  
                            case '1': // Introductory Message
                            $message_string = "Welcome,Please select a complaint:\n 1.Scaming messages\n2.Unsolicited Messages\n 3.Deregister SIM";
                            $request_type = "2";
                            
                            case '2': // Scamming Messages
                                $message_string = "Please select the type of scamming message:\n 1. If it was a Promotion\n 2. If it was a Job advert\n 3. If it was a Lottery\n 4. If it was other scams";
                                $request_type = "2";
                                // Update the session record
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                    "case_no" => 0,
                                    "step_no" => 1
                                ]);
                                break;
                            case '3': // Unsolicited Messages
                                $message_string = "Please select the type of unsolicited message:\n 1.For Adverts\n 2.For Offensive content\n 3. For Religious content\n 4. For Hate speech\n 5. For Other unsolicited message";
                                $request_type = "2";
                                // Update the session record
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                    "case_no" => 0,
                                    "step_no" => 1
                                ]);
                                break;
                            case '4': // Deregister SIM
                                $message_string = "Please enter your NRC number.";
                                $request_type = "2";
                                // Update the session record
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                    "case_no" => 0,
                                    "step_no" => 1
                                ]);
                                break;
                            default:
                                $message_string = "Invalid option selected. Press 0 to return to the main menu.";
                                $request_type = "2";
                                break;
                        }
                    } elseif ($case_no == 5 && $step_no == 2) {
                        switch ($last_part) {
                            case '1': // Scamming Messages - Type of Scam Message
                                $message_string = "Please enter the number from which you received the message.";
                                $request_type = "2";
                                // Update the session record
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                    "case_no" => 0,
                                    "step_no" => 1
                                ]);
                                break;
                            case '2': // Unsolicited Messages - Type of Message
                                $message_string = "Please enter the number of the offender.";
                                $request_type = "2";
                                // Update the session record
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                    "case_no" => 0,
                                    "step_no" => 1
                                ]);
                                break;
                            case '3': // Deregister SIM - NRC number
                                $message_string = "Please enter the number you wish to deregister.";
                                $request_type = "2";
                                // Update the session record
                                $update_session = UssdSessions::where('session_id', $session_id)->update([
                                    "case_no" => 0,
                                    "step_no" => 1
                                ]);
                                break;
                            default:
                                $message_string = "Invalid option selected. Press 0 to return to the main menu.";
                                $request_type = "2";
                                break;
                        }
                    } elseif ($case_no == 5 && $step_no == 3 && !empty($last_part)) {
                        if ($step_no == 3 && $last_part == '0') {
                            $message_string = "Welcome to ZICTA. Please select from the following options:\n 1. About Us \n 2. Request Call Back \n 3. Inquiries \n 4. Register Complaints \n 5. Types of Complaints";
                            $request_type = "2";
                            // Update the session record
                            $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 0,
                                "step_no" => 1
                            ]);
                        } else {
                            $message_string = "Dear customer, your request is being processed. You will receive an SMS shortly. Press 0 to return to the main menu.";
                            $request_type = "2";
                            // Update the session record
                            $update_session = UssdSessions::where('session_id', $session_id)->update([
                                "case_no" => 0,
                                "step_no" => 1
                            ]);
                        }
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
