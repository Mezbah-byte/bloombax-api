<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 *
 */
class Customer_model extends CI_model
{
    public function __construct()
    {
        parent::__construct();
    }

    function customerDetailsBySessionId($sessionId)
    {
        $this->db->where('sessionId', $sessionId);
        $this->db->where('status', 1);
        $data = $this->db->get('sessions')->row_array();

        if ($data == null) {
            $d = array();
            $d['status'] = false;

            return $d;
        } else {
            $d = array();
            $d['status'] = true;

            $this->db->where('un_id', $data['user_un_id']);
            $d['data'] = $this->db->get('users')->row_array();

            return $d;
        }
    }

    function cryptoTransferList($type, $un_id)
    {
        $this->db->where('from_user', $un_id);
        $this->db->where('type', $type);
        return $this->db->get('cryptotransfer')->result_array();
    }

    function customerDetailsByUnId($customerId)
    {
        $this->db->where('un_id', $customerId);
        return $this->db->get('users')->row_array();
    }

    function customerDetailsByUsername($username)
    {
        $this->db->where('username', $username);
        return $this->db->get('users')->row_array();
    }

    function roiList($un_id)
    {
        $this->db->where('to_user', $un_id);
        return $this->db->get('roi_bonus')->result_array();
    }

    function directList($un_id)
    {
        $this->db->where('to_user', $un_id);
        return $this->db->get('direct_bonus')->result_array();
    }

    function teamList($un_id)
    {
        $this->db->where('to_user', $un_id);
        return $this->db->get('team_bonus')->result_array();
    }

    function matchingList($un_id)
    {
        $this->db->where('to_user', $un_id);
        return $this->db->get('matching_bonus')->result_array();
    }

    function leadershipBonusList($un_id)
    {
        $this->db->where('to_user', $un_id);
        return $this->db->get('leadershipBonus')->result_array();
    }

    function monthlyRewardBonusList($un_id)
    {
        $this->db->where('to_user', $un_id);
        return $this->db->get('monthly_reward_bonus')->result_array();
    }

    function rankRewardBonusList($un_id)
    {
        $this->db->where('to_user', $un_id);
        return $this->db->get('rank_reward_bonus')->result_array();
    }

    function updateProfile($un_id, $form)
    {
        $this->db->where('un_id', $un_id);
        $this->db->update('users', $form);
    }

    function packages()
    {
        $this->db->where('status', 1);
        return $this->db->get('packages')->result_array();
    }

    function userPackageList($un_id)
    {
        $this->db->where('user_un_id', $un_id);
        // $this->db->where('status', 1);
        $this->db->where_in('status', [1, 3]);
        return $this->db->get('my_packages')->result_array();
    }

    function userPackageListByOrder($un_id)
    {
        $this->db->where('user_un_id', $un_id);
        $this->db->order_by('amount', 'DESC');
        $this->db->where_in('status', [1, 3]);
        return $this->db->get('my_packages')->result_array();
    }

    function userPackageListByStatus($un_id, $type)
    {
        $this->db->where('user_un_id', $un_id);
        $this->db->where('status', $type);
        // $this->db->where_in('status', [1, 3]);
        return $this->db->get('my_packages')->result_array();
    }

    function checkPackageByAmount($amount)
    {
        $this->db->where('min_price <=', $amount);
        $this->db->where('max_price >=', $amount);
        return $this->db->get('packages')->row_array();
    }

    function addOtp($form)
    {
        $this->db->insert('otp', $form);
        return $this->db->insert_id();
    }

    function otpDetails($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('otp')->row_array();
    }

    function updateOtp($id, $form)
    {
        $this->db->where('id', $id);
        return $this->db->update('otp', $form);
    }

    function settingData($key)
    {
        $this->db->where('key', $key);
        return $this->db->get('setting')->row_array();
    }

    function referList($un_id)
    {
        $this->db->where('refered_by', $un_id);
        return $this->db->get('users')->result_array();
    }

    function team_list($username)
    {
        $this->db->where('placement_id', $username);
        return $this->db->get('users')->result_array();
    }

    public function get_team_by_placement_id($placement_id)
    {
        return $this->db->where('placement_id', $placement_id)
            ->order_by('side', 'ASC')
            ->get('users')
            ->result_array();
    }

    public function get_total_package_amount_by_user($un_id)
    {
        $this->db->select_sum('amount');
        $this->db->where('user_un_id', $un_id);
        $this->db->where('nonRoi', 0);
        $query = $this->db->get('my_packages');

        if ($query->num_rows() > 0) {
            return $query->row()->amount;
        } else {
            return 0;
        }
    }

    public function get_total_package_amount_by_user_all_package($un_id)
    {
        $this->db->select_sum('amount');
        $this->db->where('user_un_id', $un_id);
        $query = $this->db->get('my_packages');

        if ($query->num_rows() > 0) {
            return $query->row()->amount;
        } else {
            return 0;
        }
    }

    public function get_total_package_amount_yesterday_by_user($un_id)
    {
        $this->db->select_sum('amount');
        $this->db->where('user_un_id', $un_id);
        $this->db->where('nonRoi', 0);

        // Filter by yesterday's date
        $this->db->where('DATE(created_at)', date('Y-m-d', strtotime('-1 day')));

        $query = $this->db->get('my_packages');

        if ($query->num_rows() > 0) {
            return $query->row()->amount;
        } else {
            return 0;
        }
    }

    public function get_total_package_amount_this_month_by_user($un_id)
    {
        $this->db->select_sum('amount');
        $this->db->where('user_un_id', $un_id);
        $this->db->where('nonRoi', 0);

        // Filter by the current month
        $this->db->where('MONTH(created_at)', date('m'));
        $this->db->where('YEAR(created_at)', date('Y'));

        $query = $this->db->get('my_packages');

        if ($query->num_rows() > 0) {
            return $query->row()->amount;
        } else {
            return 0;
        }
    }


    public function get_total_package_amount_by_user_for_current_month($un_id)
    {
        $this->db->select_sum('amount');
        $this->db->where('user_un_id', $un_id);
        $this->db->where('nonRoi', 0);
        $this->db->where('DATE_FORMAT(created_at, "%Y-%m") =', date('Y-m'));
        $query = $this->db->get('my_packages');

        if ($query->num_rows() > 0) {
            return $query->row()->amount;
        }

        return 0;
    }

    public function get_team_by_placement_id_and_side($placement_id, $side)
    {
        $this->db->where('placement_id', $placement_id);
        $this->db->where('side', $side);
        $query = $this->db->get('users');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return [];
    }



    public function get_total_specific_bonus_total($database, $un_id)
    {
        $this->db->select_sum('amount');
        $this->db->where('to_user', $un_id);
        $query = $this->db->get($database);
        if ($query->num_rows() > 0) {
            return $query->row()->amount;
        }
        return 0;
    }

    public function get_total_specific_bonus_total_today($database, $un_id)
    {
        $this->db->select_sum('amount');
        $this->db->where('to_user', $un_id);

        $this->db->where('DATE(created_at)', date('Y-m-d'));

        $query = $this->db->get($database);
        if ($query->num_rows() > 0) {
            return $query->row()->amount;
        }
        return 0;
    }

    function addCryptoTransfer($form)
    {
        $this->db->insert('cryptotransfer', $form);
    }

    function transferDetailsByTrx($trx)
    {
        $this->db->where('txHash', $trx);
        return $this->db->get('cryptotransfer')->row_array();
    }

    function updateTransfer($trx, $form)
    {
        $this->db->where('txHash', $trx);
        return $this->db->update('cryptotransfer', $form);
    }

    function addMyPackage($form)
    {
        $this->db->insert('my_packages', $form);
        return $this->db->insert_id();
    }

    function myPackageDetails($id) {
        $this->db->where('id', $id);
        return $this->db->get('my_packages')->row_array();
    }


    function addDirectBonus($form)
    {
        $this->db->insert('direct_bonus', $form);
    }




    function getRewardBalanceList($un_id)
    {
        $this->db->where('to_user', $un_id);
        $this->db->where('claimed', 0);
        return $this->db->get('reward_balance')->result_array();
    }

    function updateRewardBalance($id, $form)
    {
        $this->db->where('id', $id);
        $this->db->update('reward_balance', $form);
    }

    function getRewardBalanceTotal($un_id)
    {
        $this->db->where('to_user', $un_id);
        $this->db->where('claimed', 0);
        $query = $this->db->select_sum('amount')
            ->from('reward_balance')
            ->get();
        return $query->row()->amount;
    }

    function getDepositTotal($un_id)
    {
        $this->db->where('from_user', $un_id);
        $this->db->where('type', 'in');
        $this->db->where('status', 1);
        $query = $this->db->select_sum('amount')
            ->from('cryptotransfer')
            ->get();
        return $query->row()->amount;
    }


    function getWithdrawTotal($un_id)
    {
        $this->db->where('from_user', $un_id);
        $this->db->where('type', 'out');
        $this->db->where('status', 1);
        $query = $this->db->select_sum('amount')
            ->from('cryptotransfer')
            ->get();
        return $query->row()->amount;
    }

    function aPlacedUserDataBySide($un_id, $side)
    {
        $this->db->where('side', $side);
        $this->db->where('placement_id', $un_id);
        return $this->db->get('users')->row_array();
    }


























    function cartList($un_id)
    {
        $this->db->where('user_un_id', $un_id);
        $this->db->where('status', 1);
        return $this->db->get('cart')->result_array();
    }

    function addToCart($form)
    {
        $this->db->insert('cart', $form);
    }

    function cartItemDetails($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('cart')->row_array();
    }

    function checkIfProductExistInCart($un_id, $product_id)
    {
        $this->db->where('user_un_id', $un_id);
        $this->db->where('product_un_id', $product_id);
        $this->db->where('status', 1);

        $data = $this->db->get('cart')->row_array();

        if ($data == null) {
            $response = array();
            $response['status'] = false;
        } else {
            $response = array();
            $response['status'] = true;
            $response['data'] = $data;
        }

        return $response;
    }

    function updateCartItem($id, $form)
    {
        $this->db->where('id', $id);
        $this->db->update('cart', $form);
    }

    function incomeList($type, $un_id)
    {
        if ($type == 'affiliate') {
            $this->db->where('to_user', $un_id);
            $this->db->order_by('created_at', 'DESC'); // Change 'id' to your timestamp field if necessary
            return $this->db->get('income')->result_array();
        } else if ($type == 'monthlySalary') {
            $this->db->where('to_user', $un_id);
            $this->db->order_by('id', 'DESC'); // Change 'id' to your timestamp field if necessary
            return $this->db->get('salary_income')->result_array();
        } else if ($type == 'dailySales') {
            $this->db->where('to_user', $un_id);
            $this->db->order_by('created_at', 'DESC'); // Change 'id' to your timestamp field if necessary
            return $this->db->get('daily_sales_income')->result_array();
        } else if ($type == 'status') {
            $this->db->where('to_user', $un_id);
            $this->db->order_by('id', 'DESC'); // Change 'id' to your timestamp field if necessary
            return $this->db->get('status_income')->result_array();
        } else {
            if ($type != 'all') {
                $this->db->where('source', $type);
            }
            $this->db->where('to_user', $un_id);
            $this->db->order_by('id', 'DESC'); // Change 'id' to your timestamp field if necessary
            return $this->db->get('income')->result_array();
        }

        // if ($type != 'all') {
        //     $this->db->where('source', $type);
        // }
        // $this->db->where('to_user', $un_id);
        // return $this->db->get('income')->result_array();
    }

    function monthSalaryIncomeList($un_id)
    {
        $this->db->where('to_user', $un_id);
        return $this->db->get('salary_income')->result_array();
    }

    function incomeTotalSum($type, $un_id)
    {
        if ($type == 'affiliate') {
            $this->db->where('to_user', $un_id);
            $query = $this->db->select_sum('amount')
                ->from('income')
                ->get();
            return $query->row()->amount;
        } else if ($type == 'monthlySalary') {
            $this->db->where('to_user', $un_id);
            $query = $this->db->select_sum('amount')
                ->from('salary_income')
                ->get();
            return $query->row()->amount;
        } else if ($type == 'dailySales') {
            $this->db->where('to_user', $un_id);
            $query = $this->db->select_sum('amount')
                ->from('daily_sales_income')
                ->get();
            return $query->row()->amount;
        } else if ($type == 'status') {
            $this->db->where('to_user', $un_id);
            $query = $this->db->select_sum('amount')
                ->from('status_income')
                ->get();
            return $query->row()->amount;
        } else {
            if ($type != 'all') {
                $this->db->where('source', $type);
            }
            $this->db->where('to_user', $un_id);
            $query = $this->db->select_sum('amount')
                ->from('income')
                ->get();
            return $query->row()->amount;
        }


    }

    function withdrawList($type, $un_id)
    {
        if ($type != 'all') {
            $this->db->where('status', $type);
        }
        $this->db->where('user_id', $un_id);
        return $this->db->get('withdrawals')->result_array();
    }

    function withdrawTotalSum($type, $un_id)
    {
        if ($type != 'all') {
            $this->db->where('status', $type);
        }
        $this->db->where('user_id', $un_id);
        $query = $this->db->select_sum('amount')
            ->from('withdrawals')
            ->get();
        return $query->row()->amount;
    }



    function deleteCartItem($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('cart');
    }



    function premuimPackageDetails($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('premium_packages')->row_array();
    }

    function premuimPackageBindings($id)
    {
        $this->db->where('package_id', $id);
        return $this->db->get('product_package_binds')->result_array();
    }

    function checkTrxInUserPackage($trx)
    {
        $this->db->where('trx', $trx);
        $data = $this->db->get('user_package')->result_array();

        if (count($data) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function buyPackageRequest($form)
    {
        $this->db->insert('user_package', $form);
    }


    function addAddress($form)
    {
        $this->db->insert('customer_addresses', $form);
    }

    function addressList($un_id)
    {
        $this->db->where('user_un_id', $un_id);
        return $this->db->get('customer_addresses')->result_array();
    }

    function createOrder($form)
    {
        $this->db->insert('orders', $form);
        $inserted_id = $this->db->insert_id();
        $inserted_data = $this->db->get_where('orders', array('id' => $inserted_id))->row();

        return $inserted_data;
    }

    function createOrderItem($form)
    {
        $this->db->insert('order_items', $form);
    }

    function unPaidOrders($un_id)
    {
        $this->db->where('user_un_id', $un_id);
        $this->db->where('payment_gateway', 0);
        return $this->db->get('orders')->result_array();
    }

    function getOrdersByStatus($un_id, $type)
    {
        $this->db->where('user_un_id', $un_id);
        $this->db->where('order_status_id', $type);
        return $this->db->get('orders')->result_array();
    }

    function orderDetails($orderId)
    {
        $this->db->where('un_id', $orderId);
        return $this->db->get('orders')->row_array();
    }

    function orderItems($orderId)
    {
        $this->db->where('order_id', $orderId);
        return $this->db->get('order_items')->result_array();
    }

    function createNotifications($data)
    {
        $this->db->insert('notifications', $data);
    }



    function createInvoice($form)
    {
        $this->db->insert('invoice', $form);
    }

    function createRenewInvoice($form)
    {
        $this->db->insert('renewInvoice', $form);
    }

    function invoiceDetailsByInvoiceId($invoice_id)
    {
        $this->db->where('un_id', $invoice_id);
        return $this->db->get('invoice')->row_array();
    }

    function renewInvoiceDetailsByInvoiceId($invoice_id)
    {
        $this->db->where('un_id', $invoice_id);
        return $this->db->get('renewInvoice')->row_array();
    }

    function updateInvoice($un_id, $form)
    {
        $this->db->where('un_id', $un_id);
        $this->db->update('invoice', $form);
    }

    function addressDetails($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('customer_addresses')->row_array();
    }

    function updateAddress($id, $form)
    {
        $this->db->where('id', $id);
        $this->db->update('customer_addresses', $form);
    }

    function createRankAchive($form)
    {
        $this->db->insert('rank_achievement', $form);
    }


}
?>