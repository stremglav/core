{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Order history block
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 * @ListChild (list="order.history.base", weight="10")
 *}
<li class="block" FOREACH="getOrderHistoryEventsBlock(),index,block">
  <ul class="block-item">
    <list name="order.history.base.events" block="{block}" />
  </ul>
</li>
