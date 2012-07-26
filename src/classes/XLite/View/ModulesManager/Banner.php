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

namespace XLite\View\ModulesManager;

/**
 * Banner 
 *
 */
class Banner extends \XLite\View\ModulesManager\AModulesManager
{
    /**
     * Widget CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }
    
    /**
     * Return templates dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/banner';
    }

    /**
     * Is banner visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return (bool) $this->getBannerURL();
    }

    /**
     * Return banner URL
     *
     * @return string
     */
    protected function getBannerURL()
    {
        return \Includes\Utils\ConfigParser::getOptions(array('marketplace', 'banner_url'));
    }
}
