<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


function customerDetailsBySessionId($sessionId)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->customerDetailsBySessionId($sessionId);
}

function updatePost($un_id, $firstHand, $secondHand)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');
    // $CI->load->helper('communication');
    $userData = $CI->customer_model->customerDetailsByUnId($un_id);

    if ($userData['current_post_id'] == 0) {
        $referList = $CI->customer_model->referList($un_id);

        if (count($referList) >= 2) {
            $newPostId = 10;
            $newPostName = 'Sales Officer';
            $content = 'Congrats, You have been upgraded to ' . $newPostName . '. Thanks for being with us.';
            $profileData = array();
            $profileData['current_post_id'] = $newPostId;
            $profileData['current_post'] = $newPostName;
            $CI->customer_model->updateProfile($un_id, $profileData);

            $notification = array();
            $notification['user_un_id'] = $un_id;
            $notification['title'] = $newPostName;
            $notification['content'] = $content;
            $CI->customer_model->createNotifications($notification);

            $rankAchive = array();
            $rankAchive['user_un_id'] = $un_id;
            $rankAchive['rank_id'] = $newPostId;
            $rankAchive['rank_name'] = $newPostName;
            $rankAchive['amount'] = 0;
            $rankAchive['created_at'] = date('Y-m-d H:i:s');
            $rankAchive['status'] = 1;
            // $rankAchive['expiry_date'] 
            $CI->customer_model->createRankAchive($rankAchive);
        }
    } else if ($userData['current_post_id'] == 10) {
        $referList = $CI->customer_model->referList($un_id);
        $salesOfficerList = array();

        foreach ($referList as $user) {
            if ($user['current_post_id'] > 9) {
                array_push($salesOfficerList, $user);
            }
        }

        if (count($referList) >= 4 && $firstHand >= 6 && $secondHand >= 6) {
            $newPostId = 20;
            $newPostName = 'Team Manager';
            $content = 'Congrats, You have been upgraded to ' . $newPostName . '. Thanks for being with us.';
            $profileData = array();
            $profileData['current_post_id'] = $newPostId;
            $profileData['current_post'] = $newPostName;
            $CI->customer_model->updateProfile($un_id, $profileData);

            $notification = array();
            $notification['user_un_id'] = $un_id;
            $notification['title'] = $newPostName;
            $notification['content'] = $content;
            $CI->customer_model->createNotifications($notification);

            $rankAchive = array();
            $rankAchive['user_un_id'] = $un_id;
            $rankAchive['rank_id'] = $newPostId;
            $rankAchive['rank_name'] = $newPostName;
            $rankAchive['amount'] = 0;
            $rankAchive['created_at'] = date('Y-m-d H:i:s');
            $rankAchive['status'] = 1;
            // $rankAchive['expiry_date'] 
            $CI->customer_model->createRankAchive($rankAchive);
        }
    } else if ($userData['current_post_id'] == 20) {
        $referList = $CI->customer_model->referList($un_id);
        $teamManagerList = array();

        foreach ($referList as $user) {
            if ($user['current_post_id'] > 19) {
                array_push($teamManagerList, $user);
            }
        }

        if (count($referList) >= 6 && $firstHand >= 128 && $secondHand >= 128) {
            $newPostId = 30;
            $newPostName = 'Regional Manager';
            $content = 'Congrats, You have been upgraded to ' . $newPostName . '. Thanks for being with us.';
            $profileData = array();
            $profileData['current_post_id'] = $newPostId;
            $profileData['current_post'] = $newPostName;
            $CI->customer_model->updateProfile($un_id, $profileData);

            $notification = array();
            $notification['user_un_id'] = $un_id;
            $notification['title'] = $newPostName;
            $notification['content'] = $content;
            $CI->customer_model->createNotifications($notification);

            $rankAchive = array();
            $rankAchive['user_un_id'] = $un_id;
            $rankAchive['rank_id'] = $newPostId;
            $rankAchive['rank_name'] = $newPostName;
            $rankAchive['amount'] = 5100;
            $rankAchive['created_at'] = date('Y-m-d H:i:s');
            $rankAchive['status'] = 1;
            // $rankAchive['expiry_date'] 
            $CI->customer_model->createRankAchive($rankAchive);
        }
    } else if ($userData['current_post_id'] == 30) {
        $referList = $CI->customer_model->referList($un_id);
        $regionalManagerList = array();

        foreach ($referList as $user) {
            if ($user['current_post_id'] > 29) {
                array_push($regionalManagerList, $user);
            }
        }

        if (count($referList) >= 8 && $firstHand >= 512 && $secondHand >= 512) {
            $newPostId = 33;
            $newPostName = 'Manager';
            $content = 'Congrats, You have been upgraded to ' . $newPostName . '. Thanks for being with us.';
            $profileData = array();
            $profileData['current_post_id'] = $newPostId;
            $profileData['current_post'] = $newPostName;
            $CI->customer_model->updateProfile($un_id, $profileData);

            $notification = array();
            $notification['user_un_id'] = $un_id;
            $notification['title'] = $newPostName;
            $notification['content'] = $content;
            $CI->customer_model->createNotifications($notification);

            $rankAchive = array();
            $rankAchive['user_un_id'] = $un_id;
            $rankAchive['rank_id'] = $newPostId;
            $rankAchive['rank_name'] = $newPostName;
            $rankAchive['amount'] = 16500;
            $rankAchive['created_at'] = date('Y-m-d H:i:s');
            $rankAchive['status'] = 1;
            // $rankAchive['expiry_date'] 
            $CI->customer_model->createRankAchive($rankAchive);
        }
    } else if ($userData['current_post_id'] == 33) {
        $referList = $CI->customer_model->referList($un_id);
        $regionalManagerList = array();

        foreach ($referList as $user) {
            if ($user['current_post_id'] > 32) {
                array_push($regionalManagerList, $user);
            }
        }

        if (count($referList) >= 10 && $firstHand >= 2048 && $secondHand >= 2048) {
            $newPostId = 36;
            $newPostName = 'Deputy General Manager';
            $content = 'Congrats, You have been upgraded to ' . $newPostName . '. Thanks for being with us.';
            $profileData = array();
            $profileData['current_post_id'] = $newPostId;
            $profileData['current_post'] = $newPostName;
            $CI->customer_model->updateProfile($un_id, $profileData);

            $notification = array();
            $notification['user_un_id'] = $un_id;
            $notification['title'] = $newPostName;
            $notification['content'] = $content;
            $CI->customer_model->createNotifications($notification);

            $rankAchive = array();
            $rankAchive['user_un_id'] = $un_id;
            $rankAchive['rank_id'] = $newPostId;
            $rankAchive['rank_name'] = $newPostName;
            $rankAchive['amount'] = 32200;
            $rankAchive['created_at'] = date('Y-m-d H:i:s');
            $rankAchive['status'] = 1;
            // $rankAchive['expiry_date'] 
            $CI->customer_model->createRankAchive($rankAchive);
        }
    } else if ($userData['current_post_id'] == 36) {
        $referList = $CI->customer_model->referList($un_id);
        $regionalManagerList = array();

        foreach ($referList as $user) {
            if ($user['current_post_id'] > 35) {
                array_push($regionalManagerList, $user);
            }
        }

        if (count($referList) >= 12 && $firstHand >= 8192 && $secondHand >= 8192) {
            $newPostId = 40;
            $newPostName = 'General Manager';
            $content = 'Congrats, You have been upgraded to ' . $newPostName . '. Thanks for being with us.';
            $profileData = array();
            $profileData['current_post_id'] = $newPostId;
            $profileData['current_post'] = $newPostName;
            $CI->customer_model->updateProfile($un_id, $profileData);

            $notification = array();
            $notification['user_un_id'] = $un_id;
            $notification['title'] = $newPostName;
            $notification['content'] = $content;
            $CI->customer_model->createNotifications($notification);

            $rankAchive = array();
            $rankAchive['user_un_id'] = $un_id;
            $rankAchive['rank_id'] = $newPostId;
            $rankAchive['rank_name'] = $newPostName;
            $rankAchive['amount'] = 65400;
            $rankAchive['created_at'] = date('Y-m-d H:i:s');
            $rankAchive['status'] = 1;
            // $rankAchive['expiry_date'] 
            $CI->customer_model->createRankAchive($rankAchive);
        }
    } else if ($userData['current_post_id'] == 40) {
        $referList = $CI->customer_model->referList($un_id);
        $globalManagerList = array();

        foreach ($referList as $user) {
            if ($user['current_post_id'] > 39) {
                array_push($globalManagerList, $user);
            }
        }

        if (count($referList) >= 15 && $firstHand >= 20480 && $secondHand >= 20480) {
            $newPostId = 50;
            $newPostName = 'Executive Director';
            $content = 'Congrats, You have been upgraded to ' . $newPostName . '. Thanks for being with us.';
            $profileData = array();
            $profileData['current_post_id'] = $newPostId;
            $profileData['current_post'] = $newPostName;
            $CI->customer_model->updateProfile($un_id, $profileData);

            $notification = array();
            $notification['user_un_id'] = $un_id;
            $notification['title'] = $newPostName;
            $notification['content'] = $content;
            $CI->customer_model->createNotifications($notification);

            $rankAchive = array();
            $rankAchive['user_un_id'] = $un_id;
            $rankAchive['rank_id'] = $newPostId;
            $rankAchive['rank_name'] = $newPostName;
            $rankAchive['amount'] = 155300;
            $rankAchive['created_at'] = date('Y-m-d H:i:s');
            $rankAchive['status'] = 1;
            // $rankAchive['expiry_date'] 
            $CI->customer_model->createRankAchive($rankAchive);
        }
    }

    return true;
}


function nextSteps($un_id, $firstHand, $secondHand)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');
    // $CI->load->helper('communication');
    $userData = $CI->customer_model->customerDetailsByUnId($un_id);

    $newPostName = '';
    $totalSteps = 0;
    $completedSteps = 0;

    if ($userData['current_post_id'] == 0) {
        $newPostName = 'Sales Officer';
        $referList = $CI->customer_model->referList($un_id);
        $totalSteps = 2;
        $completedSteps = count($referList);
    } else if ($userData['current_post_id'] == 10) {
        $newPostName = 'Team Manager';
        $referList = $CI->customer_model->referList($un_id);
        $totalSteps = 2;

        if (count($referList) >= 5) {
            $completedSteps = $completedSteps + 1;
        }

        if (count(get_network($userData['un_id'])) > 19) {
            $completedSteps = $completedSteps + 1;
        }

    } else if ($userData['current_post_id'] == 20) {
        $newPostName = 'Regional Manager';
        $referList = $CI->customer_model->referList($un_id);
        $totalSteps = 3;

        if (count($referList) >= 10) {
            $completedSteps = $completedSteps + 1;
        }

        if ($firstHand >= 128) {
            $completedSteps = $completedSteps + 1;
        }

        if ($secondHand >= 128) {
            $completedSteps = $completedSteps + 1;
        }

    } else if ($userData['current_post_id'] == 30) {
        $newPostName = 'Manager';
        $referList = $CI->customer_model->referList($un_id);
        $totalSteps = 3;

        if (count($referList) >= 15) {
            $completedSteps = $completedSteps + 1;
        }
        if ($firstHand >= 512) {
            $completedSteps = $completedSteps + 1;
        }
        if ($secondHand >= 512) {
            $completedSteps = $completedSteps + 1;
        }

    } else if ($userData['current_post_id'] == 33) {
        $newPostName = 'Deputy General Manager';
        $referList = $CI->customer_model->referList($un_id);
        $totalSteps = 3;

        if (count($referList) >= 20) {
            $completedSteps = $completedSteps + 1;
        }
        if ($firstHand >= 2048) {
            $completedSteps = $completedSteps + 1;
        }
        if ($secondHand >= 2048) {
            $completedSteps = $completedSteps + 1;
        }

    } else if ($userData['current_post_id'] == 36) {
        $newPostName = 'Global Manager';
        $referList = $CI->customer_model->referList($un_id);
        $totalSteps = 3;

        if (count($referList) >= 25) {
            $completedSteps = $completedSteps + 1;
        }
        if ($firstHand >= 8192) {
            $completedSteps = $completedSteps + 1;
        }
        if ($secondHand >= 8192) {
            $completedSteps = $completedSteps + 1;
        }

    } else if ($userData['current_post_id'] == 40) {
        $newPostName = 'Executive Director';
        $referList = $CI->customer_model->referList($un_id);
        $totalSteps = 3;

        if (count($referList) >= 30) {
            $completedSteps = $completedSteps + 1;
        }
        if ($firstHand >= 20480) {
            $completedSteps = $completedSteps + 1;
        }
        if ($secondHand >= 20480) {
            $completedSteps = $completedSteps + 1;
        }
    }

    return array('newPostName' => $newPostName, 'totalSteps' => $totalSteps, 'completedSteps' => $completedSteps);
}

function customerDetailsByUnId($sessionId)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->customerDetailsByUnId($sessionId);
}

function customerDetailsByUsername($username)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->customerDetailsByUsername($username);
}


function cryptoTransferList($un_id, $type)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->cryptoTransferList($type, $un_id);
}


function bonusList($un_id, $type)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    if ($type == 'roi') {
        $data = $CI->customer_model->roiList($un_id);
    } else if ($type == 'direct') {
        $dataa = $CI->customer_model->directList($un_id);

        $data = array();

        foreach($dataa as $d){
            $fromUserDetails = $CI->customer_model->customerDetailsByUnId($d['from_user']);
            $d['fromUsername'] = $fromUserDetails['username']; 

            $myPackageDetails = $CI->customer_model->myPackageDetails($d['package_id']);
            $d['purchaseAmount'] = $myPackageDetails['amount'];

            array_push($data, $d);
        }
    } else if ($type == 'team') {
        $data = $CI->customer_model->teamList($un_id);
    } else if ($type == 'matching') {
        $data = $CI->customer_model->matchingList($un_id);
    }  else if ($type == 'leadership') {
        $data = $CI->customer_model->leadershipBonusList($un_id);
    }

    return $data;
}


function rewardBonusList($un_id, $type)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    if ($type == 'monthly') {
        $data = $CI->customer_model->monthlyRewardBonusList($un_id);
    } else if ($type == 'rank') {
        $data = $CI->customer_model->rankRewardBonusList($un_id);
    }

    return $data;
}

function updateProfile($un_id, $form)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $CI->customer_model->updateProfile($un_id, $form);
}


function get_teamDetails($un_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $members = $CI->customer_model->team_list($un_id);
    $finalArray = array();
    foreach ($members as $member) {
        $network = get_network($member['un_id']);
        $member['teamMemberCount'] = count($network);

        $mtl = $CI->customer_model->team_list($member['un_id']);

        $thisArray = array();
        foreach ($mtl as $mt) {
            $network = get_network($mt['un_id']);
            $mt['teamMemberCount'] = count($network);
            array_push($thisArray, $mt);
        }
        $member['details'] = $thisArray;

        array_push($finalArray, $member);
    }

    return $finalArray;
}

function get_network($un_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $network = array();
    $members = $CI->customer_model->team_list($un_id);
    foreach ($members as $member) {
        $network[] = $member;
        $network = array_merge($network, get_network($member['un_id']));
    }
    return $network;
}

function getUsdtBalanceBNB($wallet_address)
{
    $tokenbalance = 0.00;

    $ch = curl_init();
    // $url = 'https://dev.forioxglobal.com/api/get-token-balance/0xc2132d05d31c914a87c6611c10748aeb04b58e8f/' . $userData['wallet_address'] . '/';
    $url = 'https://dev.forioxglobal.com/api/get_usdt_balance_bnb/' . $wallet_address . '/';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'cURL Error: ' . curl_error($ch);
    } else {
        $data = json_decode($response, true);
        if (isset($data['usdt_balance'])) {
            $tokenbalance = $data['usdt_balance']/1000000000000;
        } else {
            $response = array('status' => false, 'reason' => 'Crypto Api Not Responding.');
        }
    }
    curl_close($ch);
    return $tokenbalance;
}

function teamTotalRoiToday($un_id) {
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $network = get_network($un_id);
    array_push($network,$CI->customer_model->customerDetailsByUnId($un_id));

    $total = 0;

    foreach($network as $n){
        $amount = $CI->customer_model->get_total_specific_bonus_total_today('roi_bonus',$n['un_id']);
        $total = $total+$amount;
    }

    return $total;
}


function teamTotalBusiness($un_id) {
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $network = get_network($un_id);
    array_push($network,$CI->customer_model->customerDetailsByUnId($un_id));

    $total = 0;

    foreach($network as $n){
        $amount = $CI->customer_model->get_total_package_amount_by_user($n['un_id']);
        $total = $total+$amount;
    }

    return $total;
}


function teamTotalBusinessYestwrday($un_id) {
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $network = get_network($un_id);
    array_push($network, $CI->customer_model->customerDetailsByUnId($un_id));

    $total = 0;

    foreach ($network as $n) {
        $amount = $CI->customer_model->get_total_package_amount_yesterday_by_user($n['un_id']);
        $total += $amount;
    }

    return $total;
}

function teamTotalBusinessThisMonth($un_id) {
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $network = get_network($un_id);
    array_push($network, $CI->customer_model->customerDetailsByUnId($un_id));

    $total = 0;

    foreach ($network as $n) {
        $amount = $CI->customer_model->get_total_package_amount_this_month_by_user($n['un_id']);
        $total += $amount;
    }

    return $total;
}

























function referList($un_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->referList($un_id);
}

function cartList($un_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->cartList($un_id);
}

function checkIfProductExistInCart($un_id, $product_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->checkIfProductExistInCart($un_id, $product_id);
}

function addToCart($form)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $CI->customer_model->addToCart($form);
}

function updateCartItem($id, $form)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $CI->customer_model->updateCartItem($id, $form);
}

function incomeList($type, $un_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    if ($type == "monthlySalary") {
        $data = $CI->customer_model->monthSalaryIncomeList($un_id);
    } else {
        $data = $CI->customer_model->incomeList($type, $un_id);
    }

    $finalData = array();
    foreach ($data as $d) {
        if ($d['from_user'] != "") {
            $thisUserData = customerDetailsByUnId($d['from_user']);
            $d['name'] = $thisUserData['first_name'];
            $d['img'] = $thisUserData['img'];
        } else {
            $d['name'] = "";
            $d['img'] = "";
        }
        array_push($finalData, $d);
    }
    return $finalData;
}

function incomeTotalSum($type, $un_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $amount = $CI->customer_model->incomeTotalSum($type, $un_id);

    if ($amount == null) {
        $amount = '0';
    }

    return $amount;
}

function withdrawList($type, $un_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->withdrawList($type, $un_id);
}

function withdrawTotalSum($type, $un_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $amount = $CI->customer_model->withdrawTotalSum($type, $un_id);

    if ($amount == null) {
        $amount = '0';
    }

    return $amount;
}

function teamListForBinary($un_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $data = array();
    $teamList = $CI->customer_model->team_list($un_id);

    foreach ($teamList as $team) {
        $team['team'] = $CI->customer_model->team_list($team['un_id']);

        array_push($data, $team);
    }

    return $data;
}

function build_team($placement_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');
    $team = $CI->customer_model->get_team_by_placement_id($placement_id);

    foreach ($team as &$member) {
        $member['total_package_amount_left'] = calculate_side_package_amount($member['un_id'], 'left');
        $member['total_package_amount_right'] = calculate_side_package_amount($member['un_id'], 'right');
        $member['total_this_month_package_amount_left'] = calculate_side_package_amount($member['un_id'], 'left', true);
        $member['total_this_month_package_amount_right'] = calculate_side_package_amount($member['un_id'], 'right', true);
        $member['totalBusinessVolume'] = calculate_total_package_amount($member['un_id']);
        $member['details'] = build_team($member['un_id']);
    }

    return $team;
}


function calculate_total_package_amount($placement_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');
    $totalAmount = $CI->customer_model->get_total_package_amount_by_user($placement_id);

    // Get the team members under this user
    $team = $CI->customer_model->get_team_by_placement_id($placement_id);

    // Recursively add the package amounts for all team members
    foreach ($team as $member) {
        $totalAmount += calculate_total_package_amount($member['un_id']);
    }

    return $totalAmount;
}

function get_network_by_level($un_id, $level = 0)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $network = array();
    $members = $CI->customer_model->team_list($un_id);

    foreach ($members as $member) {
        $member['level'] = $level;
        $network[] = $member;
        $sub_network = get_network_by_level($member['un_id'], $level + 1);
        if (!empty($sub_network)) {
            $network = array_merge($network, $sub_network);
        }
    }

    $levels = array_column($network, 'level');
    array_multisort($levels, SORT_ASC, $network);

    return $network;
}

function calculate_side_package_amount($placement_id, $side, $current_month = false)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');
    $team = $CI->customer_model->get_team_by_placement_id_and_side($placement_id, $side);
    $totalAmount = 0;

    foreach ($team as $member) {
        if ($current_month) {
            $totalAmount += $CI->customer_model->get_total_package_amount_by_user_for_current_month($member['un_id']);
        } else {
            $totalAmount += $CI->customer_model->get_total_package_amount_by_user($member['un_id']);
        }

        // Recursively add the package amounts for all team members on the same side
        $totalAmount += calculate_side_package_amount($member['un_id'], $side, $current_month);
    }

    return $totalAmount;
}




function premuimPackages()
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->premuimPackages();
}

function premuimPackageDetails($id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->premuimPackageDetails($id);
}

function premuimPackageBindings($id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->premuimPackageBindings($id);
}

function buyPackageRequest($form)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->buyPackageRequest($form);
}


function addAddress($form)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->addAddress($form);
}

function updateAddress($id, $form)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->updateAddress($id, $form);
}


function addressList($un_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->addressList($un_id);
}

function createOrder($form)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->createOrder($form);
}

function createOrderItem($form)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->createOrderItem($form);
}

function getOrdersByStatus($un_id, $type)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');
    if ($type == 'unPaid') {
        return $CI->customer_model->unPaidOrders($un_id);
    } else if ($type == 'processing') {
        return $CI->customer_model->getOrdersByStatus($un_id, 10);
    } else if ($type == 'shipped') {
        return $CI->customer_model->getOrdersByStatus($un_id, 20);
    } else if ($type == 'delivered') {
        return $CI->customer_model->getOrdersByStatus($un_id, 30);
    } else if ($type == 'placed') {
        return $CI->customer_model->getOrdersByStatus($un_id, 0);
    }
}

function orderDetails($orderId)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->orderDetails($orderId);
}


function orderItems($orderId)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    return $CI->customer_model->orderItems($orderId);
}

function generateUniqueFilename($filename)
{
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $timestamp = time();
    return $timestamp . '.' . $extension;
}


function get_salaryOverview($un_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $members = $CI->customer_model->team_list($un_id);
    $finalArray = array();
    foreach ($members as $member) {
        $network = get_network($member['un_id']);
        $member['teamMemberCount'] = count($network) + 1;
        $mtl = $CI->customer_model->team_list($member['un_id']);

        $thisTeam = array();
        foreach ($mtl as $m) {
            $network = get_network($m['un_id']);
            $m['teamMemberCount'] = count($network) + 1;

            array_push($thisTeam, $m);
        }

        $member['team'] = $thisTeam;

        array_push($finalArray, $member);
    }

    return $finalArray;
}


function salaryOverview($un_id)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $user = $CI->customer_model->customerDetailsByUnId($un_id);
    $network = get_network($un_id);
    $getSalary = false;
    $salaryBase = 0;
    $salary = 0;
    if (count($network) > 2 && count($network) < 4) {
        $salaryBase = 2;
        $getSalary = true;
        $salary = 8;
    } else if (count($network) > 4 && count($network) < 8) {
        $salaryBase = 4;
        $getSalary = true;
        $salary = 16;
    } else if (count($network) > 8 && count($network) < 16) {
        $salaryBase = 8;
        $getSalary = true;
        $salary = 32;
    } else if (count($network) > 16 && count($network) < 32) {
        $salaryBase = 16;
        $getSalary = true;
        $salary = 64;
    } else if (count($network) > 32 && count($network) < 64) {
        $salaryBase = 32;
        $getSalary = true;
        $salary = 128;
    } else if (count($network) > 64 && count($network) < 128) {
        $salaryBase = 64;
        $getSalary = true;
        $salary = 256;
    } else if (count($network) > 128 && count($network) < 256) {
        $salaryBase = 128;
        $getSalary = true;
        $salary = 512;
    } else if (count($network) > 256 && count($network) < 512) {
        $salaryBase = 256;
        $getSalary = true;
        $salary = 1024;
    } else if (count($network) > 512 && count($network) < 1024) {
        $salaryBase = 512;
        $getSalary = true;
        $salary = 2048;
    } else if (count($network) > 1024 && count($network) < 2048) {
        $salaryBase = 1024;
        $getSalary = true;
        $salary = 4096;
    } else if (count($network) > 2048 && count($network) < 4096) {
        $salaryBase = 2048;
        $getSalary = true;
        $salary = 8192;
    } else if (count($network) > 4096 && count($network) < 8196) {
        $salaryBase = 4096;
        $getSalary = true;
        $salary = 16384;
    } else if (count($network) >= 8196 && count($network) < 16384) {
        $getSalary = true;
        $salary = 32768;
        $salaryBase = 8196;
    } else if (count($network) >= 16384 && count($network) < 32768) {
        $getSalary = true;
        if ($user['current_post_id'] > 39) {
            $salary = 65536;
        } else {
            $salary = 32768;
        }
        $salaryBase = 16384;
    } else if (count($network) >= 32768) {
        $getSalary = true;
        if ($user['current_post_id'] > 49) {
            $salary = 131072;
        } elseif ($user['current_post_id'] > 39) {
            $salary = 65536;
        } else {
            $salary = 32768;
        }
        $salaryBase = 32768;
    }

    return array('networkBase' => $salaryBase, 'salary' => $salary);
}


function addressDetails($un_id, $addressId)
{
    $CI = &get_instance();
    $CI->load->model('api/customer_model');

    $addressDetails = $CI->customer_model->addressDetails($addressId);

    if ($un_id == $addressDetails['user_un_id']) {
        return $addressDetails;
    } else {
        return null;
    }
}








