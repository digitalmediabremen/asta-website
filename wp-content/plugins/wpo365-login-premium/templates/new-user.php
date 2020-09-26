<?php

    // Prevent public access to this script
    defined( 'ABSPATH' ) or die();
    
    ?>

    <html>
    <head></head>
    <body>
    <div style="
        width:100%;
        -webkit-text-size-adjust:none !important;
        margin:0;
        padding: 70px 0 70px 0;
    ">
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
    <tbody>
    <tr>
    <td align="center" valign="top">
    <table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container" style="
        box-shadow:0 0 0 1px #f3f3f3 !important;
        border-radius:3px !important;
        background-color: #ffffff;
        border: 1px solid #e9e9e9;
        border-radius:3px !important;
        padding: 20px;
    ">
    <tbody>
    <tr>
    <td align="center" valign="top"><!-- Header -->
    <table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header" style="
        color: #00000;
        border-top-left-radius:3px !important;
        border-top-right-radius:3px !important;
        border-bottom: 0;
        font-weight:bold;
        line-height:100%;
        text-align: center;
        vertical-align:middle;
    " bgcolor="#ffffff">
    <tbody>
    <tr>
    <td>
    <h1 style="
        color: #000000;
        margin:0;
        padding: 28px 24px;
        display:block;
        font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
        font-size:32px;
        font-weight: 500;
        line-height: 1.2;
    ">
    <?php echo $title ?></h1>
    <p><?php echo $subtitle ?></p>
    </td>
    </tr>
    </tbody>
    </table>
    <!-- End Header --></td>
    </tr>
    <tr>
    <td align="center" valign="top"><!-- Body -->
    <table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
    <tbody>
    <tr>
    <td valign="top" style="
        border-radius:3px !important;
        font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
    ">
    <!-- Content -->
    <table border="0" cellpadding="20" cellspacing="0" width="100%">
    <tbody>
    <tr>
    <td valign="top">
    <div style="
        color: #000000;
        font-size:14px;
        font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
        line-height:150%;
        text-align:left;
    ">
    <p><?php echo $salutation ?></p>
    <p><?php echo $body ?></p>
    </div>
    </td>
    </tr>
    </tbody>
    </table>
    <!-- End Content --></td>
    </tr>
    </tbody>
    </table>
    <!-- End Body --></td>
    </tr>
    <tr>
    <td align="center" valign="top"><!-- Footer -->
    <table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer" style="
        border-top:0;
        -webkit-border-radius:3px;
    ">
    <tbody>
    <tr>
    <td valign="top">
    <table border="0" cellpadding="10" cellspacing="0" width="100%">
    <tbody>
    <tr>
    <td colspan="2" valign="middle" id="credit" style="
        border:0;
        color: #000000;
        font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
        font-size:12px;
        line-height:125%;
        text-align:center;
    ">
    <p><?php echo $footer ?></p>
    </td>
    </tr>
    </tbody>
    </table>
    </td>
    </tr>
    </tbody>
    </table>
    <!-- End Footer --></td>
    </tr>
    </tbody>
    </table>
    </td>
    </tr>
    </tbody>
    </table>
    </div>
    </body>
    </html>
