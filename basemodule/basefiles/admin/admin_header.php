<?php
/**
 * Admin header file
 *
 * This file is included in all pages of the admin side and being so, it proceeds to a few
 * common things.
 *
 * @copyright	IMBUILDING_COPYRIGHT
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		IMBUILDING_TAG_AUTHOR_NAME <IMBUILDING_TAG_AUTHOR_EMAIL>
 * @package		basemodule
 */

include_once "../../../include/cp_header.php";
include_once ICMS_ROOT_PATH . "/modules/" . basename(dirname(__DIR__)) . "/include/common.php";

if (!defined("BASEMODULE_ADMIN_URL")) {
    define("BASEMODULE_ADMIN_URL", BASEMODULE_URL . "admin/");
}

include_once BASEMODULE_ROOT_PATH . "include/requirements.php";