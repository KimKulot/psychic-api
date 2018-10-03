<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midnite81\Plivo\Contracts\Services\Messaging;

class SendSmsController extends Controller
{
    public function sendSms(Messaging $messaging, Request $request) {

    	$data = [];
        $success = true;
        $errors = [];   
        $message = $request->message;
        $from = isset($request->sender_mobile_num)? $request->sender_mobile_num : '12399003577';
        $to = $request->mobile_number;
        $is_question = isset($request->is_question)? $request->is_question : 0;
        $is_random = isset($request->is_random)? $request->is_random : 0;
        $is_help = isset($request->is_help)? $request->is_help : 0;
        if ($is_random || $is_question) {

            $sms_respond = $this->autoRespondSMS($to, $from, $is_random, $is_question);
            $data = $sms_respond['data'];
            $success = $sms_respond['success'];
            $errors['respond_sms'] = $sms_respond['errors'];
        } else {
            $msg = plivo_send_text($to, $message);
            if (isset($msg['response']['error'])) {
                $errors['send_sms_error'] = $msg['response']['error'];
                $success = false;
            }

            $data['message'] = $msg;
            $type = isset($request->type)? $request->type: 'user';
            if ($is_help == 0) {
                if ($success == true && $type == 'user') {
                    $sms_respond = $this->autoRespondSMS($to, $from);
                    $errors['respond_sms'] = $sms_respond['errors'];
                }
            }
        }
    	

        return compact('data', 'success', 'errors');
    }

    public function autoRespondSMS($to, $from, $is_random = null, $is_question = null) {

        $data = [];
        $errors = []; //PSYCHICS YOUR Q to 1239-900-3577 Cost: 1 credit. We have successfully received your message!
        $success = true;
        $message = '';
        if ($is_random == 1) {
            $message = 'Purchase 4 Random Reader Replies. 1st Reader reply is free. Next 3 different reader cost $1.50 per message total $4.50. Send PSYCHICS YES to 12399003577 to agree.';
            // $message = 'PSYCHICS RANDOM YOUR Q = 1 Q answered by 4 Readers Each Answer from Readers = 1 credit NO OBLIGATION TO BUY ALL';            
        } else {
            $message = 'PSYCHICS YOUR Q Cost: 1 credit. We have successfully received your message!';
        }
        
        $msg = plivo_send_text($from, $message);
        $data = $msg;
        if (isset($msg['response']['error'])) {
            $errors = $msg['response']['error'];
        }

        if (count($errors)) {
            $success = false;
        }

        return compact('data', 'success', 'errors');
    }
}
