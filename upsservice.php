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
class UpsService {

    private $accessKey = '';
    private $userId = '';
    private $password = '';
    private $useIntegration = '';

    /**
     * Class constructor
     */
    public function __construct() {
        $this->accessKey = UPS_ACCESS_KEY;
        $this->userId = UPS_USER_ID;
        $this->password = UPS_PASSWORD;
        $this->useIntegration = UPS_USE_INTEGRATION;
    }

    /**
     * validateAddress
     * To validate an address
     * @param type $params
     * @throws Exception
     */
    public function validateAddress($params = array()) {
        $address = new Ups\Entity\Address();
        $address->setAttentionName('');
        $address->setBuildingName('');
        $address->setAddressLine1('912 COBIA DR');
        $address->setStateProvinceCode('FL');
        $address->setCity('PANAMA CITY BEACH');
        $address->setCountryCode('US');
        $address->setPostalCode('32411-7928');

        try {
            if ($address instanceof Ups\Entity\Address) {
                $xav = new Ups\AddressValidation($this->accessKey, $this->userId, $this->password, $this->useIntegration);

                if ($xav instanceof Ups\AddressValidation) {
                    $xav->activateReturnObjectOnValidate(); //This is optional
                    $response = $xav->validate($address, $requestOption = Ups\AddressValidation::REQUEST_OPTION_ADDRESS_VALIDATION, $maxSuggestion = 15);

                    if ($response->noCandidates()) {
                        echo "Address is not valid";
                    } else {
                        echo "Address is valid";
                    }

                    echo "<pre>";
                    var_dump($response);
                    echo "</pre>";
                    die();
                } else {
//                    throw new Exception('Cannot connect to UPS API');
                    echo "Cannot connect to UPS API";
                }
            } else {
                echo $e->getMessage();
                addLog($e->getMessage(), 0);
                //throw new Exception('Error occured while instantiating Simple Address Object');
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            addLog($e->getMessage(), 0);
        }
    }

    public function prepareShipment($params = '') {
        $order_id = get_var($params, 'order_id', array());
        $shipper_data = get_var($params, 'shipper', array());
        $from_address_data = get_var($params, 'from_address', array());
        $to_address_data = get_var($params, 'to_address', array());
        $sold_to_data = get_var($params, 'sold_to', array());
        $description = get_var($params, 'description', '');
        $weight = get_var($params, 'weight', 0);
        $height = get_var($params, 'height', 0);
        $width = get_var($params, 'width', 0);
        $length = get_var($params, 'length', 0);
        $return = get_var($params, 'is_return', false);
        $line_items_packages = get_var($params, 'line_items_packages', array());

//        // Start shipment
//        $shipment = new Ups\Entity\Shipment;
//
//        // Set shipper
//        $shipper = $shipment->getShipper();
//        $shipper->setShipperNumber($shipper_data['number']);
//        $shipper->setName($shipper_data['name']);
//        $shipper->setAttentionName($shipper_data['attentionName']);
//        $shipperAddress = $shipper->getAddress();
//        $shipperAddress->setAddressLine1($shipper_data['addressLine1']);
//        $shipperAddress->setPostalCode($shipper_data['postalCode']);
//        $shipperAddress->setCity($shipper_data['city']);
//        $shipperAddress->setCountryCode($shipper_data['countryCode']);
//        $shipperAddress->setStateProvinceCode($shipper_data['stateProvinceCode']);
//        $shipper->setAddress($shipperAddress);
//        $shipper->setEmailAddress($shipper_data['emailAddress']);
//        $shipper->setPhoneNumber($shipper_data['phoneNumber']);
//        $shipment->setShipper($shipper);
//
//        // To address
//        $address = new \Ups\Entity\Address();
//        $address->setAddressLine1($to_address_data['addressLine1']);
//        $address->setPostalCode($to_address_data['postalCode']);
//        $address->setCity($to_address_data['city']);
//        $address->setCountryCode($to_address_data['countryCode']);
//        $address->setStateProvinceCode($to_address_data['stateProvinceCode']);
//        $shipTo = new \Ups\Entity\ShipTo();
//        $shipTo->setAddress($address);
//        $shipTo->setCompanyName($to_address_data['companyName']);
//        $shipTo->setAttentionName($to_address_data['attentionName']);
//        $shipTo->setEmailAddress($to_address_data['emailAddress']);
//        $shipTo->setPhoneNumber($to_address_data['phoneNumber']);
//        $shipment->setShipTo($shipTo);
//
//        // From address
//        $address = new \Ups\Entity\Address();
//        $address->setAddressLine1($from_address_data['addressLine1']);
//        $address->setPostalCode($from_address_data['postalCode']);
//        $address->setCity($from_address_data['city']);
//        $address->setCountryCode($from_address_data['countryCode']);
//        $address->setStateProvinceCode($from_address_data['stateProvinceCode']);
//        $shipFrom = new \Ups\Entity\ShipFrom();
//        $shipFrom->setAddress($address);
//        $shipFrom->setName($from_address_data['name']);
//        $shipFrom->setAttentionName($shipFrom->getName());
//        $shipFrom->setCompanyName($shipFrom->getName());
//        $shipFrom->setEmailAddress($from_address_data['emailAddress']);
//        $shipFrom->setPhoneNumber($from_address_data['phoneNumber']);
//        $shipment->setShipFrom($shipFrom);
//
//        // Sold to
//        $address = new \Ups\Entity\Address();
//        $address->setAddressLine1($sold_to_data['addressLine1']);
//        $address->setPostalCode($sold_to_data['postalCode']);
//        $address->setCity($sold_to_data['city']);
//        $address->setCountryCode($sold_to_data['countryCode']);
//        $address->setStateProvinceCode($sold_to_data['stateProvinceCode']);
//        $soldTo = new \Ups\Entity\SoldTo;
//        $soldTo->setAddress($address);
//        $soldTo->setAttentionName($sold_to_data['attentionName']);
//        $soldTo->setCompanyName($soldTo->getAttentionName());
//        $soldTo->setEmailAddress($sold_to_data['emailAddress']);
//        $soldTo->setPhoneNumber($sold_to_data['phoneNumber']);
//        $shipment->setSoldTo($soldTo);
//
//        // Set service
//        $service = new \Ups\Entity\Service;
//        $service->setCode(\Ups\Entity\Service::S_GROUND);
//        $service->setDescription($service->getName());
//        $shipment->setService($service);
//
//        // Mark as a return (if return)
////        if ($return) {
////            $returnService = new \Ups\Entity\ReturnService;
////            $returnService->setCode(\Ups\Entity\ReturnService::PRINT_RETURN_LABEL_PRL);
////            $shipment->setReturnService($returnService);
////        }
//        // Set description
//        $shipment->setDescription($description);
//
//        // Add Package
//        if (!empty($line_items_packages)) {
//            foreach ($line_items_packages as $line_items) {
//                foreach ($line_items as $pack) {
//                    $weight = $pack['weight'];
//                    $height = $pack['height'];
//                    $width = $pack['width'];
//                    $length = $pack['length'];
//                    $package_type = $pack['package_type'];
//                    $w_unit = $pack['w_unit'];
//                    $d_unit = $pack['d_unit'];
//                    $package_description = $pack['p_description'];
//
//                    $package = new \Ups\Entity\Package();
//                    $package->getPackagingType()->setCode($package_type);
//                    $package->getPackageWeight()->setWeight($weight);
//                    $unit = new \Ups\Entity\UnitOfMeasurement;
//                    $unit->setCode($w_unit);
//                    $package->getPackageWeight()->setUnitOfMeasurement($unit);
//
//                    // Set dimensions
//                    $dimensions = new \Ups\Entity\Dimensions();
//                    $dimensions->setHeight($height);
//                    $dimensions->setWidth($width);
//                    $dimensions->setLength($length);
//                    $unit = new \Ups\Entity\UnitOfMeasurement;
//                    $unit->setCode($d_unit);
//                    $dimensions->setUnitOfMeasurement($unit);
//                    $package->setDimensions($dimensions);
//
//                    // Add descriptions because it is a package
//                    $package->setDescription($package_description);
//
//                    // Add this package
//                    $shipment->addPackage($package);
//                }
//            }
//        }
        // Set Reference Number
//        $referenceNumber = new \Ups\Entity\ReferenceNumber;
//        if ($return) {
//            $referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_RETURN_AUTHORIZATION_NUMBER);
//            $referenceNumber->setValue($return_id);
//        } else {
//            $referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_INVOICE_NUMBER);
//            $referenceNumber->setValue($order_id);
//        }
//        $referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_INVOICE_NUMBER);
//        $referenceNumber->setValue($order_id);
//        
//        $shipment->setReferenceNumber($referenceNumber);
        // Set payment information
//        $shipment->setPaymentInformation(new \Ups\Entity\PaymentInformation('prepaid', (object) array('AccountNumber' => UPS_ACCOUNT_NUMBER)));

        // Ask for negotiated rates (optional)
//        $rateInformation = new \Ups\Entity\RateInformation;
//        $rateInformation->setNegotiatedRatesIndicator(UPS_NEGOTIATED_RATES);
//        $shipment->setRateInformation($rateInformation);

        // Get shipment info
        # $this->useIntegration = $force_integration_mode;
        $resp = array();
        try {
            $params['AccountNumber'] = UPS_ACCOUNT_NUMBER;
            $api = new Ups\FreightShipping($this->accessKey, $this->userId, $this->password, $this->useIntegration);

            $requestData = $api->confirm(\Ups\Shipping::REQ_VALIDATE, $params);

            $mode = array
            (
                 'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
                 'trace' => 1
            );
            
            // initialize soap client
            $client = new SoapClient(__DIR__ . '/vendor/gabrielbull/ups-api/src/FREIGHT-WSDL/' . 'FreightShip' . '.wsdl' , $mode);

            //set endpoint url
            $client->__setLocation('https://wwwcie.ups.com/webservices/FreightShip');            
            
            //create soap header
            $usernameToken['Username'] = $this->userId;
            $usernameToken['Password'] = $this->password;
            $serviceAccessLicense['AccessLicenseNumber'] = $this->accessKey;
            $upss['UsernameToken'] = $usernameToken;
            $upss['ServiceAccessToken'] = $serviceAccessLicense;

            $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0','UPSSecurity',$upss);
            $client->__setSoapHeaders($header);


            //get response
            @ $resp = $client->__soapCall('ProcessShipment' ,array($requestData));

            //get status
            echo "Response Status: " . $resp->Response->ResponseStatus->Description ."\n";          
            
            //save soap request and response to file
            addLog("Request: \n" . $client->__getLastRequest() . "\n");
            addLog("Response: \n" . $client->__getLastResponse() . "\n");
            
            return $response;
        } catch (\Exception $e) {
            addLog("UPS Error : " . json_encode($e->getMessage()));
            
            echo "<pre>";
            print_r($e);
            echo "</pre>";
            die();
        }
    }
    
    public function prepareShipmentTest($params = '') {
        $order_id = get_var($params, 'order_id', array());
        $shipper_data = get_var($params, 'shipper', array());
        $from_address_data = get_var($params, 'from_address', array());
        $to_address_data = get_var($params, 'to_address', array());
        $sold_to_data = get_var($params, 'sold_to', array());
        $description = get_var($params, 'description', '');
        $weight = get_var($params, 'weight', 0);
        $height = get_var($params, 'height', 0);
        $width = get_var($params, 'width', 0);
        $length = get_var($params, 'length', 0);
        $return = get_var($params, 'is_return', false);
        $line_items_packages = get_var($params, 'line_items_packages', array());
        $force_integration_mode = get_var($params, 'force_integration_mode', false);

        // Start shipment
        $shipment = new Ups\Entity\Shipment;

        // Set shipper
        $shipper = $shipment->getShipper();
        $shipper->setShipperNumber($shipper_data['number']);
        $shipper->setName($shipper_data['name']);
        $shipper->setAttentionName($shipper_data['attentionName']);
        $shipperAddress = $shipper->getAddress();
        $shipperAddress->setAddressLine1($shipper_data['addressLine1']);
        $shipperAddress->setPostalCode($shipper_data['postalCode']);
        $shipperAddress->setCity($shipper_data['city']);
        $shipperAddress->setCountryCode($shipper_data['countryCode']);
        $shipperAddress->setStateProvinceCode($shipper_data['stateProvinceCode']);
        $shipper->setAddress($shipperAddress);
        $shipper->setEmailAddress($shipper_data['emailAddress']);
        $shipper->setPhoneNumber($shipper_data['phoneNumber']);
        $shipment->setShipper($shipper);

        // To address
        $address = new \Ups\Entity\Address();
        $address->setAddressLine1($to_address_data['addressLine1']);
        $address->setPostalCode($to_address_data['postalCode']);
        $address->setCity($to_address_data['city']);
        $address->setCountryCode($to_address_data['countryCode']);
        $address->setStateProvinceCode($to_address_data['stateProvinceCode']);
        $shipTo = new \Ups\Entity\ShipTo();
        $shipTo->setAddress($address);
        $shipTo->setCompanyName($to_address_data['companyName']);
        $shipTo->setAttentionName($to_address_data['attentionName']);
        $shipTo->setEmailAddress($to_address_data['emailAddress']);
        $shipTo->setPhoneNumber($to_address_data['phoneNumber']);
        $shipment->setShipTo($shipTo);

        // From address
        $address = new \Ups\Entity\Address();
        $address->setAddressLine1($from_address_data['addressLine1']);
        $address->setPostalCode($from_address_data['postalCode']);
        $address->setCity($from_address_data['city']);
        $address->setCountryCode($from_address_data['countryCode']);
        $address->setStateProvinceCode($from_address_data['stateProvinceCode']);
        $shipFrom = new \Ups\Entity\ShipFrom();
        $shipFrom->setAddress($address);
        $shipFrom->setName($from_address_data['name']);
        $shipFrom->setAttentionName($shipFrom->getName());
        $shipFrom->setCompanyName($shipFrom->getName());
        $shipFrom->setEmailAddress($from_address_data['emailAddress']);
        $shipFrom->setPhoneNumber($from_address_data['phoneNumber']);
        $shipment->setShipFrom($shipFrom);

        // Sold to
        $address = new \Ups\Entity\Address();
        $address->setAddressLine1($sold_to_data['addressLine1']);
        $address->setPostalCode($sold_to_data['postalCode']);
        $address->setCity($sold_to_data['city']);
        $address->setCountryCode($sold_to_data['countryCode']);
        $address->setStateProvinceCode($sold_to_data['stateProvinceCode']);
        $soldTo = new \Ups\Entity\SoldTo;
        $soldTo->setAddress($address);
        $soldTo->setAttentionName($sold_to_data['attentionName']);
        $soldTo->setCompanyName($soldTo->getAttentionName());
        $soldTo->setEmailAddress($sold_to_data['emailAddress']);
        $soldTo->setPhoneNumber($sold_to_data['phoneNumber']);
        $shipment->setSoldTo($soldTo);

        // Set service
        $service = new \Ups\Entity\Service;
        $service->setCode(\Ups\Entity\Service::S_GROUND);
        $service->setDescription($service->getName());
        $shipment->setService($service);

        // Mark as a return (if return)
//        if ($return) {
//            $returnService = new \Ups\Entity\ReturnService;
//            $returnService->setCode(\Ups\Entity\ReturnService::PRINT_RETURN_LABEL_PRL);
//            $shipment->setReturnService($returnService);
//        }
        // Set description
        $shipment->setDescription($description);

        // Add Package
        if (!empty($line_items_packages)) {
            foreach ($line_items_packages as $line_items) {
                foreach ($line_items as $pack) {
                    $weight = $pack['weight'];
                    $height = $pack['height'];
                    $width = $pack['width'];
                    $length = $pack['length'];
                    $package_type = $pack['package_type'];
                    $w_unit = $pack['w_unit'];
                    $d_unit = $pack['d_unit'];
                    $package_description = $pack['p_description'];

                    $package = new \Ups\Entity\Package();
                    $package->getPackagingType()->setCode($package_type);
                    $package->getPackageWeight()->setWeight($weight);
                    $unit = new \Ups\Entity\UnitOfMeasurement;
                    $unit->setCode($w_unit);
                    $package->getPackageWeight()->setUnitOfMeasurement($unit);

                    // Set dimensions
                    $dimensions = new \Ups\Entity\Dimensions();
                    $dimensions->setHeight($height);
                    $dimensions->setWidth($width);
                    $dimensions->setLength($length);
                    $unit = new \Ups\Entity\UnitOfMeasurement;
                    $unit->setCode($d_unit);
                    $dimensions->setUnitOfMeasurement($unit);
                    $package->setDimensions($dimensions);

                    // Add descriptions because it is a package
                    $package->setDescription($package_description);

                    // Add this package
                    $shipment->addPackage($package);
                }
            }
        }
        // Set Reference Number
//        $referenceNumber = new \Ups\Entity\ReferenceNumber;
//        if ($return) {
//            $referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_RETURN_AUTHORIZATION_NUMBER);
//            $referenceNumber->setValue($return_id);
//        } else {
//            $referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_INVOICE_NUMBER);
//            $referenceNumber->setValue($order_id);
//        }
//        $referenceNumber->setCode(\Ups\Entity\ReferenceNumber::CODE_INVOICE_NUMBER);
//        $referenceNumber->setValue($order_id);
//        
//        $shipment->setReferenceNumber($referenceNumber);
        // Set payment information
        $shipment->setPaymentInformation(new \Ups\Entity\PaymentInformation('prepaid', (object) array('AccountNumber' => UPS_ACCOUNT_NUMBER)));

        // Ask for negotiated rates (optional)
        $rateInformation = new \Ups\Entity\RateInformation;
        $rateInformation->setNegotiatedRatesIndicator(UPS_NEGOTIATED_RATES);
        $shipment->setRateInformation($rateInformation);

        // Get shipment info
        $this->useIntegration = true; # This for test
        
        $resp = array();
        try {
            $api = new Ups\Shipping($this->accessKey, $this->userId, $this->password, $this->useIntegration);

            $confirm = $api->confirm(\Ups\Shipping::REQ_VALIDATE, $shipment);

            if ($confirm) {
                $resp['status'] = 'success'; 
                $accept = $api->accept($confirm->ShipmentDigest);

                return array($confirm, $accept, $resp);
            }
        } catch (\Exception $e) {
            addLog("UPS Error : " . json_encode($e->getMessage()));
            $resp['status'] = 'fail';
            $resp['code'] = $e->getCode();
            $resp['message'] = $e->getMessage();
            return array(false, false, $resp);
        }
    }

    public function getRates($params = '') {
        $shipper = $params['shipper'];
        $from = $params['from'];
        $shTo = $params['shipTo'];
        $packageType = $params['packageType'];
        $packageWeight = $params['packageWeight'];
        $wUnit = $params['weightUnit'];
        $packageHeight = $params['packageHeight'];
        $packageWidth = $params['packageWidth'];
        $packageLength = $params['packageLength'];
        $measureUnit = $params['measureUnit'];
        $serviceType = $params['serviceType'];
        $packageQty = $params['packageQty'];
        $rate = new Ups\Rate(
                $this->accessKey, $this->userId, $this->password
        );

        try {
            $shipment = new \Ups\Entity\Shipment();            
            
            $shipperAddress = $shipment->getShipper()->getAddress();
            $shipperAddress->setPostalCode($shipper['postalCode']);
            $shipperAddress->setCountryCode('US');

            $address = new \Ups\Entity\Address();
            $address->setPostalCode($from['postalCode']);
            $address->setCountryCode('US');
            $shipFrom = new \Ups\Entity\ShipFrom();
            $shipFrom->setAddress($address);

            $shipment->setShipFrom($shipFrom);

            $shipTo = $shipment->getShipTo();
            $shipTo->setCompanyName($shTo['companyName']);
            $shipToAddress = $shipTo->getAddress();
            $shipToAddress->setPostalCode($shTo['postalCode']);

            for ($p=1;$p<=$packageQty;$p++) {
                $package = new \Ups\Entity\Package();
                $package->getPackagingType()->setCode($packageType);
                $package->getPackageWeight()->setWeight($packageWeight);

                // if you need this (depends of the shipper country)
                $weightUnit = new \Ups\Entity\UnitOfMeasurement;
                $weightUnit->setCode($wUnit);
                $package->getPackageWeight()->setUnitOfMeasurement($weightUnit);

                $dimensions = new \Ups\Entity\Dimensions();
                $dimensions->setHeight($packageHeight);
                $dimensions->setWidth($packageWidth);
                $dimensions->setLength($packageLength);

                $unit = new \Ups\Entity\UnitOfMeasurement;
                $unit->setCode($measureUnit);

                $dimensions->setUnitOfMeasurement($unit);
                $package->setDimensions($dimensions);

                $shipment->addPackage($package);
            }  
       
        
        // Set service
        $service = new \Ups\Entity\Service;
        $service->setCode($serviceType);
        $service->setDescription($service->getName());
        $shipment->setService($service);  

       

            return $rate->getRate($shipment);
        } catch (Exception $e) {
            var_dump($e);
        }
    }

    public function processShipment(
        $shipment_data,
        $validation = true,
        $operation = 'ProcessShipment',
        $wsdl = '/vendor/gabrielbull/ups-api/src/FREIGHT-WSDL/Ship.wsdl'
    ) {
        $resp = '';
        $request = $this->createShipmentRequest($shipment_data, $validation);

        try {
            $mode = array
            (
                 'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
                 'trace' => 1
            );

            // initialize soap client
            $client = new SoapClient(__DIR__ . $wsdl , $mode);

            //set endpoint url
            if ($this->useIntegration) {
                $endpointurl = 'https://wwwcie.ups.com/webservices/Ship'; 
            } else {
                $endpointurl = 'https://onlinetools.ups.com/webservices/Ship';  
            }
            
            $client->__setLocation($endpointurl);

            //create soap header
            $usernameToken['Username'] = $this->userId;
            $usernameToken['Password'] = $this->password;
            $serviceAccessLicense['AccessLicenseNumber'] = $this->accessKey;
            $upss['UsernameToken'] = $usernameToken;
            $upss['ServiceAccessToken'] = $serviceAccessLicense;

            $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0','UPSSecurity',$upss);
            $client->__setSoapHeaders($header);

            if(strcmp($operation,"ProcessShipment") == 0 ) {
                //get response
                $resp = $client->__soapCall('ProcessShipment',array($request));

                //get status
//                addLog("Response Status: " . $resp->Response->ResponseStatus->Description ."\n");

                //logging soap request and response
//                addLog("Request: \n" . $client->__getLastRequest() . "\n");
//                addLog("Response: \n" . $client->__getLastResponse() . "\n");
            } else {
                addLog("Invalid Operation");
            }
        } catch(SoapFault $sf) {
            if (!empty($sf->detail->Errors->ErrorDetail->PrimaryErrorCode->Description)) {
                $resp->message = $sf->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;
            } else {
                $resp->message = $sf->getMessage();                
            }
            $resp->status = 'fail';
            
            addLog("Exception on processing shipment: " . $sf->getMessage());
            $fw = fopen(__DIR__ . "/var/logs/soap-request-" . time() . ".xml" , 'w');
            fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
            fwrite($fw , "Response: \n" . $client->__getLastResponse() . "\n");
            fclose($fw); 
        }  
        
        return $resp;        
    }
    
    private function createShipmentRequest(
        $shipment_data = array(),
        $validation = true
    ) {
        
        if ($validation) {
            $requestoption['RequestOption'] = 'validate';
            $request['Request'] = $requestoption;
        } else {
            $requestoption['RequestOption'] = 'nonvalidate';
            $request['Request'] = $requestoption;
        }
        
        $shipper_data = $shipment_data['shipper'];
        $from_address_data = $shipment_data['from_address'];
        $to_address_data = $shipment_data['to_address'];
        $sold_to_data = $shipment_data['sold_to'];
        $line_items_packages = $shipment_data['line_items_packages'];
        
        //create soap request
        $shipment['Description'] = '';
        $shipper['Name'] = $shipper_data['name'];
        $shipper['AttentionName'] = $shipper_data['attentionName'];

        $shipper['ShipperNumber'] = UPS_ACCOUNT_NUMBER;
        $address['AddressLine'] = $shipper_data['addressLine1'];
        $address['City'] = $shipper_data['city'];
        $address['StateProvinceCode'] = $shipper_data['stateProvinceCode'];
        $address['PostalCode'] = $shipper_data['postalCode'];
        $address['CountryCode'] = $shipper_data['countryCode'];
        $shipper['Address'] = $address;
        $phone['Number'] = $shipper_data['phoneNumber'];
        $shipper['Phone'] = $phone;
        $shipment['Shipper'] = $shipper;        
        
        $shipto['Name'] = $to_address_data['attentionName'];
        $shipto['AttentionName'] = $to_address_data['companyName'];
        $addressTo['AddressLine'] = $to_address_data['addressLine1'];
        $addressTo['City'] = $to_address_data['city'];
        $addressTo['StateProvinceCode'] = $to_address_data['stateProvinceCode'];
        $addressTo['PostalCode'] = $to_address_data['postalCode'];
        $addressTo['CountryCode'] = $to_address_data['countryCode'];
        $phone2['Number'] = $to_address_data['phoneNumber'];
        $shipto['Address'] = $addressTo;
        $shipto['Phone'] = $phone2;
        $shipment['ShipTo'] = $shipto;
        
        $shipfrom['Name'] = $from_address_data['name'];
        $shipfrom['AttentionName'] = $from_address_data['name'];
        $addressFrom['AddressLine'] = $from_address_data['addressLine1'];
        $addressFrom['City'] = $from_address_data['city'];
        $addressFrom['StateProvinceCode'] = $from_address_data['stateProvinceCode'];
        $addressFrom['PostalCode'] = $from_address_data['postalCode'];
        $addressFrom['CountryCode'] = $from_address_data['countryCode'];
        $phone3['Number'] = $from_address_data['phoneNumber'];
        $shipfrom['Address'] = $addressFrom;
        $shipfrom['Phone'] = $phone3;
        $shipment['ShipFrom'] = $shipfrom;   
        
        $service['Code'] = '03';
        $service['Description'] = 'Ground';
        $shipment['Service'] = $service; 
        
    if (!empty($line_items_packages)) {
        foreach ($line_items_packages as $line_items) {
            foreach ($line_items as $pack) { 
                $weight = $pack['weight'];
                $height = $pack['height'];
                $width = $pack['width'];
                $length = $pack['length'];
                $package_type = $pack['package_type'];
                $w_unit = $pack['w_unit'];
                $d_unit = $pack['d_unit'];
                $package_description = $pack['p_description'];                
                
                $package['Description'] = '';
                $packaging['Code'] = $package_type;
                $packaging['Description'] = $package_description;
                $package['Packaging'] = $packaging;
                $unit['Code'] = $d_unit;
                $unit['Description'] = 'Inches';
                $dimensions['UnitOfMeasurement'] = $unit;
                $dimensions['Length'] = $length;
                $dimensions['Width'] = $width;
                $dimensions['Height'] = $height;
                $package['Dimensions'] = $dimensions;
                $unit2['Code'] = $w_unit;
                $unit2['Description'] = 'Pounds';
                $packageweight['UnitOfMeasurement'] = $unit2;
                $packageweight['Weight'] = $weight;
                $package['PackageWeight'] = $packageweight;
                $commodity['FreightClass'] = '70';
                $package['Commodity'] = $commodity;
                $shipment['Package'][] = $package;
            }
        }
    }

        
        $shipmentRatingOptions['NegotiatedRatesIndicator'] = 1;
        
        if (!empty($shipment_data['packaging_mode'])) {
            if ($shipment_data['packaging_mode'] == 'gfp') {
                $shipmentRatingOptions['FRSShipmentIndicator'] = 1;
            } else {
                # $shipmentRatingOptions['FRSShipmentIndicator'] = 0;
            }
        } else {
            if (!empty($shipment_data['total_qty'])) {
                if ($shipment_data['total_qty'] >= 3) {
                    $shipmentRatingOptions['FRSShipmentIndicator'] = 1;
                }
            }
        }
        
        $shipment['ShipmentRatingOptions'] = $shipmentRatingOptions;
        
        if (!empty($shipment_data['packaging_mode'])) {
            if ($shipment_data['packaging_mode'] == 'gfp') {
                $frsPaymentInformation['AccountNumber'] = UPS_ACCOUNT_NUMBER;
                $frspaymentInformationType['Code'] = '01';
                $frspaymentInformationType['Description'] = 'Prepaid';
                $frsPaymentInformation['Type'] = $frspaymentInformationType;
                $shipment['FRSPaymentInformation'] = $frsPaymentInformation;
            } else {
                $paymentInformation = [];
                $paymentInformation['ShipmentCharge']['Type'] = '01';
                $paymentInformation['ShipmentCharge']['BillShipper']['AccountNumber'] = UPS_ACCOUNT_NUMBER;
                $shipment['PaymentInformation'] = $paymentInformation;
            }
        }

        $labelimageformat['Code'] = 'GIF';
        $labelimageformat['Description'] = 'GIF';
        $labelspecification['LabelImageFormat'] = $labelimageformat;
        $labelspecification['HTTPUserAgent'] = 'Mozilla/4.5';
        $shipment['LabelSpecification'] = $labelspecification;
         
        $request['Shipment'] = $shipment;
        
        return $request;
    }    
}
