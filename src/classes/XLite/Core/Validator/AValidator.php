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

namespace XLite\Core\Validator;

/**
 * Abstract validator
 *
 */
abstract class AValidator extends \XLite\Base\SuperClass
{
    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     */
    abstract public function validate($data);

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Sanitize
     *
     * @param mixed $data Daa
     *
     * @return mixed
     */
    public function sanitize($data)
    {
        return $data;
    }

    /**
     * Throw error exception
     *
     * @param string $message    Message
     * @param array  $arguments  Language label arguments OPTIONAL
     * @param mixed  $pathItem   Path item key OPTIONAL
     * @param string $publicName Path item public name OPTIONAL
     *
     * @return \XLite\Core\Validator\Exception
     */
    protected function throwError($message, array $arguments = array(), $pathItem = null, $publicName = null)
    {
        $exception = new \XLite\Core\Validator\Exception($message);
        $exception->setLabelArguments($arguments);

        if (isset($pathItem)) {
            $exception->addPathItem($pathItem);
        }

        if ($publicName) {
            $exception->setPublicName($publicName);
        }

        return $exception;
    }

    /**
     * Throw internal error exception
     *
     * @param string $message   Message
     * @param array  $arguments Language label arguments OPTIONAL
     *
     * @return \XLite\Core\ValidateException
     */
    protected function throwInternalError($message, array $arguments = array())
    {
        $exception = new \XLite\Core\Validator\Exception($message);
        $exception->setLabelArguments($arguments);
        $exception->markAsInternal();

        return $exception;
    }
}
