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

function get_var($var, $index, $default = '') {
    if (isset($var[$index])) {
        return $var[$index];
    } else {
        return $default;
    }
}

function get_html_for_label ($encoded_image) {
    $html = '<!DOCTYPE html><html>
    <head>
    <style>
        .rotate90 {
            -webkit-transform: rotate(90deg);
            -moz-transform: rotate(90deg);
            -o-transform: rotate(90deg);
            -ms-transform: rotate(90deg);
            transform: rotate(90deg);
        }
    </style>
    </head>
    <body>';
        if (is_array(($encoded_image))) {
            $c = 0;
            foreach ($encoded_image as $image) {
                $c++;

                if ($c > 1) {
                    $html .= '<div style="page-break-before: always;"></div>';
                }

                $html .= '<br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <br>';
                $html .= '<img src="data:image/png;base64,' . $image . '" alt="" height="400" class="rotate90" />';
            }
        }
    $html .= '</body> </html>';

    return $html;
}

function get_html_for_receipt ($data = array(), $packaging_info = array()) {
    $shipper = $data['shipper'];
    $to_address = $data['to_address'];
    $weight = $packaging_info->BillingWeight->Weight;
    $uom = strtolower($packaging_info->BillingWeight->UnitOfMeasurement->Code);
    $tracking_number = strtolower($packaging_info->PackageResults->TrackingNumber);

    $html = '<!DOCTYPE html>
<html dir="ltr" lang="en">
   <head>
      <title>Shipping: UPS</title>
   </head>
   <body>
      <div id="main">
         <!-- Add List for Print Page -->
         <table>
            <tbody>
               <tr>
                  <td>
                     <h1 id="">Shipment Receipt </h1>
                  </td>
               </tr>
            </tbody>
         </table>
         <div class="appLvl mod" style="margin-bottom:0px;">
            <table class="outHozFixed1" width="100%">
               <tbody>
                  <tr valign="top">
                     <td>
                        <label id="" for="">Transaction Date:</label>
                        ' . date("F j, Y", strtotime($data['shipment_date'])) . '
                     </td>
                     <td>
                        <label id="" for="">Tracking Number:</label>
                     </td>
                     <td>
                        ' . $tracking_number . '<br/>
                     </td>
                  </tr>
               </tbody>
            </table>
            <!-- Begin Section Module -->
            <div class="secLvl step" id="addressSummary">
               <fieldset>
                  <!-- Begin Section Header -->
                  <div class="secHead clearfix">
                     <div class="stepNumberDiv"><span id="stepNumber#1" class="stepNumber">1</span></div>
                     <h3 id="">
                        Address Information
                     </h3>
                  </div>
                  <!-- End Section Header -->
                  <!-- Begin Section Body -->
                  <div class="secBody inline4col clearfix">
                     <div class="floatBegin">
                        <dl id="shipToStaticAddress">
                           <dt>
                              <label id="editShipToLabel" for="editShipTo">
                              Ship To:
                              </label>
                           </dt>
                           <dd>
                              ' . $to_address['attentionName'] . '<br>
                              ' . $to_address['addressLine1'] . '<br>
                              ' . $to_address['city'] . '&nbsp;' . $to_address['stateProvinceCode'] . '&nbsp;' . $to_address['postalCode'] . '<br>
                              Telephone:' . $to_address['phoneNumber'] . '
                              Email:' . $to_address['emailAddress'] . '
                           </dd>
                        </dl>
                     </div>
                     <div class="floatBegin">
                     </div>
                     <div class="floatBegin">
                        <dl id="shipFromStaticAddress">
                           <dt>
                              <label id="editShipFromLabel" for="editShipFrom">
                              Ship From:
                              </label>
                           </dt>
                           <dd>
                              ' . $shipper['name'] . '<br>
                              ' . $shipper['attentionName'] . '<br>
                              ' . $to_address['addressLine1'] . '<br>
                              ' . $to_address['city'] . '&nbsp;' . $to_address['stateProvinceCode'] . '&nbsp;' . $to_address['postalCode'] . '<br>
                              Telephone:' . $to_address['phoneNumber'] . '
                           </dd>
                        </dl>
                     </div>
                     <div class="floatBegin">
                        <dl id="shipperStaticAddress">
                           <dt>
                              <label id="editShipperLabel" for="editShipper">
                              Return Address:
                              </label>
                           </dt>
                           <dd>
                              ' . $shipper['name'] . '<br>
                              ' . $shipper['attentionName'] . '<br>
                              ' . $to_address['addressLine1'] . '<br>
                              ' . $to_address['city'] . '&nbsp;' . $to_address['stateProvinceCode'] . '&nbsp;' . $to_address['postalCode'] . '<br>
                              Telephone:' . $to_address['phoneNumber'] . '
                           </dd>
                        </dl>
                     </div>
                  </div>
                  <!-- End Section Body -->
               </fieldset>
            </div>
            <!-- Begin Section Module -->
            <div class="secLvl step tableonly" id="packageSummary">
               <fieldset>
                  <!-- Begin Section Header -->
                  <div class="secHead clearfix">
                     <div class="stepNumberDiv"><span id="stepNumber#2" class="stepNumber">2</span></div>
                     <h3 id="">
                        Package Information
                     </h3>
                  </div>
                  <!-- End Section Header -->
                  <!-- Begin Section Body -->
                  <div class="secBody inline4col clearfix">
                     <table border="0" cellpadding="0" cellspacing="0" class="dataTable full" width="100%"
                        summary ="Displays the Package information details for all the packages in the shipment">
                        <tr>
                           <TH scope=col class="sel alignCenter">&nbsp;</th>
                           <TH scope=col>
                              <label id="" for="">
                              Weight
                              </label>
                           </TH>
                           <TH scope=col nowrap>
                              <label id="" for="">
                              Dimensions&nbsp;/
                              Packaging
                              </label>
                           </TH>
                           <TH scope=col>
                              <label id="" for="">Declared Value</label>
                           </TH>
                           <TH scope=col class="wrap">
                              <label id="" for="">
                              Reference Numbers
                              </label>
                           </TH>
                           <!-- Only display table header if there is any package detail to display -->
                           <TH scope=col>
                              &nbsp;
                           </TH>
                        </tr>
                        <tr class="odd">
                           <td class="sel alignCenter">1.</td>
                           <td class="wrap">
                              ' . $weight . '&nbsp;' . $uom . '
                           </td>
                           <td class="nowrap">
                              UPS Letter
                           </td>
                           <td class="nowrap">
                           </td>
                           <td style="word-break: break-all">
                              Reference#1
                           </td>
                           <td>
                           </td>
                           <!-- BEGIN ER1703 -->
                           <!-- END ER1703 -->
                        </tr>
                     </table>
                  </div>
                  <!-- End Section Body -->
               </fieldset>
            </div>
            <!-- Begin Section Module -->
            <div class="secLvl step" id="serviceSummary">
               <fieldset>
                  <!-- Begin Section Header -->
                  <div class="secHead clearfix">
                     <div class="stepNumberDiv"><span id="stepNumber#3" class="stepNumber">3</span></div>
                     <h3 id="">
                        UPS Shipping Service and Shipping Options
                     </h3>
                  </div>
                  <!-- End Section Header -->
                  <!-- Begin Section Body -->
                  <div class="secBody inline4col clearfix">
                     <fieldset>
                        <!-- Begin Column -->
                        <div class="subcol10 marginBegin">
                           <dl>
                              <dt style="float:left;width:50%">
                                 <label id="" for="">Service:</label>
                              </dt>
                              <dd id="">UPS&#x20;2nd&#x20;Day&#x20;Air</dd>
                           </dl>
                           <!-- ER 7003 -->
                        </div>
                        <!-- End Column -->
                        <!-- End Column -->
                     </fieldset>
                     <!-- ER 7005 -->
                     <div class="col8 marginBegin">
                        <fieldset>
                           <dl class="outHozLedger clearfix">
                              <dt>
                                 <label id="" for="">Shipping Fees Subtotal:</label>
                              </dt>
                              <dd>
                                 <strong>
                                 20.12 USD
                                 </strong>
                              </dd>
                           </dl>
                           <div id="hideDetailsDiv">
                              <fieldset id="feesDetail">
                                 <dl class="outHozLedger indent clearfix">
                                    <dt><label id="" for="">&#xd;&#xa;&#x9;&#x9;&#x9;&#xd;&#xa;&#x9;&#x9;&#x9;&#xd;&#xa;&#x9;&#x9;
                                       Transportation
                                       </label>
                                    </dt>
                                    <dd id="">
                                       16.94 USD
                                    </dd>
                                    <dt><label id="" for="">&#xd;&#xa;&#x9;&#x9;&#x9;&#xd;&#xa;&#x9;&#x9;&#x9;&#xd;&#xa;&#x9;&#x9;
                                       Fuel Surcharge
                                       </label>
                                    </dt>
                                    <dd id="">
                                       0.73 USD
                                    </dd>
                                    <dt><label id="" for="">
                                       Delivery Area Surcharge
                                       </label>
                                    </dt>
                                    <dd id="">&nbsp;</dd>
                                    <div class="max-left">
                                       <dt><label id="" for="" class="sel">Package 1</label></dt>
                                       <dd id="">
                                          2.45 USD
                                       </dd>
                                    </div>
                                 </dl>
                              </fieldset>
                              <!-- --------------------------------------------------------- -->
                              <!-- END Shipping Fees Subtotal Expanded -->
                              <!-- --------------------------------------------------------- -->
                           </div>
                        </fieldset>
                     </div>
                  </div>
                  <!-- End Section Body -->
               </fieldset>
            </div>
            <!-- Begin Section Module -->
            <div class="secLvl step" id="serviceSummary">
               <fieldset>
                  <!-- Begin Section Header -->
                  <div class="secHead clearfix">
                     <div class="stepNumberDiv"><span id="stepNumber#4" class="stepNumber">4</span></div>
                     <h3 id="">
                        Payment Information
                     </h3>
                  </div>
                  <!-- End Section Header -->
                  <!-- Begin Section Body -->
                  <div class="secBody inline4col clearfix">
                     <dl class="outHozFixed clearfix">
                        <dt>
                           <label id="" for="">
                           Bill Shipping Charges to:
                           </label>
                        </dt>
                        <dd id="">Shipper\'s Account</dd>
                     </dl>
                     <div class="secLvl message" id="billSummary">
                        <fieldset>
                           <!-- Begin Section Body -->
                           <div class="secBody">
                              <p id="">
                              <dl class="outHozLedger clearfix totals">
                                 <dt><label id="" for="">Shipping Charges:</label></dt>
                                 <dd id="">
                                    20.12 USD
                                 </dd>
                                 <br>
                                 <table width="100%">
                                    <tr>
                                       <td>
                                          Negotiated rates were applied to this shipment.
                                       </td>
                                    </tr>
                                 </table>
                                 <br>
                                 <dl>
                                    <dt><label id="" for="">Negotiated Charges:</label></dt>
                                    <dd id="">
                                       12.74 USD
                                    </dd>
                                 </dl>
                                 <dt><label id="" for="">Subtotal Shipping Charges:</label></dt>
                                 <dd>
                                    12.74 USD
                                 </dd>
                                 <dt><label id="" for="">Total Charges:</label></dt>
                                 <dd id="">12.74 USD</dd>
                              </dl>
                              </p>
                           </div>
                           <!-- End Section Body -->
                        </fieldset>
                     </div>
                  </div>
                  <!-- End Section Body -->
               </fieldset>
            </div>
            <div>
            </div>
            <table class="note" cellSpacing="0" cellPadding="0" width="708" border="0">
               <tbody>
                  <tr>
                     <td class="reqTxtInst">
                        <span class="bold">Note:</span> This document is not an invoice. Your final invoice may vary from the displayed reference rates.<br><br>
                     </td>
                  </tr>
                  <tr>
                     <td class="reqTxtInst">
                        * For delivery and guarantee information, see the <A href="{0}" id="upsServiceGuide">UPS Service Guide</A>. To speak to a customer service representative, call 1-800-PICK-UPS for domestic services and 1-800-782-7892 for international services.
                        <br>
                        <B >Responsibility&#x20;for&#x20;Loss&#x20;or&#x20;Damage</B>
                        <br>
                        UPS&#x27;s&#x20;liability&#x20;for&#x20;loss&#x20;or&#x20;damage&#x20;to&#x20;each&#x20;domestic&#x20;package&#x20;or&#x20;international&#x20;shipment&#x20;is&#x20;limited&#x20;to&#x20;&#x24;100&#x20;without&#x20;a&#x20;declaration&#x20;of&#x20;value&#x2e;&#x20;Unless&#x20;a&#x20;greater&#x20;value&#x20;is&#x20;recorded&#x20;in&#x20;the&#x20;declared&#x20;value&#x20;field&#x20;of&#x20;the&#x20;UPS&#x20;shipping&#x20;system&#x20;used&#x2c;&#x20;the&#x20;shipper&#x20;agrees&#x20;that&#x20;the&#x20;released&#x20;value&#x20;of&#x20;each&#x20;package&#x20;covered&#x20;by&#x20;this&#x20;receipt&#x20;is&#x20;no&#x20;greater&#x20;than&#x20;&#x24;100&#x2c;&#x20;which&#x20;is&#x20;a&#x20;reasonable&#x20;value&#x20;under&#x20;the&#x20;circumstances&#x20;surrounding&#x20;the&#x20;transportation&#x2e;&#x20;To&#x20;increase&#x20;UPS&#x27;s&#x20;limit&#x20;of&#x20;liability&#x20;for&#x20;loss&#x20;or&#x20;damage&#x2c;&#x20;a&#x20;shipper&#x20;may&#x20;declare&#x20;a&#x20;higher&#x20;value&#x20;and&#x20;pay&#x20;an&#x20;additional&#x20;charge&#x2e;&#x20;See&#x20;the&#x20;UPS&#x20;Tariff&#x2f;Terms&#x20;and&#x20;Conditions&#x20;of&#x20;Service&#x20;&#x28;&quot;UPS&#x20;Terms&quot;&#x29;&#x20;at&#x20;www&#x2e;ups&#x2e;com&#x20;for&#x20;UPS&#x27;s&#x20;liability&#x20;limits&#x2c;&#x20;maximum&#x20;declared&#x20;values&#x2c;&#x20;and&#x20;other&#x20;terms&#x20;of&#x20;service&#x2e;&#x20;UPS&#x20;does&#x20;not&#x20;accept&#x20;for&#x20;transportation&#x20;and&#x20;shippers&#x20;are&#x20;prohibited&#x20;from&#x20;shipping&#x2c;&#x20;packages&#x20;with&#x20;a&#x20;value&#x20;of&#x20;more&#x20;than&#x20;&#x24;50&#x2c;000&#x2e;&#x20;&#x20;The&#x20;only&#x20;exception&#x20;to&#x20;the&#x20;&#x24;50&#x2c;000&#x20;per&#x20;package&#x20;limit&#x20;is&#x20;for&#x20;a&#x20;package&#x20;eligible&#x20;for&#x20;the&#x20;Enhanced&#x20;Maximum&#x20;Declared&#x20;Value&#x20;of&#x20;&#x24;70&#x2c;000&#x20;per&#x20;package&#x2c;&#x20;as&#x20;set&#x20;forth&#x20;in&#x20;the&#x20;UPS&#x20;Terms&#x2e;&#x20;&#x20;A&#x20;package&#x20;is&#x20;eligible&#x20;only&#x20;if&#x20;it&#x20;meets&#x20;the&#x20;following&#x20;requirements&#x2e;&#x20;&#x20;The&#x20;package&#x20;must&#x20;be&#x20;&#x28;i&#x29;&#x20;a&#x20;domestic&#x20;shipment&#x3b;&#x20;&#x28;ii&#x29;&#x20;tendered&#x20;pursuant&#x20;to&#x20;shipper&#x27;s&#x20;Scheduled&#x20;Pickup&#x20;Service&#x3b;&#x20;&#x28;iii&#x29;&#x20;a&#x20;UPS&#x20;Next&#x20;Day&#x20;Air&#x28;R&#x29;&#x20;delivery&#x20;service&#x20;is&#x20;the&#x20;service&#x20;level&#x20;selected&#x3b;&#x20;&#x28;iv&#x29;&#x20;processed&#x20;for&#x20;shipment&#x20;using&#x20;a&#x20;UPS&#x20;Shipping&#x20;System&#x20;&#x28;declarations&#x20;of&#x20;value&#x20;on&#x20;paper&#x20;Source&#x20;Documents&#x20;are&#x20;not&#x20;eligible&#x20;for&#x20;Enhanced&#x20;Maximum&#x20;Declared&#x20;Value&#x29;&#x3b;&#x20;and&#x20;&#x28;v&#x29;&#x20;does&#x20;not&#x20;contain&#x20;hazardous&#x20;material&#x20;or&#x20;a&#x20;Perishable&#x20;Commodity&#x2e;&#x20;&#x20;Claims&#x20;not&#x20;made&#x20;within&#x20;nine&#x20;months&#x20;after&#x20;delivery&#x20;of&#x20;the&#x20;package&#x20;&#x28;sixty&#x20;days&#x20;for&#x20;international&#x20;shipments&#x29;&#x2c;&#x20;or&#x20;in&#x20;the&#x20;case&#x20;of&#x20;failure&#x20;to&#x20;make&#x20;delivery&#x2c;&#x20;nine&#x20;months&#x20;after&#x20;a&#x20;reasonable&#x20;time&#x20;for&#x20;delivery&#x20;has&#x20;elapsed&#x20;&#x28;sixty&#x20;days&#x20;for&#x20;international&#x20;shipments&#x29;&#x2c;&#x20;shall&#x20;be&#x20;deemed&#x20;waived&#x2e;&#x20;The&#x20;entry&#x20;of&#x20;a&#x20;C&#x2e;O&#x2e;D&#x2e;&#x20;amount&#x20;is&#x20;not&#x20;a&#x20;declaration&#x20;of&#x20;value&#x20;for&#x20;carriage&#x20;purposes&#x2e;&#x20;All&#x20;checks&#x20;or&#x20;other&#x20;negotiable&#x20;instruments&#x20;tendered&#x20;in&#x20;payment&#x20;of&#x20;C&#x2e;O&#x2e;D&#x2e;&#x20;will&#x20;be&#x20;accepted&#x20;by&#x20;UPS&#x20;at&#x20;shipper&#x27;s&#x20;risk&#x2e;&#x20;UPS&#x20;shall&#x20;not&#x20;be&#x20;liable&#x20;for&#x20;any&#x20;special&#x2c;&#x20;incidental&#x2c;&#x20;or&#x20;consequential&#x20;damages&#x2e;&#x20;All&#x20;shipments&#x20;are&#x20;subject&#x20;to&#x20;the&#x20;terms&#x20;and&#x20;conditions&#x20;contained&#x20;in&#x20;the&#x20;UPS&#x20;Terms&#x2c;&#x20;which&#x20;can&#x20;be&#x20;found&#x20;at&#x20;www&#x2e;ups&#x2e;com&#x2e;
                        <br>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
   </body>
</html>';

    return $html;
}

function getEmailTemplateForWarehouse () {

    $template = '';
    $template .= '{to_name}';
    $template .= "<br>";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "Please find the attached shipment details.";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "<strong>Tracking Number: </strong>{tracking_number}";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "<strong>Package Number: </strong>{package_number}";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "Sincerely,";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "TrueGrid Shipment Integration System";

    return $template;
}

function getEmailTemplateForWarehouseNew ($items = array()) {

    $template = <<<EOF
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
	<head>
		<!-- NAME: 1 COLUMN - FULL WIDTH -->
		<!--[if gte mso 15]>
		<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
		</xml>
		<![endif]-->
		<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>*|MC:SUBJECT|*</title>
        
    <style type="text/css">
		p{
			margin:10px 0;
			padding:0;
		}
		table{
			border-collapse:collapse;
		}
		h1,h2,h3,h4,h5,h6{
			display:block;
			margin:0;
			padding:0;
		}
		img,a img{
			border:0;
			height:auto;
			outline:none;
			text-decoration:none;
		}
		body,#bodyTable,#bodyCell{
			height:100%;
			margin:0;
			padding:0;
			width:100%;
		}
		.mcnPreviewText{
			display:none !important;
		}
		#outlook a{
			padding:0;
		}
		img{
			-ms-interpolation-mode:bicubic;
		}
		table{
			mso-table-lspace:0pt;
			mso-table-rspace:0pt;
		}
		.ReadMsgBody{
			width:100%;
		}
		.ExternalClass{
			width:100%;
		}
		p,a,li,td,blockquote{
			mso-line-height-rule:exactly;
		}
		a[href^=tel],a[href^=sms]{
			color:inherit;
			cursor:default;
			text-decoration:none;
		}
		p,a,li,td,body,table,blockquote{
			-ms-text-size-adjust:100%;
			-webkit-text-size-adjust:100%;
		}
		.ExternalClass,.ExternalClass p,.ExternalClass td,.ExternalClass div,.ExternalClass span,.ExternalClass font{
			line-height:100%;
		}
		a[x-apple-data-detectors]{
			color:inherit !important;
			text-decoration:none !important;
			font-size:inherit !important;
			font-family:inherit !important;
			font-weight:inherit !important;
			line-height:inherit !important;
		}
		.templateContainer{
			max-width:600px !important;
		}
		a.mcnButton{
			display:block;
		}
		.mcnImage{
			vertical-align:bottom;
		}
		.mcnTextContent{
			word-break:break-word;
		}
		.mcnTextContent img{
			height:auto !important;
		}
		.mcnDividerBlock{
			table-layout:fixed !important;
		}
	/*
	@tab Page
	@section Background Style
	@tip Set the background color and top border for your email. You may want to choose colors that match your company's branding.
	*/
		body,#bodyTable{
			/*@editable*/background-color:#FAFAFA;
		}
	/*
	@tab Page
	@section Background Style
	@tip Set the background color and top border for your email. You may want to choose colors that match your company's branding.
	*/
		#bodyCell{
			/*@editable*/border-top:0;
		}
	/*
	@tab Page
	@section Heading 1
	@tip Set the styling for all first-level headings in your emails. These should be the largest of your headings.
	@style heading 1
	*/
		h1{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:26px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Page
	@section Heading 2
	@tip Set the styling for all second-level headings in your emails.
	@style heading 2
	*/
		h2{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:22px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Page
	@section Heading 3
	@tip Set the styling for all third-level headings in your emails.
	@style heading 3
	*/
		h3{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:20px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Page
	@section Heading 4
	@tip Set the styling for all fourth-level headings in your emails. These should be the smallest of your headings.
	@style heading 4
	*/
		h4{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:18px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Preheader
	@section Preheader Style
	@tip Set the background color and borders for your email's preheader area.
	*/
		#templatePreheader{
			/*@editable*/background-color:#FAFAFA;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:9px;
		}
	/*
	@tab Preheader
	@section Preheader Text
	@tip Set the styling for your email's preheader text. Choose a size and color that is easy to read.
	*/
		#templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
			/*@editable*/color:#656565;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:12px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:left;
		}
	/*
	@tab Preheader
	@section Preheader Link
	@tip Set the styling for your email's preheader links. Choose a color that helps them stand out from your text.
	*/
		#templatePreheader .mcnTextContent a,#templatePreheader .mcnTextContent p a{
			/*@editable*/color:#656565;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	/*
	@tab Header
	@section Header Style
	@tip Set the background color and borders for your email's header area.
	*/
		#templateHeader{
			/*@editable*/background-color:#ffffff;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:0;
		}
	/*
	@tab Header
	@section Header Text
	@tip Set the styling for your email's header text. Choose a size and color that is easy to read.
	*/
		#templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:16px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:left;
		}
	/*
	@tab Header
	@section Header Link
	@tip Set the styling for your email's header links. Choose a color that helps them stand out from your text.
	*/
		#templateHeader .mcnTextContent a,#templateHeader .mcnTextContent p a{
			/*@editable*/color:#2BAADF;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	/*
	@tab Body
	@section Body Style
	@tip Set the background color and borders for your email's body area.
	*/
		#templateBody{
			/*@editable*/background-color:#f5f5f5;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:9px;
		}
	/*
	@tab Body
	@section Body Text
	@tip Set the styling for your email's body text. Choose a size and color that is easy to read.
	*/
		#templateBody .mcnTextContent,#templateBody .mcnTextContent p{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:16px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:left;
		}
	/*
	@tab Body
	@section Body Link
	@tip Set the styling for your email's body links. Choose a color that helps them stand out from your text.
	*/
		#templateBody .mcnTextContent a,#templateBody .mcnTextContent p a{
			/*@editable*/color:#2BAADF;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	/*
	@tab Footer
	@section Footer Style
	@tip Set the background color and borders for your email's footer area.
	*/
		#templateFooter{
			/*@editable*/background-color:#FAFAFA;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:9px;
		}
	/*
	@tab Footer
	@section Footer Text
	@tip Set the styling for your email's footer text. Choose a size and color that is easy to read.
	*/
		#templateFooter .mcnTextContent,#templateFooter .mcnTextContent p{
			/*@editable*/color:#656565;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:12px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:center;
		}
	/*
	@tab Footer
	@section Footer Link
	@tip Set the styling for your email's footer links. Choose a color that helps them stand out from your text.
	*/
		#templateFooter .mcnTextContent a,#templateFooter .mcnTextContent p a{
			/*@editable*/color:#656565;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	@media only screen and (min-width:768px){
		.templateContainer{
			width:600px !important;
		}

}	@media only screen and (max-width: 480px){
		body,table,td,p,a,li,blockquote{
			-webkit-text-size-adjust:none !important;
		}

}	@media only screen and (max-width: 480px){
		body{
			width:100% !important;
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		#bodyCell{
			padding-top:10px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImage{
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer{
			max-width:100% !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnBoxedTextContentContainer{
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupContent{
			padding:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCaptionLeftContentOuter .mcnTextContent,.mcnCaptionRightContentOuter .mcnTextContent{
			padding-top:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardTopImageContent,.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{
			padding-top:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardBottomImageContent{
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockInner{
			padding-top:0 !important;
			padding-bottom:0 !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockOuter{
			padding-top:9px !important;
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnTextContent,.mcnBoxedTextContentColumn{
			padding-right:18px !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{
			padding-right:18px !important;
			padding-bottom:0 !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcpreview-image-uploader{
			display:none !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 1
	@tip Make the first-level headings larger in size for better readability on small screens.
	*/
		h1{
			/*@editable*/font-size:22px !important;
			/*@editable*/line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 2
	@tip Make the second-level headings larger in size for better readability on small screens.
	*/
		h2{
			/*@editable*/font-size:20px !important;
			/*@editable*/line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 3
	@tip Make the third-level headings larger in size for better readability on small screens.
	*/
		h3{
			/*@editable*/font-size:18px !important;
			/*@editable*/line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 4
	@tip Make the fourth-level headings larger in size for better readability on small screens.
	*/
		h4{
			/*@editable*/font-size:16px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Boxed Text
	@tip Make the boxed text larger in size for better readability on small screens. We recommend a font size of at least 16px.
	*/
		.mcnBoxedTextContentContainer .mcnTextContent,.mcnBoxedTextContentContainer .mcnTextContent p{
			/*@editable*/font-size:14px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Preheader Visibility
	@tip Set the visibility of the email's preheader on small screens. You can hide it to save space.
	*/
		#templatePreheader{
			/*@editable*/display:block !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Preheader Text
	@tip Make the preheader text larger in size for better readability on small screens.
	*/
		#templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
			/*@editable*/font-size:14px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Header Text
	@tip Make the header text larger in size for better readability on small screens.
	*/
		#templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
			/*@editable*/font-size:16px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Body Text
	@tip Make the body text larger in size for better readability on small screens. We recommend a font size of at least 16px.
	*/
		#templateBody .mcnTextContent,#templateBody .mcnTextContent p{
			/*@editable*/font-size:16px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Footer Text
	@tip Make the footer content text larger in size for better readability on small screens.
	*/
		#templateFooter .mcnTextContent,#templateFooter .mcnTextContent p{
			/*@editable*/font-size:14px !important;
			/*@editable*/line-height:150% !important;
		}

}
    
.order-table {
	background: white;
    margin-bottom: 15px;
    width: 98%;
}
.mcnTextContent h4 {
	margin-bottom: 5px;
    text-align: center;
}
.address-table tbody tr td {

}

.order-table tbody tr td:nth-child(1) {
	width: 20%;
}
.order-table tbody tr td:nth-child(2) {
	width: 80%;
}
.address-table, .order-table td {
    border: 1px dotted #999999;
    padding: 5px 10px
}

.address-table td {
	border: 1px dotted #999999;
    padding: 5px 10px;
}

.address-table{
	margin: 0 auto;
    background: #fff;
}

.col_2 {
	width: 48%;
    display: inline-block;
}
   </style></head>
    <body>
		<!--*|IF:MC_PREVIEW_TEXT|*-->
		<!--[if !gte mso 9]><!----><span class="mcnPreviewText" style="display:none; font-size:0px; line-height:0px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; visibility:hidden; mso-hide:all;">*|MC_PREVIEW_TEXT|*</span><!--<![endif]-->
		<!--*|END:IF|*-->
        <center>
            <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
                <tr>
                    <td align="center" valign="top" id="bodyCell">
                        <!-- BEGIN TEMPLATE // -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
								<td align="center" valign="top" id="templatePreheader">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="preheaderContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
              	<!--[if mso]>
				<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign="top" width="600" style="width:600px;">
				<![endif]-->
                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px; text-align: center;">
                        
                            <a href="*|ARCHIVE|*" target="_blank">View this email in your browser</a>
                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
							<tr>
								<td align="center" valign="top" id="templateHeader">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="headerContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width:100%;">
    <tbody class="mcnImageBlockOuter">
            <tr>
                <td valign="top" style="padding:9px" class="mcnImageBlockInner">
                    <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
                        <tbody><tr>
                            <td class="mcnImageContent" valign="top" style="padding-right: 9px; padding-left: 9px; padding-top: 0; padding-bottom: 0; text-align:center;">
                                
                                    
                                        <img align="center" alt="" src="https://gallery.mailchimp.com/3e449f22c104a6993fd50325f/images/1852f359-8b48-426d-9497-fb78a82b61c9.png" width="300" style="max-width:300px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                                    
                                
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
							<tr>
								<td align="center" valign="top" id="templateBody">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="bodyContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
              	<!--[if mso]>
				<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign="top" width="600" style="width:600px;">
				<![endif]-->
                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;">
                        
                            <h1><font size="4"><strong>New Order {order_id}</strong></font></h1>
Please call <span _digits="18753554741" _original="1-875-355-4741" _voip="clickToDialPageHandler" style="background-position:100% 50%; background-size:12px 12px; background:transparent url(chrome-extension://ildccibmanndgalianjghiompgkcmkli/inc/img/Chrome-38.png) no-repeat right 50%; cursor:pointer; padding-right:15px" title="Click-To-Dial 1-875-355-4741">1-875-355-4743 if you have any questions.</span>
                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnCodeBlock">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                <div class="mcnTextContent">
<h4>Order Details</h4>
EOF;
            
foreach ($items as $item) {
$template .= <<<EOF
<table class="order-table">
<tbody>
<tr>
<td>Product Name:</td><td> {$item['item_name']}</td>
</tr>
<tr>
<td>Quantity:</td><td> {$item['quantity']}</td>
</tr>
<tr>
<td>Tracking Number:</td><td>{tracking_number}</td>
</tr>
<tr>
<td>Package Number:</td><td>{package_number}</td>
</tr>
</tbody>
</table>
EOF;

}

$template .= <<<EOF
</div>
            </td>
        </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnCodeBlock">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                <style>
.order-table {
	background: white;
    margin-bottom: 15px;
    width: 98%;
}
.mcnTextContent h4 {
	margin-bottom: 5px;
    text-align: center;
}
.address-table tbody tr td {

}

.order-table tbody tr td:nth-child(1) {
	width: 20%;
}
.order-table tbody tr td:nth-child(2) {
	width: 80%;
}
.address-table, .order-table td {
    border: 1px dotted #999999;
    padding: 5px 10px
}

.address-table td {
	border: 1px dotted #999999;
    padding: 5px 10px;
}

.address-table{
	margin: 0 auto;
    background: #fff;
}

.col_2 {
	width: 48%;
    display: inline-block;
}

</style>


<div class="mcnTextContent">
<div class="col_2">
<h4>Shipping Address</h4>
<table class="address-table">
<tbody>
<tr>
<td>Attention:</td><td>{s_attention}</td>
</tr>
<tr>
<td>Address:</td><td>{s_address}</td>
</tr>
<tr>
<td>Street2:</td><td>{s_street2}</td>
</tr>
<tr>
<td>City:</td><td>{s_city}</td>
</tr>
<tr>
<td>State:</td><td>{s_state}</td>
</tr>
<tr>
<td>Zip:</td><td>{s_zip}</td>
</tr>
<tr>
<td>Country:</td><td>{s_country}</td>
</tr>
<tr>
<td>Phone:</td><td>{s_phone}</td>
</tr>
</tbody>
</table>
</div>
</div>
            </td>
        </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
                            <tr>
								<td align="center" valign="top" id="templateFooter">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="footerContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width:100%;">
    <tbody class="mcnDividerBlockOuter">
        <tr>
            <td class="mcnDividerBlockInner" style="min-width: 100%; padding: 10px 18px 25px;">
                <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-top: 2px solid #EEEEEE;">
                    <tbody><tr>
                        <td>
                            <span></span>
                        </td>
                    </tr>
                </tbody></table>
<!--            
                <td class="mcnDividerBlockInner" style="padding: 18px;">
                <hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
-->
            </td>
        </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
                        </table>
                        <!-- // END TEMPLATE -->
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>

EOF;
    return $template;
}

function getEmailTemplateForOfficeNew ($items = array()) {

    $template = <<<EOF
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
	<head>
		<!-- NAME: 1 COLUMN - FULL WIDTH -->
		<!--[if gte mso 15]>
		<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
		</xml>
		<![endif]-->
		<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>*|MC:SUBJECT|*</title>
        
    <style type="text/css">
		p{
			margin:10px 0;
			padding:0;
		}
		table{
			border-collapse:collapse;
		}
		h1,h2,h3,h4,h5,h6{
			display:block;
			margin:0;
			padding:0;
		}
		img,a img{
			border:0;
			height:auto;
			outline:none;
			text-decoration:none;
		}
		body,#bodyTable,#bodyCell{
			height:100%;
			margin:0;
			padding:0;
			width:100%;
		}
		.mcnPreviewText{
			display:none !important;
		}
		#outlook a{
			padding:0;
		}
		img{
			-ms-interpolation-mode:bicubic;
		}
		table{
			mso-table-lspace:0pt;
			mso-table-rspace:0pt;
		}
		.ReadMsgBody{
			width:100%;
		}
		.ExternalClass{
			width:100%;
		}
		p,a,li,td,blockquote{
			mso-line-height-rule:exactly;
		}
		a[href^=tel],a[href^=sms]{
			color:inherit;
			cursor:default;
			text-decoration:none;
		}
		p,a,li,td,body,table,blockquote{
			-ms-text-size-adjust:100%;
			-webkit-text-size-adjust:100%;
		}
		.ExternalClass,.ExternalClass p,.ExternalClass td,.ExternalClass div,.ExternalClass span,.ExternalClass font{
			line-height:100%;
		}
		a[x-apple-data-detectors]{
			color:inherit !important;
			text-decoration:none !important;
			font-size:inherit !important;
			font-family:inherit !important;
			font-weight:inherit !important;
			line-height:inherit !important;
		}
		.templateContainer{
			max-width:600px !important;
		}
		a.mcnButton{
			display:block;
		}
		.mcnImage{
			vertical-align:bottom;
		}
		.mcnTextContent{
			word-break:break-word;
		}
		.mcnTextContent img{
			height:auto !important;
		}
		.mcnDividerBlock{
			table-layout:fixed !important;
		}
	/*
	@tab Page
	@section Background Style
	@tip Set the background color and top border for your email. You may want to choose colors that match your company's branding.
	*/
		body,#bodyTable{
			/*@editable*/background-color:#FAFAFA;
		}
	/*
	@tab Page
	@section Background Style
	@tip Set the background color and top border for your email. You may want to choose colors that match your company's branding.
	*/
		#bodyCell{
			/*@editable*/border-top:0;
		}
	/*
	@tab Page
	@section Heading 1
	@tip Set the styling for all first-level headings in your emails. These should be the largest of your headings.
	@style heading 1
	*/
		h1{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:26px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Page
	@section Heading 2
	@tip Set the styling for all second-level headings in your emails.
	@style heading 2
	*/
		h2{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:22px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Page
	@section Heading 3
	@tip Set the styling for all third-level headings in your emails.
	@style heading 3
	*/
		h3{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:20px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Page
	@section Heading 4
	@tip Set the styling for all fourth-level headings in your emails. These should be the smallest of your headings.
	@style heading 4
	*/
		h4{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:18px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Preheader
	@section Preheader Style
	@tip Set the background color and borders for your email's preheader area.
	*/
		#templatePreheader{
			/*@editable*/background-color:#FAFAFA;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:9px;
		}
	/*
	@tab Preheader
	@section Preheader Text
	@tip Set the styling for your email's preheader text. Choose a size and color that is easy to read.
	*/
		#templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
			/*@editable*/color:#656565;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:12px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:left;
		}
	/*
	@tab Preheader
	@section Preheader Link
	@tip Set the styling for your email's preheader links. Choose a color that helps them stand out from your text.
	*/
		#templatePreheader .mcnTextContent a,#templatePreheader .mcnTextContent p a{
			/*@editable*/color:#656565;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	/*
	@tab Header
	@section Header Style
	@tip Set the background color and borders for your email's header area.
	*/
		#templateHeader{
			/*@editable*/background-color:#ffffff;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:0;
		}
	/*
	@tab Header
	@section Header Text
	@tip Set the styling for your email's header text. Choose a size and color that is easy to read.
	*/
		#templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:16px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:left;
		}
	/*
	@tab Header
	@section Header Link
	@tip Set the styling for your email's header links. Choose a color that helps them stand out from your text.
	*/
		#templateHeader .mcnTextContent a,#templateHeader .mcnTextContent p a{
			/*@editable*/color:#2BAADF;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	/*
	@tab Body
	@section Body Style
	@tip Set the background color and borders for your email's body area.
	*/
		#templateBody{
			/*@editable*/background-color:#f5f5f5;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:9px;
		}
	/*
	@tab Body
	@section Body Text
	@tip Set the styling for your email's body text. Choose a size and color that is easy to read.
	*/
		#templateBody .mcnTextContent,#templateBody .mcnTextContent p{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:16px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:left;
		}
	/*
	@tab Body
	@section Body Link
	@tip Set the styling for your email's body links. Choose a color that helps them stand out from your text.
	*/
		#templateBody .mcnTextContent a,#templateBody .mcnTextContent p a{
			/*@editable*/color:#2BAADF;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	/*
	@tab Footer
	@section Footer Style
	@tip Set the background color and borders for your email's footer area.
	*/
		#templateFooter{
			/*@editable*/background-color:#FAFAFA;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:9px;
		}
	/*
	@tab Footer
	@section Footer Text
	@tip Set the styling for your email's footer text. Choose a size and color that is easy to read.
	*/
		#templateFooter .mcnTextContent,#templateFooter .mcnTextContent p{
			/*@editable*/color:#656565;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:12px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:center;
		}
	/*
	@tab Footer
	@section Footer Link
	@tip Set the styling for your email's footer links. Choose a color that helps them stand out from your text.
	*/
		#templateFooter .mcnTextContent a,#templateFooter .mcnTextContent p a{
			/*@editable*/color:#656565;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	@media only screen and (min-width:768px){
		.templateContainer{
			width:600px !important;
		}

}	@media only screen and (max-width: 480px){
		body,table,td,p,a,li,blockquote{
			-webkit-text-size-adjust:none !important;
		}

}	@media only screen and (max-width: 480px){
		body{
			width:100% !important;
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		#bodyCell{
			padding-top:10px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImage{
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer{
			max-width:100% !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnBoxedTextContentContainer{
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupContent{
			padding:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCaptionLeftContentOuter .mcnTextContent,.mcnCaptionRightContentOuter .mcnTextContent{
			padding-top:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardTopImageContent,.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{
			padding-top:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardBottomImageContent{
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockInner{
			padding-top:0 !important;
			padding-bottom:0 !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockOuter{
			padding-top:9px !important;
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnTextContent,.mcnBoxedTextContentColumn{
			padding-right:18px !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{
			padding-right:18px !important;
			padding-bottom:0 !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcpreview-image-uploader{
			display:none !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 1
	@tip Make the first-level headings larger in size for better readability on small screens.
	*/
		h1{
			/*@editable*/font-size:22px !important;
			/*@editable*/line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 2
	@tip Make the second-level headings larger in size for better readability on small screens.
	*/
		h2{
			/*@editable*/font-size:20px !important;
			/*@editable*/line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 3
	@tip Make the third-level headings larger in size for better readability on small screens.
	*/
		h3{
			/*@editable*/font-size:18px !important;
			/*@editable*/line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 4
	@tip Make the fourth-level headings larger in size for better readability on small screens.
	*/
		h4{
			/*@editable*/font-size:16px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Boxed Text
	@tip Make the boxed text larger in size for better readability on small screens. We recommend a font size of at least 16px.
	*/
		.mcnBoxedTextContentContainer .mcnTextContent,.mcnBoxedTextContentContainer .mcnTextContent p{
			/*@editable*/font-size:14px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Preheader Visibility
	@tip Set the visibility of the email's preheader on small screens. You can hide it to save space.
	*/
		#templatePreheader{
			/*@editable*/display:block !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Preheader Text
	@tip Make the preheader text larger in size for better readability on small screens.
	*/
		#templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
			/*@editable*/font-size:14px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Header Text
	@tip Make the header text larger in size for better readability on small screens.
	*/
		#templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
			/*@editable*/font-size:16px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Body Text
	@tip Make the body text larger in size for better readability on small screens. We recommend a font size of at least 16px.
	*/
		#templateBody .mcnTextContent,#templateBody .mcnTextContent p{
			/*@editable*/font-size:16px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Footer Text
	@tip Make the footer content text larger in size for better readability on small screens.
	*/
		#templateFooter .mcnTextContent,#templateFooter .mcnTextContent p{
			/*@editable*/font-size:14px !important;
			/*@editable*/line-height:150% !important;
		}

}
   
.order-table {
	background: white;
    margin-bottom: 15px;
    width: 98%;
}
.mcnTextContent h4 {
	margin-bottom: 5px;
    text-align: center;
}
.address-table tbody tr td {

}

.order-table tbody tr td:nth-child(1) {
	width: 20%;
}
.order-table tbody tr td:nth-child(2) {
	width: 80%;
}
.address-table, .order-table td {
    border: 1px dotted #999999;
    padding: 5px 10px
}

.address-table td {
	border: 1px dotted #999999;
    padding: 5px 10px;
}

.address-table{
	margin: 0 auto;
    background: #fff;
}

.col_2 {
	width: 48%;
    display: inline-block;
}
   </style></head>
    <body>
		<!--*|IF:MC_PREVIEW_TEXT|*-->
		<!--[if !gte mso 9]><!----><span class="mcnPreviewText" style="display:none; font-size:0px; line-height:0px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; visibility:hidden; mso-hide:all;">*|MC_PREVIEW_TEXT|*</span><!--<![endif]-->
		<!--*|END:IF|*-->
        <center>
            <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
                <tr>
                    <td align="center" valign="top" id="bodyCell">
                        <!-- BEGIN TEMPLATE // -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
								<td align="center" valign="top" id="templatePreheader">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="preheaderContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
              	<!--[if mso]>
				<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign="top" width="600" style="width:600px;">
				<![endif]-->
                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px; text-align: center;">
                        
                            <a href="*|ARCHIVE|*" target="_blank">View this email in your browser</a>
                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
							<tr>
								<td align="center" valign="top" id="templateHeader">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="headerContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width:100%;">
    <tbody class="mcnImageBlockOuter">
            <tr>
                <td valign="top" style="padding:9px" class="mcnImageBlockInner">
                    <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
                        <tbody><tr>
                            <td class="mcnImageContent" valign="top" style="padding-right: 9px; padding-left: 9px; padding-top: 0; padding-bottom: 0; text-align:center;">
                                
                                    
                                        <img align="center" alt="" src="https://gallery.mailchimp.com/3e449f22c104a6993fd50325f/images/1852f359-8b48-426d-9497-fb78a82b61c9.png" width="300" style="max-width:300px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                                    
                                
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
							<tr>
								<td align="center" valign="top" id="templateBody">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="bodyContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
              	<!--[if mso]>
				<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign="top" width="600" style="width:600px;">
				<![endif]-->
                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;">
                        
                            <h1>Hi Team,</h1>

<p><font size="4"><strong>Meet Order {order_id},&nbsp;</strong></font></p>

<p><a href="{zoho_salesorder_link}" target="blank">See it on Inventory</a></p>

                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnCodeBlock">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                <div class="mcnTextContent">
<h4>Order Details</h4>
EOF;
            
foreach ($items as $item) {
$template .= <<<EOF
<table class="order-table">
<tbody>
<tr>
<td>Product Name:</td><td> {$item['item_name']}</td>
</tr>
<tr>
<td>Price:</td><td> {$item['rate']}</td>
</tr>
<tr>
<td>Quantity:</td><td> {$item['quantity']}</td>
</tr>
<tr>
<td>Tracking Number:</td><td>{tracking_number}</td>
</tr>
<tr>
<td>Package Number:</td><td>{package_number}</td>
</tr>
</tbody>
</table>
EOF;

}

$template .= <<<EOF
</div>
            </td>
        </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnCodeBlock">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                <style>
.order-table {
	background: white;
    margin-bottom: 15px;
    width: 98%;
}
.mcnTextContent h4 {
	margin-bottom: 5px;
    text-align: center;
}
.address-table tbody tr td {

}

.order-table tbody tr td:nth-child(1) {
	width: 20%;
}
.order-table tbody tr td:nth-child(2) {
	width: 80%;
}
.address-table, .order-table td {
    border: 1px dotted #999999;
    padding: 5px 10px
}

.address-table td {
	border: 1px dotted #999999;
    padding: 5px 10px;
}

.address-table{
	margin: 0 auto;
    background: #fff;
}

.col_2 {
	width: 48%;
    display: inline-block;
}

</style>


<div class="mcnTextContent">
<div class="col_2">
<h4>Billing Address</h4>
<table class="address-table">
<tbody>
<tr>
<td>Attention:</td><td>{b_attention}</td>
</tr>
<tr>
<td>Address:</td><td>{b_address}</td>
</tr>
<tr>
<td>Street2:</td><td>{b_street2}</td>
</tr>
<tr>
<td>City:</td><td>{b_city}</td>
</tr>
<tr>
<td>State:</td><td>{b_state}</td>
</tr>
<tr>
<td>Zip:</td><td>{b_zip}</td>
</tr>
<tr>
<td>Country:</td><td>{b_country}</td>
</tr>
<tr>
<td>Phone:</td><td>{b_phone}</td>
</tr>
</tbody>
</table>
</div>
<div class="col_2">
<h4>Shipping Address</h4>
<table class="address-table">
<tbody>
<tr>
<td>Attention:</td><td>{s_attention}</td>
</tr>
<tr>
<td>Address:</td><td>{s_address}</td>
</tr>
<tr>
<td>Street2:</td><td>{s_street2}</td>
</tr>
<tr>
<td>City:</td><td>{s_city}</td>
</tr>
<tr>
<td>State:</td><td>{s_state}</td>
</tr>
<tr>
<td>Zip:</td><td>{s_zip}</td>
</tr>
<tr>
<td>Country:</td><td>{s_country}</td>
</tr>
<tr>
<td>Phone:</td><td>{s_phone}</td>
</tr>
</tbody>
</table>
</div>
</div>
            </td>
        </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
                            <tr>
								<td align="center" valign="top" id="templateFooter">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="footerContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width:100%;">
    <tbody class="mcnDividerBlockOuter">
        <tr>
            <td class="mcnDividerBlockInner" style="min-width: 100%; padding: 10px 18px 25px;">
                <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-top: 2px solid #EEEEEE;">
                    <tbody><tr>
                        <td>
                            <span></span>
                        </td>
                    </tr>
                </tbody></table>
<!--            
                <td class="mcnDividerBlockInner" style="padding: 18px;">
                <hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
-->
            </td>
        </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
                        </table>
                        <!-- // END TEMPLATE -->
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>

EOF;
    return $template;
}

function getNonUSEmailTemplateForOffice ($items = array()) {

    $template = <<<EOF
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
	<head>
		<!-- NAME: 1 COLUMN - FULL WIDTH -->
		<!--[if gte mso 15]>
		<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
		</xml>
		<![endif]-->
		<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>*|MC:SUBJECT|*</title>
        
    <style type="text/css">
		p{
			margin:10px 0;
			padding:0;
		}
		table{
			border-collapse:collapse;
		}
		h1,h2,h3,h4,h5,h6{
			display:block;
			margin:0;
			padding:0;
		}
		img,a img{
			border:0;
			height:auto;
			outline:none;
			text-decoration:none;
		}
		body,#bodyTable,#bodyCell{
			height:100%;
			margin:0;
			padding:0;
			width:100%;
		}
		.mcnPreviewText{
			display:none !important;
		}
		#outlook a{
			padding:0;
		}
		img{
			-ms-interpolation-mode:bicubic;
		}
		table{
			mso-table-lspace:0pt;
			mso-table-rspace:0pt;
		}
		.ReadMsgBody{
			width:100%;
		}
		.ExternalClass{
			width:100%;
		}
		p,a,li,td,blockquote{
			mso-line-height-rule:exactly;
		}
		a[href^=tel],a[href^=sms]{
			color:inherit;
			cursor:default;
			text-decoration:none;
		}
		p,a,li,td,body,table,blockquote{
			-ms-text-size-adjust:100%;
			-webkit-text-size-adjust:100%;
		}
		.ExternalClass,.ExternalClass p,.ExternalClass td,.ExternalClass div,.ExternalClass span,.ExternalClass font{
			line-height:100%;
		}
		a[x-apple-data-detectors]{
			color:inherit !important;
			text-decoration:none !important;
			font-size:inherit !important;
			font-family:inherit !important;
			font-weight:inherit !important;
			line-height:inherit !important;
		}
		.templateContainer{
			max-width:600px !important;
		}
		a.mcnButton{
			display:block;
		}
		.mcnImage{
			vertical-align:bottom;
		}
		.mcnTextContent{
			word-break:break-word;
		}
		.mcnTextContent img{
			height:auto !important;
		}
		.mcnDividerBlock{
			table-layout:fixed !important;
		}
	/*
	@tab Page
	@section Background Style
	@tip Set the background color and top border for your email. You may want to choose colors that match your company's branding.
	*/
		body,#bodyTable{
			/*@editable*/background-color:#FAFAFA;
		}
	/*
	@tab Page
	@section Background Style
	@tip Set the background color and top border for your email. You may want to choose colors that match your company's branding.
	*/
		#bodyCell{
			/*@editable*/border-top:0;
		}
	/*
	@tab Page
	@section Heading 1
	@tip Set the styling for all first-level headings in your emails. These should be the largest of your headings.
	@style heading 1
	*/
		h1{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:26px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Page
	@section Heading 2
	@tip Set the styling for all second-level headings in your emails.
	@style heading 2
	*/
		h2{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:22px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Page
	@section Heading 3
	@tip Set the styling for all third-level headings in your emails.
	@style heading 3
	*/
		h3{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:20px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Page
	@section Heading 4
	@tip Set the styling for all fourth-level headings in your emails. These should be the smallest of your headings.
	@style heading 4
	*/
		h4{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:18px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Preheader
	@section Preheader Style
	@tip Set the background color and borders for your email's preheader area.
	*/
		#templatePreheader{
			/*@editable*/background-color:#FAFAFA;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:9px;
		}
	/*
	@tab Preheader
	@section Preheader Text
	@tip Set the styling for your email's preheader text. Choose a size and color that is easy to read.
	*/
		#templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
			/*@editable*/color:#656565;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:12px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:left;
		}
	/*
	@tab Preheader
	@section Preheader Link
	@tip Set the styling for your email's preheader links. Choose a color that helps them stand out from your text.
	*/
		#templatePreheader .mcnTextContent a,#templatePreheader .mcnTextContent p a{
			/*@editable*/color:#656565;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	/*
	@tab Header
	@section Header Style
	@tip Set the background color and borders for your email's header area.
	*/
		#templateHeader{
			/*@editable*/background-color:#ffffff;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:0;
		}
	/*
	@tab Header
	@section Header Text
	@tip Set the styling for your email's header text. Choose a size and color that is easy to read.
	*/
		#templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:16px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:left;
		}
	/*
	@tab Header
	@section Header Link
	@tip Set the styling for your email's header links. Choose a color that helps them stand out from your text.
	*/
		#templateHeader .mcnTextContent a,#templateHeader .mcnTextContent p a{
			/*@editable*/color:#2BAADF;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	/*
	@tab Body
	@section Body Style
	@tip Set the background color and borders for your email's body area.
	*/
		#templateBody{
			/*@editable*/background-color:#f5f5f5;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:9px;
		}
	/*
	@tab Body
	@section Body Text
	@tip Set the styling for your email's body text. Choose a size and color that is easy to read.
	*/
		#templateBody .mcnTextContent,#templateBody .mcnTextContent p{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:16px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:left;
		}
	/*
	@tab Body
	@section Body Link
	@tip Set the styling for your email's body links. Choose a color that helps them stand out from your text.
	*/
		#templateBody .mcnTextContent a,#templateBody .mcnTextContent p a{
			/*@editable*/color:#2BAADF;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	/*
	@tab Footer
	@section Footer Style
	@tip Set the background color and borders for your email's footer area.
	*/
		#templateFooter{
			/*@editable*/background-color:#FAFAFA;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:9px;
		}
	/*
	@tab Footer
	@section Footer Text
	@tip Set the styling for your email's footer text. Choose a size and color that is easy to read.
	*/
		#templateFooter .mcnTextContent,#templateFooter .mcnTextContent p{
			/*@editable*/color:#656565;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:12px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:center;
		}
	/*
	@tab Footer
	@section Footer Link
	@tip Set the styling for your email's footer links. Choose a color that helps them stand out from your text.
	*/
		#templateFooter .mcnTextContent a,#templateFooter .mcnTextContent p a{
			/*@editable*/color:#656565;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	@media only screen and (min-width:768px){
		.templateContainer{
			width:600px !important;
		}

}	@media only screen and (max-width: 480px){
		body,table,td,p,a,li,blockquote{
			-webkit-text-size-adjust:none !important;
		}

}	@media only screen and (max-width: 480px){
		body{
			width:100% !important;
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		#bodyCell{
			padding-top:10px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImage{
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer{
			max-width:100% !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnBoxedTextContentContainer{
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupContent{
			padding:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCaptionLeftContentOuter .mcnTextContent,.mcnCaptionRightContentOuter .mcnTextContent{
			padding-top:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardTopImageContent,.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{
			padding-top:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardBottomImageContent{
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockInner{
			padding-top:0 !important;
			padding-bottom:0 !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockOuter{
			padding-top:9px !important;
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnTextContent,.mcnBoxedTextContentColumn{
			padding-right:18px !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{
			padding-right:18px !important;
			padding-bottom:0 !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcpreview-image-uploader{
			display:none !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 1
	@tip Make the first-level headings larger in size for better readability on small screens.
	*/
		h1{
			/*@editable*/font-size:22px !important;
			/*@editable*/line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 2
	@tip Make the second-level headings larger in size for better readability on small screens.
	*/
		h2{
			/*@editable*/font-size:20px !important;
			/*@editable*/line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 3
	@tip Make the third-level headings larger in size for better readability on small screens.
	*/
		h3{
			/*@editable*/font-size:18px !important;
			/*@editable*/line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 4
	@tip Make the fourth-level headings larger in size for better readability on small screens.
	*/
		h4{
			/*@editable*/font-size:16px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Boxed Text
	@tip Make the boxed text larger in size for better readability on small screens. We recommend a font size of at least 16px.
	*/
		.mcnBoxedTextContentContainer .mcnTextContent,.mcnBoxedTextContentContainer .mcnTextContent p{
			/*@editable*/font-size:14px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Preheader Visibility
	@tip Set the visibility of the email's preheader on small screens. You can hide it to save space.
	*/
		#templatePreheader{
			/*@editable*/display:block !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Preheader Text
	@tip Make the preheader text larger in size for better readability on small screens.
	*/
		#templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
			/*@editable*/font-size:14px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Header Text
	@tip Make the header text larger in size for better readability on small screens.
	*/
		#templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
			/*@editable*/font-size:16px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Body Text
	@tip Make the body text larger in size for better readability on small screens. We recommend a font size of at least 16px.
	*/
		#templateBody .mcnTextContent,#templateBody .mcnTextContent p{
			/*@editable*/font-size:16px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Footer Text
	@tip Make the footer content text larger in size for better readability on small screens.
	*/
		#templateFooter .mcnTextContent,#templateFooter .mcnTextContent p{
			/*@editable*/font-size:14px !important;
			/*@editable*/line-height:150% !important;
		}

}
   
.order-table {
	background: white;
    margin-bottom: 15px;
    width: 98%;
}
.mcnTextContent h4 {
	margin-bottom: 5px;
    text-align: center;
}
.address-table tbody tr td {

}

.order-table tbody tr td:nth-child(1) {
	width: 20%;
}
.order-table tbody tr td:nth-child(2) {
	width: 80%;
}
.address-table, .order-table td {
    border: 1px dotted #999999;
    padding: 5px 10px
}

.address-table td {
	border: 1px dotted #999999;
    padding: 5px 10px;
}

.address-table{
	margin: 0 auto;
    background: #fff;
}

.col_2 {
	width: 48%;
    display: inline-block;
}
   </style></head>
    <body>
		<!--*|IF:MC_PREVIEW_TEXT|*-->
		<!--[if !gte mso 9]><!----><span class="mcnPreviewText" style="display:none; font-size:0px; line-height:0px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; visibility:hidden; mso-hide:all;">*|MC_PREVIEW_TEXT|*</span><!--<![endif]-->
		<!--*|END:IF|*-->
        <center>
            <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
                <tr>
                    <td align="center" valign="top" id="bodyCell">
                        <!-- BEGIN TEMPLATE // -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
								<td align="center" valign="top" id="templatePreheader">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="preheaderContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
              	<!--[if mso]>
				<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign="top" width="600" style="width:600px;">
				<![endif]-->
                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px; text-align: center;">
                        
                            <a href="*|ARCHIVE|*" target="_blank">View this email in your browser</a>
                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
							<tr>
								<td align="center" valign="top" id="templateHeader">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="headerContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width:100%;">
    <tbody class="mcnImageBlockOuter">
            <tr>
                <td valign="top" style="padding:9px" class="mcnImageBlockInner">
                    <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
                        <tbody><tr>
                            <td class="mcnImageContent" valign="top" style="padding-right: 9px; padding-left: 9px; padding-top: 0; padding-bottom: 0; text-align:center;">
                                
                                    
                                        <img align="center" alt="" src="https://gallery.mailchimp.com/3e449f22c104a6993fd50325f/images/1852f359-8b48-426d-9497-fb78a82b61c9.png" width="300" style="max-width:300px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                                    
                                
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
							<tr>
								<td align="center" valign="top" id="templateBody">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="bodyContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
              	<!--[if mso]>
				<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign="top" width="600" style="width:600px;">
				<![endif]-->
                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;">
                        
                            <h1>NON-US order!</h1>

<p><font size="4"><strong>Meet Order {order_id},&nbsp;</strong></font></p>
<p>(s)he is not from around here.</p>
<p>The customer would like it shipped to {shipping_state}, {shipping_country_code}</p>
<p><a href="{zoho_salesorder_link}" target="blank">See it on Inventory</a></p>

                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnCodeBlock">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                <div class="mcnTextContent">
<h4>Order Details</h4>
EOF;
            
foreach ($items as $item) {
$template .= <<<EOF
<table class="order-table">
<tbody>
<tr>
<td>Product Name:</td><td> {$item['item_name']}</td>
</tr>
<tr>
<td>Price:</td><td> {$item['rate']}</td>
</tr>
<tr>
<td>Quantity:</td><td> {$item['quantity']}</td>
</tr>
</tbody>
</table>
EOF;

}

$template .= <<<EOF
</div>
            </td>
        </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnCodeBlock">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                <style>
.order-table {
	background: white;
    margin-bottom: 15px;
    width: 98%;
}
.mcnTextContent h4 {
	margin-bottom: 5px;
    text-align: center;
}
.address-table tbody tr td {

}

.order-table tbody tr td:nth-child(1) {
	width: 20%;
}
.order-table tbody tr td:nth-child(2) {
	width: 80%;
}
.address-table, .order-table td {
    border: 1px dotted #999999;
    padding: 5px 10px
}

.address-table td {
	border: 1px dotted #999999;
    padding: 5px 10px;
}

.address-table{
	margin: 0 auto;
    background: #fff;
}

.col_2 {
	width: 48%;
    display: inline-block;
}

</style>


<div class="mcnTextContent">
<div class="col_2">
<h4>Billing Address</h4>
<table class="address-table">
<tbody>
<tr>
<td>Attention:</td><td>{b_attention}</td>
</tr>
<tr>
<td>Address:</td><td>{b_address}</td>
</tr>
<tr>
<td>Street2:</td><td>{b_street2}</td>
</tr>
<tr>
<td>City:</td><td>{b_city}</td>
</tr>
<tr>
<td>State:</td><td>{b_state}</td>
</tr>
<tr>
<td>Zip:</td><td>{b_zip}</td>
</tr>
<tr>
<td>Country:</td><td>{b_country}</td>
</tr>
<tr>
<td>Phone:</td><td>{b_phone}</td>
</tr>
</tbody>
</table>
</div>
<div class="col_2">
<h4>Shipping Address</h4>
<table class="address-table">
<tbody>
<tr>
<td>Attention:</td><td>{s_attention}</td>
</tr>
<tr>
<td>Address:</td><td>{s_address}</td>
</tr>
<tr>
<td>Street2:</td><td>{s_street2}</td>
</tr>
<tr>
<td>City:</td><td>{s_city}</td>
</tr>
<tr>
<td>State:</td><td>{s_state}</td>
</tr>
<tr>
<td>Zip:</td><td>{s_zip}</td>
</tr>
<tr>
<td>Country:</td><td>{s_country}</td>
</tr>
<tr>
<td>Phone:</td><td>{s_phone}</td>
</tr>
</tbody>
</table>
</div>
</div>
            </td>
        </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
                            <tr>
								<td align="center" valign="top" id="templateFooter">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="footerContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width:100%;">
    <tbody class="mcnDividerBlockOuter">
        <tr>
            <td class="mcnDividerBlockInner" style="min-width: 100%; padding: 10px 18px 25px;">
                <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-top: 2px solid #EEEEEE;">
                    <tbody><tr>
                        <td>
                            <span></span>
                        </td>
                    </tr>
                </tbody></table>
<!--            
                <td class="mcnDividerBlockInner" style="padding: 18px;">
                <hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
-->
            </td>
        </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
                        </table>
                        <!-- // END TEMPLATE -->
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>

EOF;
    return $template;
}

function getEmailTemplateForOffice ($items = array()) {
    $template = '';
    $template .= '{to_name}';
    $template .= "<br>";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "Please find the attached shipment details.";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "<strong>Customer Name: </strong>{customer_name}";
    $template .= "<br>";
    $template .= "<br>";
    
    $c = 0;
    foreach ($items as $item) {
        $c++;
        $template .= "<strong>Line Item $c </strong><br>";
        $template .= "Product Name: " . $item['item_name'];
        $template .= "<br>";
        $template .= "Price: " . $item['rate'];
        $template .= "<br>";
        $template .= "Quantity: " . $item['quantity'];
        $template .= "<br>";
        $template .= "<br>";
    }
    
    $template .= "<strong>Tracking Number: </strong>{tracking_number}";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "<strong>Package Number: </strong>{package_number}";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "<strong>Shipping Address:</strong>";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "{shipping_address}";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "<strong>Billing Address:</strong>";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "{billing_address}";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "<strong>Sales Order Link</strong>";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "<a href='{zoho_salesorder_link}' target='blank' title='Sales Order Link'>{zoho_salesorder_link}</a>";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "Sincerely,";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "TrueGrid Shipment Integration System";

    return $template;
}

function getEmailTemplateForInvalidAddress () {
    $template = '';
    $template .= 'Hi {customer_name}';
    $template .= "<br>";
    $template .= "<br>";
    $template .= "<p style='font-size: 14px'><strong>Thank you for placing an order with TRUEGRID!</strong> We received your payment but need additional information before your order can be shipped.</p>";
    $template .= "<p>The shipping address you provided was invalid and needs to be updated. This is what we have in our records:</p>";
    $template .= "{shipping_address}";
    $template .= "<br>";
    $template .= "<p>Please reply to this email with an updated shipping address. You may check on the UPS website that your address is valid before submitting it.</p>";
    $template .= "<p>Once we receive a valid shipping address we will reprocess your order and send you confirmation of shipment.</p>";
    $template .= "<p>Some common reasons an address may return as invalid:</p>";
    $template .= "<ol>";
        $template .= "<li>Typo</li>";
        $template .= "<li>An address outside of the continental US (lower 48 states)</li>";
        $template .= "<li>An address with an incorrect zip code. Ensure you use a 5 digit valid zip code.</li>";
    $template .= "</ol>";
    $template .= "<p>If you have any other questions please call toll free 1-855-375-4743 or email <a href='mailto:office@truegridpaver.com' target='_top'>office@truegridpaver.com</a></p>";
    $template .= "<p>Thank you for shopping with TRUEGRID.</p>";    
    $template .= "<p>Have a wonderful day!</p>";    
    $template .= "<br>";
    $template .= "<p>Sincerely,</p>";
    $template .= "<br>";
    $template .= "<br>";
    $template .= "<stong>The TRUEGRID Team</strong>";

    return $template;
}

function getEmailTemplateForInvalidAddressNew () {
    $template = <<<EOF
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
	<head>
		<!-- NAME: 1 COLUMN - FULL WIDTH -->
		<!--[if gte mso 15]>
		<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
		</xml>
		<![endif]-->
		<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>*|MC:SUBJECT|*</title>
        
    <style type="text/css">
		p{
			margin:10px 0;
			padding:0;
		}
		table{
			border-collapse:collapse;
		}
		h1,h2,h3,h4,h5,h6{
			display:block;
			margin:0;
			padding:0;
		}
		img,a img{
			border:0;
			height:auto;
			outline:none;
			text-decoration:none;
		}
		body,#bodyTable,#bodyCell{
			height:100%;
			margin:0;
			padding:0;
			width:100%;
		}
		.mcnPreviewText{
			display:none !important;
		}
		#outlook a{
			padding:0;
		}
		img{
			-ms-interpolation-mode:bicubic;
		}
		table{
			mso-table-lspace:0pt;
			mso-table-rspace:0pt;
		}
		.ReadMsgBody{
			width:100%;
		}
		.ExternalClass{
			width:100%;
		}
		p,a,li,td,blockquote{
			mso-line-height-rule:exactly;
		}
		a[href^=tel],a[href^=sms]{
			color:inherit;
			cursor:default;
			text-decoration:none;
		}
		p,a,li,td,body,table,blockquote{
			-ms-text-size-adjust:100%;
			-webkit-text-size-adjust:100%;
		}
		.ExternalClass,.ExternalClass p,.ExternalClass td,.ExternalClass div,.ExternalClass span,.ExternalClass font{
			line-height:100%;
		}
		a[x-apple-data-detectors]{
			color:inherit !important;
			text-decoration:none !important;
			font-size:inherit !important;
			font-family:inherit !important;
			font-weight:inherit !important;
			line-height:inherit !important;
		}
		.templateContainer{
			max-width:600px !important;
		}
		a.mcnButton{
			display:block;
		}
		.mcnImage{
			vertical-align:bottom;
		}
		.mcnTextContent{
			word-break:break-word;
		}
		.mcnTextContent img{
			height:auto !important;
		}
		.mcnDividerBlock{
			table-layout:fixed !important;
		}
	/*
	@tab Page
	@section Background Style
	@tip Set the background color and top border for your email. You may want to choose colors that match your company's branding.
	*/
		body,#bodyTable{
			/*@editable*/background-color:#FAFAFA;
		}
	/*
	@tab Page
	@section Background Style
	@tip Set the background color and top border for your email. You may want to choose colors that match your company's branding.
	*/
		#bodyCell{
			/*@editable*/border-top:0;
		}
	/*
	@tab Page
	@section Heading 1
	@tip Set the styling for all first-level headings in your emails. These should be the largest of your headings.
	@style heading 1
	*/
		h1{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:26px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Page
	@section Heading 2
	@tip Set the styling for all second-level headings in your emails.
	@style heading 2
	*/
		h2{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:22px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Page
	@section Heading 3
	@tip Set the styling for all third-level headings in your emails.
	@style heading 3
	*/
		h3{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:20px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Page
	@section Heading 4
	@tip Set the styling for all fourth-level headings in your emails. These should be the smallest of your headings.
	@style heading 4
	*/
		h4{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:18px;
			/*@editable*/font-style:normal;
			/*@editable*/font-weight:bold;
			/*@editable*/line-height:125%;
			/*@editable*/letter-spacing:normal;
			/*@editable*/text-align:left;
		}
	/*
	@tab Preheader
	@section Preheader Style
	@tip Set the background color and borders for your email's preheader area.
	*/
		#templatePreheader{
			/*@editable*/background-color:#FAFAFA;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:9px;
		}
	/*
	@tab Preheader
	@section Preheader Text
	@tip Set the styling for your email's preheader text. Choose a size and color that is easy to read.
	*/
		#templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
			/*@editable*/color:#656565;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:12px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:left;
		}
	/*
	@tab Preheader
	@section Preheader Link
	@tip Set the styling for your email's preheader links. Choose a color that helps them stand out from your text.
	*/
		#templatePreheader .mcnTextContent a,#templatePreheader .mcnTextContent p a{
			/*@editable*/color:#656565;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	/*
	@tab Header
	@section Header Style
	@tip Set the background color and borders for your email's header area.
	*/
		#templateHeader{
			/*@editable*/background-color:#ffffff;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:0;
		}
	/*
	@tab Header
	@section Header Text
	@tip Set the styling for your email's header text. Choose a size and color that is easy to read.
	*/
		#templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:16px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:left;
		}
	/*
	@tab Header
	@section Header Link
	@tip Set the styling for your email's header links. Choose a color that helps them stand out from your text.
	*/
		#templateHeader .mcnTextContent a,#templateHeader .mcnTextContent p a{
			/*@editable*/color:#2BAADF;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	/*
	@tab Body
	@section Body Style
	@tip Set the background color and borders for your email's body area.
	*/
		#templateBody{
			/*@editable*/background-color:#f5f5f5;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:9px;
		}
	/*
	@tab Body
	@section Body Text
	@tip Set the styling for your email's body text. Choose a size and color that is easy to read.
	*/
		#templateBody .mcnTextContent,#templateBody .mcnTextContent p{
			/*@editable*/color:#202020;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:16px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:left;
		}
	/*
	@tab Body
	@section Body Link
	@tip Set the styling for your email's body links. Choose a color that helps them stand out from your text.
	*/
		#templateBody .mcnTextContent a,#templateBody .mcnTextContent p a{
			/*@editable*/color:#2BAADF;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	/*
	@tab Footer
	@section Footer Style
	@tip Set the background color and borders for your email's footer area.
	*/
		#templateFooter{
			/*@editable*/background-color:#FAFAFA;
			/*@editable*/background-image:none;
			/*@editable*/background-repeat:no-repeat;
			/*@editable*/background-position:center;
			/*@editable*/background-size:cover;
			/*@editable*/border-top:0;
			/*@editable*/border-bottom:0;
			/*@editable*/padding-top:9px;
			/*@editable*/padding-bottom:9px;
		}
	/*
	@tab Footer
	@section Footer Text
	@tip Set the styling for your email's footer text. Choose a size and color that is easy to read.
	*/
		#templateFooter .mcnTextContent,#templateFooter .mcnTextContent p{
			/*@editable*/color:#656565;
			/*@editable*/font-family:Helvetica;
			/*@editable*/font-size:12px;
			/*@editable*/line-height:150%;
			/*@editable*/text-align:center;
		}
	/*
	@tab Footer
	@section Footer Link
	@tip Set the styling for your email's footer links. Choose a color that helps them stand out from your text.
	*/
		#templateFooter .mcnTextContent a,#templateFooter .mcnTextContent p a{
			/*@editable*/color:#656565;
			/*@editable*/font-weight:normal;
			/*@editable*/text-decoration:underline;
		}
	@media only screen and (min-width:768px){
		.templateContainer{
			width:600px !important;
		}

}	@media only screen and (max-width: 480px){
		body,table,td,p,a,li,blockquote{
			-webkit-text-size-adjust:none !important;
		}

}	@media only screen and (max-width: 480px){
		body{
			width:100% !important;
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		#bodyCell{
			padding-top:10px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImage{
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer{
			max-width:100% !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnBoxedTextContentContainer{
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupContent{
			padding:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCaptionLeftContentOuter .mcnTextContent,.mcnCaptionRightContentOuter .mcnTextContent{
			padding-top:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardTopImageContent,.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{
			padding-top:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardBottomImageContent{
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockInner{
			padding-top:0 !important;
			padding-bottom:0 !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockOuter{
			padding-top:9px !important;
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnTextContent,.mcnBoxedTextContentColumn{
			padding-right:18px !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{
			padding-right:18px !important;
			padding-bottom:0 !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcpreview-image-uploader{
			display:none !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 1
	@tip Make the first-level headings larger in size for better readability on small screens.
	*/
		h1{
			/*@editable*/font-size:22px !important;
			/*@editable*/line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 2
	@tip Make the second-level headings larger in size for better readability on small screens.
	*/
		h2{
			/*@editable*/font-size:20px !important;
			/*@editable*/line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 3
	@tip Make the third-level headings larger in size for better readability on small screens.
	*/
		h3{
			/*@editable*/font-size:18px !important;
			/*@editable*/line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Heading 4
	@tip Make the fourth-level headings larger in size for better readability on small screens.
	*/
		h4{
			/*@editable*/font-size:16px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Boxed Text
	@tip Make the boxed text larger in size for better readability on small screens. We recommend a font size of at least 16px.
	*/
		.mcnBoxedTextContentContainer .mcnTextContent,.mcnBoxedTextContentContainer .mcnTextContent p{
			/*@editable*/font-size:14px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Preheader Visibility
	@tip Set the visibility of the email's preheader on small screens. You can hide it to save space.
	*/
		#templatePreheader{
			/*@editable*/display:block !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Preheader Text
	@tip Make the preheader text larger in size for better readability on small screens.
	*/
		#templatePreheader .mcnTextContent,#templatePreheader .mcnTextContent p{
			/*@editable*/font-size:14px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Header Text
	@tip Make the header text larger in size for better readability on small screens.
	*/
		#templateHeader .mcnTextContent,#templateHeader .mcnTextContent p{
			/*@editable*/font-size:16px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Body Text
	@tip Make the body text larger in size for better readability on small screens. We recommend a font size of at least 16px.
	*/
		#templateBody .mcnTextContent,#templateBody .mcnTextContent p{
			/*@editable*/font-size:16px !important;
			/*@editable*/line-height:150% !important;
		}

}	@media only screen and (max-width: 480px){
	/*
	@tab Mobile Styles
	@section Footer Text
	@tip Make the footer content text larger in size for better readability on small screens.
	*/
		#templateFooter .mcnTextContent,#templateFooter .mcnTextContent p{
			/*@editable*/font-size:14px !important;
			/*@editable*/line-height:150% !important;
		}

}
    
.address-table td {
    border: 1px dotted #999999;
    padding: 5px 10px
}

.address-table {
	margin: 0 auto;
    background: #fff;
    width: 98%;
}
   </style></head>
            
    <body>
		<!--*|IF:MC_PREVIEW_TEXT|*-->
		<!--[if !gte mso 9]><!----><span class="mcnPreviewText" style="display:none; font-size:0px; line-height:0px; max-height:0px; max-width:0px; opacity:0; overflow:hidden; visibility:hidden; mso-hide:all;">*|MC_PREVIEW_TEXT|*</span><!--<![endif]-->
		<!--*|END:IF|*-->
        <center>
            <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
                <tr>
                    <td align="center" valign="top" id="bodyCell">
                        <!-- BEGIN TEMPLATE // -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
								<td align="center" valign="top" id="templatePreheader">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="preheaderContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
              	<!--[if mso]>
				<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign="top" width="600" style="width:600px;">
				<![endif]-->
                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding: 0px 18px 9px; text-align: center;">
                        
                            <a href="*|ARCHIVE|*" target="_blank">View this email in your browser</a>
                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
							<tr>
								<td align="center" valign="top" id="templateHeader">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="headerContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnImageBlock" style="min-width:100%;">
    <tbody class="mcnImageBlockOuter">
            <tr>
                <td valign="top" style="padding:9px" class="mcnImageBlockInner">
                    <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" class="mcnImageContentContainer" style="min-width:100%;">
                        <tbody><tr>
                            <td class="mcnImageContent" valign="top" style="padding-right: 9px; padding-left: 9px; padding-top: 0; padding-bottom: 0; text-align:center;">
                                
                                    
                                        <img align="center" alt="" src="https://gallery.mailchimp.com/3e449f22c104a6993fd50325f/images/1852f359-8b48-426d-9497-fb78a82b61c9.png" width="300" style="max-width:300px; padding-bottom: 0; display: inline !important; vertical-align: bottom;" class="mcnImage">
                                    
                                
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
							<tr>
								<td align="center" valign="top" id="templateBody">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="bodyContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
              	<!--[if mso]>
				<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign="top" width="600" style="width:600px;">
				<![endif]-->
                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;">
                        
                            <h1>Hi {customer_name},</h1>

<p><font size="4"><strong>Thank you for placing an order with TRUEGRID!</strong>&nbsp;</font></p>

<p>The shipping address you provided was invalid and needs to be updated. Below is the address we received:</p>

                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnCodeBlock">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                <style>
.address-table td {
    border: 1px dotted #999999;
    padding: 5px 10px
}

.address-table {
	margin: 0 auto;
    background: #fff;
    width: 98%;
}
</style>


<div class="mcnTextContent">
<table class="address-table">
<tbody>
<tr>
<td>Attention:</td><td>{attention_name}</td>
</tr>
<tr>
<td>Address:</td><td>{address}</td>
</tr>
<tr>
<td>Street2:</td><td>{street2}</td>
</tr>
<tr>
<td>City:</td><td>{city}</td>
</tr>
<tr>
<td>State:</td><td>{state}</td>
</tr>
<tr>
<td>Zip:</td><td>{zip}</td>
</tr>
<tr>
<td>Country:</td><td>{country}</td>
</tr>
<tr>
<td>Phone:</td><td>{phone}</td>
</tr>
</tbody>
</table>
</div>
            </td>
        </tr>
    </tbody>
</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock" style="min-width:100%;">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner" style="padding-top:9px;">
              	<!--[if mso]>
				<table align="left" border="0" cellspacing="0" cellpadding="0" width="100%" style="width:100%;">
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign="top" width="600" style="width:600px;">
				<![endif]-->
                <table align="left" border="0" cellpadding="0" cellspacing="0" style="max-width:100%; min-width:100%;" width="100%" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding-top:0; padding-right:18px; padding-bottom:9px; padding-left:18px;">
                        
                            <p>Please reply to this email with the correct shipping address. You may check on the&nbsp;<a href="https://www.ups.com/address_validator/search?loc=en_US">UPS</a>&nbsp;website that your address is valid before submitting it.</p>

<p><strong><font size="4">Once we receive the correct shipping address we will ship your product and send you confirmation.</font></strong></p>

<p>Some common reasons an address may return as invalid:</p>

<ol>
	<li>Typos</li>
	<li>An address outside of the continental US (lower 48 states)</li>
	<li>An address with an incorrect zip code. Ensure you use a 5 digit valid zip code.</li>
</ol>

<p>If you have any other questions please call toll free&nbsp;<a href="tel:(855)%20375-4743" target="_blank" value="+18553754743">1-855-375-4743</a>&nbsp;or email&nbsp;<a href="mailto:office@truegridpaver.com" target="_blank">office@truegridpaver.com</a></p>

<p>Thank you for shopping with&nbsp;<strong><font color="#6aa84f">TRUE</font>GRID</strong>.</p>

<p>Have a wonderful day!</p>
<a href="#">View Your Order</a>

<p>Sincerely,</p>
The TRUEGRID Team

<p><a href="http://email.mg.truegridpaver.com/u/eJwNzDEOwyAMAMDXhI0KbAP2wJCngHEa1KatovxfzXzSjTpCA05u1iZdoGhT42ydixJDkKGMCVGiLhTmz8cCHqMH9IJur9lyGUDUSRNshjebAQh3oQ2TuLO-DpufvZ3jKnfxPNp8P_R7uKsusP4BrU8jmQ" target="_blank">unsubscribe</a></p>

                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
                            <tr>
								<td align="center" valign="top" id="templateFooter">
									<!--[if gte mso 9]>
									<table align="center" border="0" cellspacing="0" cellpadding="0" width="600" style="width:600px;">
									<tr>
									<td align="center" valign="top" width="600" style="width:600px;">
									<![endif]-->
									<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="templateContainer">
										<tr>
                                			<td valign="top" class="footerContainer"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width:100%;">
    <tbody class="mcnDividerBlockOuter">
        <tr>
            <td class="mcnDividerBlockInner" style="min-width: 100%; padding: 10px 18px 25px;">
                <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="100%" style="min-width: 100%;border-top: 2px solid #EEEEEE;">
                    <tbody><tr>
                        <td>
                            <span></span>
                        </td>
                    </tr>
                </tbody></table>
<!--            
                <td class="mcnDividerBlockInner" style="padding: 18px;">
                <hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
-->
            </td>
        </tr>
    </tbody>
</table></td>
										</tr>
									</table>
									<!--[if gte mso 9]>
									</td>
									</tr>
									</table>
									<![endif]-->
								</td>
                            </tr>
                        </table>
                        <!-- // END TEMPLATE -->
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>
EOF;

    return $template;
}

/**
 *
 * @param object $order_details : order details retrieved from ZOHO Inventory server
 * @return object
 */
function getWarehouseFromOrderDetails ($order_details) {
    $order_details->warehouses;
    if (!empty($order_details->warehouses)) {
       $warehouses = $order_details->warehouses;

        if (count($warehouses) > 1) {
            foreach ($warehouses as $warehouse) {
                if (!empty($warehouse->is_primary)) {
                    return $warehouse;
                }
            }
        } else {
            foreach ($warehouses as $warehouse) {
                return $warehouse;
            }
        }
    }
}

function getStateCodeByName ($state = '', $states=array()) {
    $state_code = '';
	$state = strtoupper($state);
	$strlen = strlen(trim($state));

	if ($strlen == 2) {
		return $state;
	} else {
		foreach ($states as $st) {
			if ($state == strtoupper($st['state'])) {
				return strtoupper($st['st_code']);
			}
		}
	}
}

function getStateNameByCode ($state = '', $states=array()) {
    $state_code = '';
	$state = strtoupper($state);
	$strlen = strlen(trim($state));

	if ($strlen == 2) {
		foreach ($states as $st) {
                    if ($state == strtoupper($st['st_code'])) {
                            return strtoupper($st['state']);
                    }
		}
	} else {
            return $state;
	}
}

function getCountryCodeByName ($country = '') {
    $country_code = '';
    switch ($country) {
        case 'U.S.A':
            $country_code = 'US';
            break;
        case 'USA':
            $country_code = 'US';
            break;
        case 'usa':
            $country_code = 'US';
            break;
        default:
            break;
    }

    return $country_code;
}


function check_cli () {
    if (CLI_ONLY) {
        if (php_sapi_name() != "cli") {
            echo "Direct http access not allowed..!";
            die();
        }
    }
}

function addLog ($log_data) {
    $logger = new Katzgrau\KLogger\Logger(LOG_FILE_PATH);
    $logger->info($log_data);
}

function getToAddressFromOrderDetails ($params = array()) {
    $order_details = $params['order_details'];
    $customer_details = $params['customer_details'];
    $states = $params['states'];
    $customer_email = $params['customer_email'];
    
    $toAddress = '';
    $toAddress['addressLine1'] = $order_details->shipping_address->address;
    $toAddress['postalCode'] = $order_details->shipping_address->zip;
    $toAddress['city'] = $order_details->shipping_address->city;
    $toAddress['countryCode'] = 'US';
    $toAddress['stateProvinceCode'] = getStateCodeByName(strtoupper(!empty($order_details->shipping_address->state) ? $order_details->shipping_address->state : $customer_details->billing_address->state), $states);
    $toAddress['companyName'] = !empty($customer_details->company_name) ? $customer_details->company_name :  $order_details->customer_name;
    $toAddress['attentionName'] = !empty($order_details->shipping_address->attention) ? $order_details->shipping_address->attention : $order_details->customer_name;
    $toAddress['emailAddress'] = !empty($customer_email) ? $customer_email : COMPANY_EMAIL;
    $toAddress['phoneNumber'] = getValidatedPhoneNumber($order_details->shipping_address->phone,COMPANY_PHONE);

    foreach ($toAddress as $v) {
        if (empty($v)) {
            return false;
        }
    }
    
    return $toAddress;
}

function getSoldToAddressFromOrderDetails ($params = array()) {
    $order_details = $params['order_details'];
    $customer_email = $params['customer_email'];
    $states = $params['states'];
    
    $soldTo = '';
    $soldTo['addressLine1'] = $order_details->shipping_address->address;
    $soldTo['postalCode'] = $order_details->shipping_address->zip;
    $soldTo['city'] = $order_details->shipping_address->city;
    $soldTo['countryCode'] =  'US';
    $soldTo['stateProvinceCode'] = getStateCodeByName(strtoupper($order_details->shipping_address->state), $states);
    $soldTo['attentionName'] = !empty($order_details->shipping_address->attention) ? $order_details->shipping_address->attention : $order_details->customer_name;
    $toAddress['emailAddress'] = !empty($customer_email) ? $customer_email : COMPANY_EMAIL;
    $toAddress['phoneNumber'] = !empty($order_details->shipping_address->phone) ? $order_details->shipping_address->phone : COMPANY_PHONE;

    foreach ($soldTo as $v) {
        if (empty($v)) {
            return false;
        }
    }
    
    return $soldTo;
}

function getValidatedPhoneNumber ($to_phone_number = '', $default_phone_number='') {
    $phone_number = trim(preg_replace("/[^0-9]/","",$to_phone_number));
    
    if (empty($phone_number)) {
        return $default_phone_number;
    }
    
    if (strlen($phone_number) != 10) {
        return $default_phone_number;
    }
    
    return $phone_number;    
}