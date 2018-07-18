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
?>
<?php include(__DIR__  . '/../config.php'); ?>
<?php define('BASE_URL', 'http://' . $_SERVER['SERVER_NAME'] . '/tgservice/'); ?>
<?php
    $TGModel = new TGModel();
    $stored_sales_orders = $TGModel->findOrdersForDB();
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TrueGrid Shipping Itegration System</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <!-- Styles -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">

        <style type="text/css">
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: bold;
                height: 100vh;
                margin: 0;
                padding: 25px;
            }
            
            .page_title {
                padding: 5px;
            }
            
            .full-height {
                height: 100vh;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }
            select > option {
                font-weight: bold;
            }
            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            
            table tr td {
                font-family: sans-serif, Arial;
                font-weight: normal;
            }
        </style>
    </head>
    <body>
        <h1 class="page_title" style="border-bottom:1px solid grey;">TRUEGRID <span>Shipping Integration Dashboard</span></h1>
        <br>
        <div class="flex-center position-ref full-height">
            <table id="shipment_table" class="display" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Customer Email</th>
                            <th>Date Created</th>
                            <th>Tracking Number</th>
                            <th>Email Sent Date</th>
                            <th>Email Status</th>
                            <th>Labels-OrderID</th>
                            <th>Packaging_Slip-OrderID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($stored_sales_orders)) { ?>
                            <?php foreach ($stored_sales_orders as $stored_sales_order) { ?>
                                <?php
                                    $email_sent = 'Not Sent';
                                    $email_sent_date = '';
                                    if (!empty($stored_sales_order['email_sent_date']) 
                                            AND $stored_sales_order['email_sent_date'] != '0000-00-00 00:00:00') {
                                        $email_sent = 'Sent';
                                        $email_sent_date = date('m/d/Y, g:i a',strtotime($stored_sales_order['email_sent_date']));
                                    }
                                    
                                    $label_file_link = BASE_URL . "documents/" . $stored_sales_order['label_file'];
                                    $packaging_file_link = BASE_URL . "documents/" . $stored_sales_order['packaging_slip_file'];
                                    $sales_order_created_date = date('m/d/Y, g:i a',strtotime($stored_sales_order['salesorder_created_time']));
                                ?>
                                <tr>
                                    <td><?php echo $stored_sales_order['salesorder_number']?></td>
                                    <td><?php echo $stored_sales_order['salesorder_id']?></td>
                                    <td><?php echo $stored_sales_order['salesorder_customer_name']?></td>
                                    <td><?php echo $stored_sales_order['salesorder_customer_email']?></td>
                                    <td><?php echo $sales_order_created_date; ?></td>
                                    <td><?php echo $stored_sales_order['ups_tracking_number']?></td>
                                    <td><?php echo $email_sent_date; ?></td>
                                    <td><?php echo $email_sent?></td>
                                    <td>
                                        <?php if (!empty($stored_sales_order['label_file'])) { ?>
                                            <a href="<?php echo $label_file_link ?>" target="blank" title="Label PDF">
                                                <img src="images/pdf.png">
                                            </a>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($stored_sales_order['packaging_slip_file'])) { ?>
                                            <a href="<?php echo $packaging_file_link ?>" target="blank" title="Packaging Slip PDF"><img src="images/pdf.png"></a>
                                        <?php } ?>
                                    
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
            </table>
        </div>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>        
        <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>        
        <script type="text/javascript">
            $(function() {				
                $('#shipment_table').DataTable({
                    "aaSorting": []
                });
            });
        </script>        
    </body>
</html>


