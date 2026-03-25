<?php
    //show all errors - useful whilst developing
    error_reporting(E_ALL);

    // these keys can be obtained by registering at http://developer.ebay.com

    $production         = true;   // toggle to true if going against production
    $debug              = false;   // toggle to provide debugging info
    $compatabilityLevel = 681;    // eBay API version
    $findingVer = "1.8.0"; //eBay Finding API version

    //SiteID must also be set in the request
    //SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
    //SiteID Indicates the eBay site to associate the call with
    $siteID = 0;

    if ($production) {
        $devID = '73b8492a-f471-4170-86b8-ce9e6e2d6796';   // these prod keys are different from sandbox keys
        $appID = '73b8492a-f471-4170-86b8-ce9e6e2d6796';
        $certID = 'PRD-f78dd8ce63e4-212d-4ac1-8aa3-d2ad';
        //set the Server to use (Sandbox or Production)
        $serverUrl   = 'https://api.ebay.com/ws/api.dll';      // server URL different for prod and sandbox
        $shoppingURL = 'http://open.api.ebay.com/shopping';
        $findingURL= 'http://svcs.ebay.com/services/search/FindingService/v1';


        // This is used in the Auth and Auth flow

        // This is an initial token, not to be confused with the token that is fetched by the FetchToken call
        $appToken = 'AgAAAA**AQAAAA**aAAAAA**0Y/RXw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AHkIKmAJWAowSdj6x9nY+seQ**Cz4GAA**AAMAAA**hBmRP25hzAW+2yC1y07QV4jwW1/hbujM8pu01RSk9vCEEaDaeV5VTffsq2jzl/EwkEefepZiS+KJJykSN14Bq0m6BT6a3qsHdzn063/nlmjdx/M0+E/ZrtTqAx2gK4KP3Y0zm4pf9K+J2aRnYN3gMFEyJEuMyn0KPy8OTFJqxsZIO0mIWr98uysMj6D14yzR6saGmRQkuOvZ/sJaHbp3ljq7GbF5wF4Cyrwys4xRaT1KHFq6Trw5WqLdkvEn1K6KRxSvvZYlvZQW0IKbAlZ8cAXUewEgmOVABH8QP3xpODGMoKg5cqmz44RgULfm/e3JGx5gZ7ZT0sd0pWdOf7fHzVQqmmMuhGSNmpyx+rxu4eCM0q2Ssm4hMGyJd/bXmNJ7eIkOLacCQqg0Sw3SFyjaWUANdVcdIU/MYmk4c7fp3x9K0px5Yukez/Im6wm23LjkK4bjwK8w+2bcQAMA6CSAY5SoTSPt5QF7tOPqySMhnFJmTwHV+2OU6ImlClWc+nm+X6iwrp5yZZqHkMWvTU6Es/UQQmoKfZcHPu0um09ZLJs/U89bNn3VpO2T2hVCb3qjE4IAkr7ZphS9ucVkRo5lsO9xsP60G3qQhHsQ+sO2aPf9tZr+B2quYLAyA62dkskpbkychqiqL5g50ZL2No3RavrDHA/20erygkUNBgL5RFpZJzYmPFIH3m6DXItIVGllZrnadnPqce4pqb7JbZn/9y5XTsQ1Dui1Cnc/7qPy7wIPPjh7zyvgKwLzWMPIDml1';
    } else {
        // sandbox (test) environment
        $devID  = '';   // insert your devID for sandbox
        $appID  = '';   // different from prod keys
        $certID = '';   // need three keys and one token
        //set the Server to use (Sandbox or Production)
        $serverUrl = 'https://api.sandbox.ebay.com/ws/api.dll';
        $shoppingURL = 'http://open.api.sandbox.ebay.com/shopping';
        $findingURL= 'http://svcs.sandbox.ebay.com/services/search/FindingService/v1';


        $loginURL = 'https://signin.sandbox.ebay.com/ws/eBayISAPI.dll'; // This is the URL to start the Auth & Auth process
        $feedbackURL = 'http://feedback.sandbox.ebay.com/ws/eBayISAPI.dll'; // This is used to for link to feedback

        $runame = '';  // sandbox runame

        // This is the sandbox application token, not to be confused with the sandbox user token that is fetched.
        // This token is a long string - do not insert new lines.
        $appToken = '';
    }


?>