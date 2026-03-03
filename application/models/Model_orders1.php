<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_orders1 extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /* ===============================
       GET ORDERS DATA
    =================================*/
    public function getOrdersData($id = null)
    {
        if($id) {
            $sql = "SELECT * FROM orders1 WHERE id1 = ?";
            $query = $this->db->query($sql, array($id));
            return $query->row_array();
        }

        $sql = "SELECT * FROM orders1 ORDER BY id1 DESC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /* ===============================
       GET ORDER ITEMS
    =================================*/
   public function getOrdersItemData($order_id = null)
{
    if(!$order_id){
        return array();
    }

    $sql = "SELECT * FROM orders_item WHERE order_id = ?";
    $query = $this->db->query($sql, array($order_id));

    return $query->result_array();
}
    /* ===============================
       CREATE ORDER
    =================================*/
  public function create1()
    {
        // ambil user login
        $user_id1 = $this->session->userdata('id');

        if(!$user_id1){
            $user_id1 = 1; // fallback supaya tidak NULL
        }

        // generate bill number otomatis
        $bill_no1 = 'BILPR-' . strtoupper(substr(md5(uniqid()), 0, 4));

        $data = array(
            'bill_no1'            => $bill_no1,
            'customer_name1'      => $this->input->post('customer_name'),
            'customer_address1'   => $this->input->post('customer_address'),
            'customer_phone1'     => $this->input->post('customer_phone'),
            'date_time1'          => time(),
            'gross_amount1'       => 0,
            'service_charge_rate1'=> 0,
            'service_charge1'     => 0,
            'vat_charge_rate1'    => 0,
            'vat_charge1'         => 0,
            'net_amount1'         => 0,
            'discount1'           => 0,
            'paid_status1'        => 2,
            'user_id1'            => $user_id1
        );

        $this->db->insert('orders1', $data);

        return ($this->db->affected_rows() == 1) ? $this->db->insert_id() : false;
    

        /* ===== INSERT ITEMS ===== */
        $this->load->model('model_products');

        $products = $this->input->post('product');

        if($products){
            foreach($products as $key => $product_id){

                $qty    = $this->input->post('qty')[$key];
                $rate   = $this->input->post('rate_value')[$key];
                $amount = $this->input->post('amount_value')[$key];

                $items = array(
                    'order_id'  => $order_id,
                    'product_id'=> $product_id,
                    'qty'       => $qty,
                    'rate'      => $rate,
                    'amount'    => $amount
                );

                $this->db->insert('orders_item', $items);

                // update stock
                $product_data = $this->model_products->getProductData($product_id);

                if($product_data){
                    $new_qty = (int)$product_data['qty'] - (int)$qty;

                    $this->model_products->update(
                        array('qty'=>$new_qty),
                        $product_id
                    );
                }
            }
        }

        return $order_id;
    }

    /* ===============================
       UPDATE ORDER
    =================================*/
    public function update($id1 = null)
{
    // =========================
    // VALIDASI ID
    // =========================
    if(empty($id1)){
        redirect('orders1', 'refresh');
    }

    $user_id = $this->session->userdata('id');

    // =========================
    // DATA HEADER ORDER
    // =========================
    $data = array(
        'customer_name1'      => $this->input->post('customer_name') ?? '',
        'customer_address1'   => $this->input->post('customer_address') ?? '',
        'customer_phone1'     => $this->input->post('customer_phone') ?? '',

        // ✅ FIELD PO & DO (INI YANG TADI KOSONG)
        'no_po1'              => $this->input->post('no_po') ?? '',
        'tanggal_po1'         => $this->input->post('tanggal_po') ?? NULL,
        'no_do1'              => $this->input->post('no_do') ?? '',
        'tanggal_do1'         => $this->input->post('tanggal_do') ?? NULL,

        // TOTAL
        'gross_amount1'       => $this->input->post('gross_amount_value') ?: 0,
        'service_charge_rate1'=> $this->input->post('service_charge_rate') ?: 0,
        'service_charge1'     => $this->input->post('service_charge_value') ?: 0,
        'vat_charge_rate1'    => $this->input->post('vat_charge_rate') ?: 0,
        'vat_charge1'         => $this->input->post('vat_charge_value') ?: 0,
        'net_amount1'         => $this->input->post('net_amount_value') ?: 0,
        'discount1'           => $this->input->post('discount') ?: 0,
        'paid_status1'        => $this->input->post('paid_status') ?: 0,

        'user_id1'            => $user_id
    );

    // =========================
    // UPDATE HEADER ORDER
    // =========================
    $this->db->where('id1', $id1);
    $this->db->update('orders1', $data);

    // =========================
    // LOAD MODEL PRODUCT
    // =========================
    $this->load->model('model_products');

    // =========================
    // RESTORE STOCK LAMA
    // =========================
    $old_items = $this->db
        ->where('order_id', $id1)
        ->get('orders_item')
        ->result_array();

    foreach($old_items as $item){

        $product = $this->model_products
                        ->getProductData($item['product_id']);

        if($product){
            $restore_qty = $product['qty'] + $item['qty'];

            $this->model_products->update(
                ['qty' => $restore_qty],
                $item['product_id']
            );
        }
    }

    // =========================
    // HAPUS ITEM LAMA
    // =========================
    $this->db->where('order_id', $id1);
    $this->db->delete('orders_item');

    // =========================
    // INSERT ITEM BARU
    // =========================
    $products = $this->input->post('product');

    if(!empty($products)){

        $qtys    = $this->input->post('qty');
        $rates   = $this->input->post('rate_value');
        $amounts = $this->input->post('amount_value');

        foreach($products as $key => $product_id){

            $qty    = $qtys[$key] ?? 0;
            $rate   = $rates[$key] ?? 0;
            $amount = $amounts[$key] ?? 0;

            // insert item
            $this->db->insert('orders_item', [
                'order_id'  => $id1,
                'product_id'=> $product_id,
                'qty'       => $qty,
                'rate'      => $rate,
                'amount'    => $amount
            ]);

            // =========================
            // KURANGI STOCK
            // =========================
            $product = $this->model_products
                            ->getProductData($product_id);

            if($product){
                $new_qty = $product['qty'] - $qty;

                $this->model_products->update(
                    ['qty' => $new_qty],
                    $product_id
                );
            }
        }
    }

    // =========================
    // SUCCESS
    // =========================
    $this->session->set_flashdata('success', 'Order berhasil diupdate');
    redirect('orders1', 'refresh');
}

    /* ===============================
       DELETE ORDER
    =================================*/
    public function remove($id)
    {
        $this->db->where('id1',$id)->delete('orders1');
        $this->db->where('order_id',$id)->delete('orders_item');

        return true;
    }

    /* ===============================
       TOTAL PAID ORDER
    =================================*/
    public function countTotalPaidOrders()
    {
        return $this->db
            ->where('paid_status1',1)
            ->count_all_results('orders1');
    }
}