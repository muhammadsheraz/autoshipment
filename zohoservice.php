<?php

/**
 * ZOHO CRM/Inventory to UPS Shipping Integration System
 *
 * Copyright (C) SixtySixTen.com - All Rights Reserved
 * This file is part of ZOHO CRM/Inventory to UPS Shipping Integration System.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Muhammad Sheraz, Karachi, Pakistan <msherazjaved@jmail.com>, July 2017
 */

use shqear\lib\ZohoClient;

class ZohoService {
    private $accessToken = '';


    public function __construct() {
        $this->accessToken = ZOHO_ACCESS_TOKEN_INV;
    }

    public function getSalesOrders () {
        $config['accessToken'] = $this->accessToken;

        $zoho = new ZohoClient($config);

        try {
            if ($zoho instanceof ZohoClient) {
                $response = $zoho->listSalesOrders();

                return $response->salesorders;
            } else {
                throw new Exception('Cannot connect to ZohoClient API');
            }
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function getPackages () {
        $config['accessToken'] = $this->accessToken;

        $zoho = new ZohoClient($config);

        try {
            if ($zoho instanceof ZohoClient) {
                $response = $zoho->listSalesOrders();

                return $response->salesorders;
            } else {
                throw new Exception('Cannot connect to ZohoClient API');
            }
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function getOrderDetails ($params = array()) {
        $order_id = $params['order_id'];

        $config['accessToken'] = $this->accessToken;

        $zoho = new ZohoClient($config);

        try {
            if ($zoho instanceof ZohoClient) {

                $response = $zoho->retrieveSalesOrder($order_id);

                return $response->salesorder;
            } else {
                throw new Exception('Cannot connect to ZohoClient API');
            }
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function getLineItemsByOrderId ($params = array()) {
        $order_id = $params['order_id'];

        try {
            $order = $this->getOrderDetails(array('order_id'=>$order_id));

            return $order->line_items;
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function getContactDetails ($params = array()) {
        $contact_id = get_var($params,'contact_id','');

        $config['accessToken'] = $this->accessToken;
        $zoho = new ZohoClient($config);

        try {
            $contact = $zoho->retrieveContact($contact_id);

            return $contact;
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function getItemDetails ($params = array()) {
        $item_id = get_var($params,'item_id','');

        $config['accessToken'] = $this->accessToken;
        $zoho = new ZohoClient($config);

        try {
            $item = $zoho->retrieveItem($item_id);

            return $item;
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function getItems () {
        $config['accessToken'] = $this->accessToken;
        $zoho = new ZohoClient($config);

        try {
            $items = $zoho->listItems();

            return $items;
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function getProducts () {
        try {
            header("Content-type: application/json");
            $token=ZOHO_ACCESS_TOKEN_CRM;
            $url = "https://crm.zoho.com/crm/private/json/Products/getRecords";
            $param= "authtoken=".$token."&scope=crmapi";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
            $result = curl_exec($ch);
            curl_close($ch);

            $products = json_decode($result);

            $products_master_arr = array();
            foreach ($products->response->result->Products->row as $product_obj) {
                $product_arr = array();
                foreach ($product_obj->FL as $product_attr) {
                    $product_arr[$product_attr->val] = $product_attr->content;
                }
                $products_master_arr[] = $product_arr;
            }

            return $products_master_arr;
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function getProduct ($params = array()) {
        $product_id = get_var($params,'product_id','');
        try {
            header("Content-type: application/json");
            $token = ZOHO_ACCESS_TOKEN_CRM;
            $url = "https://crm.zoho.com/crm/private/json/Products/getRecordById?authtoken=$token&scope=crmapi&id=$product_id";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, 1);
            $result = curl_exec($ch);
            curl_close($ch);

            $product = json_decode($result);

            if (!empty($product->response->result->Products->row->FL)) {
                $products_master_arr = array();
                $product_arr = $product->response->result->Products->row->FL;
                foreach ($product_arr as $product_obj) {
                    $products_master_arr[$product_obj->val] = $product_obj->content;
                }

                return $products_master_arr;
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    /**
     * To get an organization details
     * Disabled as it is not working as expected. Will be deleted in future.
     *
     * @param type $params
     * @return type
     */
    public function getOrganization ($params = array()) {
        $organizationId = get_var($params,'organization_id', '');

        try {
            $headers = array(
                'Accept: application/json',
                'Content-Type: application/json',
            );

            $token = ZOHO_ACCESS_TOKEN_INV;

            $url = "https://inventory.zoho.com/api/v1/organizations/$organizationId?authtoken=$token&organization_id=$organizationId";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            return json_decode($result);
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function getListOrganizations () {
        $config['accessToken'] = $this->accessToken;
        $zoho = new ZohoClient($config);

        try {
            $organizations = $zoho->retrieveOrganizationsInfo();

            return $organizations;
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function createNewPackage (array $params = [], $saleorder_id = null) {
        $config['accessToken'] = $this->accessToken;
        $zoho = new ZohoClient($config);

        try {
            header("Content-type: application/json");
            $token=ZOHO_ACCESS_TOKEN_INV;
            $url = "https://inventory.zoho.com/api/v1/packages?authtoken=".$token . "&salesorder_id=$saleorder_id" . "&organization_id=" . ZOHO_ORGANIZATION_ID;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array('JSONString'=>json_encode($params)));
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            
            $result = curl_exec($ch);
            
            curl_close($ch);

            return json_decode($result);
        } catch (Exception $e) {
            addLog($e->getMessage(), 0);
        }
    }

    public function createNewShipmentOrder (array $params = [], $package_ids = null, $saleorder_id = null) {
        $config['accessToken'] = $this->accessToken;
        $zoho = new ZohoClient($config);

        try {
            $response = $zoho->createShipmentOrder($params, $package_ids, $saleorder_id);

            return $response;
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function listPackages ($params = array()) {
        $config['accessToken'] = $this->accessToken;
        $zoho = new ZohoClient($config);

        try {
            $packages = $zoho->listPackages();

            return $packages;
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function getPackage ($params = array()) {
        $config['accessToken'] = $this->accessToken;
        $package_id = get_var($params,'package_id', '');
        $zoho = new ZohoClient($config);

        try {
            $package = $zoho->getPackage($package_id);

            return $package;
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function printPackages ($params = array()) {
        try {
            $package_ids = $params['package_ids'];
            header("Content-Type: application/pdf;charset=UTF-8");
            header("Content-Disposition:attachment;filename=downloaded.pdf");
            $token = ZOHO_ACCESS_TOKEN_INV;
            $url = "https://inventory.zoho.com/api/v1/packages/print?authtoken=$token&package_ids=$package_ids";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            $result = curl_exec($ch);

            echo $result;
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function printPackageSlip ($params = array(), $file_name = '') {
        try {
            $package_ids = $params['package_ids'];
            $token = ZOHO_ACCESS_TOKEN_INV;
            $url = "https://inventory.zoho.com/api/v1/packages/print?authtoken=$token&package_ids=$package_ids";

            $ch = curl_init();
            $fp = fopen($file_name, 'wb');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            if (curl_exec($ch)) {
                $status = true;
            } else {
                $status = false;
            }
            
            curl_close($ch);
            fclose($fp);
            
            return $status;
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }

    public function getSalesOrdersNDS () {
        try {
            header("Content-type: application/json");
            $token=ZOHO_ACCESS_TOKEN_CRM;
            $url = "https://crm.zoho.com/crm/private/json/Products/getRecords";
            $param= "authtoken=".$token."&scope=crmapi";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
            $result = curl_exec($ch);
            curl_close($ch);

            $products = json_decode($result);

            $products_master_arr = array();
            foreach ($products->response->result->Products->row as $product_obj) {
                $product_arr = array();
                foreach ($product_obj->FL as $product_attr) {
                    $product_arr[$product_attr->val] = $product_attr->content;
                }
                $products_master_arr[] = $product_arr;
            }

            return $products_master_arr;
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
        }
    }


//    public function printPackages ($params = array()) {
//        $config['accessToken'] = $this->accessToken;
//        $zoho = new ZohoClient($config);
//
//        try {
//            $packages = $zoho->printPackages();
//
//            return $packages;
//        } catch (Exception $e) {
//            error_log($e->getMessage(), 0);
//        }
//    }
}






