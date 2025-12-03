<?php
/**
 * Common file of the module included on all pages of the module
 *
 * @copyright	IMBUILDING_COPYRIGHT
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		IMBUILDING_TAG_AUTHOR_NAME <IMBUILDING_TAG_AUTHOR_EMAIL>
 * @package		basemodule
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

if (!defined("BASEMODULE_DIRNAME")) {
    define("BASEMODULE_DIRNAME", $modversion["dirname"] = basename(dirname(__DIR__)));
}
if (!defined("BASEMODULE_URL")) {
    define("BASEMODULE_URL", ICMS_URL . "/modules/" . BASEMODULE_DIRNAME . "/");
}
if (!defined("BASEMODULE_ROOT_PATH")) {
    define("BASEMODULE_ROOT_PATH", ICMS_ROOT_PATH . "/modules/" . BASEMODULE_DIRNAME . "/");
}
if (!defined("BASEMODULE_IMAGES_URL")) {
    define("BASEMODULE_IMAGES_URL", BASEMODULE_URL . "images/");
}
if (!defined("BASEMODULE_ADMIN_URL")) {
    define("BASEMODULE_ADMIN_URL", BASEMODULE_URL . "admin/");
}

// Load Composer autoloader if available
$composerAutoload = BASEMODULE_ROOT_PATH . 'vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

// Include the common language file of the module
icms_loadLanguageFile("basemodule", "common");