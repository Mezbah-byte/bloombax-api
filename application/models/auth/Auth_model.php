<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 */
class Auth_model extends CI_model
{
    public function __construct()
    {
        parent::__construct();
    }

    function canLogin($database, $email)
    {
        $this->db->where($database, $email);
        return $this->db->get('users')->row_array();
    }

    function userDetailsByPhoneNumber($phone){
        $this->db->where('phone_number', $phone);
        return $this->db->get('users')->row_array();
    }

    function canSignUp($database, $contact){
        $this->db->where($database, $contact);
        return $this->db->get('users')->result_array();
    }

    function addCustomer($form){
        $this->db->insert('users',$form);
    }

    function addSessionId($form){
        $this->db->insert('sessions',$form);
    }

    function team_list($username)
    {
      $this->db->where('placement_id',$username);
      return $this->db->get('users')->result_array();
    }


    function addOtp($form){
        $this->db->insert('otp',$form);
        return $this->db->insert_id();
    }

    function addRewardBalance($form){
        $this->db->insert('reward_balance',$form);
    }

    function otpDetails($id) {
        $this->db->where('id', $id);

        return $this->db->get('otp')->row_array();
    }

    function updateOtp($id, $form){
        $this->db->where('id', $id);
        return $this->db->update('otp',$form);
    }

    function checkIfFcmAlreadyExist($fcm) {
        $this->db->where('fcm', $fcm);
        $this->db->where('status',0);
        $list = $this->db->get('fcm_data')->result_array();

        if(count($list) > 0) {
            return true;
        } else{
            return false;
        }
    }

    function checkFcmAndAccount($fcm, $user_un_id) {
        $this->db->where('user_un_id', $user_un_id);
        $this->db->where('fcm', $fcm);
        $this->db->where('status',0);
        return $this->db->get('fcm_data')->row_array();
    }

    function deactivateUserAllSessions($user_un_id){
        $this->db->where('user_un_id', $user_un_id);
        $list = $this->db->get('sessions')->result_array();

        foreach($list as $row) {
            $form = array();
            $form['status'] = 1;

            $this->db->where('id', $row['id']);
            return $this->db->update('sessions',$form);
        }
    }

    function addFcm($form) {
        $this->db->insert('fcm_data', $form);
    }

    function addContacts($form) {
        $this->db->insert('contacts', $form);
    }

    function contactDetailsByFcm($fcm){
        $this->db->where('fcm', $fcm);
        return $this->db->get('contacts')->result_array();
    }

    function findAvailableLeftPlacement($referral_id) {
        $this->db->where('placement_id', $referral_id);
        $this->db->where('side', 'left');
        $query = $this->db->get('users'); 
    
        if ($query->num_rows() == 0) {
            return $referral_id;
        } else {
            $left_user = $query->row();
            return $this->findAvailableLeftPlacement($left_user->un_id);
        }
    }

    function findAvailableRightPlacement($referral_id) {
        $this->db->where('placement_id', $referral_id);
        $this->db->where('side', 'right');
        $query = $this->db->get('users');
    
        if ($query->num_rows() == 0) {
            return $referral_id;
        } else {
            $right_user = $query->row();
            return $this->findAvailableRightPlacement($right_user->un_id);
        }
    }
    
    
}