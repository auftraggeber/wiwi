<?php

use de\langner_dev\ui\utils\document\Button;
use de\langner_dev\ui\utils\document\Div;
use de\langner_dev\ui\utils\document\ErrorAlert;
use de\langner_dev\ui\utils\document\Form;
use de\langner_dev\ui\utils\document\FormDivItem;
use de\langner_dev\ui\utils\document\FormLabel;
use de\langner_dev\ui\utils\document\FormText;
use de\langner_dev\ui\utils\document\Link;
use de\langner_dev\ui\utils\document\NumberInput;
use de\langner_dev\ui\utils\document\Section;
use de\langner_dev\ui\utils\document\Select;
use de\langner_dev\ui\utils\document\SubmitButton;
use de\langner_dev\ui\utils\document\SuccessAlert;
use de\langner_dev\ui\utils\document\TextInput;
use de\langner_dev\wiwi\model\Good;

require_once 'head.php';
require_once 'model/classes/Good.php';

define("FORM_CREATE_GOOD_NAME", "good-name");
define("FORM_CREATE_GOOD_MAIN_GOOD_AMOUNT", "good-amount");
define("FORM_CREATE_GOOD_MAIN_GOOD_ID", "good-main-good-id");

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

if (isset($_POST[FORM_CREATE_GOOD_NAME])) {
    $name = $_POST[FORM_CREATE_GOOD_NAME];
    $main_good = (isset($_POST[FORM_CREATE_GOOD_MAIN_GOOD_ID])) ? new Good($_POST[FORM_CREATE_GOOD_MAIN_GOOD_ID]) : null;

    $main_amount = (isset($_POST[FORM_CREATE_GOOD_MAIN_GOOD_AMOUNT]) && $_POST[FORM_CREATE_GOOD_MAIN_GOOD_AMOUNT] != "") ? intval($_POST[FORM_CREATE_GOOD_MAIN_GOOD_AMOUNT]) : 1;

    $good = new Good();
    $good->setName($name);

    if ($main_good != null && $main_good->exists())
        $good->setMainGood($main_good);

    $good->setAmount($main_amount);

    if ($good->save()) {
        $overview->addElement(new SuccessAlert("Das Teil konnte erfolgreich gesichert werden."));
    }
    else {
        $overview->addElement(new ErrorAlert("Das Teil konnte nicht gesichert werden. Versuchen Sie es bitte später erneut."));
    }
}

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

    $select_values = array(-1 => "--- Kein Hauptteil ---");

    foreach (Good::getGoods() as $good) {
        $select_values[$good->getId()] = $good->getName();
    }

    $select = new Select($select_values);
    $select->setId(FORM_CREATE_GOOD_MAIN_GOOD_ID);
    $select->setAttribute("name", FORM_CREATE_GOOD_MAIN_GOOD_ID);

    $form_select_item = new FormDivItem();
    $form_select_item->addElement(new FormLabel("Übergeordnetes Teil", FORM_CREATE_GOOD_MAIN_GOOD_ID));
    $form_select_item->addElement($select);
    $form_select_item->addElement(new FormText("Wählen Sie das übergeordnete Teil aus. Da Teile aus weiteren Teilen bestehen können, wird dieses Teil automatisch mit in den Auftrag des übergeordneten Teils einbezogen."));

    $form_position_item = new FormDivItem();
    $form_position_item->addElement(new FormLabel("Anzahl", FORM_CREATE_GOOD_MAIN_GOOD_AMOUNT));
    $form_position_item->addElement((new NumberInput(FORM_CREATE_GOOD_MAIN_GOOD_AMOUNT, 1, null, 1, FORM_CREATE_GOOD_MAIN_GOOD_AMOUNT))->placeholder("Anzahl"));
    $form_position_item->addElement(new FormText("<i>Optional</i><br/>Wenn dieses Teil zu einem weiteren Teil gehört, gibt die Anzahl an, wie viele Teile zu dem übergeordneten Teil gehören."));


    $form->addElement($form_name_item);
    $form->addElement($form_select_item);
    $form->addElement($form_position_item);
    $form->addElement(new SubmitButton("Teil sichern", BS5_BUTTON_TYPE_PRIMARY));

    $create_section->addElement($form);

    $doc->addElement($create_section);
}

$doc->setUrl("good");

$doc->printHTMLText();