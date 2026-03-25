<?php
class ModelExtensionEbay extends Model {
	public function updatequantitytoebay($product_id,$quantity) {
		
		$product_info = $this->model_catalog_product->getProduct($product_id);
		$updquantity= $product_info['quantity']+$quantity;
		$ebay_id=$product_info['ebay_id'];
		
		$post = '<?xml version="1.0" encoding="utf-8"?>
				<ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">
					<RequesterCredentials>
						<eBayAuthToken><eBayAuthToken>AgAAAA**AQAAAA**aAAAAA**mzpxXg**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AHkIKmAJWAowSdj6x9nY+seQ**Cz4GAA**AAMAAA**hBmRP25hzAW+2yC1y07QV4jwW1/hbujM8pu01RSk9vCEEaDaeV5VTffsq2jzl/EwkEefepZiS+KJJykSN14Bq0m6BT6a3qsHdzn063/nlmjdx/M0+E/ZrtTqAx2gK4KP3Y0zm4pf9K+J2aRnYN3gMFEyJEuMyn0KPy8OTFJqxsZIO0mIWr98uysMj6D14yzR6saGmRQkuOvZ/sJaHbp3ljq7GbF5wF4Cyrwys4xRaT1KHFq6Trw5WqLdkvEn1K6KRxSvvZYlvZQW0IKbAlZ8cAXUewEgmOVABH8QP3xpODGMoKg5cqmz44RgULfm/e3JGx5gZ7ZT0sd0pWdOf7fHzVQqmmMuhGSNmpyx+rxu4eCM0q2Ssm4hMGyJd/bXmNJ7eIkOLacCQqg0Sw3SFyjaWUANdVcdIU/MYmk4c7fp3x9K0px5Yukez/Im6wm23LjkK4bjwK8w+2bcQAMA6CSAY5SoTSPt5QF7tOPqySMhnFJmTwHV+2OU6ImlClWc+nm+X6iwrp5yZZqHkMWvTU6Es/UQQmoKfZcHPu0um09ZLJs/U89bNn3VpO2T2hVCb3qjE4IAkr7ZphS9ucVkRo5lsO9xsP60G3qQhHsQ+sO2aPf9tZr+B2quYLAyA62dkskpbkychqiqL5g50ZL2No3RavrDHA/20erygkUNBgL5RFpZJzYmPFIH3m6DXItIVGllZrnadnPqce4pqb7JbZn/9y5XTsQ1Dui1Cnc/7qPy7wIPPjh7zyvgKwLzWMPIDml1</eBayAuthToken>
					</RequesterCredentials>
					<ErrorLanguage>en_US</ErrorLanguage>
					<WarningLevel>High</WarningLevel>
					<InventoryStatus>
					<ItemID>'.$ebay_id.'</ItemID>
					<Quantity>'.$updquantity.'</Quantity>
					</InventoryStatus>
				</ReviseInventoryStatusRequest>'; 

		$headers = array(
					"X-EBAY-API-COMPATIBILITY-LEVEL: 967",
					"X-EBAY-API-DEV-NAME: 73b8492a-f471-4170-86b8-ce9e6e2d6796",
					"X-EBAY-API-APP-NAME: 73b8492a-f471-4170-86b8-ce9e6e2d6796",
					"X-EBAY-API-CERT-NAME: PRD-d10eaf1ba793-d52a-46e3-919b-b4ec",
					"X-EBAY-API-CALL-NAME: ReviseInventoryStatus",
					"X-EBAY-API-SITEID: 0" // 3 for UK
		);
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_URL, "https://api.ebay.com/ws/api.dll");
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($connection, CURLOPT_POST, 1);
		curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($connection);
		curl_close($connection);

		$xml = new SimpleXMLElement($response);
	}
}