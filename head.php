<?php

use de\langner_dev\ui\utils\document\HTMLDocument;
use de\langner_dev\ui\utils\document\NavBar;
use de\langner_dev\ui\utils\document\NavBarList;
use de\langner_dev\ui\utils\document\NavBarListItem;

require_once "view/document.php";
require_once "view/elements.php";

function buildHTMLDocument($title = "WIWI", $icon = null): HTMLDocument {
    $doc = new HTMLDocument($title, $icon);

    $navbar = new NavBar("WIWI", "index");

    $navbar_list = new NavBarList();

    $navbar->addElement($navbar_list);
    $navbar_list->addElement(new NavBarListItem("Aufträge", "order"));
    $navbar_list->addElement(new NavBarListItem("Maschinen", "machine"));
    $navbar_list->addElement(new NavBarListItem("Teile", "good"));

    $doc->addElement($navbar);

    return $doc;
}

function displayDate($time) {
    if (!isset($time) || !is_int($time) || $time <= 0) {
        return "";
    }

    return date("d. m. Y", $time);
}