<?php
/**
 * Class representing BaseModule item objects with SEO support
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
 * Item object class with SEO support
 */
class Item extends \icms_ipf_seo_Object
{
    /**
     * Constructor
     *
     * @param \icms_ipf_Handler $handler Object handler
     */
    public function __construct(\icms_ipf_Handler &$handler)
    {
        parent::__construct($handler);

        $this->quickInitVar("item_id", XOBJ_DTYPE_INT, true);
/** IMBUILDING_INITIATE_VARS **/

        $this->initiateSEO();
    }

    /**
     * Overriding the icms_ipf_Object::getVar method to assign a custom method on some
     * specific fields to handle the value before returning it
     *
     * @param string $key key of the field
     * @param string $format format that is requested
     * @return mixed value of the field that is requested
     */
    public function getVar(string $key, string $format = "s"): mixed
    {
        if ($format === "s" && in_array($key, [], true)) {
            return call_user_func([$this, $key]);
        }
        return parent::getVar($key, $format);
    }
}