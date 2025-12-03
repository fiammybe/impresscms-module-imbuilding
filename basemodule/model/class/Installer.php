<?php
/**
 * Installer class for BaseModule
 *
 * Contains installation, update, and uninstall logic for the module
 *
 * @copyright	IMBUILDING_COPYRIGHT
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		IMBUILDING_TAG_AUTHOR_NAME <IMBUILDING_TAG_AUTHOR_EMAIL>
 * @package		basemodule
 */

namespace ImpressCMS\Module\Basemodule;

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

/**
 * Module installer class
 */
class Installer
{
    /**
     * Module installation handler
     *
     * Called when the module is first installed
     *
     * @param \icms_module_Object $module Module object
     * @return bool
     */
    public static function onInstall(\icms_module_Object $module): bool
    {
        return true;
    }

    /**
     * Module update handler
     *
     * Called when the module is updated
     *
     * @param \icms_module_Object $module Module object
     * @param int $previousVersion Previous version number
     * @return bool
     */
    public static function onUpdate(\icms_module_Object $module, int $previousVersion): bool
    {
        return true;
    }

    /**
     * Module uninstall handler
     *
     * Called when the module is uninstalled
     *
     * @param \icms_module_Object $module Module object
     * @return bool
     */
    public static function onUninstall(\icms_module_Object $module): bool
    {
        return true;
    }
}
