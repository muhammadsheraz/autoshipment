<?php
/**
 * ZOHO CRM/Inventory to UPS Shipping Integration System
 * 
 * Copyright (C) SixtySixTen.com - All Rights Reserved
 * This file is part of ZOHO CRM/Inventory to UPS Shipping Integration System.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Muhammad Sheraz, Karachi, Pakistan <msherazjaved@gmail.com>, July 2017
 */


/**
 * TGModel
 * Model Class to handle Truegrid ZOHO - UPS Integration database related processes
 */
class TGModel{
    public $connection;
    private $orders_table;
    
    public function __construct(PDO $connection = null) {
        $this->connection = $connection;

        if ($this->connection === null) {
            $this->connection = new PDO(
                    'mysql:host='.DB_HOST.';dbname='.DB_NAME, 
                    DB_USER, 
                    DB_PASSWORD
                );

            $this->connection->setAttribute(
                PDO::ATTR_ERRMODE, 
                PDO::ERRMODE_EXCEPTION
            );
        }
        
        $this->orders_table = 'tg_orders_sync';
    }
    
    public function find($id) {
        $stmt = $this->connection->prepare('
            SELECT * 
             FROM '.$this->orders_table.' 
             WHERE id = :id
        ');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findByOrderId($orderId) {
        $stmt = $this->connection->prepare('
            SELECT * 
             FROM '.$this->orders_table.' 
             WHERE salesorder_id = :id
        ');
        $stmt->bindParam(':id', $orderId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function findAllForShipmentProcess($params = array()) {
        $salesorder_id = get_var($params , 'salesorder_id', 0);

        $sql = "
            SELECT * 
            FROM " . $this->orders_table . " 
                WHERE (ups_tracking_number = '' OR ups_tracking_number IS NULL)";
        
            if (!empty($salesorder_id)) {
                $sql .=  " AND salesorder_id = $salesorder_id";
            }                
            $sql .=  " AND zoho_package_created = 0";
            $sql .=  " AND zoho_shipment_created = 0";
            $sql .=  " AND ups_shipment_created = 0";
            $sql .=  " AND (last_processed IS NULL OR last_processed = '' OR last_processed <= DATE_SUB(NOW(), INTERVAL 59 MINUTE))";
        $sql .=  " ORDER BY modified_date ASC";
        $sql .=  " LIMIT 1";
        
        $stmt = $this->connection->prepare($sql);
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all salesorders from database table
     * 
     * @param array $params
     * @return array
     */
    public function findAll($params = array()) {
        $salesorder_id = get_var($params , 'salesorder_id', 0);
        
        $sql = "
            SELECT * 
            FROM " . $this->orders_table . " 
                WHERE (ups_tracking_number = '' OR ups_tracking_number IS NULL)";
        
        if (!empty($salesorder_id)) {
            $sql .=  " AND salesorder_id = :salesorder_id";
        }
        $stmt = $this->connection->prepare($sql);
        
        if (!empty($salesorder_id)) {
            $stmt->bindParam(':salesorder_id', $salesorder_id);
        }
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findAllExisting($params = array()) {
        $salesorder_id = get_var($params , 'salesorder_id', 0);
        
        $sql = "
            SELECT * 
            FROM " . $this->orders_table . " 
                WHERE (ups_tracking_number = '' OR ups_tracking_number IS NULL)";
        
        if (!empty($salesorder_id)) {
            $sql .=  " AND salesorder_id = :salesorder_id";
        }
        $stmt = $this->connection->prepare($sql);
        
        if (!empty($salesorder_id)) {
            $stmt->bindParam(':salesorder_id', $salesorder_id);
        }
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findAllExistingNew($params = array()) {
        $salesorder_id = get_var($params , 'salesorder_id', 0);
        
        $sql = "
            SELECT * 
            FROM " . $this->orders_table . " 
                WHERE ('' = '')";
        
        if (!empty($salesorder_id)) {
            $sql .=  " AND salesorder_id = :salesorder_id";
        }
        $stmt = $this->connection->prepare($sql);
        
        if (!empty($salesorder_id)) {
            $stmt->bindParam(':salesorder_id', $salesorder_id);
        }
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findAllFix($params = array()) {
        $salesorder_id = get_var($params , 'salesorder_id', 0);
        
        $sql = "
            SELECT * 
            FROM " . $this->orders_table . " 
                WHERE ('' = '')";
        
        if (!empty($salesorder_id)) {
            $sql .=  " AND salesorder_id = :salesorder_id";
        }
        $stmt = $this->connection->prepare($sql);
        
        if (!empty($salesorder_id)) {
            $stmt->bindParam(':salesorder_id', $salesorder_id);
        }
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all salesorders from database table
     * 
     * @param array $params
     * @return array
     */
    public function findAllShipmentFix() {
        $sql = "
            SELECT * 
            FROM " . $this->orders_table . " 
                WHERE ups_tracking_number != ''";
        
        #$sql .=  " AND salesorder_id IN (621081000001289042,621081000001325144,621081000001339020,621081000001391133,621081000001391265,621081000001391351,621081000001391440,621081000001419040,621081000001419149,621081000001419469,621081000001419743)";
        #$sql .=  " AND salesorder_id = 621081000001419743";
        $sql .=  " AND zoho_package_created = 0";
        $sql .=  " AND zoho_shipment_created = 0";
        $sql .=  " AND ups_shipment_created = 1";
        $sql .=  " LIMIT 1";

        $stmt = $this->connection->prepare($sql);
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    ## TG-STATIC remove this on production "AND salesorder_id = 621081000001419980;"
    public function findOrdersForDB() {
        $stmt = $this->connection->prepare("
            SELECT * 
            FROM " . $this->orders_table . " 
                ORDER BY salesorder_number DESC;
        ");
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function save($order) {
        $order = (object)$order;

        $stmt = $this->connection->prepare('
            INSERT INTO ' . $this->orders_table . ' (
                salesorder_id, 
                salesorder_number, 
                salesorder_customer_id, 
                salesorder_customer_name, 
                salesorder_customer_email,
                salesorder_order_status,
                salesorder_reference_number,
                salesorder_created_time,
                salesorder_modified_time,
                salesorder_lineitems,
                salesorder_shipping_address,
                salesorder_billing_address,
                label_file,
                packaging_slip_file,
                ups_tracking_number,
                ups_confirm_response,
                ups_accept_response,
                email_sent_date,
                last_sync,
                created_date
            ) VALUES (
                :salesorder_id, 
                :salesorder_number, 
                :salesorder_customer_id, 
                :salesorder_customer_name, 
                :salesorder_customer_email,
                :salesorder_order_status,
                :salesorder_reference_number,
                :salesorder_created_time,
                :salesorder_modified_time,
                :salesorder_lineitems,
                :salesorder_shipping_address,
                :salesorder_billing_address,
                :label_file,
                :packaging_slip_file,
                :ups_tracking_number,
                :ups_confirm_response,
                :ups_accept_response,
                :email_sent_date,
                :last_sync,
                :created_date
            )
        ');
        
        $stmt->bindParam(':salesorder_id', $order->salesorder_id);
        $stmt->bindParam(':salesorder_number', $order->salesorder_number);
        $stmt->bindParam(':salesorder_customer_id', $order->salesorder_customer_id);
        $stmt->bindParam(':salesorder_customer_name', $order->salesorder_customer_name);
        $stmt->bindParam(':salesorder_customer_email', $order->salesorder_customer_email);
        $stmt->bindParam(':salesorder_order_status', $order->salesorder_order_status);
        $stmt->bindParam(':salesorder_reference_number', $order->salesorder_reference_number);
        $stmt->bindParam(':salesorder_created_time', $order->salesorder_created_time);
        $stmt->bindParam(':salesorder_modified_time', $order->salesorder_modified_time);
        $stmt->bindParam(':salesorder_lineitems', $order->salesorder_lineitems);
        $stmt->bindParam(':salesorder_shipping_address', $order->salesorder_shipping_address);
        $stmt->bindParam(':salesorder_billing_address', $order->salesorder_billing_address);
        $stmt->bindParam(':label_file', $order->label_file);
        $stmt->bindParam(':packaging_slip_file', $order->packaging_slip_file);
        $stmt->bindParam(':ups_tracking_number', $order->ups_tracking_number);
        $stmt->bindParam(':ups_confirm_response', $order->ups_confirm_response);
        $stmt->bindParam(':ups_accept_response', $order->ups_accept_response);
        $stmt->bindParam(':email_sent_date', $order->email_sent_date);
        $stmt->bindParam(':last_sync', $order->last_sync);
        $stmt->bindParam(':created_date', $order->created_date);
        
        return $stmt->execute();
    }
    
    public function update($order) {
        $stmt = $this->connection->prepare('
            UPDATE '.$this->orders_table.'
            SET salesorder_customer_id = :salesorder_customer_id, 
                salesorder_customer_name = :salesorder_customer_name, 
                salesorder_customer_email = :salesorder_customer_email,
                salesorder_order_status = :salesorder_order_status,
                salesorder_reference_number = :salesorder_reference_number,
                salesorder_created_time = :salesorder_created_time,
                salesorder_modified_time = :salesorder_modified_time,
                salesorder_lineitems = :salesorder_lineitems,
                salesorder_shipping_address = :salesorder_shipping_address,
                salesorder_billing_address = :salesorder_billing_address,
                label_file = :label_file,
                packaging_slip_file = :packaging_slip_file,
                ups_tracking_number = :ups_tracking_number,
                ups_confirm_response = :ups_confirm_response,
                ups_accept_response = :ups_accept_response,
                email_sent_date = :email_sent_date,
                modified_date = :modified_date
            WHERE salesorder_id = :salesorder_id
        ');
        
        $stmt->bindParam(':salesorder_id', $order->salesorder_id);
        $stmt->bindParam(':salesorder_customer_id', $order->salesorder_customer_id);
        $stmt->bindParam(':salesorder_customer_name', $order->salesorder_customer_name);
        $stmt->bindParam(':salesorder_customer_email', $order->salesorder_customer_email);
        $stmt->bindParam(':salesorder_order_status', $order->salesorder_order_status);
        $stmt->bindParam(':salesorder_reference_number', $order->salesorder_reference_number);
        $stmt->bindParam(':salesorder_created_time', $order->salesorder_created_time);
        $stmt->bindParam(':salesorder_modified_time', $order->salesorder_modified_time);
        $stmt->bindParam(':salesorder_lineitems', $order->salesorder_lineitems);
        $stmt->bindParam(':salesorder_shipping_address', $order->salesorder_shipping_address);
        $stmt->bindParam(':salesorder_billing_address', $order->salesorder_billing_address);
        $stmt->bindParam(':label_file', $order->label_file);
        $stmt->bindParam(':packaging_slip_file', $order->packaging_slip_file);
        $stmt->bindParam(':ups_tracking_number', $order->ups_tracking_number);
        $stmt->bindParam(':ups_confirm_response', $order->ups_confirm_response);
        $stmt->bindParam(':ups_accept_response', $order->ups_accept_response);
        $stmt->bindParam(':email_sent_date', $order->email_sent_date);
        $stmt->bindParam(':modified_date', date("Y-m-d H:i:s"));
        
        return $stmt->execute();
    }
    
    public function updateUPSResponse($order) {
        $order = (object)$order;
        $stmt = $this->connection->prepare('
            UPDATE '.$this->orders_table.'
            SET 
                label_file = :label_file,
                packaging_slip_file = :packaging_slip_file,
                ups_tracking_number = :ups_tracking_number,
                ups_confirm_response = :ups_confirm_response,
                ups_accept_response = :ups_accept_response,
                modified_date = :modified_date
            WHERE salesorder_id = :salesorder_id
        ');
        
        $stmt->bindParam(':label_file', $order->label_file);
        $stmt->bindParam(':packaging_slip_file', $order->packaging_slip_file);
        $stmt->bindParam(':ups_tracking_number', $order->ups_tracking_number);
        $stmt->bindParam(':ups_confirm_response', $order->ups_confirm_response);
        $stmt->bindParam(':ups_accept_response', $order->ups_accept_response);
        $stmt->bindParam(':modified_date', $order->modified_date);
        $stmt->bindParam(':salesorder_id', $order->salesorder_id);
        
        return $stmt->execute();
    }
    
    public function updateZohoPackageCreated($order) {
        $order = (object)$order;
        $zoho_package_created = 1;
        $stmt = $this->connection->prepare('
            UPDATE '.$this->orders_table.'
            SET 
                zoho_package_created = :zoho_package_created,
                modified_date = :modified_date
            WHERE salesorder_id = :salesorder_id
        ');
        
        $stmt->bindParam(':zoho_package_created', $zoho_package_created);
        $stmt->bindParam(':modified_date', $order->modified_date);
        $stmt->bindParam(':salesorder_id', $order->salesorder_id);
        
        return $stmt->execute();
    }
    
    public function updateZohoShipmentCreated($order) {
        $order = (object)$order;
        $zoho_shipment_created = 1;
        $stmt = $this->connection->prepare('
            UPDATE '.$this->orders_table.'
            SET 
                zoho_shipment_created = :zoho_shipment_created,
                modified_date = :modified_date
            WHERE salesorder_id = :salesorder_id
        ');
        
        $stmt->bindParam(':zoho_shipment_created', $zoho_shipment_created);
        $stmt->bindParam(':modified_date', $order->modified_date);
        $stmt->bindParam(':salesorder_id', $order->salesorder_id);
        
        return $stmt->execute();
    }
    
    public function updateUPSShipmentCreated($order) {
        $order = (object)$order;
        $ups_shipment_created = 1;
        $stmt = $this->connection->prepare('
            UPDATE '.$this->orders_table.'
            SET 
                ups_shipment_created = :ups_shipment_created,
                modified_date = :modified_date
            WHERE salesorder_id = :salesorder_id
        ');
        
        $stmt->bindParam(':ups_shipment_created', $ups_shipment_created);
        $stmt->bindParam(':modified_date', $order->modified_date);
        $stmt->bindParam(':salesorder_id', $order->salesorder_id);
        
        return $stmt->execute();
    }
    
    public function updateUPSShipmentInvalidAddressNotified($order) {
        $order = (object)$order;
        $invalid_address_notified = 1;
        $stmt = $this->connection->prepare('
            UPDATE '.$this->orders_table.'
            SET 
                invalid_address_notified = :invalid_address_notified,
                modified_date = :modified_date
            WHERE salesorder_id = :salesorder_id
        ');
        
        $stmt->bindParam(':invalid_address_notified', $invalid_address_notified);
        $stmt->bindParam(':modified_date', $order->modified_date);
        $stmt->bindParam(':salesorder_id', $order->salesorder_id);
        
        return $stmt->execute();
    }
    
    public function updateNonUSOrderNotified($order) {
        $order = (object)$order;
        $non_us_order_notified = 1;
        $stmt = $this->connection->prepare('
            UPDATE '.$this->orders_table.'
            SET 
                non_us_order_notified = :non_us_order_notified,
                modified_date = :modified_date
            WHERE salesorder_id = :salesorder_id
        ');
        
        $stmt->bindParam(':non_us_order_notified', $non_us_order_notified);
        $stmt->bindParam(':modified_date', $order->modified_date);
        $stmt->bindParam(':salesorder_id', $order->salesorder_id);
        
        return $stmt->execute();
    }
    
    public function updateLastProcessedDateTime($order) {
        $order = (object)$order;

        $stmt = $this->connection->prepare('
            UPDATE '.$this->orders_table.'
            SET 
                last_processed = NOW(),
                modified_date = NOW()
            WHERE salesorder_id = :salesorder_id
        ');
        
        $stmt->bindParam(':salesorder_id', $order->salesorder_id);
        
        return $stmt->execute();
    }
    
    public function updateEmailSent($data) {
        $data = (object)$data;
        $stmt = $this->connection->prepare('
            UPDATE '.$this->orders_table.'
            SET 
                email_sent_date = :email_sent_date
            WHERE salesorder_id = :salesorder_id
        ');
        
        $stmt->bindParam(':email_sent_date', $data->email_sent_date);
        $stmt->bindParam(':salesorder_id', $data->salesorder_id);
        
        return $stmt->execute();
    }
    
    public function updateModifiedDate($order) {
        $order = (object)$order;

        $stmt = $this->connection->prepare('
            UPDATE '.$this->orders_table.'
            SET 
                modified_date = :modified_date
            WHERE salesorder_id = :salesorder_id
        ');
        
        $stmt->bindParam(':modified_date', $order->modified_date);
        $stmt->bindParam(':salesorder_id', $order->salesorder_id);
        
        return $stmt->execute();
    }
    
    public function findAllStates() {
        $stmt = $this->connection->prepare("
            SELECT * 
            FROM tg_states 
                WHERE (1=1);
        ");
        
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }	
}