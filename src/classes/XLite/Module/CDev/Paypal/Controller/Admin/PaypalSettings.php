<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * LiteCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@litecommerce.com so we can send you a copy immediately.
 *
 * PHP version 5.3.0
 *
 * @category  LiteCommerce
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011-2012 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 */

namespace XLite\Module\CDev\Paypal\Controller\Admin;

/**
 * Paypal settings controller
 * 
 */
class PaypalSettings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Paypal settings';
    }


    /**
     * Save options
     *
     * @return void
     */
    protected function doActionSaveOptions()
    {
        if (isset(\XLite\Core\Request::getInstance()->options)) {

            $moduleOptions = $this->getModuleOptions();
            $postedData = \XLite\Core\Request::getInstance()->options;

            foreach ($moduleOptions as $option) {
                if (isset($postedData[$option])) {
                    \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
                        array(
                            'category' => 'CDev\\Paypal',
                            'name'     => $option,
                            'value'    => $postedData[$option],
                        )
                    );
                }
            }
        }
    }

    /**
     * Return allowed module options list
     *
     * @return array
     */
    protected function getModuleOptions()
    {
        return array(
            'vendor',
            'user',
            'pwd',
            'partner',
            'transaction_type',
            'test',
            'prefix',
        );
    }
}
