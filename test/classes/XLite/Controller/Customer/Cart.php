<?php
/*
+------------------------------------------------------------------------------+
| LiteCommerce                                                                 |
| Copyright (c) 2003-2009 Creative Development <info@creativedevelopment.biz>  |
| All rights reserved.                                                         |
+------------------------------------------------------------------------------+
| PLEASE READ  THE FULL TEXT OF SOFTWARE LICENSE AGREEMENT IN THE  'COPYRIGHT' |
| FILE PROVIDED WITH THIS DISTRIBUTION.  THE AGREEMENT TEXT  IS ALSO AVAILABLE |
| AT THE FOLLOWING URLs:                                                       |
|                                                                              |
| FOR LITECOMMERCE                                                             |
| http://www.litecommerce.com/software_license_agreement.html                  |
|                                                                              |
| FOR LITECOMMERCE ASP EDITION                                                 |
| http://www.litecommerce.com/software_license_agreement_asp.html              |
|                                                                              |
| THIS  AGREEMENT EXPRESSES THE TERMS AND CONDITIONS ON WHICH YOU MAY USE THIS |
| SOFTWARE PROGRAM AND ASSOCIATED DOCUMENTATION THAT CREATIVE DEVELOPMENT, LLC |
| REGISTERED IN ULYANOVSK, RUSSIAN FEDERATION (hereinafter referred to as 'THE |
| AUTHOR')  IS  FURNISHING  OR MAKING AVAILABLE TO  YOU  WITH  THIS  AGREEMENT |
| (COLLECTIVELY,  THE 'SOFTWARE'). PLEASE REVIEW THE TERMS AND  CONDITIONS  OF |
| THIS LICENSE AGREEMENT CAREFULLY BEFORE INSTALLING OR USING THE SOFTWARE. BY |
| INSTALLING,  COPYING OR OTHERWISE USING THE SOFTWARE, YOU AND  YOUR  COMPANY |
| (COLLECTIVELY,  'YOU')  ARE ACCEPTING AND AGREEING  TO  THE  TERMS  OF  THIS |
| LICENSE AGREEMENT. IF YOU ARE NOT WILLING TO BE BOUND BY THIS AGREEMENT,  DO |
| NOT  INSTALL  OR USE THE SOFTWARE. VARIOUS COPYRIGHTS AND OTHER INTELLECTUAL |
| PROPERTY  RIGHTS PROTECT THE SOFTWARE. THIS AGREEMENT IS A LICENSE AGREEMENT |
| THAT  GIVES YOU LIMITED RIGHTS TO USE THE SOFTWARE AND NOT AN AGREEMENT  FOR |
| SALE  OR  FOR TRANSFER OF TITLE. THE AUTHOR RETAINS ALL RIGHTS NOT EXPRESSLY |
| GRANTED  BY  THIS AGREEMENT.                                                 |
|                                                                              |
| The Initial Developer of the Original Code is Creative Development LLC       |
| Portions created by Creative Development LLC are Copyright (C) 2003 Creative |
| Development LLC. All Rights Reserved.                                        |
+------------------------------------------------------------------------------+
*/

/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */

/**
* Class description.
*
* @package Dialog
* @access public
* @version $Id$
*/
class XLite_Controller_Customer_Cart extends XLite_Controller_Customer_Abstract
{	
    protected $currentItem = null;

    function getCurrentItem()
    {
        if (is_null($this->currentItem)) {
            $this->currentItem = new XLite_Model_OrderItem();
            $this->currentItem->set('product', $this->get('product'));
        }
        return $this->currentItem;
    }

    function action_add()
    {
		if (!$this->canAddProductToCart()) {
			return;
		}
		$this->collectCartGarbage();

        // add product to the cart
        $this->cart->addItem($this->get('currentItem'));
        $this->updateCart(); // recalculate shopping cart

        // switch back to product catalog or to shopping cart
        $this->set('returnUrlAbsolute', false);
        $productListUrl = ($this->config->General->add_on_mode && isset($_SERVER['HTTP_REFERER']))
			? $_SERVER['HTTP_REFERER']
			: $this->session->get('productListURL');

        if ($this->config->General->redirect_to_cart) {
            $this->session->set('continueURL', $productListUrl);

        } else {
            $this->set('returnUrl', $productListUrl);
            $this->set('returnUrlAbsolute', $this->config->General->add_on_mode && isset($_SERVER['HTTP_REFERER']));
        }
    }

    function action_delete()
    {
        // delete an item from the shopping cart
        $items = $this->cart->get('items');
        if (isset($items[$this->cart_id])) {
            $this->cart->deleteItem($items[$this->cart_id]);
            $this->updateCart();
        }

        if ($this->cart->isEmpty()) {
            $this->cart->delete();
		}
    }

    function action_update()
    {
        // update the specified product quantity in cart
        $items = $this->cart->get('items');
        foreach ($items as $key => $i) {
            if (isset($this->amount[$key]) && (!isset($this->cart_id) || $this->cart_id == $key)) {
                $items[$key]->updateAmount($this->amount[$key]);
                $this->cart->updateItem($items[$key]);
            }
        }

        if (isset($this->shipping)) {
            $this->cart->set('shipping_id', $this->shipping);
        }

        $this->updateCart();

        if ($this->cart->isEmpty()) {
            $this->cart->delete();
		}
    }
    
    function action_checkout()
    {
        $this->action_update();
        // switch to checkout dialog 
        $this->set('returnUrl', 'cart.php?target=checkout');
    }

    function action_clear()
    {
    	if (!$this->cart->isEmpty()) {
            $this->cart->delete();
        }
    }

    function isSecure()
    {
    	if ($this->is('HTTPS')) {
    		return true;
    	}
        return parent::isSecure();
    }

	function canAddProductToCart()
	{
		if (!$this->getProduct()->filter()) {
			$this->set('valid', false);
			return false;	
		}
		return true;
	}

	function collectCartGarbage()
	{
		// don't collect garbage, if the cart already has products
		if ($this->cart->is('empty')) {
			$this->cart->collectGarbage(5);
		}
	}

    /**
     * Get page instance data (name and URL)
     * 
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getPageInstanceData()
    {
		$this->target = 'cart';

        return parent::getPageInstanceData();
    }

    /**
     * Get page type name
     * 
     * @return string
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getPageTypeName()
    {
        return 'Shopping cart';
    }

}

