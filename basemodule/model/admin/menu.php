<?php
/**
 * Configuring the admin side menu for the module
 *
 * @copyright	IMBUILDING_COPYRIGHT
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		IMBUILDING_TAG_AUTHOR_NAME <IMBUILDING_TAG_AUTHOR_EMAIL>
 * @package		basemodule
 */

/** IMBUILDING_ADMIN_MENU_ITEMS */

$module = icms::handler("icms_module")->getByDirname(basename(dirname(__DIR__)));

$headermenu[] = [
    "title" => _PREFERENCES,
    "link" => "../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $module->getVar("mid")
];
$headermenu[] = [
    "title" => _CO_ICMS_GOTOMODULE,
    "link" => ICMS_URL . "/modules/basemodule/"
];
$headermenu[] = [
    "title" => _CO_ICMS_UPDATE_MODULE,
    "link" => ICMS_URL . "/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module=" . basename(dirname(__DIR__))
];
$headermenu[] = [
    "title" => _MODABOUT_ABOUT,
    "link" => ICMS_URL . "/modules/basemodule/admin/about.php"
];

unset($module_handler);