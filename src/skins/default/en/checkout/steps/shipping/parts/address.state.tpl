{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Shipping address : state
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011-2012 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 *
 * @ListChild (list="checkout.shipping.address", weight="40")
 *}
<li class="state">
  <label for="shipping_address_state">{t(#State#)}:</label>
  <widget class="\XLite\View\StateSelect" field="shippingAddress[state]" fieldId="shipping_address_state" state="{address.state}" isLinked="1" className="field-required" />
</li>
