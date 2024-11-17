<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


function is_valid_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_valid_phone($phone)
{
    $phone = preg_replace("/[^0-9]/", "", $phone);
    return strlen($phone) === 11;
}

function login($contact, $password)
{
    $CI = &get_instance();
    $CI->load->model('auth/auth_model');

    $result = array();

    if (is_valid_email($contact)) {
        $database = 'email';
    } else if (is_valid_phone($contact)) {
        $database = 'phone_number';
    } else {
        // $result['status'] = false;
        // $result['reason'] = 'Invalid contact address!';
        // return $result;
        $database = 'username';
    }

    // $database = 'username';

    if ($CI->auth_model->canLogin($database, $contact) == null) {
        $result['status'] = false;
        $result['reason'] = 'No account found, sign up instead.';
        return $result;
    } else {
        $userDetails = $CI->auth_model->canLogin($database, $contact);

        // if ($userDetails[$database] == $contact && $userDetails['password'] == $password) {
            if ($userDetails['password'] == $password) {
                $CI->auth_model->deactivateUserAllSessions($userDetails['un_id']);
                $sessionId = md5(uniqid());
                $result['status'] = true;
                $result['sessionId'] = $sessionId;
                $result['data'] = $userDetails;
                addSessionId($userDetails['un_id'], $sessionId);

                return $result;
        } else {
            $result['status'] = false;
            $result['reason'] = 'Invalid credintials!';
            return $result;
        }
    }
}

function canSignUp($email, $phone, $usernsme)
{
    $CI = &get_instance();
    $CI->load->model('auth/auth_model');

    $result = array();

    if (count($CI->auth_model->canSignUp('email', $email)) > 0) {
        $result['status'] = false;
        $result['reason'] = 'Email already registered!';
        return $result;
    } else if (count($CI->auth_model->canSignUp('phone_number', $phone)) > 0) {
        $result['status'] = false;
        $result['reason'] = 'Phone number already registered!';
        return $result;
    } else if (count($CI->auth_model->canSignUp('username', $usernsme)) > 0) {
        $result['status'] = false;
        $result['reason'] = 'Username already registered!';
        return $result;
    } else {
        $result['status'] = true;
        return $result;
    }
}

function addSessionId($user_un_id, $sessionId)
{
    $CI = &get_instance();
    $CI->load->model('auth/auth_model');

    $form = array();
    $form['sessionId'] = $sessionId;
    $form['user_un_id'] = $user_un_id;
    $form['status'] = 1;
    $form['created_at'] = date('Y-m-d H:i:s');

    $CI->auth_model->addSessionId($form);

    return true;
}

function userDetailsByPhoneNumber($phone) {
    $CI = &get_instance();
    $CI->load->model('auth/auth_model');

    return $CI->auth_model->userDetailsByPhoneNumber($phone);
}


