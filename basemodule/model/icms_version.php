<?php
/**
 * BaseModule version information
 *
 * This file holds the configuration information of this module
 *
 * @copyright	IMBUILDING_COPYRIGHT
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		IMBUILDING_TAG_AUTHOR_NAME <IMBUILDING_TAG_AUTHOR_EMAIL>
 * @package		basemodule
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

// Load Composer autoloader if available
$composerAutoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

/**  General Information  */
$modversion = [
    "name"                      => _MI_BASEMODULE_MD_NAME,
    "version"                   => "1.0.0",
    "description"               => _MI_BASEMODULE_MD_DESC,
    "author"                    => "IMBUILDING_TAG_AUTHOR_NAME",
    "credits"                   => "IMBUILDING_TAG_CREDITS",
    "help"                      => "",
    "license"                   => "GPL-2.0-or-later",
    "official"                  => 0,
    "dirname"                   => basename(__DIR__),
    "modname"                   => "basemodule",

    /**  Images information  */
    "iconsmall"                 => "images/icon_small.png",
    "iconbig"                   => "images/icon_big.png",
    "image"                     => "images/icon_big.png",

    /**  Development information */
    "status_version"            => "1.0.0",
    "status"                    => "Beta",
    "date"                      => "Unreleased",
    "author_word"               => "",
    "warning"                   => _CO_ICMS_WARNING_BETA,

    /** Contributors */
    "developer_website_url"     => "IMBUILDING_TAG_AUTHOR_WEBSITE_URL",
    "developer_website_name"    => "IMBUILDING_TAG_AUTHOR_WEBSITE_NAME",
    "developer_email"           => "IMBUILDING_TAG_AUTHOR_EMAIL",

    /** Administrative information */
    "hasAdmin"                  => 1,
    "adminindex"                => "admin/index.php",
    "adminmenu"                 => "admin/menu.php",

    /** Install and update information - using namespaced Installer class */
    "onInstall"                 => ['\\ImpressCMS\\Module\\Basemodule\\Installer', 'onInstall'],
    "onUpdate"                  => ['\\ImpressCMS\\Module\\Basemodule\\Installer', 'onUpdate'],
    "onUninstall"               => ['\\ImpressCMS\\Module\\Basemodule\\Installer', 'onUninstall'],

    /** Search information */
    "hasSearch"                 => 0,
    "search"                    => ["file" => "include/search.inc.php", "func" => "basemodule_search"],

    /** Menu information */
    "hasMain"                   => 1,

    /** Comments information */
    "hasComments"               => 1,
    "comments"                  => [
        "itemName" => "post_id",
        "pageName" => "post.php",
        "callbackFile" => "include/comment.inc.php",
        "callback" => [
            "approve" => "basemodule_com_approve",
            "update" => "basemodule_com_update"
        ]
    ]
];

/** other possible types: testers, translators, documenters and other */
$modversion['people']['developers'][] = "IMBUILDING_TAG_DEVELOPER_INFO";

/** Manual */
$modversion['manual']['wiki'][] = "<a href='http://wiki.impresscms.org/index.php?title=BaseModule' target='_blank'>English</a>";

/** Database information */
/** IMBUILDING_OBJECT_ITEMS */
$modversion["tables"] = icms_getTablesArray($modversion['dirname'], $modversion['object_items']);

/** Object handlers - using FQCN */
/** IMBUILDING_OBJECT_HANDLERS */

/** Templates information */
$modversion['templates'] = [
/** IMBUILDING_OBJECT_TEMPLATES */
    ['file' => 'basemodule_header.html', 'description' => 'Module Header'],
    ['file' => 'basemodule_footer.html', 'description' => 'Module Footer']
];

/** Blocks information */
/** To come soon in imBuilding... */

/** Preferences information */
/** To come soon in imBuilding... */

/** Notification information */
/** To come soon in imBuilding... */