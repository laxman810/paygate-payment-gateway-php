<?php
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");

require_once 'config.php';
require_once 'API.php';
require_once 'PayHost/global.inc.php';
require_once 'PayHost/paygate.payhost_soap.php';

class MyAPI extends API {

    protected function addCard($args) {

        if ($args['ent_token'] == '')
            return array('errNum' => 1, 'errFlag' => 1, 'errMsg' => "card token missing");
        
        if ($args['ent_cvc'] == "")
            return array('errNum' => 1, 'errFlag' => 1, 'errMsg' => "cvc missing");

        $payGateId = PAY_GATE_ID;
        $encryptionKey = PAY_GATE_ENCRYPTION_KEY;
        $vaultId = $args['ent_token'];

        $xml = <<<XML
<ns1:SingleVaultRequest>
    <ns1:LookUpVaultRequest>
        <ns1:Account>
            <ns1:PayGateId>{$payGateId}</ns1:PayGateId>
            <ns1:Password>{$encryptionKey}</ns1:Password>
        </ns1:Account>
        <ns1:VaultId>{$vaultId}</ns1:VaultId>
    </ns1:LookUpVaultRequest>
</ns1:SingleVaultRequest>
XML;

        ini_set("soap.wsdl_cache_enabled", "0");
        $soapClient = new SoapClient(PayHostSOAP::$process_url . "?wsdl", array('trace' => 1)); //point to WSDL and set trace value to debug

        try {
            $result = $soapClient->__soapCall('SingleVault', array(
                new SoapVar($xml, XSD_ANYXML)
            ));
        } catch (SoapFault $sf) {
            $err = $sf->getMessage();
        }
        if (!$result)
            return array('errNum' => 2, 'errFlag' => 1, 'errMsg' => $err);
        else {
            $data = array();
            $xmlString = $soapClient->__getLastResponse();
            $doc = new DOMDocument;
            $doc->loadXML($xmlString);
            $statusName = $doc->getElementsByTagName('StatusName')->item(0)->nodeValue;

            if ($statusName == "Error") {
                $errMsg = $doc->getElementsByTagName('ResultDescription')->item(0)->nodeValue;
                return array('errNum' => 3, 'errFlag' => 1, 'errMsg' => $errMsg);
            } else if ($statusName == "Completed") {

                $PayVaultDataCard0 = $doc->getElementsByTagName('PayVaultData')->item(0);
                $cardNumber = $PayVaultDataCard0->getElementsByTagName('value')->item(0)->nodeValue;
                $PayVaultDataExp = $doc->getElementsByTagName('PayVaultData')->item(1);
                $cardexpDate = $PayVaultDataExp->getElementsByTagName('value')->item(0)->nodeValue;

                $PaymentType = $doc->getElementsByTagName('PaymentType')->item(0);
                $cardMethod = $PaymentType->getElementsByTagName('Detail')->item(0)->nodeValue;

                $data = array('cardToken' => $vaultId, 'status' => $statusName,
                    'last4' => substr($cardNumber, -4),
                    'exp_year' => substr($cardexpDate, -4),
                    'exp_month' => substr($cardexpDate, 0, 2),
                    'cvc' => $args['ent_cvc'],
                    'cardMethod' => $cardMethod);
            } else {
                $errMsg = 'Error Occured In payhost';
                return array('errNum' => 4, 'errFlag' => 1, 'errMsg' => $errMsg);
            }
            return array( 'errFlag' => 5, 'errFlag' => 0, 'errMsg' => "success", 'cards' => $data);
        }
    }

    protected function removeCard($args) {

        if ($args['ent_token'] == '')
            return array('errNum' => 1, 'errFlag' => 1, 'errMsg' => "card token missing");
        $custid = (int) $args['ent_cust_id'];
        $payGateId = PAY_GATE_ID;
        $encryptionKey = PAY_GATE_ENCRYPTION_KEY;
        $vaultId = $args['ent_token'];

        $xml = <<<XML
<ns1:SingleVaultRequest>
    <ns1:DeleteVaultRequest>
        <ns1:Account>
            <ns1:PayGateId>{$payGateId}</ns1:PayGateId>
            <ns1:Password>{$encryptionKey}</ns1:Password>
        </ns1:Account>
        <ns1:VaultId>{$vaultId}</ns1:VaultId>
    </ns1:DeleteVaultRequest>
</ns1:SingleVaultRequest>
XML;
        ini_set("soap.wsdl_cache_enabled", "0");
        $soapClient = new SoapClient(PayHostSOAP::$process_url . "?wsdl", array('trace' => 1)); //point to WSDL and set trace value to debug
        try {
            $result = $soapClient->__soapCall('SingleVault', array(
                new SoapVar($xml, XSD_ANYXML)
            ));
        } catch (SoapFault $sf) {
            $err = $sf->getMessage();
            return array('errNum' => 1, 'errFlag' => 1, 'errMsg' => $err);
        }
        $xmlString = $soapClient->__getLastResponse();
        $doc = new DOMDocument;
        $doc->loadXML($xmlString);
        $statusName = $doc->getElementsByTagName('StatusName')->item(0)->nodeValue;

        if ($statusName == "Error") {
            $errMsg = $doc->getElementsByTagName('ResultDescription')->item(0)->nodeValue;
            return array('errNum' => 2, 'errFlag' => 1, 'errMsg' => $errMsg);
        } else if ($statusName == "Completed") {
                return array('errNum' => 1, 'errFlag' => 0, 'errMsg' => "success");
        } else {
            $errMsg = 'Error Occured In payhost';
            return array('errNum' => 3, 'errFlag' => 1, 'errMsg' => $errMsg);
        }
    }

    protected function chargeCard($args) {

        if ($args['ent_token'] == '')
            return array('errNum' => 1, 'errFlag' => 1,'errMsg' => "card token missing");        
        if ($args['ent_cvc'] == '')
            return array('errNum' => 1, 'errFlag' => 1,'errMsg' => "cvc is missing");        
        if ($args['amount'] == '')
            return array('errNum' => 1, 'errFlag' => 1,'errMsg' => "amount is missing");
        if ($args['customerFirstName'] == '')
            return array('errNum' => 1, 'errFlag' => 1,'errMsg' => "customerFirstName is missing");
        if ($args['customerPhone'] == '')
            return array('errNum' => 1, 'errFlag' => 1,'errMsg' => "customerPhone is missing");
        if ($args['customerEmail'] == '')
            return array('errNum' => 1, 'errFlag' => 1,'errMsg' => "customerEmail is missing");
        if ($args['currencySymbol'] == '')
            return array('errNum' => 1, 'errFlag' => 1,'errMsg' => "currencySymbol is missing");

        $payGateId = PAY_GATE_ID;
        $encryptionKey = PAY_GATE_ENCRYPTION_KEY;
        $dateTime = new DateTime();
        $Reference = 'pgtest_' . $dateTime->format('YmdHis');

        $amount = (int) $args['amount'] * 100;

        $xml = <<<XML
<SinglePaymentRequest xmlns="http://www.paygate.co.za/PayHOST">
<CardPaymentRequest>
<Account>
<PayGateId>{$payGateId}</PayGateId>
<Password>{$encryptionKey}</Password>
</Account>
<Customer>
<Title>Mr</Title>
<FirstName>{$args['customerFirstName']}</FirstName>
<LastName>{$args['customerLastName']}</LastName>
<Telephone>{$args['customerPhone']}</Telephone>
<Mobile>{$args['customerPhone']}</Mobile>
<Email>{$args['customerEmail']}</Email>
</Customer>
<VaultId>{$args['ent_token']}</VaultId>
<CVV>{$args['ent_cvc']}</CVV>
<BudgetPeriod>0</BudgetPeriod>
<Redirect>
<NotifyUrl>http://www.melikey.co.za/</NotifyUrl>
<ReturnUrl>http://www.melikey.co.za/</ReturnUrl>
</Redirect>
<Order>
<MerchantOrderId>{$Reference}</MerchantOrderId>
<Currency>{$args['currencySymbol']}</Currency>
<Amount>{$amount}</Amount>
</Order>
</CardPaymentRequest>
</SinglePaymentRequest>
XML;
        ini_set("soap.wsdl_cache_enabled", "0");

        $soapClient = new SoapClient(PayHostSOAP::$process_url . "?wsdl", array('trace' => 1)); //point to WSDL and set trace value to debug
        try {
            $result = $soapClient->__soapCall('SinglePayment', array(
                new SoapVar($xml, XSD_ANYXML)
            ));
        } catch (SoapFault $sf) {
            $err = $sf->getMessage();
        }
        if (!$result)
            return array('errNum' => 3, 'errFlag' => 1, 'errMsg' => $err, 'err1' => $soapClient->__getLastResponse());
        else {
            $xmlString = $soapClient->__getLastResponse();
            $doc = new DOMDocument;
            $doc->loadXML($xmlString);
            $statusName = $doc->getElementsByTagName('StatusName')->item(0)->nodeValue;
            if ($statusName == "Error") {
                $errMsg = $doc->getElementsByTagName('ResultDescription')->item(0)->nodeValue;
                return array('errNum' => 4, 'errFlag' => 1, 'errMsg' => $errMsg, 'test' => $soapClient->__getLastResponse());
            } else if ($statusName == "Completed") {
                $tra_status = $doc->getElementsByTagName('TransactionStatusCode')->item(0)->nodeValue;
                if ($tra_status == 1) {
                    $TransactionId = $doc->getElementsByTagName('TransactionId')->item(0)->nodeValue;
                    if (empty($TransactionId)) {
                        $errMsg = 'Transaction Id Not Getting';
                        return array('errNum' => 5, 'errFlag' => 1, 'errMsg' => $errMsg, 'test' => $soapClient->__getLastResponse());
                    }
                    return array('errNum' => 6, 'errFlag' => 0, 'errMsg' => "success", 'data' => $TransactionId);                    
                } else {
                    $errMsg = $doc->getElementsByTagName('TransactionStatusDescription')->item(0)->nodeValue;
                    $errMsg = $errMsg . " - " . $doc->getElementsByTagName('ResultDescription')->item(0)->nodeValue;
                    return array('errNum' => 7, 'errFlag' => 1, 'errMsg' => $errMsg, 'test' => $soapClient->__getLastResponse());
                }
            } else if ($statusName == "ThreeDSecureRedirectRequired") {
                $errMsg = '3D Secure Redirect Requried To Complete Transaction';
                return array('errNum' => 8, 'errFlag' => 1, 'errMsg' => $errMsg, 'test' => $soapClient->__getLastResponse());
            } else {
                $errMsg = $statusName;
                return array('errNum' => 9, 'errFlag' => 1, 'errMsg' => $errMsg, 'test' => $soapClient->__getLastResponse());
            }
        }
    }
}


if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}
try {
    $API = new MyAPI($_SERVER['REQUEST_URI'], $_REQUEST, $_SERVER['HTTP_ORIGIN']);
    echo $API->processAPI();
}catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}
?>
