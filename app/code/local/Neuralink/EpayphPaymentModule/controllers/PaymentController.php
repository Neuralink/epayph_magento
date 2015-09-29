<?php
/**
 * EpayPh Payment Module for Magento > 1.6
 *
 * @category    EpayPh
 * @package     Neuralink_EpayphPaymentModule
 * @copyright   Copyright (c) 2015 Neuralink Inc. (https://www.epay.ph)
 * @autor       Paolo Abadesco <admin@epay.ph>
 * @version   	0.0.1
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Neuralink_EpayphPaymentModule_PaymentController extends Mage_Core_Controller_Front_Action {
	public function redirectAction() {
		$this->loadLayout();
		$block = $this->getLayout()->createBlock('Mage_Core_Block_Template','epayphPaymentModule',array('template' => 'epayphPaymentModule/redirect.phtml'));
		$this->getLayout()->getBlock('content')->append($block);
		$this->renderLayout();
	}

	public function webhookAction() {
		/*
		Receives transactions status change notifications and updates order statuses
		*/

		// retrieve user configuration:
		$apiSecret = Mage::getStoreConfig('payment/epayphPaymentModule/epayphApiSecret');

		// parse raw json-encoded POST data
		$json_data = $this->getRequest()->getRawBody();
		$data = json_decode($json_data);

		$type = $data->Type;
		$subtype = $data->Subtype;
		$id = $data->Id;
		$created = $data->Created;
		$triggered = $data->Triggered;
		$value = $data->Value;

		// verify signature:
		$signature = $this->getRequest()->getHeader('X-Epayph-Signature');

		$hash = hash_hmac('sha1', $json_data, $apiSecret);
		$validated = ($hash == $signature);

		// retrieve order based on transaction ID:
		$orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('epayph_transaction_id', $id);

		// If no orders were found, wait 5 seconds, then try again
		// This might happen when users pay from their Epay.ph balance
		// and the Webhook hits before the redirect registers
		if(!$orders || sizeof($orders) < 1) {
			sleep(5);
			$orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('epayph_transaction_id', $id);
		}

		// update order state and status based on notification of new status:
		$status_to_order_state = array(
			"completed" => Mage_Sales_Model_Order::STATE_PROCESSING,
			"pending"   => Mage_Sales_Model_Order::STATE_PROCESSING,
			"failed"    => Mage_Sales_Model_Order::STATE_CANCELED,
			"cancelled" => Mage_Sales_Model_Order::STATE_CANCELED,
			"reclaimed" => Mage_Sales_Model_Order::STATE_CANCELED,
			"processed" => "payment_complete"
		);

		foreach($orders as $order) {
			$message = '';

			if($validated) {
				$message = "Current Epay.ph transaction status: {$value}, as of {$triggered}. Epay.ph's signature was successfully verified.";
			} else {
				$value = 'failed';
				$message = "Epay.ph's signature failed to verify. Terminating order. Contact support@epay.ph with the following information: Proposed signature: {$signature} ... Expected Signature: {$hash} ... Body: {$json_data}";
			}

			$order->setState($status_to_order_state[$value], true, $message);
			$order->setStatus($status_to_order_state[$value]);
			$order->save();

			echo "Magento order ID #{$order['increment_id']} / Epay.ph Transaction ID #{$id} is now set to {$status_to_order_state[$value]}.";
		}

		// always respond to Epay.ph with 200/OK, else Epay.ph will try sending a second time:
		$this->getResponse()->setHttpResponseCode(200);
	}

	public function responseAction() {
		// Get User Configuration
		$epayphId = Mage::getStoreConfig('payment/epayphPaymentModule/epayphId');
		$apiKey = Mage::getStoreConfig('payment/epayphPaymentModule/epayphApiKey');
		$apiSecret = Mage::getStoreConfig('payment/epayphPaymentModule/epayphApiSecret');

		$url = 'https://epay.ph/api/validateIPN';
		$json = json_encode($_POST);
		$client = new Zend_Http_Client($url);
		$client->setHeaders(array(
			'Accept: application/json',
		));
		
		$_POST['cmd'] = '_notify-validate';
		$client->setParameterPost($_POST);
		//$client->setRawData($json, 'application/x-www-form-urlencoded')->request('POST');
		$request = $client->request('POST');
		//var_dump($client->request()->getBody());
		
		mail('pjabadesco@gmail.com','post',json_encode($_POST));
		mail('pjabadesco@gmail.com','responseAction',$request->getBody());

		if($request->getBody()=='{"return":"VERIFIED"}') {
			$order = Mage::getModel('sales/order');
			$order->loadByIncrementId($_POST['invoice']);
			//$order->setEpayphTransactionId($transactionId);
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, "Epay.ph Gateway informed us: the payment's Transaction ID is {$_POST['txn_id']}");

			$order->sendNewOrderEmail();
			$order->setEmailSent(true);
			$order->save();

			Mage::getSingleton('checkout/session')->unsQuoteId();

			Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=>true));
		}
		else {
			$this->cancelAction(FALSE, 'Epay.ph signature did not validate');
			Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
		}
	}

	public function cancelAction($orderId = FALSE, $errorDescription = "Unknown reason") {
		if(!$orderId) {
		  $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		}

	    if ($orderId) {
	      $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
	      if($order->getId()) {
	        $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, "Gateway has declined the payment: {$errorDescription}.")->save();
	        Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
	      }
	    }
	}
}
