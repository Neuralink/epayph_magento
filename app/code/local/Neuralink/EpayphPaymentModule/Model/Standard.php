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

class Neuralink_EpayphPaymentModule_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'epayphPaymentModule';

	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = true;

	protected $_formBlockType = 'epayphPaymentModule/form';

	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('epayphPaymentModule/payment/redirect', array('_secure' => true));
	}
}
?>
