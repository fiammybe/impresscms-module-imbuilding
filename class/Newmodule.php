<?php
/**
 * Classes responsible for generating new module
 *
 * @copyright	http://smartfactory.ca The SmartFactory
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since		1.0
 * @author		marcan aka Marc-André Lanciault <marcan@smartfactory.ca>
 * @version		$Id$
 */

defined("ICMS_ROOT_PATH") or die("ICMS root path not defined");

class mod_imbuilding_Newmodule {
	public $moduleinfo;
	public $archiveUrl = FALSE;
	private $_basemoduleInfo;
	private $_moduleBaseFilesPath;
	private $_searchArray;
	private $_replaceArray;
	private $_moduleObj;
	private $_log;
	private $_objectsArray;
	private $_sort = array();

	/**
	 * Constructor
	 *
	 * @param mod_imbuilding_Module $moduleObj module object
	 */
	public function __construct($moduleObj) {
		$this->_moduleObj = $moduleObj;
		$this->_moduleBaseFilesPath = IMBUILDING_ROOT_PATH . 'basemodule/basefiles';
		$this->_basemoduleInfo['ModuleName'] = 'BaseModule';
		$this->_basemoduleInfo['MODULENAME'] = 'BASEMODULE';
		$this->_basemoduleInfo['modulename'] = 'basemodule';
		$this->_basemoduleInfo['Modulename'] = 'Basemodule';
		$this->_basemoduleInfo['namespace'] = 'ImpressCMS\\Module\\Basemodule';
		$this->_basemoduleInfo['author'] = 'IMBUILDING_TAG_AUTHOR_NAME';
		$this->_basemoduleInfo['author_email'] = 'IMBUILDING_TAG_AUTHOR_EMAIL';
		$this->_basemoduleInfo['author_website_name'] = 'IMBUILDING_TAG_AUTHOR_WEBSITE_NAME';
		$this->_basemoduleInfo['author_website_url'] = 'IMBUILDING_TAG_AUTHOR_WEBSITE_URL';
		$this->_basemoduleInfo['author_profile_url'] = 'IMBUILDING_TAG_AUTHOR_PROFILE_URL';
		$this->_basemoduleInfo['developer_info'] = 'IMBUILDING_TAG_DEVELOPER_INFO';
		$this->_basemoduleInfo['credits'] = 'IMBUILDING_TAG_CREDITS';
		$this->_basemoduleInfo['default_object'] = 'IMBUILDING_DEFAULT_OBJECT';
		$this->_basemoduleInfo['copyright'] = 'IMBUILDING_COPYRIGHT';

		$this->setModuleInfo('ModuleName', $this->_moduleObj->getVar('module_name'));
		$this->setModuleInfo('author', $this->_moduleObj->getVar('author_name'));
		$this->setModuleInfo('author_email', $this->_moduleObj->getVar('author_email'));
		$this->setModuleInfo('author_website_url', $this->_moduleObj->getVar('author_website_url'));
		$this->setModuleInfo('author_profile_url', $this->_moduleObj->getVar('author_profile'));
		$this->setModuleInfo('author_website_name', $this->_moduleObj->getVar('author_website_name'));
		$this->setModuleInfo('credits', $this->_moduleObj->getVar('module_credits'));
		$this->setModuleInfo('default_object', $this->_moduleObj->getVar('default_object'));
		$this->setModuleInfo('copyright', $this->_moduleObj->getVar('module_copyright'));
	}

	/**
	 * Set module info
	 *
	 * @param string $key module info key
	 * @param string $value module info value
	 */
	private function setModuleInfo($key, $value) {
		$this->moduleinfo[$key] = trim($value);
	}

	/**
	 * Create module
	 */
	public function create() {
		/** check to see if the module is ready to be generated */
		$this->checkIfReady();

		if (function_exists('mb_convert_encoding')) {
			$newModuleName = mb_convert_encoding($this->moduleinfo['ModuleName'], "", "auto");
		} else {
			$newModuleName = $this->moduleinfo['ModuleName'];
		}

		$this->addLog('Starting the module generation process...');

		$newModuleName = str_replace('-', 'xyz', $newModuleName);
		$newModuleName = preg_replace("/[[:punct:]]/i", "", $newModuleName);
		$newModuleName = str_replace('xyz', '-', $newModuleName);
		$newModuleName = str_replace(' ', '_', $newModuleName);
		$this->moduleinfo['ModuleName'] = $newModuleName;

		$this->moduleinfo['MODULENAME'] = strtoupper(str_replace("-", "_", $this->moduleinfo['ModuleName']));
		$this->moduleinfo['modulename'] = strtolower(str_replace("-", "_", $this->moduleinfo['ModuleName']));
		$this->moduleinfo['Modulename'] = ucfirst(strtolower($this->moduleinfo['ModuleName']));
		// Generate namespace based on module name (PSR-4 compliant)
		$this->moduleinfo['namespace'] = 'ImpressCMS\\Module\\' . $this->moduleinfo['Modulename'];

		$this->moduleinfo['developer_info'] = $this->getDeveloperInfo();

		foreach ($this->moduleinfo as $k => $v) {
			$this->_searchArray[] = $this->_basemoduleInfo[$k];
			$this->_replaceArray[] = $v;
		}

		$this->addLog('Copying the basemodule files and replacing content');
		$this->cloneFileFolder($this->_moduleBaseFilesPath);

		foreach ($this->moduleinfo as $k => $v) {
			$this->addLog("$k: $v");
		}

		$common_language_data = '';
		$admin_language_data = '';
		$mi_language_data = '';
		$main_language_data = '';

		$this->addLog('Creating sort array');
		$imbuilding_object_handler = icms_getModuleHandler("object", basename(dirname(dirname(__FILE__))), "imbuilding");
		$criteria = new icms_db_criteria_Compo();
		$criteria->add(new icms_db_criteria_Item("module_id", $this->_moduleObj->getVar("module_id")));
		$criteria->setSort("sort");
		$criteria->setOrder("ASC");
		$objects = $imbuilding_object_handler->getObjects($criteria, TRUE);
		$i = 0;
		foreach (array_keys($objects) as $object_id) $this->_sort[$object_id] = $i++;

		$this->addLog('Creating model classes and files...');
		foreach ($objects as $objectObj) {
			$this->createClassFiles($objectObj);
			$common_language_data .= $this->getCommonLanguageData($objectObj);
			$admin_language_data .= $this->getAdminLanguageData($objectObj);
			$mi_language_data .= $this->getMiLanguageData($objectObj);
			$main_language_data .= $this->getMainLanguageData($objectObj);
		}
		unset($objects);

		$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/language/english/common.php';
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/language/english/common.php';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace('/** IMBUILDING_COMMON_LANGUAGE_DATA */', $common_language_data, $content);
		file_put_contents($newPath, $content);
		$this->addLog('Common language file created: ' . $newPath);

		$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/language/english/admin.php';
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/language/english/admin.php';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace('/** IMBUILDING_ADMIN_LANGUAGE_DATA */', $admin_language_data, $content);
		file_put_contents($newPath, $content);
		$this->addLog('Admin language file created: ' . $newPath);

		$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/language/english/modinfo.php';
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/language/english/modinfo.php';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace('/** IMBUILDING_MI_LANGUAGE_DATA */', $mi_language_data, $content);
		file_put_contents($newPath, $content);
		$this->addLog('Admin language file created: ' . $newPath);

		$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/language/english/main.php';
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/language/english/main.php';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace('/** IMBUILDING_MAIN_LANGUAGE_DATA */', $main_language_data, $content);
		file_put_contents($newPath, $content);
		$this->addLog('Main language file created: ' . $newPath);

		// Generate Installer.php class
		$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/class/Installer.php';
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/class/Installer.php';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		file_put_contents($newPath, $content);
		$this->addLog('Installer class file created: ' . $newPath);

		$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/icms_version.php';
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/icms_version.php';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace('/** IMBUILDING_OBJECT_ITEMS */', $this->getObjectItemsArray(), $content);
		$content = str_replace('/** IMBUILDING_OBJECT_TEMPLATES */', $this->getObjectTemplatesArray(), $content);
		$content = str_replace('/** IMBUILDING_OBJECT_HANDLERS */', $this->getObjectHandlersArray(), $content);
		//$content = str_replace('/** IMBUILDING_NOTIFICATIONS_FROM */', $this->getNotificationsFromArray(), $content);
		file_put_contents($newPath, $content);
		$this->addLog('icms_version file created: ' . $newPath);

		$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/admin/menu.php';
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/admin/menu.php';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace('/** IMBUILDING_ADMIN_MENU_ITEMS */', $this->getAdminMenuItems(), $content);
		file_put_contents($newPath, $content);
		$this->addLog('admin menu file created: ' . $newPath);

		$this->addLog('Module generated succesfully');

		$this->createArchive();
		$this->addLog('Creating archive: ' . $this->archiveUrl);
	}

	/**
	 * Check if the module is ready to be created
	 */
	private function checkIfReady() {
		/** check if the module's default object was set */
		if (!$this->_moduleObj->getVar('default_object', 'e'))
			redirect_header('module.php?op=mod&module_id=' . $this->_moduleObj->id(), 3, _AM_IMBUILDING_CREATE_MODULE_NO_DEFAULT_OBJECT);

		/** check if all objects have a default field */
		foreach($this->_moduleObj->getObjects() as $objectObj) {
			if (!$objectObj->getVar('object_identifier_name')) {
				redirect_header('object.php?op=mod&object_id=' . $objectObj->id(), 3, sprintf(_AM_IMBUILDING_CREATE_MODULE_OBJECT_NO_DEFAULT_FIELD, $objectObj->title()));
			}
		}
	}

	/**
	 * Get developer info
	 *
	 * @return string developer info
	 */
	private function getDeveloperInfo() {
		if ($this->moduleinfo['author_profile_url']) {
			$ret = "[url=" . $this->moduleinfo['author_profile_url'] . "]" . $this->moduleinfo['author'] . "[/url]";
		} else {
			$ret = $this->moduleinfo['author'];
		}
		return $ret;
	}

	/**
	 * Clone file/folder
	 *
	 * @param string $path path to file/folder to clone
	 */
	private function cloneFileFolder($path) {
		$newPath = str_replace($this->_moduleBaseFilesPath, ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'], $path);
		$newPath = str_replace('basemodule', $this->moduleinfo['modulename'], $newPath);

		if (strpos($path, '.svn') === FALSE) {
			if (is_dir($path)) {
				// Create new dir
				if (!file_exists($newPath) && !mkdir($newPath, 0777, TRUE)){
					icms_core_Debug::message('could not create dir: ' . $newPath);
				}
				chmod($newPath, 0777);
				// check all files in dir, and process it
				if ($handle = opendir($path)) {
					while ($file = readdir($handle)) {
						if ($file != '.' && $file != '..') {
							$this->cloneFileFolder("$path/$file");
						}
					}
					closedir($handle);
				}
			} else {
				if (preg_match('/(.jpg|.gif|.png|.zip)$/i', $path)) {
					copy($path, $newPath);
				} else {
					// file, read it
					$content = file_get_contents($path);
					$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
					file_put_contents($newPath, $content);
				}
			}
		}
	}

	/**
	 * Create class files for object
	 *
	 * @param mod_imbuilding_Object $objectObj object object
	 */
	private function createClassFiles(&$objectObj) {
		$enable_seo = $objectObj->getVar('enable_seo');
		$object_name = $this->getObjectName($objectObj->getVar('object_name'));
		$this->_objectsArray[] = $object_name;
		$classSearchArray = array(
								'item',
								'Item',
								'ITEM',
								'object_identifier_name',
								'object_identifier_desc',
								'/** IMBUILDING_INITIATE_VARS **/',
								'/** IMBUILDING_OBJECT_TEMPLATE_VARS **/',
								'/** IMBUILDING_HANDLER_ENABLE_UPLOAD **/',
								'/** IMBUILDING_HANDLER_EVENT_METHODS **/',
								'/** IMBUILDING_OBJECT_SORT **/');

		$classReplaceArray = array(
								$object_name,
								ucfirst($object_name),
								strtoupper($object_name),
								$objectObj->getVar('object_identifier_name'),
								$objectObj->getVar('object_identifier_desc'),
								$this->getInitiateVarsCode($objectObj),
								$this->getUserTemplateVars($objectObj),
								$this->getHandlerEnableUpload($objectObj),
								$this->getHandlerEventMethods($objectObj),
								$this->getObjectSort($objectObj));

		if ($enable_seo) {
			$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/class/persistableseoobject.php';
		} else {
			$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/class/persistableobject.php';
		}
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/class/' . ucfirst($object_name) . '.php';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace($classSearchArray, $classReplaceArray, $content);
		file_put_contents($newPath, $content);
		$this->addLog('Path of new object class file is: ' . $newPath);

		$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/class/persistableobjecthandler.php';
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/class/' . ucfirst($object_name) . 'Handler.php';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace($classSearchArray, $classReplaceArray, $content);
		file_put_contents($newPath, $content);
		$this->addLog('Path of new handler class file is: ' . $newPath);

		if ($enable_seo) {
			$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/seoobject.php';
		} else {
			$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/object.php';
		}
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/' . $object_name . '.php';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace($classSearchArray, $classReplaceArray, $content);
		file_put_contents($newPath, $content);
		$this->addLog('New user object file created: ' . $newPath);

		$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/admin/object.php';
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/admin/' . $object_name . '.php';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace($classSearchArray, $classReplaceArray, $content);
		file_put_contents($newPath, $content);
		$this->addLog('New admin object file created: ' . $newPath);

		$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/templates/basemodule_item.html';
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/templates/' . $this->moduleinfo['modulename'] . '_' . $object_name . '.html';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace($classSearchArray, $classReplaceArray, $content);
		file_put_contents($newPath, $content);
		$this->addLog('New user template file created: ' . $newPath);

		$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/templates/basemodule_admin_item.html';
		$newPath = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'] . '/templates/' . $this->moduleinfo['modulename'] . '_admin_' . $object_name . '.html';
		$content = file_get_contents($path);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace($classSearchArray, $classReplaceArray, $content);
		file_put_contents($newPath, $content);
		$this->addLog('New admin template file created: ' . $newPath);
	}

	/**
	 * convert object name to valid object name
	 *
	 * @param string $object_name
	 * @return string converted object name
	 */
	private function getObjectName($object_name) {
		$ret = strtolower($object_name);
		$ret = str_replace('-', 'xyz', $ret);
		$ret = preg_replace("/[[:punct:]]/i", "", $ret);
		$ret = str_replace('xyz', '-', $ret);
		$ret = str_replace(' ', '_', $ret);
		return $ret;
	}

	/**
	 * generate initiate vars code for object class
	 *
	 * @param mod_imbuilding_Object $objectObj object object
	 * @return string
	 */
	private function getInitiateVarsCode(&$objectObj) {
		$fieldObjs = $objectObj->getFields();
		$ret = '';
		$field_control = array();
		foreach($fieldObjs as $fieldObj) {
			$field_name = $fieldObj->getVar('field_name');
			$this->addLog('Generating initiate var for field ' . $fieldObj->getVar('field_name'));
			/** check if special type */
			$field_type = $fieldObj->getVar('field_type', 'e');
			switch ($field_type) {
				case 'XOBJ_DTYPE_FILE':
					$objectObj->setVar('enable_upload', TRUE);
					break;
				case 'XOBJ_DTYPE_IMAGE':
					$field_control[] = '$this->setControl("' . $field_name . '", "image")';
					$objectObj->setVar('enable_upload', TRUE);
					break;
				case 'TOBJ_DTYPE_USER':
					$field_type = 'XOBJ_DTYPE_INT';
					$field_control[] = '$this->setControl("' . $field_name . '", "user")';
					break;
				case 'TOBJ_DTYPE_LANGUAGE':
					$field_type = 'XOBJ_DTYPE_TXTBOX';
					$field_control[] = '$this->setControl("' . $field_name . '", "language")';
					break;
				case 'TOBJ_DTYPE_YESNO':
					$field_type = "XOBJ_DTYPE_INT";
					$field_control[] = '$this->setControl("' . $field_name . '", "yesno")';
					break;
			}

			if ($fieldObj->getVar("field_refer") > 0) {
				$imbuilding_object_handler = icms_getModuleHandler("object", basename(dirname(dirname(__FILE__))), "imbuilding");
				$objectObj_tmp = $imbuilding_object_handler->get($fieldObj->getVar("field_refer"));
				$field_type = "XOBJ_DTYPE_INT";
				$field_control[] = '$this->setControl("' . $field_name . '", array(
			"itemHandler" => "' . $objectObj_tmp->getVar("object_name") . '",
			"method" => "getList",
			"module" => "' . $this->moduleinfo['modulename'] . '"))';
			}

			$ret .= '		$this->quickInitVar("' . $field_name . '", ' . $field_type . ', ' . $fieldObj->getVar('field_required') . ');
';
		}
		if ($objectObj->getVar('object_counter')) {
			$ret .= '		$this->initCommonVar("counter");
';
		}
		if ($objectObj->getVar('object_dohtml')) {
			$ret .= '		$this->initCommonVar("dohtml");
';
		}
		if ($objectObj->getVar('object_dobr')) {
			$ret .= '		$this->initCommonVar("dobr");
';
		}
		if ($objectObj->getVar('object_doimage')) {
			$ret .= '		$this->initCommonVar("doimage");
';
		}
		if ($objectObj->getVar('object_dosmiley')) {
			$ret .= '		$this->initCommonVar("dosmiley");
';
		}
		if ($objectObj->getVar('object_doxcode')) {
			$ret .= '		$this->initCommonVar("docxode");
';
		}

		if (count($field_control) > 0) {
			foreach ($field_control as $control) {
				$ret .= '		' . $control . ';
';
			}
		}

		return $ret;
	}

	/**
	 * Generate template vars for object
	 *
	 * @param mod_imbuilding_Object $objectObj object object
	 * @return string
	 */
	private function getUserTemplateVars(&$objectObj) {
		$fieldObjs = $objectObj->getFields();
		$object_name = $this->getObjectName($objectObj->getVar('object_name'));
		$OBJECT_NAME = strtoupper($object_name);
		$this->addLog('Generating user template vars for object ' . $objectObj->getVar('object_name'));
		$ret = '';
		foreach ($fieldObjs as $fieldObj) {
			$field_name = $fieldObj->getVar('field_name');
			if ($field_name != $objectObj->getVar('object_identifier_name')) {
				$this->addLog($field_name);
				if ($fieldObj->getVar('field_type', 'e') == 'XOBJ_DTYPE_TXTAREA') {
					$ret .= '		<div><b><{$smarty.const._CO_' . $this->moduleinfo['MODULENAME'] . '_' . $OBJECT_NAME . '_' . strtoupper($field_name)  . '}></b></div>
				<div><{$' . $this->moduleinfo['modulename'] . '_' . $object_name . '.' . $field_name . '}></div>
';
				} else {
					$ret .= '		<div><b><{$smarty.const._CO_' . $this->moduleinfo['MODULENAME'] . '_' . $OBJECT_NAME . '_' . strtoupper($field_name)  . '}></b>: <{$' . $this->moduleinfo['modulename'] . '_' . $object_name . '.' . $field_name . '}></div>
';
				}
			}
		}
		return $ret;
	}

	/**
	 * Check if upload needs to be enabled for object handler and generate PHP code
	 *
	 * @param mod_imbuilding_Object $objectObj object object
	 * @return string
	 */
	private function getHandlerEnableUpload(&$objectObj) {
		if (!$objectObj->getVar("enable_upload")) return "";
		return '		$this->enableUpload(array("image/gif", "image/jpeg", "image/pjpeg", "image/png"), 512000, 800, 600);';
	}

	/**
	 * Generate event methods for object handler
	 *
	 * @param mod_imbuilding_Object $objectObj object object
	 * @return string
	 */
	private function getHandlerEventMethods(&$objectObj) {
		$imbuilding_field_handler = icms_getModuleHandler("field", basename(dirname(dirname(__FILE__))), "imbuilding");
		$fields = $imbuilding_field_handler->getObjects(icms_buildCriteria(array("field_refer" => $objectObj->getVar("object_id"))));
		if (count($fields) == 0) return "";

		$ret = '	/*
	 * beforeDelete event
	 *
	 * Event automatically triggered by IcmsPersistable Framework before the object is deleted
	 *
	 * @param mod_' . $this->moduleinfo['modulename'] . '_' . ucfirst($objectObj->getVar("object_name")) . ' $obj ' . ucfirst($objectObj->getVar("object_name")) . ' object
	 * @return bool
	 */
	protected function beforeDelete(&$obj) {
';

		$objectname_id = $this->getObjectName($objectObj->getVar("object_name")) . "_id";
		$object_ids = array();
		foreach ($fields as $fieldObj) {
			if (count(array_intersect($object_ids, array($fieldObj->getVar("object_id")))) > 0) continue;
			$object_ids[] = $fieldObj->getVar("object_id");
			$imbuilding_object_handler = icms_getModuleHandler("object", basename(dirname(dirname(__FILE__))), "imbuilding");
			$objectObj_tmp = $imbuilding_object_handler->get($fieldObj->getVar("object_id"));
			$handler = '$' . $this->moduleinfo['modulename'] . '_' . $objectObj_tmp->getVar("object_name") . '_handler';
			$ret .= '		' . $handler . ' = icms_getModuleHandler("'. $objectObj_tmp->getVar("object_name") . '", basename(dirname(dirname(__FILE__))), "' . $this->moduleinfo['modulename'] . '");
		' . $handler . '->deleteAll(icms_buildCriteria(array("' . $fieldObj->getVar("field_name") . '" => $obj->getVar("' . $objectname_id . '"))));
';
		}

		$ret .= '		return TRUE;
	}';

		return $ret;
	}

	private function getObjectSort(&$objectObj) {
		return $this->_sort[$objectObj->getVar("object_id")];
	}

	/**
	 * Get common language data for object
	 *
	 * @param mod_imbuilding_Object $objectObj object object
	 * @return string
	 */
	private function getCommonLanguageData(&$objectObj) {
		$object_name = $this->getObjectName($objectObj->getVar('object_name'));
		$OBJECT_NAME = strtoupper($object_name);
		$this->addLog('Adding common language data for object ' . $object_name);
		$ret = "// " . $object_name . "
";
		$fieldObjs = $objectObj->getFields();
		foreach($fieldObjs as $fieldObj) {
			$ret .= "define(\"_CO_" . $this->moduleinfo['MODULENAME'] . "_" . $OBJECT_NAME . "_" . strtoupper($fieldObj->getVar('field_name')) . "\", \"" . $fieldObj->getVar('field_caption') . "\");
";
			$ret .= "define(\"_CO_" . $this->moduleinfo['MODULENAME'] . "_" . $OBJECT_NAME . "_" . strtoupper($fieldObj->getVar('field_name')) . "_DSC\", \"" . $fieldObj->getVar('field_desc') . "\");
";
		}
		return $ret;
	}

	/**
	 * Get admin language data for object
	 *
	 * @param mod_imbuilding_Object $objectObj object object
	 * @return string
	 */
	private function getAdminLanguageData(&$objectObj) {
		$object_name = $this->getObjectName($objectObj->getVar('object_name'));
		$OBJECT_NAME = strtoupper($object_name);
		$this->addLog('Adding admin language data for object ' . $object_name);

		$classSearchArray = array(
								'item',
								'Item',
								'ITEM'
							);
		$classReplaceArray = array(
								$object_name,
								ucfirst($object_name),
								$OBJECT_NAME
						);

		$path = IMBUILDING_ROOT_PATH . 'basemodule' . '/model/language/english/admin_language.tpl';
		$handle = fopen($path, "r");
		$content = fread($handle, filesize($path));
		fclose($handle);
		$content = str_replace($this->_searchArray, $this->_replaceArray, $content);
		$content = str_replace($classSearchArray, $classReplaceArray, $content);
		return $content;
	}

	/**
	 * Get module info language data for object
	 *
	 * @param mod_imbuilding_Object $objectObj object object
	 * @return string
	 */
	private function getMiLanguageData(&$objectObj) {
		$object_name = $this->getObjectName($objectObj->getVar('object_name'));
		$OBJECT_NAME = strtoupper($object_name);
		$this->addLog('Adding MI language data for object ' . $object_name);
		$ret = '';
		$ret .= "define(\"_MI_" . $this->moduleinfo['MODULENAME'] . "_" . $OBJECT_NAME . "S\", \"" . ucfirst($object_name) . "s\");
";
		return $ret;
	}

	/**
	 * Get main language data for object
	 *
	 * @param mod_imbuilding_Object $objectObj object object
	 * @return string
	 */
	private function getMainLanguageData(&$objectObj) {
		$object_name = $this->getObjectName($objectObj->getVar('object_name'));
		$OBJECT_NAME = strtoupper($object_name);
		$this->addLog('Adding Main language data for object ' . $object_name);
		$ret = '';
		$ret .= "define(\"_MD_" . $this->moduleinfo['MODULENAME'] . "_ALL_" . $OBJECT_NAME . "S\", \"All " . $object_name . "s\");
";
		return $ret;
	}

	/**
	 * Get object items for icms_version.php
	 *
	 * @return string
	 */
	private function getObjectItemsArray() {
		$ret = '';
		for ($i=0; $i<count($this->_objectsArray); $i++) {
			if ($i == 0) {
				$ret .= '$modversion[\'object_items\'][1] = \'' . $this->_objectsArray[$i] . '\';
';
			} else {
				$ret .= '$modversion[\'object_items\'][] = \'' . $this->_objectsArray[$i] . '\';
';
			}
		}
		return $ret;
	}

	/**
	 * Get template information for icms_version.php
	 *
	 * @return string
	 */
	private function getObjectTemplatesArray() {
		$ret = '';
		if (!$this->_objectsArray) return $ret;

		foreach ($this->_objectsArray as $object) {
			$object_name = $this->getObjectName($object);
			$ret .= '	["file" => "' . $this->moduleinfo['modulename'] . "_admin_" . $object_name . '.html", "description" => "' . $object_name . ' Admin Index"],
	["file" => "' . $this->moduleinfo['modulename'] . "_" . $object_name . '.html", "description" => "' . $object_name . ' Index"],
';
		}
		return $ret;
	}

	/**
	 * Get object handlers array for icms_version.php using FQCN
	 *
	 * @return string
	 */
	private function getObjectHandlersArray() {
		$ret = '';
		if (!$this->_objectsArray) return $ret;

		foreach ($this->_objectsArray as $object) {
			$object_name = $this->getObjectName($object);
			$Object_name = ucfirst($object_name);
			// The namespace stored has single backslashes, we need to escape them for the generated PHP file
			$escapedNamespace = str_replace('\\', '\\\\', $this->moduleinfo['namespace']);
			$ret .= '$modversion[\'object_handlers\'][\'' . $object_name . '\'] = \'\\\\' . $escapedNamespace . '\\\\' . $Object_name . 'Handler\';
';
		}
		return $ret;
	}

	/**
	 * Get admin menu items for admin/menu.php
	 *
	 * @return string
	 */
	private function getAdminMenuItems() {
		$ret = '';
		if (!$this->_objectsArray) return $ret;

		foreach ($this->_objectsArray as $object) {
			$object_name = $this->getObjectName($object);
			$OBJECT_NAME = strtoupper($object_name);
			$ret .= '$adminmenu[] = [
	"title" => _MI_' . $this->moduleinfo['MODULENAME'] . '_' . $OBJECT_NAME . 'S,
	"link" => "admin/' . $object_name . '.php"];
';
		}
		return $ret;
	}

	/**
	 * Generate zip archive of generated module source - based heavily on
	 * this response in stackoverflow :
	 * http://stackoverflow.com/questions/4914750/how-to-zip-a-whole-folder-using-php
	 */
	private function createArchive() {
		//icms::$logger->disableLogger();

		$zip = new ZipArchive();

		$fileName = $this->moduleinfo['modulename'] . '_' . time() . '.zip';
		$archiveFilePath = ICMS_UPLOAD_PATH . '/imbuilding/packages/' . $fileName;
		$archiveSource = ICMS_CACHE_PATH . '/imbuilding/' . $this->moduleinfo['modulename'];

		$zip->open($archiveFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

		// Create recursive directory iterator
		/** @var SplFileInfo[] $files */
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($archiveSource),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file)
		{
			// Skip directories (they would be added automatically)
			if (!$file->isDir())
			{
				// Get real and relative path for current file
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($rootPath) + 1);

				// Add current file to archive
				$zip->addFile($filePath, $relativePath);
			}
		}

		// Zip archive will be created only after closing object
		$zip->close();

		$this->archiveUrl = ICMS_UPLOAD_URL . '/imbuilding/packages/' . $fileName;
	}

	/**
	 * add log entry
	 *
	 * @param string $text log text
	 */
	private function addLog($text) {
		$this->_log[] = $text;
	}

	/**
	 * display log
	 *
	 * @return string
	 */
	public function displayLog() {
		$ret = "";
		foreach ($this->_log as $v) {
			$ret .= $v . "<br />";
		}
		return $ret;
	}
}