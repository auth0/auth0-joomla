<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        http://www.gnu.org/licenses/gpl-2.0-standalone.html
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */
defined('_JEXEC') or die('Restricted access');

class com_auth0InstallerScript
{
    public function install(JAdapterInstance $adapter)
    {
        jimport('joomla.installer.installer');
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');
        $app = JFactory::getApplication();
        $src = dirname(__FILE__);
        if (is_dir($src . '/modules/mod_auth0')) {
            $installer = new JInstaller;
            $result = @$installer->install($src . '/modules/mod_auth0');
            if ($result) {
                $app->enqueueMessage('Installing module [mod_auth0] was successful.', 'message');
            } else {
                $app->enqueueMessage('Installing module [mod_auth0] was unsuccessful.', 'error');
            }
        }
    }

    public function uninstall(JAdapterInstance $adapter)
    {
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        jimport('joomla.installer.installer');
        $db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `element` = "mod_auth0" AND `type` = "module"');
        $id = $db->loadResult();
        if ($id) {
            $installer = new JInstaller;
            $result = @$installer->uninstall('module', $id, 1);
            if ($result) {
                $app->enqueueMessage('Uninstalling module [mod_auth0] was successful.', 'message');
            } else {
                $app->enqueueMessage('Uninstalling module [mod_auth0] was unsuccessful. Module may not exist or need manual uninstallation.', 'error');
            }

        }

    }
}
