<?php

use de\langner_dev\ui\utils\document\Button;
use de\langner_dev\ui\utils\document\Div;
use de\langner_dev\ui\utils\document\ErrorAlert;
use de\langner_dev\ui\utils\document\Form;
use de\langner_dev\ui\utils\document\FormDivItem;
use de\langner_dev\ui\utils\document\FormLabel;
use de\langner_dev\ui\utils\document\FormText;
use de\langner_dev\ui\utils\document\HiddenInput;
use de\langner_dev\ui\utils\document\Link;
use de\langner_dev\ui\utils\document\NumberInput;
use de\langner_dev\ui\utils\document\Section;
use de\langner_dev\ui\utils\document\Select;
use de\langner_dev\ui\utils\document\SubmitButton;
use de\langner_dev\ui\utils\document\SuccessAlert;
use de\langner_dev\ui\utils\document\Table;
use de\langner_dev\ui\utils\document\TableBody;
use de\langner_dev\ui\utils\document\TableBodyItem;
use de\langner_dev\ui\utils\document\TableHead;
use de\langner_dev\ui\utils\document\TableHeadItem;
use de\langner_dev\ui\utils\document\TableRow;
use de\langner_dev\ui\utils\document\TextInput;
use de\langner_dev\ui\utils\document\WarningAlert;
use de\langner_dev\wiwi\model\Good;

require_once 'head.php';
require_once 'model/classes/Good.php';

define("FORM_EDIT_GOOD_ID", "good-id");
define("FORM_CREATE_GOOD_NAME", "good-name");
define("FORM_CREATE_GOOD_MAIN_GOOD_AMOUNT", "good-amount");
define("FORM_CREATE_GOOD_MAIN_GOOD_ID", "good-main-good-id");

define("EDIT_GOOD_GET_ID_PARAM", "e");
define("DELETE_GOOD_GET_ID_PARAM", "d");

function buildListId(Good $good): string {
    return "good-list-element-" . $good->getId();
}

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

    $id = $_POST[FORM_EDIT_GOOD_ID] ?? -1;

    $good = new Good($id);
    $good->setName($name);

    if ($main_good != null && $main_good->exists())
        $good->setMainGood($main_good);
    else
        $good->setMainGood(null);

    $good->setAmount($main_amount);

    if ($good->save()) {
        $overview->addElement(new SuccessAlert("Das Teil konnte erfolgreich gesichert werden."));
    }
    else {
        $overview->addElement(new ErrorAlert("Das Teil konnte nicht gesichert werden. Versuchen Sie es bitte später erneut."));
    }
}

if (isset($_GET[DELETE_GOOD_GET_ID_PARAM]) && $_GET[DELETE_GOOD_GET_ID_PARAM] != null) {
    $good = new Good($_GET[DELETE_GOOD_GET_ID_PARAM]);

    if ($good->delete()) {
        $overview->addElement(new SuccessAlert("Das Teil wurde erfolgreich entfernt."));
    }
    else $overview->addElement(new ErrorAlert("Das Teil konnte nicht entfernt werden. Versuchen Sie es später erneut."));
}

#
#
#   erstellen / bearbeiten
#
#
#
$goods = Good::getGoods();

if (isset($_GET[EDIT_GOOD_GET_ID_PARAM])) {
    $id = ($_GET[EDIT_GOOD_GET_ID_PARAM] != null) ? intval($_GET[EDIT_GOOD_GET_ID_PARAM]) : -1;
    $good = new Good($id);

    $name = ($good->exists()) ? $good->getName() : null;
    $main_id = ($good->exists() && $good->getMainGood() != null && $good->getMainGood()->exists()) ? $good->getMainGood()->getId() : null;
    $amount = ($good->exists()) ? $good->getAmount() : null;

    $create_section = new Section("Teil anlegen", "h2");
    $create_section->container_xl()->mb_5();

    $form = new Form("good");

    if ($id > 0)
        $form->addElement(new HiddenInput(FORM_EDIT_GOOD_ID, $id));

    $form_name_item = new FormDivItem();
    $form_name_item->addElement(new FormLabel("Name", FORM_CREATE_GOOD_NAME));
    $form_name_item->addElement((new TextInput(FORM_CREATE_GOOD_NAME, FORM_CREATE_GOOD_NAME))->required()->max_length(16)->placeholder("Name")->value($name));
    $form_name_item->addElement(new FormText("Geben Sie den Namen des Teils an. Der Name ist auf maximal 16 Zeichen begrenzt."));

    $select_values = array(-2 => "--- Kein Hauptteil ---");

    foreach ($goods as $good) {
        if ($good->getId() == $id)
            continue;

        $select_values[$good->getId()] = $good->getName();
    }

    $select = new Select($select_values);
    $select->setId(FORM_CREATE_GOOD_MAIN_GOOD_ID);
    $select->setAttribute("name", FORM_CREATE_GOOD_MAIN_GOOD_ID);
    $select->setSelectedKey($main_id);

    $form_select_item = new FormDivItem();
    $form_select_item->addElement(new FormLabel("Übergeordnetes Teil", FORM_CREATE_GOOD_MAIN_GOOD_ID));
    $form_select_item->addElement($select);
    $form_select_item->addElement(new FormText("Wählen Sie das übergeordnete Teil aus. Da Teile aus weiteren Teilen bestehen können, wird dieses Teil automatisch mit in den Auftrag des übergeordneten Teils einbezogen."));

    $form_position_item = new FormDivItem();
    $form_position_item->addElement(new FormLabel("Anzahl", FORM_CREATE_GOOD_MAIN_GOOD_AMOUNT));
    $form_position_item->addElement((new NumberInput(FORM_CREATE_GOOD_MAIN_GOOD_AMOUNT, 1, null, 1, FORM_CREATE_GOOD_MAIN_GOOD_AMOUNT))->placeholder("Anzahl")->value($amount));
    $form_position_item->addElement(new FormText("<i>Optional</i><br/>Wenn dieses Teil zu einem weiteren Teil gehört, gibt die Anzahl an, wie viele Teile zu dem übergeordneten Teil gehören."));


    $form->addElement($form_name_item);
    /*$form->addElement($form_select_item);
    $form->addElement($form_position_item);*/
    $form->addElement(new SubmitButton("Teil sichern", BS5_BUTTON_TYPE_PRIMARY));

    $create_section->addElement($form);

    $doc->addElement($create_section);
}

$doc->setUrl("good");



#
#
#
# anzeige
#
#




$section = new Section("Verfügbare Teile", "h2");
//$section->addElement((new WarningAlert("Beachten Sie, dass Sie auch die Subteile, die nicht mehr benötigt werden, entfernen."))->mt_1()->mb_2());
$section->container_xl();

$table_div = new Div("", array("table-responsive"));
$table_div->container_fluid()->bg_body()->p_0()->border()->rounded();
$table = new Table();
$table->mb_0();
$thead = new TableHead();
$thead_row = new TableRow();

$thead_row->addElement(new TableHeadItem("#"));
$thead_row->addElement(new TableHeadItem("Name"));
//$thead_row->addElement(new TableHeadItem("Subteile"));
$thead_row->addElement(new TableHeadItem("Aktion"));

$tbody = new TableBody();

if (empty($goods)) {
    $row = new TableRow();
    $td = new TableBodyItem("Es existieren noch keine Teile.");
    $td->setAttribute("colspan", 3);
    $td->text_danger()->text_center();
    $row->addElement($td);

    $tbody->addElement($row);
}

foreach ($goods as $good) {
    $row = new TableRow();

    $link = new Link($good->getId());
    $link->setId(buildListId($good));

    $row->addElement(new TableBodyItem($link));
    $row->addElement(new TableBodyItem($good->getName()));

    $subgoods = $good->getSubGoods();

    if (empty($subgoods)) {
       // $row->addElement((new TableBodyItem("Keine Subteile"))->text_danger());
    }
    else {
        $str = "";

        foreach ($subgoods as $subgood) {
            if ($str != "")
                $str .= "<hr style='margin: 0.5em 0;'/>";

            $str .= "<a href='#" . buildListId($subgood) . "'>" . $subgood->getName() . "</a><span class='ms-3'>x" . $subgood->getAmount() . "</span>";
        }

       // $row->addElement(new TableBodyItem($str));
    }

    $action_td = new TableBodyItem();
    $edit = new Link(new Button("Bearbeiten", BS5_BUTTON_TYPE_PRIMARY, true), "good?" . EDIT_GOOD_GET_ID_PARAM . "=" . $good->getId());
    $delete = new Link(new Button("Löschen", BS5_BUTTON_TYPE_DANGER, true), "good?" . DELETE_GOOD_GET_ID_PARAM . "=" . $good->getId());
    $delete->ms_2();
    $action_td->addElement($edit);
    $action_td->addElement($delete);
    $action_td->setStyle("white-space: nowrap;");

    $row->addElement($action_td);

    $tbody->addElement($row);
}

$thead->addElement($thead_row);
$table->addElement($thead);
$table->addElement($tbody);
$table_div->addElement($table);
$section->addElement($table_div);

$doc->addElement($section);

$doc->printHTMLText();