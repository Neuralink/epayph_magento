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

class Neuralink_EpayphPaymentModule_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * Set template and redirect message
     */
    public function __construct()
    {
	    parent::__construct();
	    $this
		    ->setTemplate('epayphPaymentModule/form.phtml')
			->setRedirectMessage(
				Mage::helper('paypal')->__('You will be redirected to the Epay.ph website when you place an order.')
			);
    }
}
