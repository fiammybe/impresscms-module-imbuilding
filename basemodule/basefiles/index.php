<?php
/**
 * User index page of the module
 *
 * Redirects to the default object page
 *
 * @copyright	IMBUILDING_COPYRIGHT
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		IMBUILDING_TAG_AUTHOR_NAME <IMBUILDING_TAG_AUTHOR_EMAIL>
 * @package		basemodule
 */

include_once "../../mainfile.php";
include_once ICMS_ROOT_PATH . "/header.php";

header('Location: IMBUILDING_DEFAULT_OBJECT.php');
exit;