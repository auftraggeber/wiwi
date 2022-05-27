<?php

use de\langner_dev\ui\utils\document\Button;
use de\langner_dev\ui\utils\document\Div;
use de\langner_dev\ui\utils\document\Form;
use de\langner_dev\ui\utils\document\FormDivItem;
use de\langner_dev\ui\utils\document\FormLabel;
use de\langner_dev\ui\utils\document\FormText;
use de\langner_dev\ui\utils\document\Link;
use de\langner_dev\ui\utils\document\NumberInput;
use de\langner_dev\ui\utils\document\Section;
use de\langner_dev\ui\utils\document\TextInput;

require_once 'head.php';
require_once 'model/classes/Good.php';

define("FORM_CREATE_GOOD_NAME", "good-name");
define("FORM_CREATE_GOOD_POSITION", "good-position");
define("FORM_CREATE_GOOD_DURATION", "good-duration");

define("EDIT_GOOD_GET_ID_PARAM", "e");

$doc = buildHTMLDocument("Teile");

$overview = new Section("Teile");
$overview->container_xl()->mb_3();

$overview->addElement(
    new Div(
        "<p>Teile sind Produktionsgüter, die von verschiedenen <a href='machine'>Maschinen</a> bearbeitet werden.</p>Hier sehen die Stammdaten aller Teile. Über die entsprechenden Schaltflächen können Sie diese bearbeiten oder entfernen.<br/>Zudem können Sie auch neue anlegen."
    )
);
$overview->addElement(
    new Link(
        (new Button("Teil erstellen",BS5_BUTTON_TYPE_SUCCESS, true))->mt_3(), "good?" . EDIT_GOOD_GET_ID_PARAM
    )
);

$doc->addElement($overview);


#
#
#   erstellen / bearbeiten
#
#
#

if (isset($_GET[EDIT_GOOD_GET_ID_PARAM])) {


    $create_section = new Section("Teil anlegen", "h2");
    $create_section->container_xl()->mb_5();

    $form = new Form("good");

    $form_name_item = new FormDivItem();
    $form_name_item->addElement(new FormLabel("Name", FORM_CREATE_GOOD_NAME));
    $form_name_item->addElement((new TextInput(FORM_CREATE_GOOD_NAME, FORM_CREATE_GOOD_NAME))->required()->max_length(16)->placeholder("Name"));
    $form_name_item->addElement(new FormText("Geben Sie den Namen des Teils an. Der Name ist auf maximal 16 Zeichen begrenzt."));

    $form_duration_item = new FormDivItem();
    $form_duration_item->addElement(new FormLabel("Bearbeitungsdauer", FORM_CREATE_GOOD_DURATION));
    $form_duration_item->addElement((new NumberInput(FORM_CREATE_GOOD_DURATION, 10, 1440, 1,FORM_CREATE_GOOD_DURATION))->required()->placeholder("Bearbeitungsdauer (min)"));
    $form_duration_item->addElement(new FormText("Geben Sie die Bearbeitungszeit in Minuten an."));

    $form_position_item = new FormDivItem();
    $form_position_item->addElement(new FormLabel("Position", FORM_CREATE_GOOD_POSITION));
    $form_position_item->addElement((new NumberInput(FORM_CREATE_GOOD_POSITION, 0, null, 1, FORM_CREATE_GOOD_POSITION))->placeholder("Position"));
    $form_position_item->addElement(new FormText("<i>Optional</i><br/>Wenn dieses Teil zu einem weiteren Teil gehört, gibt die Position an, welche Teile vor diesem fertig gestellt werden müssen. Falls zwei Subteile eines Teils gleichzeitig bearbeitet werden können, muss die Position gleich sein."));


    $form->addElement($form_name_item);
    $form->addElement($form_duration_item);
    $form->addElement($form_position_item);

    $create_section->addElement($form);

    $doc->addElement($create_section);
}



#
#
# Anzeige
#
#
$table_section = new Section("Verfügbare Teile", "h2");
$table_section->container_xl()->mb_5();



$doc->printHTMLText();