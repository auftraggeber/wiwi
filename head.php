<?php

use de\langner_dev\ui\utils\document\NavBar;
use de\langner_dev\ui\utils\document\NavBarList;
use de\langner_dev\ui\utils\document\NavBarListItem;

require_once "view/document.php";
require_once "view/elements.php";

function buildHTMLDocument($title = "WIWI", $icon = null): \de\langner_dev\ui\utils\document\HTMLDocument {
    $doc = new \de\langner_dev\ui\utils\document\HTMLDocument($title, $icon);

    $navbar = new NavBar("WIWI", );

    $navbar_list = new NavBarList();
    $navbar_list_machine_item = new NavBarListItem("Maschinen", "machine");

    $navbar->addElement($navbar_list);
    $navbar_list->addElement($navbar_list_machine_item);

    $doc->addElement($navbar);

    return $doc;
}

function displayDate($time) {
    if (!isset($time) || !is_int($time)) {
        return "";
    }

    return date("d. m. Y", $time);
}