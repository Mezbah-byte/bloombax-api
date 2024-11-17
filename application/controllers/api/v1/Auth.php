<?php
/**
 *
 */
class Auth extends CI_controller
{




    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('auth');
        $this->load->helper('communication');
        $this->load->helper('api/customer');
        $this->load->library('upload');

        $this->load->library('form_validation');
        $this->load->model('auth/auth_model');
        // $this->load->model('user_model');


    }



    function login()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->email) && isset($request->password)) {
            $email = $request->email;
            $password = $request->password;
            $response = login($email, $password);
        } else {
            $response = array('status' => false, 'reason' => 'Email or password missing');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function signUp()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->email, $request->password, $request->name, $request->phone_number, $request->refered_by, $request->side)) {
            $email = $request->email;
            $password = $request->password;
            $firstName = $request->name;
            $phone_number = $request->phone_number;
            $username = $request->username;
            $refered_by = $request->refered_by;
            $side = $request->side;

            $un_id = uniqid();

            $canSignUp = canSignUp($email, $phone_number, $username);

            if ($canSignUp['status']) {
                $data = customerDetailsByUsername($username);
                if ($data == null) {
                    $form = array();
                    $form['un_id'] = $un_id;
                    $form['name'] = $firstName;
                    $form['username'] = $username;
                    $form['phone_number'] = $phone_number;
                    $form['email'] = $email;
                    $form['password'] = $password;

                    // Get the referred user data
                    $referer_data = customerDetailsByUsername($refered_by);
                    $form['refered_by'] = $referer_data['un_id'];

                    // Determine placement based on the side selected
                    if ($side == 'left') {
                        $placement_id = $this->auth_model->findAvailableLeftPlacement($referer_data['un_id']);
                    } else if ($side == 'right') {
                        $placement_id = $this->auth_model->findAvailableRightPlacement($referer_data['un_id']);
                    } else {
                        $response = array('status' => false, 'reason' => 'Invalid side selected');
                        $this->output->set_content_type('application/json')->set_output(json_encode($response));
                        return;
                    }

                    $form['placement_id'] = $placement_id;
                    $form['side'] = $side;
                    $form['created_at'] = date('Y-m-d H:i:s');

                    // Handle wallet creation and other operations
                    $ch = curl_init();
                    $url = 'https://dev.forioxglobal.com/api/create_wallet/';
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    if (curl_errno($ch)) {
                        echo 'cURL Error: ' . curl_error($ch);
                    } else {
                        $data = json_decode($response, true);
                        if (isset($data['address'])) {
                            $form['wallet_address'] = $data['address'];
                            $form['private_key'] = $data['private_key'];
                        } else {
                            echo "Address not found.";
                        }
                    }
                    curl_close($ch);

                    $this->auth_model->addCustomer($form);

                    $formArray = array();
                    $formArray['from'] = 'regBonus';
                    $formArray['to_user'] = $un_id;
                    $formArray['amount'] = 20;
                    $formArray['created_at'] = date('Y-m-d H:i:s');
                    $this->auth_model->addRewardBalance($formArray);

                    $sessionId = md5(uniqid());
                    addSessionId($un_id, $sessionId);

                    $this->send_verify_email($email,  "Welcome to DFM Trade","Dear ". $firstName .",

Welcome to DFM Trade!!! We’re excited to have you as part of our growing community of investors. At DFM Trade, we are committed to helping you achieve your financial goals through smart and strategic investments. Specially Thank you for choosing DFM Trade as your trusted investment partner. We look forward to helping you achieve your financial aspirations! If you have any questions or need assistance, feel free to reach out to us at. Happy Investing!

Your User Name : ".$username."
Current Password :".$password."
Website: www.dfmtrade.com

Best regards,
DFM Trade Team.

© 2024, All rights reserved by DFM Trade");

                    $response = array('status' => true, 'sessionId' => $sessionId, 'data' => customerDetailsByUsername($username));
                } else {
                    $response = array('status' => false, 'reason' => 'Username already taken.');
                }
            } else {
                $response = $canSignUp;
            }
        } else {
            $response = array('status' => false, 'reason' => 'Missing required fields');
        }

        $jsonResponse = json_encode($response);
        $this->output->set_content_type('application/json')->set_output($jsonResponse);
    }



    function get_network($un_id)
    {
        $network = array();
        $members = $this->admin_model->team_list($un_id);
        foreach ($members as $member) {
            $network[] = $member;
            $network = array_merge($network, $this->get_network($member['un_id']));
        }
        return $network;
    }


    function get_network_by_level($un_id, $level = 0)
    {
        $network = array();
        $members = $this->auth_model->team_list($un_id);

        foreach ($members as $member) {
            $member['level'] = $level;
            if (count($this->auth_model->team_list($member['un_id'])) < 2) {
                $network[] = $member;
            }
            $sub_network = $this->get_network_by_level($member['un_id'], $level + 1);
            if (!empty($sub_network)) {
                $network = array_merge($network, $sub_network);
            }
        }

        $levels = array_column($network, 'level');
        array_multisort($levels, SORT_ASC, $network);

        return $network;
    }



    function checkUserExistByUsername()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->username)) {
            $data = customerDetailsByUsername($request->username);
            if ($data != null) {
                $response = array('status' => true, 'data' => $data);
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No username found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function checkUserSideByUsername()
    {
        $username = isset($_GET['username']) ? $_GET['username'] : null;

        if ($username) {
            $data = customerDetailsByUsername($username);
            if ($data != null) {
                $response = array(
                    'status' => true,
                    'userData' => $data,
                    'teamList' => referList($data['un_id'])
                );
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No username found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function send_verify_email($to, $subject, $message) {
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'tls://dfmtrade.com',
            'smtp_port' => 465,
            'smtp_user' => 'verify@dfmtrade.com',
            'smtp_pass' => '92cDzzmPxLRC',
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'wordwrap'   => TRUE
        );
    
        $this->load->library('email');
        $this->email->initialize($config);
    
        $this->email->from('verify@dfmtrade.com', 'DFM Trade');
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
    
        if ($this->email->send()) {
            return true;
        } else {
            return false;
            // show_error($this->email->print_debugger());
        }
    }


    function send_test_email() {
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'tls://dfmtrade.com',
            'smtp_port' => 465,
            'smtp_user' => 'verify@dfmtrade.com',
            'smtp_pass' => '92cDzzmPxLRC',
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'wordwrap'   => TRUE
        );
    
        $this->load->library('email');
        $this->email->initialize($config);
    
        $this->email->from('verify@dfmtrade.com', 'DFM Trade');
        $this->email->to('mazbahurrahman04@gmail.com');
        $this->email->subject('Test Email');
        $this->email->message('This is a test email.');
    
        if ($this->email->send()) {
            echo 'Email sent successfully!';
        } else {
            show_error($this->email->print_debugger());
        }
    }

}

?>