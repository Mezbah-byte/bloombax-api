<?php
/**
 *
 */
class Home extends CI_controller
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
        $this->load->model('api/customer_model');

        // redirect(base_url().'admin/auth/logout');
    }


    function home()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId)) {
            $data = customerDetailsBySessionId($request->sessionId);
            if ($data != null) {
                $userData = $data['data'];
                $packageList = $this->customer_model->userPackageListByOrder($userData['un_id']);
                $packageAmountTotal = $this->customer_model->get_total_package_amount_by_user_all_package($userData['un_id']);

                $leftUserData = $this->customer_model->aPlacedUserDataBySide($userData['un_id'], 'left');
                $rightUserData = $this->customer_model->aPlacedUserDataBySide($userData['un_id'], 'right');
                $response = array(
                    'status' => true,
                    // 'deposit' => $this->customer_model->getRewardBalanceTotal($userData['un_id']) + $this->customer_model->getDepositTotal($userData['un_id']),
                    'deposit' => (!is_null(getUsdtBalanceBNB($userData['wallet_address'])) ? getUsdtBalanceBNB($userData['wallet_address']) : 0)
                        + (!is_null($this->customer_model->getRewardBalanceTotal($userData['un_id'])) ? $this->customer_model->getRewardBalanceTotal($userData['un_id']) : 0),
                    'withdraw' => $userData['current_balance'],
                    'total_withdraw' => !is_null($this->customer_model->getWithdrawTotal($userData['un_id'])) ? $this->customer_model->getWithdrawTotal($userData['un_id']) : 0,
                    'roi_bonus' => !is_null($this->customer_model->get_total_specific_bonus_total('roi_bonus', $userData['un_id'])) ? $this->customer_model->get_total_specific_bonus_total('roi_bonus', $userData['un_id']) : 0,
                    'direct_bonus' => !is_null($this->customer_model->get_total_specific_bonus_total('direct_bonus', $userData['un_id'])) ? $this->customer_model->get_total_specific_bonus_total('direct_bonus', $userData['un_id']) : 0,
                    'team_bonus' => !is_null($this->customer_model->get_total_specific_bonus_total('team_bonus', $userData['un_id'])) ? $this->customer_model->get_total_specific_bonus_total('team_bonus', $userData['un_id']) : 0,
                    'matching_bonus' => !is_null($this->customer_model->get_total_specific_bonus_total('matching_bonus', $userData['un_id'])) ? $this->customer_model->get_total_specific_bonus_total('matching_bonus', $userData['un_id']) : 0,
                    'monthly_reward' => !is_null($this->customer_model->get_total_specific_bonus_total('monthly_reward_bonus', $userData['un_id'])) ? $this->customer_model->get_total_specific_bonus_total('monthly_reward_bonus', $userData['un_id']) : 0,
                    'rank_reward' => !is_null($this->customer_model->get_total_specific_bonus_total('monthly_reward_bonus', $userData['un_id'])) ? $this->customer_model->get_total_specific_bonus_total('monthly_reward_bonus', $userData['un_id']) : 0,
                    'roi_volume_left' => $leftUserData == null ? 0 : teamTotalRoiToday($leftUserData['un_id']),
                    'roi_volume_right' => $rightUserData == null ? 0 : teamTotalRoiToday($rightUserData['un_id']),
                    'business_volume_left' => $leftUserData == null ? 0 : teamTotalBusiness($leftUserData['un_id']),
                    'business_volume_right' => $rightUserData == null ? 0 : teamTotalBusiness($rightUserData['un_id']),
                    'business_volume_yesterday' => ($leftUserData == null ? 0 : teamTotalBusinessYestwrday($leftUserData['un_id'])) + ($rightUserData == null ? 0 : teamTotalBusinessYestwrday($rightUserData['un_id'])),
                    'this_month_left' => $leftUserData == null ? 0 : teamTotalBusinessThisMonth($leftUserData['un_id']),
                    'this_month_right' => $rightUserData == null ? 0 : teamTotalBusinessThisMonth($rightUserData['un_id']),
                    'rank_status' => count($packageList) > 0 ? $packageList[0]['package_name'] : 'Inactive',
                    'rank_logo' => 'https://img.freepik.com/free-vector/first-place-number-one-winner-label-design_1017-32242.jpg',
                    'rank' => 0,
                    'capping_limit' => $packageAmountTotal,
                    'earning_limit' => $packageAmountTotal * 5,
                    'totalEarning' => (!is_null($this->customer_model->get_total_specific_bonus_total('direct_bonus', $userData['un_id'])) ? $this->customer_model->get_total_specific_bonus_total('direct_bonus', $userData['un_id']) : 0) + (!is_null($this->customer_model->get_total_specific_bonus_total('team_bonus', $userData['un_id'])) ? $this->customer_model->get_total_specific_bonus_total('team_bonus', $userData['un_id']) : 0) + (!is_null($this->customer_model->get_total_specific_bonus_total('matching_bonus', $userData['un_id'])) ? $this->customer_model->get_total_specific_bonus_total('matching_bonus', $userData['un_id']) : 0)
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

    function depositList()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId)) {
            // Replace this with your actual function to get user data
            $data = customerDetailsBySessionId($request->sessionId);
            if ($data != null) {
                $userData = $data['data'];

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.bscscan.com/api?module=account&action=tokentx&address=' . $userData['wallet_address'] . '&contractaddress=0x55d398326f99059ff775485246999027b3197955&startblock=0&endblock=99999999&apikey=MMMJ7NAW2CN9JE9RBQSJQWG55AIDB9Q2CJ&sort=asc',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                ));

                $response = curl_exec($curl);
                if (curl_errno($curl)) {
                    $response = array('status' => false, 'reason' => 'cURL Error: ' . curl_error($curl));
                } else {
                    $dataa = json_decode($response, true);
                    if ($dataa['status'] == "1") {
                        $transactions = $dataa['result'];

                        // Initialize an array to hold processed transactions
                        $processedTransactions = array();

                        // Process each transaction
                        foreach ($transactions as $transaction) {
                            // Check if the transaction is received by the user's wallet address
                            // if ($transaction['to'] == $userData['wallet_address']) {
                            $processedTransaction = array(
                                'userWallet' => $userData['wallet_address'],
                                'txHash' => $transaction['hash'],
                                'from_address' => $transaction['from'],
                                'to' => $transaction['to'],
                                'amount' => $transaction['value'] / 1e18, // Convert value from Wei to Ether
                                'blockNumber' => $transaction['blockNumber'],
                                'created_at' => date('Y-m-d H:i:s', $transaction['timeStamp'])
                            );
                            $processedTransactions[] = $processedTransaction;
                            // }
                        }

                        $response = array('status' => true, 'data' => $processedTransactions);
                    } else {
                        $response = array('status' => false, 'reason' => 'Crypto API Not Responding.');
                    }
                }
                curl_close($curl);

            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session ID found.');
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }




    function purchaseList()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId)) {
            $data = customerDetailsBySessionId($request->sessionId);
            if ($data != null) {
                $userData = $data['data'];
                $response = array(
                    'status' => true,
                    'data' => cryptoTransferList($userData['un_id'], 'in')
                );
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }

    function withdrawList()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId)) {
            $data = customerDetailsBySessionId($request->sessionId);
            if ($data != null) {
                $userData = $data['data'];
                $response = array(
                    'status' => true,
                    'data' => cryptoTransferList($userData['un_id'], 'out')
                );
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }

    function bonusList()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId) && isset($request->type)) {
            $data = customerDetailsBySessionId($request->sessionId);
            $type = $request->type;
            if ($data != null) {
                $userData = $data['data'];
                $response = array(
                    'status' => true,
                    'data' => bonusList($userData['un_id'], $type)
                );
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }

    function rewardBonusList()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId) && isset($request->type)) {
            $data = customerDetailsBySessionId($request->sessionId);
            $type = $request->type;
            if ($data != null) {
                $userData = $data['data'];
                $response = array(
                    'status' => true,
                    'data' => rewardBonusList($userData['un_id'], $type)
                );
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }

    function userDetails()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId)) {
            $data = customerDetailsBySessionId($request->sessionId);
            if ($data != null) {
                $userData = $data['data'];
                $response = array(
                    'status' => true,
                    'data' => $userData
                );
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function updateProfile()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId) && isset($request->name) && isset($request->phone_number)) {
            $data = customerDetailsBySessionId($request->sessionId);
            if ($data != null) {
                $userData = $data['data'];
                $form = array();
                $form['name'] = $request->name;
                $form['phone_number'] = $request->phone_number;
                updateProfile($userData['un_id'], $form);
                $data = customerDetailsBySessionId($request->sessionId);
                $userData = $data['data'];

                $response = array(
                    'status' => true,
                    'data' => $userData
                );
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function updatePassword()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId) && isset($request->oldPassword) && isset($request->newPassword) && isset($request->conPassword)) {
            $oldPassword = $request->oldPassword;
            $newPassword = $request->newPassword;
            $conPassword = $request->conPassword;
            $data = customerDetailsBySessionId($request->sessionId);
            if ($data != null) {
                $userData = $data['data'];
                if ($userData['password'] == $oldPassword) {
                    if ($newPassword == $conPassword) {
                        $form = array();
                        $form['password'] = $newPassword;
                        updateProfile($userData['un_id'], $form);
                        $data = customerDetailsBySessionId($request->sessionId);
                        $userData = $data['data'];
                        $response = array(
                            'status' => true,
                            'data' => $userData
                        );
                    } else {
                        $response = array('status' => false, 'reason' => 'Password did not match!');
                    }
                } else {
                    $response = array('status' => false, 'reason' => 'Wrong Password.');
                }
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function packagesList()
    {
        $data = $this->customer_model->packages();
        $response = array('status' => true, 'data' => $data);

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }

    function userPackageList()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId)) {
            $data = customerDetailsBySessionId($request->sessionId);
            if ($data != null) {
                $userData = $data['data'];

                $data = $this->customer_model->userPackageListByStatus($userData['un_id'], 1);
                $response = array('status' => true, 'data' => $data);
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }

    function checkPackageByAmount()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->amount)) {
            $amount = $request->amount;

            $data = $this->customer_model->checkPackageByAmount($amount);
            if ($data != null) {
                $response = array('status' => true, 'data' => $data);
            } else {
                $response = array('status' => false, 'reason' => 'No package found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No amount found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }

    function cryptoBalance()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId)) {
            $data = customerDetailsBySessionId($request->sessionId);
            if ($data != null) {
                $userData = $data['data'];

                $tokenbalance = 0.00;
                $gas_balance = 0.00;



                $ch = curl_init();
                // $url = 'https://dev.forioxglobal.com/api/get-token-balance/0xc2132d05d31c914a87c6611c10748aeb04b58e8f/' . $userData['wallet_address'] . '/';
                $url = 'https://dev.forioxglobal.com/api/get_usdt_balance_bnb/' . $userData['wallet_address'] . '/';
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    echo 'cURL Error: ' . curl_error($ch);
                } else {
                    $data = json_decode($response, true);
                    if (isset($data['token_balance'])) {
                        $tokenbalance = $data['token_balance'];
                    } else {
                        $response = array('status' => false, 'reason' => 'Crypto Api Not Responding.');
                    }
                }
                curl_close($ch);


                $ch = curl_init();
                $url = 'https://dev.forioxglobal.com/api/get_bnb_balance_bnb/';
                // Prepare the POST data
                $postData = array(
                    'address' => $userData['wallet_address']
                );

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData)); // Send data as JSON

                // Set the content type to application/json
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json'
                    )
                );

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    echo 'cURL Error: ' . curl_error($ch);
                } else {
                    $data = json_decode($response, true);
                    if (isset($data['token_balance'])) {
                        $gas_balance = $data['token_balance'];
                    } else {
                        $response = array('status' => false, 'reason' => 'Crypto Api Not Responding.');
                    }
                }
                curl_close($ch);
                $response = array('status' => true, 'usdt' => $tokenbalance, 'gas' => $gas_balance);
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function withdrawOtp()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId)) {
            $data = customerDetailsBySessionId($request->sessionId);
            if ($data != null) {
                $userData = $data['data'];

                $otp = rand(1000, 9999);
                // $otp = '1234';

                $this->send_verify_email($userData['email'], "Verify your account", "Dear " . $userData['username'] . ",
Your otp is" . $otp . "
Website: www.dfmtrade.com

Best regards,
DFM Trade Team.

© 2024, All rights reserved by DFM Trade");

                $form = array();
                $form['user_un_id'] = $userData['un_id'];
                $form['otp'] = $otp;
                $form['type'] = 'withdraw';
                $form['created_at'] = date('Y-m-d H:i:s');
                $data = $this->customer_model->addOtp($form);

                $response = array('status' => true, 'otpId' => $data);
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function verifyWithdrawOtp()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId) && isset($request->otpId) && isset($request->otp)) {
            $data = customerDetailsBySessionId($request->sessionId);
            $otpId = $request->otpId;
            $otp = $request->otp;
            if ($data != null) {
                $userData = $data['data'];

                $otpDetails = $this->customer_model->otpDetails($otpId);

                if ($otpDetails != null && $otpDetails['user_un_id'] == $userData['un_id'] && $otpDetails['otp'] == $otp) {
                    $response = array('status' => true);
                } else {
                    $response = array('status' => false, 'reason' => 'Wrong otp.');
                }
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function withdraw()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId) && isset($request->amount) && isset($request->address)) {
            $data = customerDetailsBySessionId($request->sessionId);
            $amount = $request->amount;
            $address = $request->address;
            if ($data != null) {

                $userData = $data['data'];
                if ($amount <= $userData['current_balance']) {
                    $ch = curl_init();
                    $url = 'https://dev.forioxglobal.com/api/transfer_token_bnb/';

                    $data = array(
                        "token_address" => "0x55d398326f99059fF775485246999027B3197955",
                        "from_address" => $this->customer_model->settingData('withdrawAccountAddress')['value'],
                        "private_key" => $this->customer_model->settingData('withdrawAccountPrivateKey')['value'],
                        "to_address" => $address,
                        "amount_usdt" => $amount * 0.9
                    );

                    $postData = json_encode($data);
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($postData)
                        )
                    );

                    $responsee = curl_exec($ch);

                    if (curl_errno($ch)) {
                        $response = array('status' => false, 'reason' => curl_error($ch));
                    } else {
                        $data = json_decode($responsee, true);
                        if (isset($data['transaction_hash'])) {
                            $transaction_hash = $data['transaction_hash'];

                            $form = array();
                            $form['current_balance'] = $userData['current_balance'] - $amount;
                            updateProfile($userData['un_id'], $form);

                            $form = array();
                            $form['from_user'] = $userData['un_id'];
                            $form['from_address'] = $this->customer_model->settingData('withdrawAccountAddress')['value'];
                            $form['to_address'] = $address;
                            $form['amount'] = $amount;
                            $form['txHash'] = $transaction_hash;
                            $form['type'] = 'out';
                            $form['created_at'] = date('Y-m-d H:i:s');
                            $form['status'] = '0';

                            $this->customer_model->addCryptoTransfer($form);
                            $response = array('status' => true, 'transaction_hash' => $transaction_hash);
                        } else {
                            $response = array('status' => false, 'reason' => 'Crypto Api Not Responding.');
                        }
                    }
                    curl_close($ch);
                } else {
                    $response = array('status' => false, 'reason' => 'Low balance!');
                }

            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function referList()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId)) {
            $data = customerDetailsBySessionId($request->sessionId);
            if ($data != null) {
                $userData = $data['data'];
                $data = $this->customer_model->referList($userData['un_id']);
                $response = array('status' => true, 'data' => $data);
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }

    function teamListForBinary()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId)) {
            $data = customerDetailsBySessionId($request->sessionId);
            if ($data['status']) {
                $userData = $data['data'];
                $response = array();
                $response['status'] = true;
                $response['userData'] = $userData;
                // $response['teamListForBinary'] = teamListForBinary($userData['un_id']);
                $response['teamDetails'] = build_team($userData['un_id']);
                // $response['get_network_by_level'] = get_network_by_level($userData['un_id']);
                // $response['teamDetails'] = get_teamDetails($userData['un_id']);
            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No sessionId found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function deposit()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->sessionId) && isset($request->amount)) {
            $data = customerDetailsBySessionId($request->sessionId);
            $amount = $request->amount;
            if ($data != null) {
                $userData = $data['data'];
                $gas_balance = 0.00;
                $usdtBalance = 0.00;

                $givtBalance = $this->customer_model->getRewardBalanceTotal($userData['un_id']);

                $ch = curl_init();
                $url = 'https://dev.forioxglobal.com/api/get_usdt_balance_bnb/' . $userData['wallet_address'] . '/';
                $postData = array(
                    'address' => $userData['wallet_address']
                );

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Content-Type: application/json'
                    )
                );
                $response = curl_exec($ch);
                if (curl_errno($ch)) {
                    $response = array('status' => false, 'reason' => curl_error($ch));
                    $jsonResponse = json_encode($response);
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output($jsonResponse);
                    return;
                } else {
                    $data = json_decode($response, true);
                    if (isset($data['usdt_balance'])) {
                        $usdtBalance = ($data['usdt_balance'] / 1000000000000);
                    } else {
                        $response = array('status' => false, 'reason' => "Something went wrong! Please try again later.", 'response' => $response);
                        $jsonResponse = json_encode($response);
                        $this->output
                            ->set_content_type('application/json')
                            ->set_output($jsonResponse);
                        return;
                    }
                }
                curl_close($ch);


                if ($usdtBalance < 1) {
                    if ($amount > $givtBalance) {
                        $response = array('status' => false, 'reason' => "Low balance.");
                        $jsonResponse = json_encode($response);
                        $this->output
                            ->set_content_type('application/json')
                            ->set_output($jsonResponse);
                        return;
                    } else {

                        $form = array();
                        $form['from_user'] = $userData['un_id'];
                        $form['from_address'] = $userData['wallet_address'];
                        $form['to_address'] = $this->customer_model->settingData('depositAccountAddress')['value'];
                        $form['amount'] = $amount;
                        $form['txHash'] = $this->generateFakeTxHash();
                        $form['type'] = 'in';
                        $form['created_at'] = date('Y-m-d H:i:s');
                        $form['status'] = '1';

                        $this->customer_model->addCryptoTransfer($form);


                        $packageData = $this->customer_model->checkPackageByAmount($amount);
                        $formData = array();
                        $formData['user_un_id'] = $userData['un_id'];
                        $formData['package_un_id'] = $packageData['un_id'];
                        $formData['package_name'] = $packageData['title'];
                        $formData['amount'] = $amount;
                        $formData['duration'] = $packageData['duration'];
                        $formData['status'] = 1;
                        $formData['created_at'] = date('Y-m-d H:i:s');
                        $packageId = $this->customer_model->addMyPackage($formData);

                        $customerDetailsByUnId = customerDetailsByUnId($userData['un_id']);

                        $userForm = array();
                        $userForm['active'] = 1;
                        updateProfile($customerDetailsByUnId['un_id'], $userForm);

                        $refererDetails = customerDetailsByUnId($customerDetailsByUnId['refered_by']);

                        if ($refererDetails != null && $refererDetails['active'] == 1) {
                            $directBonus = array();
                            $directBonus['from_user'] = $userData['un_id'];
                            $directBonus['to_user'] = $customerDetailsByUnId['refered_by'];
                            $directBonus['amount'] = $amount * 0.07;
                            $directBonus['package_id'] = $packageId;
                            $directBonus['package_name'] = $packageData['title'];
                            $directBonus['created_at'] = date('Y-m-d H:i:s');
                            $this->customer_model->addDirectBonus($directBonus);

                            $addEarning = array();
                            $addEarning['current_balance'] = $refererDetails['current_balance'] + $amount * 0.07;
                            updateProfile($customerDetailsByUnId['refered_by'], $addEarning);
                        }

                        $claimingList = $this->customer_model->getRewardBalanceList($userData['un_id']);

                        $leftAmount = $amount;

                        foreach ($claimingList as $c) {
                            if ($leftAmount >= $c['amount']) {
                                $form = array();
                                $form['claimed'] = 1;
                                $this->customer_model->updateRewardBalance($c['id'], $form);
                            } else {
                                $form = array();
                                $form['amount'] = $c['amount'] - $leftAmount;
                                $this->customer_model->updateRewardBalance($c['id'], $form);
                            }

                            $leftAmount = $leftAmount - $c['amount'];

                        }

                        $response = array('status' => true, 'message' => 'Successfull');
                    }
                } else {
                    $ch = curl_init();
                    $url = 'https://dev.forioxglobal.com/api/get_bnb_balance_bnb/'; // Replace with your actual endpoint

                    // Prepare the POST data
                    $postData = array(
                        'address' => $userData['wallet_address']
                    );

                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData)); // Send data as JSON

                    // Set the content type to application/json
                    curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        array(
                            'Content-Type: application/json'
                        )
                    );

                    $response = curl_exec($ch);

                    if (curl_errno($ch)) {
                        $response = array('status' => false, 'reason' => curl_error($ch));
                        $jsonResponse = json_encode($response);
                        $this->output
                            ->set_content_type('application/json')
                            ->set_output($jsonResponse);
                        return;
                    } else {
                        $data = json_decode($response, true);
                        if (isset($data['bnb_balance'])) {
                            $gas_balance = $data['bnb_balance'];
                        } else {
                            $matic_balance = 'Balance not available'; // Handle the case where the balance is not returned
                        }
                    }

                    curl_close($ch);


                    if ($gas_balance < 0.0005) {
                        $ch = curl_init();
                        $url = 'https://dev.forioxglobal.com/api/transfer_bnb_bnb/';

                        $data = array(
                            "token_address" => "0x7d1afa7b718fb893db30a3abc0cfc608aacfebb0",
                            "from_address" => $this->customer_model->settingData('gasAccountAddress')['value'],
                            "private_key" => $this->customer_model->settingData('gasAccountPrivateKey')['value'],
                            "to_address" => $userData['wallet_address'],
                            "amount_bnb" => 0.0019
                        );

                        $postData = json_encode($data);
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                        curl_setopt(
                            $ch,
                            CURLOPT_HTTPHEADER,
                            array(
                                'Content-Type: application/json',
                                'Content-Length: ' . strlen($postData)
                            )
                        );

                        $responsee = curl_exec($ch);

                        if (curl_errno($ch)) {
                            $response = array('status' => false, 'reason' => curl_error($ch));
                        } else {
                            $data = json_decode($responsee, true);
                            if (isset($data['transaction_hash'])) {
                                $transaction_hash = $data['transaction_hash'];
                                $response = array('status' => true, 'transaction_hash' => $transaction_hash);
                                $gasTransfer = true;
                            } else {
                                $response = array('status' => false, 'reason' => 'Crypto Api Not Responding.', 'data' => $data);
                                $gasTransfer = false;
                            }
                        }
                        curl_close($ch);
                    } else {
                        $gasTransfer = true;
                    }


                    if ($gasTransfer) {

                        if ($amount > $usdtBalance) {
                            $ch = curl_init();
                            $url = 'https://dev.forioxglobal.com/api/transfer_token_bnb/';

                            $data = array(
                                "token_address" => "0x55d398326f99059fF775485246999027B3197955",
                                "from_address" => $userData['wallet_address'],
                                "private_key" => $userData['private_key'],
                                "to_address" => $this->customer_model->settingData('depositAccountAddress')['value'],
                                "amount_usdt" => $usdtBalance
                            );

                            $postData = json_encode($data);
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                            curl_setopt(
                                $ch,
                                CURLOPT_HTTPHEADER,
                                array(
                                    'Content-Type: application/json',
                                    'Content-Length: ' . strlen($postData)
                                )
                            );

                            $responsee = curl_exec($ch);

                            if (curl_errno($ch)) {
                                $response = array('status' => false, 'reason' => curl_error($ch));
                            } else {
                                $data = json_decode($responsee, true);
                                if (isset($data['transaction_hash'])) {
                                    $transaction_hash = $data['transaction_hash'];

                                    $form = array();
                                    $form['from_user'] = $userData['un_id'];
                                    $form['from_address'] = $userData['wallet_address'];
                                    $form['to_address'] = $this->customer_model->settingData('depositAccountAddress')['value'];
                                    // $form['amount'] = $amount*1000000000000;
                                    $form['amount'] = $amount;
                                    $form['txHash'] = $transaction_hash;
                                    $form['type'] = 'in';
                                    $form['created_at'] = date('Y-m-d H:i:s');
                                    $form['status'] = '0';

                                    $this->customer_model->addCryptoTransfer($form);

                                    $claimingList = $this->customer_model->getRewardBalanceList($userData['un_id']);

                                    $leftAmount = $amount - $usdtBalance;
                                    foreach ($claimingList as $c) {
                                        if ($leftAmount >= $c['amount']) {
                                            $form = array();
                                            $form['claimed'] = 1;
                                            $this->customer_model->updateRewardBalance($c['id'], $form);
                                        } else {
                                            $form = array();
                                            $form['amount'] = $c['amount'] - $leftAmount;
                                            $this->customer_model->updateRewardBalance($c['id'], $form);
                                        }

                                        $leftAmount = $leftAmount - $c['amount'];

                                    }

                                    $response = array('status' => true, 'transaction_hash' => $transaction_hash);
                                } else {
                                    $response = array('status' => false, 'reason' => 'Crypto Api Not Responding.', 'data' => $data);
                                }
                            }
                            curl_close($ch);
                        } else {
                            $ch = curl_init();
                            $url = 'https://dev.forioxglobal.com/api/transfer_token_bnb/';

                            $data = array(
                                "token_address" => "0x55d398326f99059fF775485246999027B3197955",
                                "from_address" => $userData['wallet_address'],
                                "private_key" => $userData['private_key'],
                                "to_address" => $this->customer_model->settingData('depositAccountAddress')['value'],
                                "amount_usdt" => ($amount)
                            );

                            $postData = json_encode($data);
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                            curl_setopt(
                                $ch,
                                CURLOPT_HTTPHEADER,
                                array(
                                    'Content-Type: application/json',
                                    'Content-Length: ' . strlen($postData)
                                )
                            );

                            $responsee = curl_exec($ch);

                            if (curl_errno($ch)) {
                                $response = array('status' => false, 'reason' => curl_error($ch));
                            } else {
                                $data = json_decode($responsee, true);
                                if (isset($data['transaction_hash'])) {
                                    $transaction_hash = $data['transaction_hash'];

                                    $form = array();
                                    $form['from_user'] = $userData['un_id'];
                                    $form['from_address'] = $userData['wallet_address'];
                                    $form['to_address'] = $this->customer_model->settingData('depositAccountAddress')['value'];
                                    $form['amount'] = $amount;
                                    $form['txHash'] = $transaction_hash;
                                    $form['type'] = 'in';
                                    $form['created_at'] = date('Y-m-d H:i:s');
                                    $form['status'] = '0';

                                    $this->customer_model->addCryptoTransfer($form);

                                    $response = array('status' => true, 'transaction_hash' => $transaction_hash);
                                } else {
                                    $response = array('status' => false, 'reason' => 'Crypto Api Not Responding.', 'data' => $data);
                                }
                            }
                            curl_close($ch);
                        }


                    } else {
                        $response = array('status' => false, 'reason' => 'Something went wrong, please try again later.');
                    }



                }

            } else {
                $response = array('status' => false, 'reason' => 'No user found.');
            }
        } else {
            $response = array('status' => false, 'reason' => 'No session id found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function generateFakeTxHash()
    {
        $characters = '0123456789abcdef';
        $hash = '';

        for ($i = 0; $i < 64; $i++) {
            $hash .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $hash;
    }


    function checkTransactionStatus()
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        if (isset($request->trx)) {
            $data = $this->customer_model->transferDetailsByTrx($request->trx);
            if ($data['type'] == 'in') {
                if ($data['status'] == 0) {
                    $ch = curl_init();
                    $url = 'https://dev.forioxglobal.com/api/check_transfer_status_bnb/';

                    $datae = array(
                        "transaction_hash" => $data['txHash']
                    );

                    $postData = json_encode($datae);
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($postData)
                        )
                    );

                    $responsee = curl_exec($ch);

                    if (curl_errno($ch)) {
                        $response = array('status' => false, 'reason' => curl_error($ch));
                    } else {
                        $dataa = json_decode($responsee, true);
                        if (isset($dataa['status'])) {
                            $transferStatus = $dataa['status'];
                            if ($transferStatus == 'Successful') {
                                $form = array();
                                $form['status'] = 1;
                                $this->customer_model->updateTransfer($data['txHash'], $form);

                                $packageData = $this->customer_model->checkPackageByAmount($data['amount']);


                                $formData = array();
                                $formData['user_un_id'] = $data['from_user'];
                                $formData['package_un_id'] = $packageData['un_id'];
                                $formData['package_name'] = $packageData['title'];
                                $formData['amount'] = $data['amount'];
                                $formData['duration'] = $packageData['duration'];
                                $formData['status'] = 1;
                                $formData['created_at'] = date('Y-m-d H:i:s');
                                $packageId = $this->customer_model->addMyPackage($formData);

                                $customerDetailsByUnId = customerDetailsByUnId($data['from_user']);

                                $userForm = array();
                                $userForm['active'] = 1;
                                updateProfile($customerDetailsByUnId['un_id'], $userForm);

                                $refererDetails = customerDetailsByUnId($customerDetailsByUnId['refered_by']);

                                if ($refererDetails != null && $refererDetails['active'] == 1) {

                                    $directBonus = array();
                                    $directBonus['from_user'] = $data['from_user'];
                                    $directBonus['to_user'] = $customerDetailsByUnId['refered_by'];
                                    $directBonus['amount'] = $data['amount'] * 0.07;
                                    $directBonus['package_id'] = $packageId;
                                    $directBonus['package_name'] = $packageData['title'];
                                    $directBonus['created_at'] = date('Y-m-d H:i:s');
                                    $this->customer_model->addDirectBonus($directBonus);

                                    $addEarning = array();
                                    $addEarning['current_balance'] = $refererDetails['current_balance'] + $data['amount'] * 0.07;
                                    updateProfile($customerDetailsByUnId['refered_by'], $addEarning);
                                }

                                $claimingList = $this->customer_model->getRewardBalanceList($data['from_user']);

                                foreach ($claimingList as $c) {
                                    $form = array();
                                    $form['claimed'] = 1;

                                    $this->customer_model->updateRewardBalance($c['id'], $form);
                                }

                                $response = array('status' => true, 'message' => 'Successfull');
                            } else if ($transferStatus == 'Pending') {
                                $response = array('status' => false, 'reason' => 'Pending');
                            } else if ($transferStatus == 'Failed') {
                                $form = array();
                                $form['status'] = 2;
                                $this->customer_model->updateTransfer($data['txHash'], $form);

                                $userDetails = customerDetailsByUnId($data['from_user']);

                                // $mmmmm = array();
                                // $mmmmm['current_balance'] = $userDetails['current_balance'] + $data['amount'];
                                // updateProfile($data['from_user'], $mmmmm);

                                $response = array('status' => false, 'reason' => 'Failed');
                            }
                        } else {
                            $response = array('status' => false, 'reason' => 'Crypto Api Not Responding.', 'data' => $data);
                        }
                    }
                    curl_close($ch);
                } else if ($data['status'] == 1) {
                    $response = array('status' => true, 'message' => 'Successfull');
                } else if ($data['status'] == 2) {
                    $response = array('status' => false, 'reason' => 'Canceled');
                }
            } else {
                //need to work
                if ($data['status'] == 0) {
                    $ch = curl_init();
                    $url = 'https://dev.forioxglobal.com/api/check_transfer_status_bnb/';

                    $datae = array(
                        "transaction_hash" => $data['txHash']
                    );

                    $postData = json_encode($datae);
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($postData)
                        )
                    );

                    $responsee = curl_exec($ch);

                    if (curl_errno($ch)) {
                        $response = array('status' => false, 'reason' => curl_error($ch));
                    } else {
                        $dataa = json_decode($responsee, true);
                        if (isset($dataa['status'])) {
                            $transferStatus = $dataa['status'];
                            if ($transferStatus == 'Successful') {
                                $form = array();
                                $form['status'] = 1;
                                $this->customer_model->updateTransfer($data['txHash'], $form);
                                $response = array('status' => true, 'message' => 'Successfull');
                            } else if ($transferStatus == 'Pending') {
                                $response = array('status' => false, 'reason' => 'Pending');
                            } else if ($transferStatus == 'Failed') {
                                $form = array();
                                $form['status'] = 2;
                                $this->customer_model->updateTransfer($data['txHash'], $form);

                                $response = array('status' => false, 'reason' => 'Failed');
                            }
                        } else {
                            $response = array('status' => false, 'reason' => 'Crypto Api Not Responding.', 'data' => $data);
                        }
                    }
                    curl_close($ch);
                } else if ($data['status'] == 1) {
                    $response = array('status' => true, 'message' => 'Successfull');
                } else if ($data['status'] == 2) {
                    $response = array('status' => false, 'reason' => 'Canceled');
                }
            }

        } else {
            $response = array('status' => false, 'reason' => 'No TRX Hash found.');
        }

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }


    function bmxPrice()
    {
        $response = array('status' => true, 'bmxPrice' => $this->customer_model->settingData('bmxPrice')['value']);

        $jsonResponse = json_encode($response);
        $this->output
            ->set_content_type('application/json')
            ->set_output($jsonResponse);
    }

    // function teamListForBinary()
    // {
    //     $postdata = file_get_contents("php://input");
    //     $request = json_decode($postdata);

    //     if (isset($request->sessionId)) {
    //         $data = customerDetailsBySessionId($request->sessionId);
    //         if ($data['status']) {
    //             $userData = $data['data'];
    //             $response = array();
    //             $response['status'] = true;
    //             $response['userData'] = $userData;
    //             $response['teamDetails'] = build_team($userData['un_id']);
    //         } else {
    //             $response = array('status' => false, 'reason' => 'No user found.');
    //         }
    //     } else {
    //         $response = array('status' => false, 'reason' => 'No sessionId found.');
    //     }

    //     $jsonResponse = json_encode($response);
    //     $this->output
    //         ->set_content_type('application/json')
    //         ->set_output($jsonResponse);
    // }

    function send_verify_email($to, $subject, $message)
    {
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'tls://dfmtrade.com',
            'smtp_port' => 465,
            'smtp_user' => 'verify@dfmtrade.com',
            'smtp_pass' => '92cDzzmPxLRC',
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'wordwrap' => TRUE
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
}