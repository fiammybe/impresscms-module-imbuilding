<?php
/**
 * Classes responsible for managing BaseModule item objects
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
 * Item handler class
 */
class ItemHandler extends \icms_ipf_Handler
{
    /**
     * Constructor
     *
     * @param \icms_db_legacy_Database $db database connection object
     */
    public function __construct(\icms_db_legacy_Database &$db)
    {
        parent::__construct($db, "item", "item_id", "object_identifier_name", "object_identifier_desc", "basemodule");
/** IMBUILDING_HANDLER_ENABLE_UPLOAD **/
    }

/** IMBUILDING_HANDLER_EVENT_METHODS **/
}