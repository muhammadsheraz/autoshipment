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

date_default_timezone_set('UTC');

define('CLI_ONLY', false);

define('ZOHO_ACCESS_TOKEN_INV', 'xxxxxxxxxxxxxx');
define('ZOHO_ACCESS_TOKEN_CRM', 'xxxxxxxxxxxxxx');
define('ZOHO_ACCESS_TOKEN_ZSC', 'xxxxxxxxxxxxxx');
define('ZOHO_ORGANIZATION_ID', 'xxxxxxxxxxxxxx');

define('UPS_ACCOUNT_NUMBER', 'xxxxxxxxxxxxxx');
define('UPS_ACCESS_KEY', 'xxxxxxxxxxxxxx');
define('UPS_USER_ID', 'xxxxxxxxxxxxxx');
define('UPS_PASSWORD', 'xxxxxxxxxxxxxx!');
define('UPS_USE_INTEGRATION', false);
define('UPS_NEGOTIATED_RATES', true);


define('SERVICE_ZOHO', 'z');
define('SERVICE_UPS', 'u');

define('SMTP_PORT', 25);
define('SMTP_HOST', "smtp.mailgun.org");
define('SMTP_AUTH', true);
define('SMTP_USERNAME', 'postmaster@mg.truegridpaver.com');
define('SMTP_PASSWORD', 'xxxxxxxxxxxxxx');

define('MAIL_FROM_EMAIL', 'msherazjaved@gmail.com');
define('MAIL_FROM_NAME', 'TrueGrid Inc.');

define('COMPANY_PHONE', 'xxxxxxxxxxxxxx');
define('COMPANY_EMAIL', 'xxxxxxxxxxxxxx@truegridpaver.com');

define('SEND_EMAIL_NOTIFICATION', true); # Enable/Disable email notification sending feature

define('DOCUMENTS_DIR', __DIR__ . '/documents/');
define('LOG_FILE_PATH', __DIR__ . '/var/logs/');

include(__DIR__  . '/connection.php');
include(__DIR__  . '/includes/database.php');
include(__DIR__  . '/includes/functions.php');
include(__DIR__  . '/vendor/zohoinventoryapi/ZohoClient.php');
include(__DIR__  . '/vendor/dompdf/autoload.inc.php');
include(__DIR__  . '/vendor/PHPMailer/PHPMailerAutoload.php');
include(__DIR__  . '/vendor/autoload.php');
