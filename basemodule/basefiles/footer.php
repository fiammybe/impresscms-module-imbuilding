<?php
/**
 * Footer page included at the end of each page on user side of the module
 *
 * @copyright	IMBUILDING_COPYRIGHT
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		IMBUILDING_TAG_AUTHOR_NAME <IMBUILDING_TAG_AUTHOR_EMAIL>
 * @package		basemodule
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

$icmsTpl->assign("basemodule_adminpage", "<a href='" . ICMS_URL . "/modules/" . icms::$module->getVar("dirname") . "/admin/index.php'>" . _MD_BASEMODULE_ADMIN_PAGE . "</a>");
$icmsTpl->assign("basemodule_is_admin", icms_userIsAdmin(BASEMODULE_DIRNAME));
$icmsTpl->assign('basemodule_url', BASEMODULE_URL);
$icmsTpl->assign('basemodule_images_url', BASEMODULE_IMAGES_URL);

$xoTheme->addStylesheet(BASEMODULE_URL . 'module' . ((defined("_ADM_USE_RTL") && _ADM_USE_RTL) ? '_rtl' : '') . '.css');

include_once ICMS_ROOT_PATH . '/footer.php';