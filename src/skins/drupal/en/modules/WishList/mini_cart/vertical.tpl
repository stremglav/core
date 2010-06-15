{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Widhlist blokc for vertical minicart
 *  
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 * @ListChild (list="minicart.vertical.base")
 *}
<div id="lc-minilist-{displayMode}" class="lc-minilist lc-minilist-{displayMode} collapsed" IF="countWishlistProducts()">

  <div class="list-link">
    <h3><a href="{buildURL(#wishlist#)}">Wishlist</a></h3>
  </div>

  <div class="list-items">
    <p><span class="toggle-button"><a href="{buildURL(#wishlist#)}" onclick="javascript:xlite_minicart_toggle('lc-minilist-{displayMode}'); return false;">{countWishlistProducts()} item(s)</a> </span></p>
    <div class="items-list">
      <ul>
        <li FOREACH="getWishlistItems(),item">
          <span class="item-name"><a href="{buildURL(#product#,##,_ARRAY_(#product_id#^item.product_id,#category_id#^item.category_id))}">{item.name}</a></span>
          <span class="item-price">{price_format(item,#price#):h}</span><span class="delimiter">x</span><span class="item-qty">{item.amount}</span>
        </li>
      </ul>
      <p IF="isWishlistTruncated()" class="other-items"><a href="{buildURL(#wishlist#)}">Other items</a></p>
    </div>
  </div>

</div>
