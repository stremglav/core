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
 * @subpackage View
 * @author     Creative Development LLC <info@cdev.ru>
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id: Checkout.php 3788 2010-08-14 22:23:55Z vvs $
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\View\Model\Profile;

/**
 * \XLite\View\Model\Profile\Checkout
 *
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Checkout extends \XLite\View\Model\Profile\Main
{
    /**
     * Return name of web form widget class
     *
     * @return string
     * @access protected
     * @since  3.0.0
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Checkout\ModifyProfile';
    }

    /**
     * Prepare the "Personal info" section for checkout
     *
     * @param array $sections Passed list of sections
     *
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function prepareMainSections(array $sections)
    {
        unset($sections[self::SECTION_ACCESS]);

        if (\XLite\Core\Auth::getInstance()->isLogged()) {
            unset($sections[self::SECTION_MAIN]);
        }

        return $sections;
    }

    /**
     * Prepare secret hash string to log in anonymous user
     *
     * @return string
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function prepareProfileSecureHash()
    {
        $result = \XLite\Core\Converter::generateRandomToken();
        \XLite\Core\Auth::getInstance()->setSecureHash($result);

        return $result;
    }

    /**
     * setPasswords 
     * 
     * @param string $password Password to set
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function setPasswords($password)
    {
        $this->setRequestData('password', $password);
        $this->setRequestData('password_conf', $password);
        $this->setModelProperties(array('password' => $password));
    }

    /**
     * Three possibilities to log in
     *
     * @return array
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function prepareProfilePassword()
    {
        $secureHash = null;
        // 1) Password is passed in request (entered or generated by third side)
        $password = $this->getRequestData('password');

        if (empty($password)) {
            // 2) Password is already defined and saved.
            // In this case where is only one approach - to log in using secret hash
            $password = $this->getModelObject()->getPassword();

            if (empty($password)) {
                // 3) Generate new password
                $password = \XLite\Core\Converter::generateRandomToken();
                // It's needed for the parent class
                $this->setPasswords($password);
            } else {
                // See p.2
                $secureHash = $this->prepareProfileSecureHash();
            }
        }

        return array($password, $secureHash);
    }

    /**
     * Log into LiteCommerce
     *
     * @param string      $password   Account password
     * @param string|null $secureHash Secret hash string (if needed)
     *
     * @return boolean 
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function loginProfile($password, $secureHash)
    {
        return is_object(
            \XLite\Core\Auth::getInstance()->login(
                $this->getModelObject()->getLogin(), $password, $secureHash
            )
        );
    }

    /**
     * Perform some actions on success
     *
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function postprocessSuccessActionModify()
    {
        parent::postprocessSuccessActionModify();

        $this->renewShippingMethod();
    }

    /**
     * Check if current user trying to register as anonymous
     * FIXME: adapt this for the standalone LC
     *
     * @return boolean 
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function isAnonymousUser()
    {
        return !\XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Renew shipping method
     *
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function renewShippingMethod()
    {
        /* TODO - rework: do a renew entire cart, not only shipping
        \XLite\Model\Cart::getInstance()->refresh('shippingRates');
        \XLite\Model\Cart::getInstance()->refresh('profile');
        */
        \XLite\Model\Cart::getInstance()->calculate();
    }

    /**
     * isPassedEmailDifferent
     *
     * @return boolean 
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function isPassedEmailDifferent()
    {
        return $this->getRequestData('login')
            && \XLite\Core\Auth::getInstance()->isLogged()
            && \XLite\Core\Auth::getInstance()->getProfile()->getLogin() !== $this->getModelObject()->getLogin();
    }

    /**
     * Perform certain action for the model object
     *
     * @return boolean 
     * @access protected
     * @since  3.0.0
     */
    protected function performActionModify()
    {
        // Do not move this call: there are some required action performed in it
        list($password, $secureHash) = $this->prepareProfilePassword();

        // Anonymous user has no primary account, only the one associated with current order
        if ($this->isAnonymousUser()) {
            $this->getModelObject()->setOrderId(\XLite\Model\Cart::getInstance()->getOrderId());
        }

        $result = parent::performActionModify();

        // Log in on success
        if ($result && !\XLite\Core\Auth::getInstance()->isLogged()) {

            // This request var will modify the behaviour
            // of the "\XLite\Core\Auth::findForAuth()" method
            if ($this->isAnonymousUser()) {
                \XLite\Core\Request::getInstance()->anonymous = true;
            }

            $result = $this->loginProfile($password, $secureHash);
        }

        return $result;
    }


    /**
     * Return current profile ID
     *
     * @param boolean $checkMode Check mode or not OPTIONAL
     *
     * @return integer 
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getProfileId($checkMode = true)
    {
        return \XLite\Model\Cart::getInstance()->getProfileId();
    }


    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'checkout';

        return $result;
    }
}
