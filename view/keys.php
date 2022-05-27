<?php
use function de\langner_dev\ui\utils\localization\l_str;

require_once "localization.php";

define("INDEX_TITLE", l_str("index_title"));
define("ICON_URL", null);

define("NAV_BAR_TITLE", l_str("nav_bar_title"));
define("NAV_BAR_TOGGLE_NAV_AREA_LABEL", l_str("nav_bar_toggle_nav_area_label")); # toggle navigation
define("NAV_BAR_ITEM_FILES_TITLE", l_str("nav_bar_item_files_title"));
define("NAV_BAR_ITEM_FILES_ID", "nav_bar_item_files_id");
define("NAV_BAR_LOGIN_BUTTON_CONTENT", l_str("login"));
define("NAV_BAR_LOGOUT_BUTTON_CONTENT", l_str("logout"));

define("INDEX_WELCOME_TEXT", l_str("index_welcome_text", array("files")));

define("INDEX_SERVICES_TITLE", l_str("index_services_title"));

define("INDEX_SERIVCES_TEXT", l_str("index_services_text"));

define("SERVICE_WITHOUT_LOGIN_CARD_TITLE", l_str("service_without_login_card_title"));
define("SERVICE_WITHOUT_LOGIN_CARD_TEXT", l_str("service_without_login_card_text"));
define("SERVICE_WITHOUT_LOGIN_CARD_ITEM_STORAGE_PER_FILE_TITLE", l_str("service_without_login_card_item_storage_per_file_title"));

define("SERVICE_WITH_LOGIN_CARD_TITLE", l_str("service_with_login_card_title"));
define("SERVICE_WITH_LOGIN_CARD_TEXT", l_str("service_with_login_card_text"));
define("SERVICE_WITH_LOGIN_CARD_ITEM_STORAGE_PER_FILE_TITLE", l_str("service_without_login_card_item_storage_per_file_title"));

define("UNSET_STR", "!!UNSET");

define("LANGUAGE_DE", "Deutsch");
