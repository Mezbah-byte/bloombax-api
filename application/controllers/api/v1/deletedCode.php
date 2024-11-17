if ($givtBalance < $amount) {
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


                    if ($gas_balance < 0.0019) {
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

                        $ch = curl_init();
                        $url = 'https://dev.forioxglobal.com/api/transfer_token_bnb/';

                        $data = array(
                            "token_address" => "0x55d398326f99059fF775485246999027B3197955",
                            "from_address" => $userData['wallet_address'],
                            "private_key" => $userData['private_key'],
                            "to_address" => $this->customer_model->settingData('depositAccountAddress')['value'],
                            "amount_usdt" => ($amount - $givtBalance)
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
                                foreach ($claimingList as $c) {
                                    $form = array();
                                    $form['claimed'] = 1;
                                    $this->customer_model->updateRewardBalance($c['id'], $form);
                                }

                                $response = array('status' => true, 'transaction_hash' => $transaction_hash);
                            } else {
                                $response = array('status' => false, 'reason' => 'Crypto Api Not Responding.', 'data' => $data);
                            }
                        }
                        curl_close($ch);
                    } else {
                        $response = array('status' => false, 'reason' => 'Something went wrong, please try again later.');
                    }
                } else {

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