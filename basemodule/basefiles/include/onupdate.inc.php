<?php
/**
 * Legacy file containing onUpdate and onInstall functions for the module
 *
 * NOTE: Modern modules should use the namespaced Installer class instead.
 * This file is kept for backward compatibility with older ImpressCMS versions.
 *
 * @copyright	IMBUILDING_COPYRIGHT
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		IMBUILDING_TAG_AUTHOR_NAME <IMBUILDING_TAG_AUTHOR_EMAIL>
 * @package		basemodule
 * @see         \ImpressCMS\Module\Basemodule\Installer
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

// This needs to be the latest db version
define('BASEMODULE_DB_VERSION', 1);

/**
 * It is possible to define custom functions which will be called when the module is updating at the
 * correct time in update incrementation. Simply define a function named <dirname_db_upgrade_db_version>
 */
/*
function basemodule_db_upgrade_1(): bool {
    return true;
}
function basemodule_db_upgrade_2(): bool {
    return true;
}
*/

/**
 * Module update handler (legacy function)
 *
 * @param \icms_module_Object $module Module object
 * @return bool
 */
function icms_module_update_basemodule(\icms_module_Object $module): bool
{
    return true;
}

/**
 * Module install handler (legacy function)
 *
 * @param \icms_module_Object $module Module object
 * @return bool
 */
function icms_module_install_basemodule(\icms_module_Object $module): bool
{
    return true;
}