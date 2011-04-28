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
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.0
 */

namespace XLite\Upgrade;

/**
 * Cell 
 * 
 * @see   ____class_see____
 * @since 1.0.0
 */
class Cell extends \XLite\Base\Singleton
{
    /**
     * Name of TmpVar
     */
    const CELL_NAME = 'upgradeCell';

    /**
     * Dedicated cell entry - LC core
     */
    const CORE_IDENTIFIER = '____CORE____';

    /**
     * List of cell entries 
     * 
     * @var   array
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $entries = array();

    /**
     * Core version to upgrade to
     * 
     * @var   string
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $coreVersion;

    /**
     * List of cores recieved from marketplace (cache)
     *
     * @var   array
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $coreVersions;

    /**
     * List of incompatible modules 
     * 
     * @var   array
     * @see   ____var_see____
     * @since 1.0.0
     */
    protected $incompatibleModules = array();

    // {{{ Public methods

    /**
     * Getter
     * 
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Return list of incompatible modules 
     * 
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getIncompatibleModules()
    {
        return array_filter(
            array_map(
                array(\XLite\Core\Database::getRepo('\XLite\Model\Module'), 'find'),
                $this->incompatibleModules
            )
        );
    }

    /**
     * Method to clean up cell
     * 
     * @param boolean $clearCoreVersion Flag OPTIONAL
     *  
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function clear($clearCoreVersion = true)
    {
        $this->entries = array();
        $this->incompatibleModules = array();

        if ($clearCoreVersion) {
            $this->coreVersion = null;
        }

        $this->collectEntries();
    }

    /**
     * Define version of core to upgrade to
     *
     * @param string $version Version to set
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function setCoreVersion($version)
    {
        $this->coreVersion = $version;
    }

    /**
     * Add module to update/install
     * 
     * @param \XLite\Model\Module $module Module model
     * @param boolean             $force  Flag to install modules OPTIONAL
     *  
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function addMarketplaceModule(\XLite\Model\Module $module, $force = false)
    {
        if ($force) {
            $toUpgrade = $module;

        } else {
            $method = $this->isUpgrade() ? 'getModuleForUpgrade' : 'getModuleForUpdate';

            // "ForUpgrade" or "ForUpdate" method call
            $toUpgrade = \XLite\Core\Database::getRepo('\XLite\Model\Module')->$method(
                $module,
                $this->getCoreMajorVersion()
            );
        }

        $hash = $module->getActualName();

        if ($toUpgrade) {
            $this->addEntry($hash, 'Module\Marketplace', array($module, $toUpgrade));

        } elseif ($module->getEnabled()) {
            $this->incompatibleModules[$hash] = $module->getModuleID();
        }
    }

    /**
     * Add module to update/install
     * 
     * @param string $path Path to uploaded module pack
     *  
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function addUploadedModule($path)
    {
        $this->addEntry(md5($path), 'Module\Uploaded', array($path));
    }

    /**
     * Check if all entry packages were downloaded and unpacked
     * 
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isDownloaded()
    {
        $list = \Includes\Utils\ArrayManager::getObjectsArrayFieldValues($this->getEntries(), 'getRepositoryPath');

        return array_filter($list) === $list;
    }

    // }}}

    // {{{ Core version routines

    /**
     * Quick access to the "Core" entry
     * 
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getCoreMajorVersion()
    {
        return $this->callCoreEntryMethod('getMajorVersionNew') ?: \XLite::getInstance()->getMajorVersion();
    }

    /**
     * Quick access to the "Core" entry
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getCoreMinorVersion()
    {
        return $this->callCoreEntryMethod('getMinorVersionNew') ?: \XLite::getInstance()->getMinorVersion();
    }

    /**
     * Quick access to the "Core" entry
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getCoreVersion()
    {
        return $this->callCoreEntryMethod('getVersionNew') ?: \XLite::getInstance()->getVersion();
    }

    /**
     * Get list of available kernel versions from the marketplace
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getCoreVersions()
    {
        if (!isset($this->coreVersions)) {
            $this->coreVersions = (array) \XLite\Core\Marketplace::getInstance()->getCoreVersions($this->getCacheTTL());
        }

        return $this->coreVersions;
    }

    /**
     * Check if we upgrade core major version
     * 
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function isUpgrade()
    {
        return \XLite::getInstance()->checkVersion($this->getCoreMajorVersion(), '<');
    }

    /**
     * Helper
     * 
     * @param string $method Name of method to call
     *  
     * @return mixed
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function callCoreEntryMethod($method)
    {
        $entry = \Includes\Utils\ArrayManager::getIndex($this->getEntries(), self::CORE_IDENTIFIER, true);

        // If core entry found, call method with the passed name on it
        return isset($entry) ? $entry->$method() : null;
    }

    // }}}

    // {{{ Constructor and destructor

    /**
     * Save data in DB
     * 
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function __destruct()
    {
        \XLite\Core\TmpVars::getInstance()->{self::CELL_NAME} = array(
            $this->entries,
            $this->incompatibleModules
        );
    }

    /**
     * Protected constructor
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function __construct()
    {
        parent::__construct();

        // Upload addons info into the database
        \XLite\Core\Marketplace::getInstance()->saveAddonsList($this->getCacheTTL());

        list($entries, $incompatibleModules) = \XLite\Core\TmpVars::getInstance()->{self::CELL_NAME};

        if (is_array($entries)) {
            $this->entries = array_merge($this->entries, $entries);
            $this->incompatibleModules = array_merge($this->incompatibleModules, (array) $incompatibleModules);
        } else {
            $this->collectEntries();
        }
    }

    /**
     * Return so called "short" TTL
     *
     * @return integer
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getCacheTTL()
    {
        return \XLite\Core\Marketplace::TTL_SHORT;
    }

    // }}}

    // {{{ Methods to collect entries

    /**
     * Check and add (if needed) upgrade entries
     * 
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function collectEntries()
    {
        // :NOTE: do not change call order!
        $this->checkForCoreUpgrade();
        $this->checkForModulesUpgrade();
    }

    /**
     * Check and add (if needed) core upgrade entry
     * 
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function checkForCoreUpgrade()
    {
        $majorVersion = $this->coreVersion ?: \XLite::getInstance()->getMajorVersion();
        $data = \Includes\Utils\ArrayManager::getIndex($this->getCoreVersions(), $majorVersion, true);

        if (is_array($data)) {
            $this->addEntry(self::CORE_IDENTIFIER, 'Core', array_merge(array($majorVersion), $data));
        }
    }

    /**
     * Check and add (if needed) upgrade entries
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function checkForModulesUpgrade()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Module::P_INSTALLED} = true;

        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Module')->search($cnd) as $module) {
            $this->addMarketplaceModule($module);
        }
    }

    /**
     * Common method to add entries
     * 
     * @param string $index Index in the "entries" array
     * @param string $class Entry class name
     * @param array  $args  Constructor arguments OPTIONAL
     *  
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function addEntry($index, $class, array $args = array())
    {
        try {
            $entry = \Includes\Pattern\Factory::create('\XLite\Upgrade\Entry\\' . $class, $args);

        } catch (\Exception $exception) {
            $entry = null;
            $this->logAddEntryError($exception);
        }

        if (isset($entry)) {
            $this->entries[$index] = $entry;
        }
    }

    /**
     * Logging
     *        
     * @param \Exception $exception Thrown exception
     *                                              
     * @return void                                 
     * @see    ____func_see____                     
     * @since  1.0.0                                
     */                                             
    protected function logAddEntryError(\Exception $exception)
    {                                                        
        \XLite\Logger::getInstance()->log($exception->getMessage(), $this->getLogLevel());
    }

    /**
     * Return type of log messages
     *                            
     * @return integer            
     * @see    ____func_see____   
     * @since  1.0.0              
     */                           
    protected function getLogLevel()
    {                               
        return PEAR_LOG_WARNING;    
    }

    // }}}
}
