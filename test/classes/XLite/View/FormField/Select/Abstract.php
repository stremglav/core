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
 * @category   LiteCommerce
 * @package    XLite
 * @subpackage ____sub_package____
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */


abstract class XLite_View_FormField_Select_Abstract extends XLite_View_FormField_Abstract
{
    /**
     * Widget param names 
     */

    const PARAM_OPTIONS = 'options';

    
    /**
     * getDefaultOptions
     *
     * @return array
     * @access protected
     * @since  3.0.0
     */
    abstract protected function getDefaultOptions();


    /**
     * Return field template
     *
     * @return string
     * @access protected
     * @since  3.0.0
     */
    protected function getFieldTemplate()
    {
        return 'select.tpl';
    }

    /**
     * getOptions 
     * 
     * @return array
     * @access protected
     * @since  3.0.0
     */
    protected function getOptions()
    {
        return $this->getParam(self::PARAM_OPTIONS);
    }

    /**
     * Define widget params
     *
     * @return void
     * @access protected
     * @since  3.0.0
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_OPTIONS => new XLite_Model_WidgetParam_Array('Options', $this->getDefaultOptions(), false),
        );
    }


    /**
     * Return field type
     *
     * @return string
     * @access public
     * @since  3.0.0
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_SELECT;
    }
}

