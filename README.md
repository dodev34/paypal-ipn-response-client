PHP-PayPal-IPN and Client Response
==================================

A PayPal Instant Payment Notification (IPN) class for PHP 5. 

Use the `IpnListener` class in your PHP IPN script to handle the encoding 
of POST data, post back to PayPal, and parsing of the response from PayPal.


Features
--------

* Switch between live and sandbox by setting the `use_sandbox` property.
* Supports both secure SSL and plain HTTP transactions by setting the `use_ssl`
  property (SSL is recommended).
* Supports both cURL and fsockopen network libraries by setting the `use_curl`
  property (cURL is recommended).
* Verifies an HTTP &quot;200&quot; response status code from the PayPal server.
* Get detailed plain text reports of the entire IPN using the `getTextReport()` 
  method for use in emails and logs to administrators.
* Throws various exceptions to differentiate between common errors in code or
  server configuration versus invalid IPN responses.


Getting Started
---------------

This code is intended for web developers. You should understand how the IPN
process works conceptually and you should understand when and why you would be
using IPN. Reading the [PayPal Instant Payment Notification Guide][1] is a good
place to start.

You should also have a [PayPal Sandbox Account][2] with a test buyer account and
a test seller account. When logged into your sandbox account there is an IPN
simulator under the 'Test Tools' menu which you can used to test your IPN 
listener.

[1]: https://cms.paypal.com/cms_content/US/en_US/files/developer/IPNGuide.pdf
[2]: https://developer.paypal.com

Once you have your sandbox account setup, you simply create a PHP script that
will be your IPN listener. In that script, use the `IpnListener()` class as shown
below.

    <?php

    // Email receiver
    define('PAYPAL_EMAIL_ACCOUNT','seller@paypalsandbox.com');
    // Template email for client response.
    define('EMAIL_TEMPLATE','exemple.html');
    
    // Class IPN listener
    include('paypal/Classes/IpnListener.php');
    // Class email response
    include('paypal/Classes/EmailResponse.php');

    $listener = new IpnListener();
    $listener->use_sandbox = true;

    try {
        $verified = $listener->processIpn();
    } catch (Exception $e) {
        // fatal error trying to process IPN.
        exit(0);
    }

    if ($verified) {
        if ($listener->getPostData('receiver_email') == PAYPAL_EMAIL_ACCOUNT){
            $email_response = new EmailResponse($listener, EMAIL_TEMPLATE);
            $email_response->sendConfirmationClient();
        }
    } else {
        // IPN response was "INVALID"
    }

    ?>
