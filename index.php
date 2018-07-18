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

include(__DIR__  . '/config.php');
include(__DIR__  . '/upsservice.php');
include(__DIR__  . '/zohoservice.php');

# Checking if CLI access allowed only
check_cli();


use Dompdf\Dompdf;
use Mailgun\Mailgun;

$service = get_var($_GET, 'srvc', '');
$process = get_var($_GET, 'procs', '');
$order_id = get_var($_GET, 'order_id', 0);
$customer_id = get_var($_GET, 'customer_id', 0);


if (!empty($service) AND $service == SERVICE_UPS) {
    // Accessing UPS Services
    $UPSService = new UpsService();
    $UPSService->validateAddress();
    exit();
}

if (!empty($service) AND $service == SERVICE_ZOHO) {
    // Accessing Zoho Services
    $ZHService = new ZohoService();
    $TGModel = new TGModel();

    if ($process == 'shipment') {
        try {
            addLog('Shipment process started.');
            $error = array();
            # Retrieving Sales Orders from TG Integration Database
            # $stored_sales_orders = $TGModel->findAllShipmentFix();
            
            # $stored_sales_orders = $TGModel->findAllFix(array('salesorder_id'=>$order_id));

            $stored_sales_orders = $TGModel->findAllForShipmentProcess(array('salesorder_id'=>$order_id));

            $states = $TGModel->findAllStates();

            # Preparing allowed states array
            $allowed_states = array();
            foreach ($states as $state) {
                if (!empty($state['shipping_allowed']) AND (int)$state['shipping_allowed'] == 1) {
                    $allowed_states[] = trim($state['st_code']);
                }
            }
            if (!empty($stored_sales_orders)) {
                foreach ($stored_sales_orders as $stored_sales_order) {
                    $customer_email = get_var($stored_sales_order,'salesorder_customer_email', array());
                    $customer_name = get_var($stored_sales_order,'salesorder_customer_name', array());
                    $reference_number = get_var($stored_sales_order,'salesorder_reference_number', array());
                    $order_id = get_var($stored_sales_order,'salesorder_id',0);
                    $order_number = get_var($stored_sales_order,'salesorder_number',0);
                    $invalid_address_notified = get_var($stored_sales_order,'invalid_address_notified',0);
                    $non_us_order_notified = get_var($stored_sales_order,'non_us_order_notified',0);

                    if (!empty($order_id)) {
                        # Updating last processed datetime
                        $order_data='';
                        $order_data['salesorder_id'] = $order_id;
                        $updated = $TGModel->updateLastProcessedDateTime($order_data);

                        $order_details = $ZHService->getOrderDetails(array('order_id'=>$order_id));

                        $customer_details = $ZHService->getContactDetails(array('contact_id'=>$order_details->customer_id))->contact;

                        $organization_details = $ZHService->getOrganization(array('organization_id'=>ZOHO_ORGANIZATION_ID))->organization;
                        $warehouse = getWarehouseFromOrderDetails($order_details);
                        $warehouse_id = $warehouse->warehouse_id;
                        $warehouse_email = $warehouse->email;
                        $warehouse_name = $warehouse->warehouse_name;
                        $warehouse_state_code = getStateCodeByName($warehouse->state, $states);
                        $warehouse_country_code = getCountryCodeByName($warehouse->country);

                        $weight = 0;
                        $height = 0;
                        $length = 0;
                        $width = 0;
                        $line_items = array();
                        $so_line_items = $order_details->line_items;
                        $total_qty = 0;
                        $items_for_email = array();
                        if (!empty($so_line_items)) {
                            $line_items_packages = array();
                            $li = 0;
                            $lic = 0;
                            $total_weight = 0;
                            foreach ($so_line_items as $so_line_item) {
                                if (!empty($so_line_item->item_id) AND !empty($so_line_item->name) AND !empty($so_line_item->quantity)) {
                                    $li++;
                                    $line_items[$lic]['so_line_item_id'] =  $so_line_item->line_item_id;
                                    $line_items[$lic]['quantity'] =  $so_line_item->quantity;

                                    # Fetching Item Details from Zoho Inventory
                                    $item = $ZHService->getItemDetails(array('item_id'=>$so_line_item->item_id));

                                    # Fetching Product Details from Zoho CRM using $item->item->zcrm_product_id
                                    $product = $ZHService->getProduct(array('product_id'=>$item->item->zcrm_product_id));
                                    #addLog(DOCUMENTS_DIR . 'log.txt', json_encode($product), FILE_APPEND | LOCK_EX);


                                    if (isset($product['Box Height'])
                                        AND isset($product['Box Width'])
                                        AND isset($product['Box Length'])) {
                                            $length = $product['Box Length'];
                                            $width = $product['Box Width'];
                                            $height = $product['Box Height'];
                                        } else {
                                            if (isset($product['Product Length'])) {
                                                $length = $product['Product Length'];
                                            } else {
                                               $length = '';
                                            }

                                            if (isset($product['Product Width'])) {
                                                $width = $product['Product Width'];
                                            } else {
                                               $width = '';
                                            }

                                            if (isset($product['Product Height'])) {
                                                $height = $product['Product Height'];
                                            } else {
                                               $height = '';
                                            }
                                        }

                                    # Setting Total Weight and Quantity
                                    $product_weight = $product['Shipping Weight'];
                                    $no_qty = (int)$line_items[$lic]['quantity'];

                                    # Setting Total Quantity
                                    $total_qty += $no_qty;

                                    if (!empty($product['Product Name'])) {
                                        $product_name = $product['Product Name'];
                                    } else {
                                        $product_name = '';
                                    }

                                    # Creating Number of packages based on number of quanitiy in line items

                                    for ($p=1;$p<=$no_qty;$p++) {
                                        $line_items_packages[$li][$p]['length'] = $length;
                                        $line_items_packages[$li][$p]['width'] = $width;
                                        $line_items_packages[$li][$p]['height'] = $height;
                                        $line_items_packages[$li][$p]['weight'] = $product_weight;
                                        $line_items_packages[$li][$p]['package_type'] = \Ups\Entity\PackagingType::PT_PACKAGE; # Package Type
                                        $line_items_packages[$li][$p]['w_unit'] = \Ups\Entity\UnitOfMeasurement::UOM_LBS; # Weight Unit
                                        $line_items_packages[$li][$p]['d_unit'] = \Ups\Entity\UnitOfMeasurement::UOM_IN; # Dimensions Unit
                                        $line_items_packages[$li][$p]['p_description'] = $product_name; # Description

                                        # Setting Total Weight
                                        $total_weight += $product_weight;
                                    }
                                    $line_items[$lic]['item_name'] = $so_line_item->name;
                                    $line_items[$lic]['rate'] = $so_line_item->rate;
    
                                    $items_for_email[] = $line_items[$lic];
                                }


                                unset($line_items[$lic]['item_name']);
                                unset($line_items[$lic]['rate']);

                                $lic++;
                            }
                        }

                        # Modifying product weight if overall quantity reaches 3 and $products total weight is between 135 lbs to 150 lbs inclusive.
                        $packaging_mode = 'gfp';
                        
                        if ($total_qty == 3) {
                            $packaging_mode = 'gfp';
                            
                            if ((int)$total_weight >= 135 OR (int)$total_weight <= 150) {
                                $product_weight = 50;
                                foreach ($line_items_packages as $kli=>$li) {
                                    foreach ($li as $kp=>$p) {
                                        $line_items_packages[$kli][$kp]['weight'] = 50;
                                    }
                                }
                                # $packaging_mode = 'Ground Freight Packaging';
                            } else if ((int)$calc_weight < 135) {
                                # $packaging_mode = 'Small Pack Packaging';
                            }
                        } else if ($total_qty > 3) {
                            $packaging_mode = 'gfp';
                        } else if ($total_qty < 3) {
                            $packaging_mode = 'rp';
                        }

                        ## TG-STATIC : Static values should be replaced with dynamic variables on production
                        $description = !empty($order_details->notes) ? $order_details->notes : 'Description not available';
                        ## TG-STATIC :

                        $sh_data['order_id'] = $order_id;

                        $shipper['number'] = UPS_ACCOUNT_NUMBER;
                        $shipper['name'] = $warehouse->warehouse_name;
                        $shipper['attentionName'] = $organization_details->contact_name;
                        $shipper['addressLine1'] = $warehouse->address;
                        $shipper['postalCode'] = $warehouse->zip;
                        $shipper['city'] = $warehouse->city;
                        $shipper['stateProvinceCode'] = $warehouse_state_code;
                        $shipper['countryCode'] = 'US';
                        $shipper['emailAddress'] = $warehouse->email;
                        $shipper['phoneNumber'] = $warehouse->phone;

                        $sh_data['shipper'] = $shipper;

                        $fromAddress['addressLine1'] = $warehouse->address;
                        $fromAddress['postalCode'] = $warehouse->zip;
                        $fromAddress['city'] = $warehouse->city;
                        $fromAddress['countryCode'] = 'US';
                        $fromAddress['stateProvinceCode'] = strtoupper($warehouse_state_code);
                        $fromAddress['name'] = $warehouse->warehouse_name;
                        $fromAddress['emailAddress'] = $warehouse->email;
                        $fromAddress['phoneNumber'] = $warehouse->phone;

                        $sh_data['from_address'] = $fromAddress;

                        $toAddress = getToAddressFromOrderDetails(array('order_details'=>$order_details, 'customer_details'=>$customer_details, 'states'=>$states, 'customer_email'=>$customer_email));
                        $soldTo = getSoldToAddressFromOrderDetails(array('order_details'=>$order_details, 'customer_details'=>$customer_details, 'states'=>$states, 'customer_email'=>$customer_email));

                        if (!empty($toAddress)) {
                            $sh_data['to_address'] = $toAddress;
                        } else {
                            $sh_data['to_address'] = '';
                            addLog('Missing Shipping and Billing address in sales order details, to address cannot be prepared for UPS API' . ' Order Id: ' . $order_id);
                            $error[] = 'Missing Shipping and Billing address in sales order details, to address cannot be prepared for UPS API';
                        }

                        if (!empty($soldTo)) {
                            $sh_data['sold_to'] = $soldTo;
                        } else {
                            $sh_data['sold_to'] = '';
                            addLog('Missing Shipping and Billing address sales order details, sold to address cannot be prepared for UPS API' . ' Order Id: ' . $order_id);
                            $error[] = 'Missing Shipping and Billing address in sales order details, sold to address cannot be prepared for UPS API';
                        }

                        $sh_data['description'] = $description;
                        $sh_data['line_items_packages'] = $line_items_packages;
                        $sh_data['shipment_date'] = date("Y-m-d H:i:s");
                        $sh_data['packaging_mode'] = $packaging_mode;
                        $sh_data['total_qty'] = $total_qty;

                        # Checking for Non-US Order
                        $orderShippingStateCode = getStateCodeByName(strtoupper($order_details->shipping_address->state), $states);

                        $state_allowed = false;
                        foreach ($allowed_states as $allowed_state) {
                            if (trim($orderShippingStateCode) == trim($allowed_state)) {
                                $state_allowed = true;
                            }
                        }

                        if (!$state_allowed) {
                            if ($non_us_order_notified == '0') {
                                # Send NON-US Email to Office
                                $email_sent = false;
                                if (!empty(SEND_EMAIL_NOTIFICATION)) {
                                    # Sending email to office

                                    $email_template = getNonUSEmailTemplateForOffice($items_for_email);
                                    $email_template = str_replace('{order_id}', $order_id, $email_template);
                                    $email_template = str_replace('{shipping_state}', getStateNameByCode($order_details->shipping_address->state, $states), $email_template);
                                    $email_template = str_replace('{shipping_country_code}', $order_details->shipping_address->country, $email_template);

                                    $email_template = str_replace('{b_attention}', $order_details->billing_address->attention, $email_template);
                                    $email_template = str_replace('{b_address}', $order_details->billing_address->address, $email_template);
                                    $email_template = str_replace('{b_street2}', $order_details->billing_address->street2, $email_template);
                                    $email_template = str_replace('{b_city}', $order_details->billing_address->city, $email_template);
                                    $email_template = str_replace('{b_state}', $order_details->billing_address->state, $email_template);
                                    $email_template = str_replace('{b_zip}', $order_details->billing_address->zip, $email_template);
                                    $email_template = str_replace('{b_country}', $order_details->billing_address->country, $email_template);
                                    $email_template = str_replace('{b_fax}', $order_details->billing_address->fax, $email_template);
                                    $email_template = str_replace('{b_phone}', $order_details->billing_address->phone, $email_template);

                                    $email_template = str_replace('{s_attention}', $order_details->shipping_address->attention, $email_template);
                                    $email_template = str_replace('{s_address}', $order_details->shipping_address->address, $email_template);
                                    $email_template = str_replace('{s_street2}', $order_details->shipping_address->street2, $email_template);
                                    $email_template = str_replace('{s_city}', $order_details->shipping_address->city, $email_template);
                                    $email_template = str_replace('{s_state}', $order_details->shipping_address->state, $email_template);
                                    $email_template = str_replace('{s_zip}', $order_details->shipping_address->zip, $email_template);
                                    $email_template = str_replace('{s_country}', $order_details->shipping_address->country, $email_template);
                                    $email_template = str_replace('{s_fax}', $order_details->shipping_address->fax, $email_template);
                                    $email_template = str_replace('{s_phone}', $order_details->shipping_address->phone, $email_template);

                                    $email_template = str_replace('{zoho_salesorder_link}', 'https://inventory.zoho.com/app#/salesorders/' . $order_id . '?filter_by=Status.All&per_page=200&sort_column=created_time&sort_order=D', $email_template);

                                    $mail = new PHPMailer();

                                    $mail->IsSMTP();

                                    $mail->Host = SMTP_HOST;
                                    $mail->SMTPAuth = SMTP_AUTH;
                                    $mail->Username = SMTP_USERNAME;
                                    $mail->Password = SMTP_PASSWORD;
                                    $mail->Port = SMTP_PORT;

                                    $mail->addAddress('office@truegridpaver.com', 'Truegrid Management');
                                    $mail->addCC('lbialas@truegridpaver.com', 'Truegrid Management');
                                    $mail->addCC('kmeinhardt@truegridpaver.com', 'Truegrid Management');

//                                    $mail->addAddress('msherazjaved@gmail.com', $warehouse_name);
//                                    $mail->addCC('marawan.aziz@gmail.com', $warehouse_name);

                                    $mail->From         = MAIL_FROM_EMAIL;
                                    $mail->FromName     = MAIL_FROM_NAME;
                                    $mail->Subject      = "TrueGrid - Non-US Order Notification";
                                    $mail->Body         = $email_template;

                                    $mail->IsHTML(true);

                                    if(!$mail->send()) {
                                        error_log('Mailer Error: ' . $mail->ErrorInfo);
                                        // addLog('Email not sent. ' . $mail->ErrorInfo);
                                    } else {
                                        # Updating email_sent date-time in database
                                        $email_sent = true;
                                        addLog('Non-US Order(s) Email Sent.' . ' Order Id: ' . $order_id);

                                        # Updating invalid address notification flag
                                        $order_data='';
                                        $order_data['salesorder_id'] = $order_id;
                                        $order_data['modified_date'] = date("Y-m-d H:i:s");
                                        $updated = $TGModel->updateNonUSOrderNotified($order_data);
                                    }
                                }

                            } else {
                                addLog('Non-US Order(s) Email Previously Sent.' . ' Order Id: ' . $order_id);
                            }
                            addLog('Non-US Order cannot be processed ' . ' Order Id: ' . $order_id);
                            $error[] = 'Non-US Order cannot be processed';
                        }
                        # Checking for Non-US Order

                        $UPSService = new UpsService();

                        if (empty($error)) {
                            $response = $UPSService->processShipment($sh_data);
                            
//                            list($confirm, $accept, $response) = $UPSService->prepareShipment($sh_data);

                            $invalidd_address = false;
                            
                            if (empty($response->message)) {
                                $response->message = '';
                            }

                            $pos = strpos($response->message, 'Address Validation Error');
                            
                            if ((!empty($response->status) AND $response->status == 'fail') AND $pos !== false) {
                                $invalidd_address = true;

                                if ($invalid_address_notified == '0') {
                                        if (empty($customer_email)) {
                                            addLog('Invalid Shipping Address email notification cannot be sent to customer. Incorrect or missing customer email address in sales order.' . ' Order Id: ' . $order_id);
                                        }

                                        # Sending Email To customer
                                        $email_sent = false;
                                        if (!empty(SEND_EMAIL_NOTIFICATION)) {
                                            # Sending email to office

                                            $email_template = getEmailTemplateForInvalidAddressNew();
                                            $email_template = str_replace('{customer_name}', $customer_name, $email_template);

                                            $email_template = str_replace('{attention_name}', $order_details->shipping_address->attention, $email_template);
                                            $email_template = str_replace('{address}', $order_details->shipping_address->address, $email_template);
                                            $email_template = str_replace('{street2}', $order_details->shipping_address->street2, $email_template);
                                            $email_template = str_replace('{city}', $order_details->shipping_address->city, $email_template);
                                            $email_template = str_replace('{state}', $order_details->shipping_address->state, $email_template);
                                            $email_template = str_replace('{zip}', $order_details->shipping_address->zip, $email_template);
                                            $email_template = str_replace('{country}', $order_details->shipping_address->country, $email_template);
                                            $email_template = str_replace('{fax}', $order_details->shipping_address->fax, $email_template);
                                            $email_template = str_replace('{phone}', $order_details->shipping_address->phone, $email_template);

                                            $mail = new PHPMailer();

                                            $mail->IsSMTP();

                                            $mail->Host = SMTP_HOST;
                                            $mail->SMTPAuth = SMTP_AUTH;
                                            $mail->Username = SMTP_USERNAME;
                                            $mail->Password = SMTP_PASSWORD;
                                            $mail->Port = SMTP_PORT;

                                            $mail->addAddress($customer_email, $customer_name);
                                            $mail->addAddress('office@truegridpaver.com', 'Truegrid Management');
                                            $mail->addCC('lbialas@truegridpaver.com', 'Truegrid Management');
                                            $mail->addCC('kmeinhardt@truegridpaver.com', 'Truegrid Management');

//                                            $mail->addBCC('msherazjaved@gmail.com', $warehouse_name);
//                                            $mail->addCC('marawan.aziz@gmail.com', $warehouse_name);

                                            $mail->From         = MAIL_FROM_EMAIL;
                                            $mail->FromName     = MAIL_FROM_NAME;
                                            $mail->Subject      = "TrueGrid - Invalid Shipping Address Notification";
                                            $mail->Body         = $email_template;

                                            $mail->IsHTML(true);

                                            if(!$mail->send()) {
                                                error_log('Mailer Error: ' . $mail->ErrorInfo);
                                                addLog('Email not sent. ' . $mail->ErrorInfo);
                                            } else {
                                                # Updating email_sent date-time in database
                                                $email_sent = true;
                                                addLog('Invalid Shipping Address Email Sent.' . ' Order Id: ' . $order_id);

                                                # Updating invalid address notification flag
                                                $order_data='';
                                                $order_data['salesorder_id'] = $order_id;
                                                $order_data['modified_date'] = date("Y-m-d H:i:s");
                                                $updated = $TGModel->updateUPSShipmentInvalidAddressNotified($order_data);
                                            }
                                        }
                                } else {
                                    addLog('Invalid Shipping Address Email Previously Sent.' . ' Order Id: ' . $order_id);
                                }
                            }
                        } else {
                           addLog('Shipment process stopped for Order Id: ' . $order_id . '.');
                           exit();
                        }

                        if (!empty($response->Response->ResponseStatus->Description)
                                AND $response->Response->ResponseStatus->Description == 'Success'
                                AND !empty($response->ShipmentResults->ShipmentIdentificationNumber)) {

                            if (sizeof($response->ShipmentResults->PackageResults) == 1) {
                                $packageResults[] = $response->ShipmentResults->PackageResults;
                            } else {
                                $packageResults = $response->ShipmentResults->PackageResults;
                            }

                            if (!empty($packageResults)) {
                                $count = 0;
                                foreach ($packageResults as $packageResult) {
                                    $count++;
                                    $tracking_number[$count] = $packageResult->TrackingNumber;
                                    $label_image_format[$count] = $packageResult->ShippingLabel->ImageFormat->Code;
                                    $label_image_string[$count] = $packageResult->ShippingLabel->GraphicImage;
                                }
                            }

                            if (!empty($tracking_number[1])) {
                                addLog('Shipment Order created at UPS with Tracking Number : ' . $tracking_number[1] . ' Order Id: ' . $order_id);

                                # Updating ups_package_created for corresponding salesorder
                                $order_data='';
                                $order_data['salesorder_id'] = $order_id;
                                $order_data['modified_date'] = date("Y-m-d H:i:s");
                                $updated = $TGModel->updateUPSShipmentCreated($order_data);
                            } else {
                                addLog('Shipment Order created at UPS' . ' Order Id: ' . $order_id);
                            }

                            # Creating Label File PDF
                            $file_lb = "Label-$order_number.pdf";
                            $dompdf = new Dompdf();
                            $html = get_html_for_label($label_image_string);

                            $dompdf->loadHtml($html);
                            $dompdf->setPaper('A4', 'portrait');
                            $dompdf->render();
                            $output = $dompdf->output();
                            if (file_exists(DOCUMENTS_DIR . $file_lb)) {
                                unlink(DOCUMENTS_DIR . $file_lb);
                            }
                            $label_created = file_put_contents(DOCUMENTS_DIR . $file_lb, $output);

                            if ($label_created) {
                                addLog('Label PDF successfully created.' . ' Order Id: ' . $order_id);
                            } else {
                                addLog('Label PDF not created.' . ' Order Id: ' . $order_id);
                            }

                            # Creating a Shipment Package in Zoho CRM
                            $shipment_number = '';
                            #date(DateTime::ISO8601, strtotime("2017-07-05"));
                            $package_date = date('Y-m-d');
                            $package_data['date'] = $package_date;
                            $package_data['line_items'] = $line_items;
                            $package_data['notes'] = $order_details->notes;

                            $new_package = $ZHService->createNewPackage($package_data, $order_id);

                            if ((isset($new_package->code) AND (int)$new_package->code < 1) AND !empty($new_package->package)) {

                                $package_info = $new_package->package;

                                $package_number = $package_info->package_number;
                                $package_ids = $package_info->package_id;
                                $template_id = $package_info->template_id;
                                $shipment_order = $package_info->shipment_order;

                                addLog('Package Successfully created at Zoho with package id: ' . $package_info->package_id . ' and shipment number ' . $shipment_order->shipment_number . '.');
                                # Updating zoho_package_created for corresponding salesorder
                                $order_data='';
                                $order_data['salesorder_id'] = $order_id;
                                $order_data['modified_date'] = date("Y-m-d H:i:s");
                                $updated = $TGModel->updateZohoPackageCreated($order_data);


                                $shipment_id = $shipment_order->shipment_id;
                                $shipment_carrier = $shipment_order->delivery_method;
                                $shipment_number = $shipment_order->shipment_number;

                                if (empty($shipment_number)) {
                                    addLog('Shipment number not generated by Zoho API in Package create process.' . ' Order Id: ' . $order_id);
                                }

                                # Creating Shipment Order in Zoho CRM
                                $ship_order['shipment_number'] = $shipment_number;
                                $ship_order['date'] = $package_date;
                                $ship_order['delivery_method'] =  'UPS';//$shipment_carrier;
                                $ship_order['tracking_number'] = $tracking_number[1];  # Taking the first tracking number
                                $ship_order['shipping_charge'] = '';
                                $ship_order['exchange_rate'] = '';
                                $ship_order['template_id'] = $template_id;
                                $ship_order['notes'] = '';

                                $shipment_order_info = $ZHService->createNewShipmentOrder($ship_order, $package_ids, $order_id);

                                ## Need to log the response received in : $shipment_order_info->shipment_order

                                if ((isset($shipment_order_info->code) AND (int)$shipment_order_info->code < 1) AND !empty($shipment_order_info->shipmentorder)) {
                                    addLog('Shipment order created successfully.' . ' Order Id: ' . $order_id);

                                    # Updating zoho_shipment_created for corresponding salesorder
                                    $order_data='';
                                    $order_data['salesorder_id'] = $order_id;
                                    $order_data['modified_date'] = date("Y-m-d H:i:s");
                                    $updated = $TGModel->updateZohoShipmentCreated($order_data);
                                } else {
                                    addLog('Shipment order not created. Shipment Process stopped for Order Id: ' . $order_id);
                                    exit();
                                }
                            } else {
                                addLog('Package not created at Zoho. Shipment Process stopped for Order Id: ' . $order_id);
                                addLog('Zoho Response: ' . json_encode($new_package));
                                exit();
                            }

                            # Creating Packaging File PDF
                            $file_pkg = "Packaging_Slip-$order_number.pdf";
                            if (file_exists(DOCUMENTS_DIR . $file_pkg)) {
                                unlink(DOCUMENTS_DIR . $file_pkg);
                            }
                            $items = $ZHService->printPackageSlip(array('package_ids'=>$package_ids), DOCUMENTS_DIR . $file_pkg);

                            if ($items) {
                                addLog('Package Slip created successfully. Order Id: ' . $order_id);
                            } else {
                                addLog('Package Slip not created. Order Id: ' . $order_id);
                            }

                            # Updating UPS Information for corresponding salesorder
                            $order_data='';
                            $order_data['salesorder_id'] = $order_id;
                            $order_data['label_file'] = $file_lb;
                            $order_data['packaging_slip_file'] = $file_pkg;
                            $order_data['ups_tracking_number'] = $tracking_number[1]; # Taking the first tracking number
                            $order_data['ups_confirm_response'] = '';
                            $order_data['ups_accept_response'] = '';
                            $order_data['modified_date'] = date("Y-m-d H:i:s");
                            $updated = $TGModel->updateUPSResponse($order_data);

                            if ($updated) {
                                addLog('Order record (Order Id : ' . $order_id . ') updated in database.');
                            } else {
                                addLog('Order record (Order Id : ' . $order_id . ') not updated in database.');
                            }

                            # Sending Email To Warehouse
                            $email_sent = false;
                            if (!empty(SEND_EMAIL_NOTIFICATION)) {
                                unset($email_template);

                                $email_template = getEmailTemplateForWarehouseNew($items_for_email);
                                $email_template = str_replace('{order_id}', $order_id, $email_template);

                                $email_template = str_replace('{tracking_number}', $tracking_number[1], $email_template);
                                $email_template = str_replace('{package_number}', $package_number, $email_template);

                                $email_template = str_replace('{s_attention}', $order_details->shipping_address->attention, $email_template);
                                $email_template = str_replace('{s_address}', $order_details->shipping_address->address, $email_template);
                                $email_template = str_replace('{s_street2}', $order_details->shipping_address->street2, $email_template);
                                $email_template = str_replace('{s_city}', $order_details->shipping_address->city, $email_template);
                                $email_template = str_replace('{s_state}', $order_details->shipping_address->state, $email_template);
                                $email_template = str_replace('{s_zip}', $order_details->shipping_address->zip, $email_template);
                                $email_template = str_replace('{s_country}', $order_details->shipping_address->country, $email_template);
                                $email_template = str_replace('{s_fax}', $order_details->shipping_address->fax, $email_template);
                                $email_template = str_replace('{s_phone}', $order_details->shipping_address->phone, $email_template);

                                $email_template = str_replace('{zoho_salesorder_link}', 'https://inventory.zoho.com/app#/salesorders/' . $order_id, $email_template);

                                $mail = new PHPMailer();

                                $mail->IsSMTP();

                                $mail->Host = SMTP_HOST;
                                $mail->SMTPAuth = SMTP_AUTH;
                                $mail->Username = SMTP_USERNAME;
                                $mail->Password = SMTP_PASSWORD;
                                $mail->Port = SMTP_PORT;

                                $mail->addAddress($warehouse_email, $warehouse_name);
                                $mail->addCC('kmeinhardt@truegridpaver.com', $warehouse_name);

//                                $mail->addBCC('msherazjaved@gmail.com', $warehouse_name);
//                                $mail->addCC('marawan.aziz@gmail.com', $warehouse_name);

                                $mail->From         = MAIL_FROM_EMAIL;
                                $mail->FromName     = MAIL_FROM_NAME;
                                $mail->Subject      = "TrueGrid Shipment Details For Warehouse Purpose";
                                $mail->Body         = $email_template;

                                $mail->IsHTML(true);

                                $mail->addAttachment(DOCUMENTS_DIR . $file_lb);
                                $mail->addAttachment(DOCUMENTS_DIR . $file_pkg);

                                if(!$mail->send()) {
                                    error_log('Mailer Error: ' . $mail->ErrorInfo);
                                    addLog('Email not sent. ' . $mail->ErrorInfo . 'Order Id: ' . $order_id);
                                } else {
                                    # Updating email_sent date-time in database
                                    $email_sent = true;
                                    addLog('Email sent.' . ' Order Id: ' . $order_id);
                                }

                                unset($email_template);

                                # Sending email to office
                                $email_template = getEmailTemplateForOfficeNew($items_for_email);
                                $email_template = str_replace('{order_id}', $order_id, $email_template);

                                $email_template = str_replace('{tracking_number}', $tracking_number[1], $email_template);
                                $email_template = str_replace('{package_number}', $package_number, $email_template);

                                $email_template = str_replace('{b_attention}', $order_details->billing_address->attention, $email_template);
                                $email_template = str_replace('{b_address}', $order_details->billing_address->address, $email_template);
                                $email_template = str_replace('{b_street2}', $order_details->billing_address->street2, $email_template);
                                $email_template = str_replace('{b_city}', $order_details->billing_address->city, $email_template);
                                $email_template = str_replace('{b_state}', $order_details->billing_address->state, $email_template);
                                $email_template = str_replace('{b_zip}', $order_details->billing_address->zip, $email_template);
                                $email_template = str_replace('{b_country}', $order_details->billing_address->country, $email_template);
                                $email_template = str_replace('{b_fax}', $order_details->billing_address->fax, $email_template);
                                $email_template = str_replace('{b_phone}', $order_details->billing_address->phone, $email_template);

                                $email_template = str_replace('{s_attention}', $order_details->shipping_address->attention, $email_template);
                                $email_template = str_replace('{s_address}', $order_details->shipping_address->address, $email_template);
                                $email_template = str_replace('{s_street2}', $order_details->shipping_address->street2, $email_template);
                                $email_template = str_replace('{s_city}', $order_details->shipping_address->city, $email_template);
                                $email_template = str_replace('{s_state}', $order_details->shipping_address->state, $email_template);
                                $email_template = str_replace('{s_zip}', $order_details->shipping_address->zip, $email_template);
                                $email_template = str_replace('{s_country}', $order_details->shipping_address->country, $email_template);
                                $email_template = str_replace('{s_fax}', $order_details->shipping_address->fax, $email_template);
                                $email_template = str_replace('{s_phone}', $order_details->shipping_address->phone, $email_template);

                                $email_template = str_replace('{zoho_salesorder_link}', 'https://inventory.zoho.com/app#/salesorders/' . $order_id . '?filter_by=Status.All&per_page=200&sort_column=created_time&sort_order=D', $email_template);

                                $mail = new PHPMailer();

                                $mail->IsSMTP();

                                $mail->Host = SMTP_HOST;
                                $mail->SMTPAuth = SMTP_AUTH;
                                $mail->Username = SMTP_USERNAME;
                                $mail->Password = SMTP_PASSWORD;
                                $mail->Port = SMTP_PORT;

                                $mail->addAddress('office@truegridpaver.com', 'Truegrid Management');

                                $mail->addCC('lbialas@truegridpaver.com', 'Truegrid Management');
                                $mail->addCC('kmeinhardt@truegridpaver.com', 'Truegrid Management');
//                                $mail->addBCC('msherazjaved@gmail.com', $warehouse_name);
//                                $mail->addCC('marawan.aziz@gmail.com', $warehouse_name);

                                $mail->From         = MAIL_FROM_EMAIL;
                                $mail->FromName     = MAIL_FROM_NAME;
                                $mail->Subject      = "TrueGrid Shipment Details For Office Management Purpose";
                                $mail->Body         = $email_template;

                                $mail->IsHTML(true);

                                $mail->addAttachment(DOCUMENTS_DIR . $file_lb);
                                $mail->addAttachment(DOCUMENTS_DIR . $file_pkg);

                                if(!$mail->send()) {
                                    error_log('Mailer Error: ' . $mail->ErrorInfo);
                                    addLog('Email not sent. ' . $mail->ErrorInfo);
                                } else {
                                    # Updating email_sent date-time in database
                                    $email_sent = true;
                                    addLog('Email sent.');
                                }
                            }

                            # Updating Email Sent date in database
                            # TG-STATIC
                            if (!empty($email_sent)) {
                                $email_data['salesorder_id'] = $order_id;
                                $email_data['email_sent_date'] = date("Y-m-d H:i:s");
                                $emailSent = $TGModel->updateEmailSent($email_data);

                                if ($emailSent) {
                                    addLog('Email sent date updated in database.' . ' Order Id: ' . $order_id);
                                } else {
                                    addLog('Email sent date not updated in database.' . ' Order Id: ' . $order_id);
                                }
                            }
                        } else {
                            addLog('Shipment not created at UPS, Shipment process stopped for Order Id : ' . $order_id);
                            exit();
                        }
                    }
                }
            } else {
                addLog('No Orders to process. Order Id: ' . $order_id);
            }

            addLog('Shipment process completed.' . ' Order Id: ' . $order_id);
        } catch (Exception $exc) {
            addLog('Shipment process not completed. ' . $exc->getMessage());
            error_log($exc->getTraceAsString());
        }

        exit();
    }

    if ($process == 'shipment_fix') {
        try {
            addLog('Order Fix : Shipment process started.');
            $error = array();
            # Retrieving Sales Orders from TG Integration Database
            # $stored_sales_orders = $TGModel->findAllShipmentFix();

            $stored_sales_orders = $TGModel->findAllFix(array('salesorder_id'=>$order_id));

            # $stored_sales_orders = $TGModel->findAllForShipmentProcess();

            $states = $TGModel->findAllStates();

            if (!empty($stored_sales_orders)) {
                foreach ($stored_sales_orders as $stored_sales_order) {
                    $customer_email = get_var($stored_sales_order,'salesorder_customer_email', array());
                    $customer_name = get_var($stored_sales_order,'salesorder_customer_name', array());
                    $reference_number = get_var($stored_sales_order,'salesorder_reference_number', array());
                    $order_id = get_var($stored_sales_order,'salesorder_id',0);
                    $order_number = get_var($stored_sales_order,'salesorder_number',0);
                    $invalid_address_notified = get_var($stored_sales_order,'invalid_address_notified',0);
                    $tracking_number = get_var($stored_sales_order,'ups_tracking_number',0);

                    if (!empty($order_id)) {
                        $order_details = $ZHService->getOrderDetails(array('order_id'=>$order_id));

                        $customer_details = $ZHService->getContactDetails(array('contact_id'=>$order_details->customer_id))->contact;

                        $organization_details = $ZHService->getOrganization(array('organization_id'=>ZOHO_ORGANIZATION_ID))->organization;
                        $warehouse = getWarehouseFromOrderDetails($order_details);
                        $warehouse_id = $warehouse->warehouse_id;
                        $warehouse_email = $warehouse->email;
                        $warehouse_name = $warehouse->warehouse_name;
                        $warehouse_state_code = getStateCodeByName($warehouse->state, $states);
                        $warehouse_country_code = getCountryCodeByName($warehouse->country);

                        $weight = 0;
                        $height = 0;
                        $length = 0;
                        $width = 0;
                        $line_items = array();
                        $so_line_items = $order_details->line_items;
                        $total_qty = 0;
                        $items_for_email = array();
                        if (!empty($so_line_items)) {
                            $line_items_packages = array();
                            $li = 0;
                            $lic = 0;
                            $total_weight = 0;
                            foreach ($so_line_items as $so_line_item) {
                                if (!empty($so_line_item->item_id) AND !empty($so_line_item->name) AND !empty($so_line_item->quantity)) {
                                    $li++;
                                    $line_items[$lic]['so_line_item_id'] =  $so_line_item->line_item_id;
                                    $line_items[$lic]['quantity'] =  $so_line_item->quantity;

                                    # Fetching Item Details from Zoho Inventory
                                    $item = $ZHService->getItemDetails(array('item_id'=>$so_line_item->item_id));

                                    # Fetching Product Details from Zoho CRM using $item->item->zcrm_product_id
                                    $product = $ZHService->getProduct(array('product_id'=>$item->item->zcrm_product_id));
                                    #addLog(DOCUMENTS_DIR . 'log.txt', json_encode($product), FILE_APPEND | LOCK_EX);


                                    if (isset($product['Box Height'])
                                        AND isset($product['Box Width'])
                                        AND isset($product['Box Length'])) {
                                            $length = $product['Box Length'];
                                            $width = $product['Box Width'];
                                            $height = $product['Box Height'];
                                        } else {
                                            if (isset($product['Product Length'])) {
                                                $length = $product['Product Length'];
                                            } else {
                                               $length = '';
                                            }

                                            if (isset($product['Product Width'])) {
                                                $width = $product['Product Width'];
                                            } else {
                                               $width = '';
                                            }

                                            if (isset($product['Product Height'])) {
                                                $height = $product['Product Height'];
                                            } else {
                                               $height = '';
                                            }
                                        }

                                    # Setting Total Weight and Quantity
                                    $product_weight = $product['Shipping Weight'];
                                    $no_qty = (int)$line_items[$lic]['quantity'];

                                    # Setting Total Quantity
                                    $total_qty += $no_qty;

                                    if (!empty($product['Product Name'])) {
                                        $product_name = $product['Product Name'];
                                    } else {
                                        $product_name = '';
                                    }

                                    # Creating Number of packages based on number of quanitiy in line items

                                    for ($p=1;$p<=$no_qty;$p++) {
                                        $line_items_packages[$li][$p]['length'] = $length;
                                        $line_items_packages[$li][$p]['width'] = $width;
                                        $line_items_packages[$li][$p]['height'] = $height;
                                        $line_items_packages[$li][$p]['weight'] = $product_weight;
                                        $line_items_packages[$li][$p]['package_type'] = \Ups\Entity\PackagingType::PT_PACKAGE; # Package Type
                                        $line_items_packages[$li][$p]['w_unit'] = \Ups\Entity\UnitOfMeasurement::UOM_LBS; # Weight Unit
                                        $line_items_packages[$li][$p]['d_unit'] = \Ups\Entity\UnitOfMeasurement::UOM_IN; # Dimensions Unit
                                        $line_items_packages[$li][$p]['p_description'] = $product_name; # Description

                                        # Setting Total Weight
                                        $total_weight += $product_weight;
                                    }
                                }

                                $line_items[$lic]['item_name'] = $so_line_item->name;
                                $line_items[$lic]['rate'] = $so_line_item->rate;

                                $items_for_email[] = $line_items[$lic];

                                unset($line_items[$lic]['item_name']);
                                unset($line_items[$lic]['rate']);

                                $lic++;
                            }
                        }

                        # Modifying product weight if overall quantity reaches 3 and $products total weight is between 135 lbs to 150 lbs inclusive.
                        if ($total_qty == 3) {
                            if ((int)$total_weight >= 135 OR (int)$total_weight <= 150) {
                                $product_weight = 50;
                                foreach ($line_items_packages as $kli=>$li) {
                                    foreach ($li as $kp=>$p) {
                                        $line_items_packages[$kli][$kp]['weight'] = 50;
                                    }
                                }
                                # $packaging_mode = 'Ground Freight Packaging';
                            } else if ((int)$calc_weight < 135) {
                                # $packaging_mode = 'Small Pack Packaging';
                            }
                        } else if ($total_qty > 3) {
                            # $packaging_mode = 'Ground Freight Packaging';
                        } else if ($total_qty < 3) {
                            # $packaging_mode = 'Small Pack Packaging';
                        }

                        ## TG-STATIC : Static values should be replaced with dynamic variables on production
                        $description = !empty($order_details->notes) ? $order_details->notes : 'Description not available';
                        ## TG-STATIC :

                        $sh_data['order_id'] = $order_id;

                        $shipper['number'] = UPS_ACCOUNT_NUMBER;
                        $shipper['name'] = $warehouse->warehouse_name;
                        $shipper['attentionName'] = $organization_details->contact_name;
                        $shipper['addressLine1'] = $warehouse->address;
                        $shipper['postalCode'] = $warehouse->zip;
                        $shipper['city'] = $warehouse->city;
                        $shipper['stateProvinceCode'] = $warehouse_state_code;
                        $shipper['countryCode'] = 'US';
                        $shipper['emailAddress'] = $warehouse->email;
                        $shipper['phoneNumber'] = $warehouse->phone;

                        $sh_data['shipper'] = $shipper;

                        $fromAddress['addressLine1'] = $warehouse->address;
                        $fromAddress['postalCode'] = $warehouse->zip;
                        $fromAddress['city'] = $warehouse->city;
                        $fromAddress['countryCode'] = 'US';
                        $fromAddress['stateProvinceCode'] = strtoupper($warehouse_state_code);
                        $fromAddress['name'] = $warehouse->warehouse_name;
                        $fromAddress['emailAddress'] = $warehouse->email;
                        $fromAddress['phoneNumber'] = $warehouse->phone;

                        $sh_data['from_address'] = $fromAddress;

                        $toAddress = getToAddressFromOrderDetails(array('order_details'=>$order_details, 'customer_details'=>$customer_details, 'states'=>$states, 'customer_email'=>$customer_email));
                        $soldTo = getSoldToAddressFromOrderDetails(array('order_details'=>$order_details, 'customer_details'=>$customer_details, 'states'=>$states, 'customer_email'=>$customer_email));

                        if (!empty($toAddress)) {
                            $sh_data['to_address'] = $toAddress;
                        } else {
                            $sh_data['to_address'] = '';
                            addLog('Order Fix : Missing Shipping and Billing address in sales order details, to address cannot be prepared for UPS API' . ' Order Id: ' . $order_id);
                            $error[] = 'Missing Shipping and Billing address in sales order details, to address cannot be prepared for UPS API';
                        }

                        if (!empty($soldTo)) {
                            $sh_data['sold_to'] = $soldTo;
                        } else {
                            $sh_data['sold_to'] = '';
                            addLog('Order Fix : Missing Shipping and Billing address sales order details, sold to address cannot be prepared for UPS API' . ' Order Id: ' . $order_id);
                            $error[] = 'Missing Shipping and Billing address in sales order details, sold to address cannot be prepared for UPS API';
                        }

                        $sh_data['description'] = $description;
                        $sh_data['line_items_packages'] = $line_items_packages;
                        $sh_data['shipment_date'] = date("Y-m-d H:i:s");


                        $UPSService = new UpsService();

                        if (empty($error)) {
                            list($confirm, $accept, $response) = $UPSService->prepareShipment($sh_data);

                            $invalidd_address = false;

                            $pos = strpos($response['message'], 'Failure: Address Validation');
                            if ((!empty($response['status']) AND $response['status'] == 'fail') AND $pos !== false) {
                                $invalidd_address = true;

                                addLog('Order Fix : Invalid Shipping Address.' . ' Order Id: ' . $order_id);
                            }
                        } else {
                           addLog('Order Fix : Shipment process stopped for Order Id: ' . $order_id . '.');
                           exit();
                        }

                        if (!empty($confirm->Response->ResponseStatusDescription)
                                AND $confirm->Response->ResponseStatusDescription == 'Success'
                                AND !empty($confirm->ShipmentIdentificationNumber)
                                AND !empty($confirm->ShipmentDigest)) {

                            if (sizeof($accept->PackageResults) == 1) {
                                $packageResults[] = $accept->PackageResults;
                            } else {
                                $packageResults = $accept->PackageResults;
                            }

                            if (!empty($packageResults)) {
                                $count = 0;
                                foreach ($packageResults as $packageResult) {
                                    $count++;
                                    //$tracking_number[$count] = $packageResult->TrackingNumber;
                                    $label_image_format[$count] = $packageResult->LabelImage->LabelImageFormat->Code;
                                    $label_image_string[$count] = $packageResult->LabelImage->GraphicImage;
                                }
                            }

                            if (!empty($tracking_number)) {
                                addLog('Order Fix : Shipment Order created at UPS with Tracking Number : ' . $tracking_number . ' Order Id: ' . $order_id);

                                # Updating ups_package_created for corresponding salesorder
                                $order_data='';
                                $order_data['salesorder_id'] = $order_id;
                                $order_data['modified_date'] = date("Y-m-d H:i:s");
                                $updated = $TGModel->updateUPSShipmentCreated($order_data);
                            } else {
                                addLog('Order Fix : Shipment Order created at UPS' . ' Order Id: ' . $order_id);
                            }

                            # Creating Label File PDF
                            $file_lb = "Label-$order_number.pdf";
//                            $dompdf = new Dompdf();
//                            $html = get_html_for_label($label_image_string);
//
//                            $dompdf->loadHtml($html);
//                            $dompdf->setPaper('A4', 'portrait');
//                            $dompdf->render();
//                            $output = $dompdf->output();
//                            if (file_exists(DOCUMENTS_DIR . $file_lb)) {
//                                unlink(DOCUMENTS_DIR . $file_lb);
//                            }
//                            $label_created = file_put_contents(DOCUMENTS_DIR . $file_lb, $output);
//
//                            if ($label_created) {
//                                addLog('Order Fix : Label PDF successfully created.' . ' Order Id: ' . $order_id);
//                            } else {
//                                addLog('Order Fix : Label PDF not created.' . ' Order Id: ' . $order_id);
//                            }

                            # Creating a Shipment Package in Zoho CRM
                            $shipment_number = '';
                            #date(DateTime::ISO8601, strtotime("2017-07-05"));
                            $package_date = date('Y-m-d');
                            $package_data['date'] = $package_date;
                            $package_data['line_items'] = $line_items;
                            $package_data['notes'] = $order_details->notes;

                            $new_package = $ZHService->createNewPackage($package_data, $order_id);

                            if ((isset($new_package->code) AND (int)$new_package->code < 1) AND !empty($new_package->package)) {

                                $package_info = $new_package->package;

                                $package_number = $package_info->package_number;
                                $package_ids = $package_info->package_id;
                                $template_id = $package_info->template_id;
                                $shipment_order = $package_info->shipment_order;

                                addLog('Order Fix : Package Successfully created at Zoho with package id: ' . $package_info->package_id . ' and shipment number ' . $shipment_order->shipment_number . '.');
                                # Updating zoho_package_created for corresponding salesorder
                                $order_data='';
                                $order_data['salesorder_id'] = $order_id;
                                $order_data['modified_date'] = date("Y-m-d H:i:s");
                                $updated = $TGModel->updateZohoPackageCreated($order_data);


                                $shipment_id = $shipment_order->shipment_id;
                                $shipment_carrier = $shipment_order->delivery_method;
                                $shipment_number = $shipment_order->shipment_number;

                                if (empty($shipment_number)) {
                                    addLog('Order Fix : Shipment number not generated by Zoho API in Package create process.' . ' Order Id: ' . $order_id);
                                }

                                # Creating Shipment Order in Zoho CRM
                                $ship_order['shipment_number'] = $shipment_number;
                                $ship_order['date'] = $package_date;
                                $ship_order['delivery_method'] = $shipment_carrier;
                                $ship_order['tracking_number'] = $tracking_number;
                                $ship_order['shipping_charge'] = '';
                                $ship_order['exchange_rate'] = '';
                                $ship_order['template_id'] = $template_id;
                                $ship_order['notes'] = '';

                                $shipment_order_info = $ZHService->createNewShipmentOrder($ship_order, $package_ids, $order_id);

                                ## Need to log the response received in : $shipment_order_info->shipment_order

                                if ((isset($shipment_order_info->code) AND (int)$shipment_order_info->code < 1) AND !empty($shipment_order_info->shipmentorder)) {
                                    addLog('Order Fix : Shipment order created successfully.' . ' Order Id: ' . $order_id);

                                    # Updating zoho_shipment_created for corresponding salesorder
                                    $order_data='';
                                    $order_data['salesorder_id'] = $order_id;
                                    $order_data['modified_date'] = date("Y-m-d H:i:s");
                                    $updated = $TGModel->updateZohoShipmentCreated($order_data);
                                } else {
                                    addLog('Order Fix : Shipment order not created. Shipment Process stopped for Order Id: ' . $order_id);
                                    exit();
                                }
                            } else {
                                addLog('Order Fix : Package not created at Zoho. Shipment Process stopped for Order Id: ' . $order_id);
                                addLog('Zoho Response: ' . json_encode($new_package));
                                exit();
                            }

                            # Creating Packaging File PDF
                            $file_pkg = "Packaging_Slip-$order_number.pdf";
                            if (file_exists(DOCUMENTS_DIR . $file_pkg)) {
                                unlink(DOCUMENTS_DIR . $file_pkg);
                            }
                            $items = $ZHService->printPackageSlip(array('package_ids'=>$package_ids), DOCUMENTS_DIR . $file_pkg);

                            if ($items) {
                                addLog('Order Fix : Package Slip created successfully. Order Id: ' . $order_id);
                            } else {
                                addLog('Order Fix : Package Slip not created. Order Id: ' . $order_id);
                            }

                            # Updating UPS Information for corresponding salesorder
                            $order_data='';
                            $order_data['salesorder_id'] = $order_id;
                            $order_data['label_file'] = $file_lb;
                            $order_data['packaging_slip_file'] = $file_pkg;
                            $order_data['ups_tracking_number'] = $tracking_number;
                            $order_data['ups_confirm_response'] = '';
                            $order_data['ups_accept_response'] = '';
                            $order_data['modified_date'] = date("Y-m-d H:i:s");
                            $updated = $TGModel->updateUPSResponse($order_data);

                            if ($updated) {
                                addLog('Order Fix : Order record (Order Id : ' . $order_id . ') updated in database.');
                            } else {
                                addLog('Order Fix : Order record (Order Id : ' . $order_id . ') not updated in database.');
                            }

                            # Sending Email To Warehouse
                            $email_sent = false;
                            if (!empty(SEND_EMAIL_NOTIFICATION)) {
                                unset($email_template);

                                $email_template = getEmailTemplateForWarehouseNew($items_for_email);
                                $email_template = str_replace('{order_id}', $order_id, $email_template);

                                $email_template = str_replace('{tracking_number}', $tracking_number, $email_template);
                                $email_template = str_replace('{package_number}', $package_number, $email_template);

                                $email_template = str_replace('{s_attention}', $order_details->shipping_address->attention, $email_template);
                                $email_template = str_replace('{s_address}', $order_details->shipping_address->address, $email_template);
                                $email_template = str_replace('{s_street2}', $order_details->shipping_address->street2, $email_template);
                                $email_template = str_replace('{s_city}', $order_details->shipping_address->city, $email_template);
                                $email_template = str_replace('{s_state}', $order_details->shipping_address->state, $email_template);
                                $email_template = str_replace('{s_zip}', $order_details->shipping_address->zip, $email_template);
                                $email_template = str_replace('{s_country}', $order_details->shipping_address->country, $email_template);
                                $email_template = str_replace('{s_fax}', $order_details->shipping_address->fax, $email_template);
                                $email_template = str_replace('{s_phone}', $order_details->shipping_address->phone, $email_template);

                                $email_template = str_replace('{zoho_salesorder_link}', 'https://inventory.zoho.com/app#/salesorders/' . $order_id, $email_template);

                                $mail = new PHPMailer();

                                $mail->IsSMTP();

                                $mail->Host = SMTP_HOST;
                                $mail->SMTPAuth = SMTP_AUTH;
                                $mail->Username = SMTP_USERNAME;
                                $mail->Password = SMTP_PASSWORD;
                                $mail->Port = SMTP_PORT;

                                $mail->addAddress($warehouse_email, $warehouse_name);
                                $mail->addCC('kmeinhardt@truegridpaver.com', $warehouse_name);

//                                $mail->addAddress('msherazjaved@gmail.com', $warehouse_name);
//                                $mail->addCC('marawan.aziz@gmail.com', $warehouse_name);

                                $mail->From         = MAIL_FROM_EMAIL;
                                $mail->FromName     = MAIL_FROM_NAME;
                                $mail->Subject      = "TrueGrid Shipment Details For Warehouse Purpose";
                                $mail->Body         = $email_template;

                                $mail->IsHTML(true);

                                $mail->addAttachment(DOCUMENTS_DIR . $file_lb);
                                $mail->addAttachment(DOCUMENTS_DIR . $file_pkg);

                                if(!$mail->send()) {
                                    error_log('Mailer Error: ' . $mail->ErrorInfo);
                                    addLog('Order Fix : Email not sent. ' . $mail->ErrorInfo . 'Order Id: ' . $order_id);
                                } else {
                                    # Updating email_sent date-time in database
                                    $email_sent = true;
                                    addLog('Order Fix : Email sent.' . ' Order Id: ' . $order_id);
                                }

                                unset($email_template);

                                # Sending email to office
                                $email_template = getEmailTemplateForOfficeNew($items_for_email);
                                $email_template = str_replace('{order_id}', $order_id, $email_template);

                                $email_template = str_replace('{tracking_number}', $tracking_number[1], $email_template);
                                $email_template = str_replace('{package_number}', $package_number, $email_template);

                                $email_template = str_replace('{b_attention}', $order_details->billing_address->attention, $email_template);
                                $email_template = str_replace('{b_address}', $order_details->billing_address->address, $email_template);
                                $email_template = str_replace('{b_street2}', $order_details->billing_address->street2, $email_template);
                                $email_template = str_replace('{b_city}', $order_details->billing_address->city, $email_template);
                                $email_template = str_replace('{b_state}', $order_details->billing_address->state, $email_template);
                                $email_template = str_replace('{b_zip}', $order_details->billing_address->zip, $email_template);
                                $email_template = str_replace('{b_country}', $order_details->billing_address->country, $email_template);
                                $email_template = str_replace('{b_fax}', $order_details->billing_address->fax, $email_template);
                                $email_template = str_replace('{b_phone}', $order_details->billing_address->phone, $email_template);

                                $email_template = str_replace('{s_attention}', $order_details->shipping_address->attention, $email_template);
                                $email_template = str_replace('{s_address}', $order_details->shipping_address->address, $email_template);
                                $email_template = str_replace('{s_street2}', $order_details->shipping_address->street2, $email_template);
                                $email_template = str_replace('{s_city}', $order_details->shipping_address->city, $email_template);
                                $email_template = str_replace('{s_state}', $order_details->shipping_address->state, $email_template);
                                $email_template = str_replace('{s_zip}', $order_details->shipping_address->zip, $email_template);
                                $email_template = str_replace('{s_country}', $order_details->shipping_address->country, $email_template);
                                $email_template = str_replace('{s_fax}', $order_details->shipping_address->fax, $email_template);
                                $email_template = str_replace('{s_phone}', $order_details->shipping_address->phone, $email_template);

                                $email_template = str_replace('{zoho_salesorder_link}', 'https://inventory.zoho.com/app#/salesorders/' . $order_id . '?filter_by=Status.All&per_page=200&sort_column=created_time&sort_order=D', $email_template);

                                $mail = new PHPMailer();

                                $mail->IsSMTP();

                                $mail->Host = SMTP_HOST;
                                $mail->SMTPAuth = SMTP_AUTH;
                                $mail->Username = SMTP_USERNAME;
                                $mail->Password = SMTP_PASSWORD;
                                $mail->Port = SMTP_PORT;

                                $mail->addAddress('office@truegridpaver.com', 'Truegrid Management');
//
                                $mail->addCC('lbialas@truegridpaver.com', 'Truegrid Management');
                                $mail->addCC('kmeinhardt@truegridpaver.com', 'Truegrid Management');
//                                $mail->addAddress('msherazjaved@gmail.com', $warehouse_name);
//                                $mail->addCC('marawan.aziz@gmail.com', $warehouse_name);

                                $mail->From         = MAIL_FROM_EMAIL;
                                $mail->FromName     = MAIL_FROM_NAME;
                                $mail->Subject      = "TrueGrid Shipment Details For Office Management Purpose";
                                $mail->Body         = $email_template;

                                $mail->IsHTML(true);

                                $mail->addAttachment(DOCUMENTS_DIR . $file_lb);
                                $mail->addAttachment(DOCUMENTS_DIR . $file_pkg);

                                if(!$mail->send()) {
                                    error_log('Mailer Error: ' . $mail->ErrorInfo);
                                    addLog('Order Fix : Email not sent. ' . $mail->ErrorInfo);
                                } else {
                                    # Updating email_sent date-time in database
                                    $email_sent = true;
                                    addLog('Order Fix : Email sent.');
                                }
                            }

                            # Updating Email Sent date in database
                            # TG-STATIC
                            if (!empty($email_sent)) {
                                $email_data['salesorder_id'] = $order_id;
                                $email_data['email_sent_date'] = date("Y-m-d H:i:s");
                                $emailSent = $TGModel->updateEmailSent($email_data);

                                if ($emailSent) {
                                    addLog('Order Fix : Email sent date updated in database.' . ' Order Id: ' . $order_id);
                                } else {
                                    addLog('Order Fix : Email sent date not updated in database.' . ' Order Id: ' . $order_id);
                                }
                            }
                        } else {
                            addLog('Order Fix : Shipment not created at UPS, Shipment process stopped for Order Id : ' . $order_id);
                            exit();
                        }
                    }
                }
            }

            addLog('Order Fix : Shipment process completed.' . ' Order Id: ' . $order_id);
        } catch (Exception $exc) {
            addLog('Order Fix : Shipment process not completed. ' . $exc->getMessage());
            error_log($exc->getTraceAsString());
        }

        exit();
    }

    if ($process == 'view_order') {
        try {
            $order_id = get_var($_GET,'order_id',0);

            if (!empty($order_id)) {
                $order_details = $ZHService->getOrderDetails(array('order_id'=>$order_id));
                echo "<pre>";
                print_r($order_details);
                echo "</pre>";

                exit();
            }
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
        }
    }

    if ($process == 'contact') {
        if (!empty($customer_id)) {
            $contact_details = $ZHService->getContactDetails(array('contact_id'=>$customer_id));


            echo "<pre>";
            print_r($contact_details);
            echo "</pre>";
            die();
        } else {
            throw new Exception('Customer id not provided.');
        }
    }

    if ($process == 'orders') {
        $orders = $ZHService->getSalesOrders();

        echo "<pre>";
        print_r($orders);
        echo "</pre>";
        die();
    }

    if ($process == 'update_orders') {
        $TGModel = new TGModel();

        $data = array();

        # Retrieving Sales Orders from Zoho Inventory Server
        $sales_orders = $ZHService->getSalesOrders();

        # Retrieving Sales Orders from Zoho Inventory Server
        $stored_sales_orders = $TGModel->findAllExistingNew();

        $stored_sales_order_ids = array();
        if (!empty($stored_sales_orders)) {
            foreach ($stored_sales_orders as $stored_sales_order) {
                $stored_sales_order_ids[] = $stored_sales_order['salesorder_id'];
            }
        }

        if (!empty($sales_orders)) {
            try {
                addLog('Update Orders process started.');
                $TGModel->connection->beginTransaction();
                $save_count = 0;

                foreach ($sales_orders as $order) {
                    # Only inserting orders that weren't previously existed
                    if (!in_array($order->salesorder_id, $stored_sales_order_ids) AND $order->sales_channel != 'direct_sales') {
                        # Preparing DB Values
                        $data['salesorder_id'] = $order->salesorder_id;
                        $data['salesorder_number'] = $order->salesorder_number;
                        $data['salesorder_customer_id'] = $order->customer_id;
                        $data['salesorder_customer_name'] = $order->customer_name;
                        $data['salesorder_customer_email'] = $order->email;
                        $data['salesorder_order_status'] = $order->order_status;
                        $data['salesorder_reference_number'] = $order->reference_number;
                        $data['salesorder_created_time'] = $order->created_time;
                        $data['salesorder_modified_time'] = $order->last_modified_time;
                        $data['salesorder_lineitems'] = '';
                        $data['salesorder_shipping_address'] = '';
                        $data['salesorder_billing_address'] = '';
                        $data['label_file'] = '';
                        $data['packaging_slip_file'] = '';
                        $data['tracking_number'] = '';
                        $data['email_sent_date'] = '';
                        $data['last_sync'] = date("Y-m-d H:i:s");
                        $data['created_date'] = date("Y-m-d H:i:s");

                        #Saving order to database
                        $TGModel->save($data);

                        $save_count++;
                    }
                }

                $TGModel->connection->commit();
                echo "$save_count Orders inserted in database";
                addLog("$save_count Orders inserted in database");
                addLog('Update Orders process completed.');
                exit();
            } catch (Exception $exc) {

                error_log($exc->getMessage());
                addLog('Update Orders process stopped.' . $exc->getMessage());
                $TGModel->connection->rollBack();
            }
        }

        exit();
    }

    if ($process == 'items') {
        $items = $ZHService->getItems();
        echo "<pre>";
        print_r($items);
        echo "</pre>";
        die();
    }

    if ($process == 'products') {
        $products = $ZHService->getProducts();

        echo "<pre>";
        print_r($products);
        echo "</pre>";
        die();

        die();
    }

    if ($process == 'product') {
        $product_id = get_var($_GET,'product_id',0);
        $product = $ZHService->getProduct(array('product_id'=>$product_id));

        echo "<pre>";
        print_r($product);
        echo "</pre>";
        die();
    }

    if ($process == 'view_item') {
        $item_id = !empty($_GET['item_id']) ? $_GET['item_id'] : '';
        $items = $ZHService->getItemDetails(array('item_id'=>$item_id));

        echo "<pre>";
        print_r($items);
        echo "</pre>";
        die();
    }

    if ($process == 'packages') {
        $items = $ZHService->listPackages();

        echo "<pre>";
        print_r($items);
        echo "</pre>";
        die();
    }

    if ($process == 'print_packages') {

        $org = $ZHService->getOrganization(array());

        echo "<pre>";
        print_r($org);
        echo "</pre>";
        die();
        $package_ids = !empty($_GET['package_ids']) ? $_GET['package_ids'] : '';
        $items = $ZHService->printPackages(array('package_ids'=>$package_ids));
        die();
    }

    if ($process == 'view_packages') {

        $org = $ZHService->getOrganization(array());

        $package_ids = !empty($_GET['package_id']) ? $_GET['package_id'] : '';
        $items = $ZHService->getPackage(array('package_id'=>$package_ids));

        echo "<pre>";
        print_r($items);
        echo "</pre>";
        die();
    }

    if ($process == 'crontest1') {

        addLog('Executed Cron Test 1 Inline');
        die();
    }

    if ($process == 'crontest2') {

        addLog('Executed Cron Test 2 Inline');
        die();
    }

    exit();
}

exit();
