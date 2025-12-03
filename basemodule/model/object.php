<?php
/**
 * Item page
 *
 * @copyright	IMBUILDING_COPYRIGHT
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		IMBUILDING_TAG_AUTHOR_NAME <IMBUILDING_TAG_AUTHOR_EMAIL>
 * @package		basemodule
 */

include_once "header.php";

$xoopsOption["template_main"] = "basemodule_item.html";
include_once ICMS_ROOT_PATH . "/header.php";

$basemodule_item_handler = icms_getModuleHandler("item", basename(__DIR__), "basemodule");

/** Use a naming convention that indicates the source of the content of the variable */
$clean_item_id = isset($_GET["item_id"]) ? (int)$_GET["item_id"] : 0;
$itemObj = $basemodule_item_handler->get($clean_item_id);

if ($itemObj && !$itemObj->isNew()) {
    $icmsTpl->assign("basemodule_item", $itemObj->toArray());
} else {
    $icmsTpl->assign("basemodule_title", _MD_BASEMODULE_ALL_ITEMS);

    $objectTable = new icms_ipf_view_Table($basemodule_item_handler, false, []);
    $objectTable->isForUserSide();
    $objectTable->addColumn(new icms_ipf_view_Column("object_identifier_name"));
    $icmsTpl->assign("basemodule_item_table", $objectTable->fetch());
}

$icmsTpl->assign("basemodule_module_home", '<a href="' . ICMS_URL . "/modules/" . icms::$module->getVar("dirname") . '/">' . icms::$module->getVar("name") . "</a>");

include_once "footer.php";