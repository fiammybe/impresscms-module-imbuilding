<?php
/**
 * Admin page to manage items
 *
 * List, add, edit and delete item objects
 *
 * @copyright	IMBUILDING_COPYRIGHT
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		IMBUILDING_TAG_AUTHOR_NAME <IMBUILDING_TAG_AUTHOR_EMAIL>
 * @package		basemodule
 */

/**
 * Edit an Item
 *
 * @param int $item_id Item id to be edited
 */
function edititem(int $item_id = 0): void
{
    global $basemodule_item_handler, $icmsAdminTpl;

    $itemObj = $basemodule_item_handler->get($item_id);

    if (!$itemObj->isNew()) {
        icms::$module->displayAdminMenu(/** IMBUILDING_OBJECT_SORT **/, _AM_BASEMODULE_ITEMS . " > " . _CO_ICMS_EDITING);
        $sform = $itemObj->getForm(_AM_BASEMODULE_ITEM_EDIT, "additem");
        $sform->assign($icmsAdminTpl);
    } else {
        icms::$module->displayAdminMenu(/** IMBUILDING_OBJECT_SORT **/, _AM_BASEMODULE_ITEMS . " > " . _CO_ICMS_CREATINGNEW);
        $sform = $itemObj->getForm(_AM_BASEMODULE_ITEM_CREATE, "additem");
        $sform->assign($icmsAdminTpl);
    }
    $icmsAdminTpl->display("db:basemodule_admin_item.html");
}

include_once "admin_header.php";

$basemodule_item_handler = icms_getModuleHandler("item", basename(dirname(__DIR__)), "basemodule");

/** Use a naming convention that indicates the source of the content of the variable */
$clean_op = "";

/** Create a whitelist of valid values, be sure to use appropriate types for each value
 * Be sure to include a value for no parameter, if you have a default condition
 */
$valid_op = ["mod", "changedField", "additem", "del", "view", ""];

if (isset($_GET["op"])) {
    $clean_op = htmlentities($_GET["op"]);
}
if (isset($_POST["op"])) {
    $clean_op = htmlentities($_POST["op"]);
}

/** Again, use a naming convention that indicates the source of the content of the variable */
$clean_item_id = isset($_GET["item_id"]) ? (int)$_GET["item_id"] : 0;

/**
 * in_array() is a native PHP function that will determine if the value of the
 * first argument is found in the array listed in the second argument. Strings
 * are case sensitive and the 3rd argument determines whether type matching is
 * required
 */
if (in_array($clean_op, $valid_op, true)) {
    switch ($clean_op) {
        case "mod":
        case "changedField":
            icms_cp_header();
            edititem($clean_item_id);
            break;

        case "additem":
            $controller = new icms_ipf_Controller($basemodule_item_handler);
            $controller->storeFromDefaultForm(_AM_BASEMODULE_ITEM_CREATED, _AM_BASEMODULE_ITEM_MODIFIED);
            break;

        case "del":
            $controller = new icms_ipf_Controller($basemodule_item_handler);
            $controller->handleObjectDeletion();
            break;

        case "view":
            $itemObj = $basemodule_item_handler->get($clean_item_id);
            icms_cp_header();
            $itemObj->displaySingleObject();
            break;

        default:
            icms_cp_header();
            icms::$module->displayAdminMenu(/** IMBUILDING_OBJECT_SORT **/, _AM_BASEMODULE_ITEMS);
            $objectTable = new icms_ipf_view_Table($basemodule_item_handler);
            $objectTable->addColumn(new icms_ipf_view_Column("object_identifier_name"));
            $objectTable->addIntroButton("additem", "item.php?op=mod", _AM_BASEMODULE_ITEM_CREATE);
            $icmsAdminTpl->assign("basemodule_item_table", $objectTable->fetch());
            $icmsAdminTpl->display("db:basemodule_admin_item.html");
            break;
    }
    icms_cp_footer();
}
/**
 * If you want to have a specific action taken because the user input was invalid,
 * place it at this point. Otherwise, a blank page will be displayed
 */