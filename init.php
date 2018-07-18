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

include(__DIR__  . '/config.php');
include(__DIR__  . '/upsservice.php');
include(__DIR__  . '/zohoservice.php');

//$ZHService = new ZohoService();
//
//$orders = $ZHService->getSalesOrders();
//
//echo count($orders);

$UPSService = new UpsService();

$UPSService->validateAddress();

exit();