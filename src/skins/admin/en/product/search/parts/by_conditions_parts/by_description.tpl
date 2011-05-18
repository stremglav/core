{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * ____file_title____
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 *
 * @ListChild (list="product.search.conditions.by-keywords", weight="20")
 *}

<li>
  <input type="checkbox" name="by_descr" id="by-descr" value="Y" checked="{getCondition(#by_descr#)}" />
  <label for="by-descr">{t(#in description#)}</label>
</li>
