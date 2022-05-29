<?php

use de\langner_dev\ui\utils\document\Alert;
use de\langner_dev\ui\utils\document\Button;
use de\langner_dev\ui\utils\document\Div;
use de\langner_dev\ui\utils\document\ErrorAlert;
use de\langner_dev\ui\utils\document\Form;
use de\langner_dev\ui\utils\document\FormDivItem;
use de\langner_dev\ui\utils\document\FormLabel;
use de\langner_dev\ui\utils\document\FormText;
use de\langner_dev\ui\utils\document\HiddenInput;
use de\langner_dev\ui\utils\document\HTMLDocument;
use de\langner_dev\ui\utils\document\Input;
use de\langner_dev\ui\utils\document\Link;
use de\langner_dev\ui\utils\document\NavBar;
use de\langner_dev\ui\utils\document\NavBarList;
use de\langner_dev\ui\utils\document\NavBarListItem;
use de\langner_dev\ui\utils\document\NumberInput;
use de\langner_dev\ui\utils\document\Section;
use de\langner_dev\ui\utils\document\SubmitButton;
use de\langner_dev\ui\utils\document\SuccessAlert;
use de\langner_dev\ui\utils\document\Table;
use de\langner_dev\ui\utils\document\TableBody;
use de\langner_dev\ui\utils\document\TableBodyItem;
use de\langner_dev\ui\utils\document\TableHead;
use de\langner_dev\ui\utils\document\TableRow;
use de\langner_dev\ui\utils\document\TextInput;
use de\langner_dev\ui\utils\document\WarningAlert;
use de\langner_dev\wiwi\model\Machine;

require_once 'head.php';
require_once 'model/classes/Machine.php';

define("FORM_EDIT_MACHINE_ID", "machine-edit-id");
define("FORM_CREATE_MACHINE_NAME", "machine-name");
define("FORM_CREATE_MACHINE_DATE_FROM", "machine-available-from");
define("FORM_CREATE_MACHINE_DATE_TO", "machine-available-to");
define("FORM_CREATE_MACHINE_CAPACITY", "machine-available-capacity");

define("EDIT_MACHINE_GET_ID_PARAM", "e");
define("DELETE_MACHINE_GET_ID_PARAM", "d");


$doc = buildHTMLDocument("Maschienen");

$overview = new Section("Maschinen");
$overview->container_xl()->mb_3();

$text = new Div("<p>Maschinen bilden die Grundlage zum Arbeiten. Jede Maschine wird als individueller Arbeitsplatz mit einer bestimmten Kapazität betrachtet.</p>Hier sehen Sie die Stammdaten aller Maschinen. Sie können diese über die Aktionsschaltflächen bearbeiten oder löschen.<br/>Alternativ können Sie auch neue Maschinen anlegen.");

$link = (new Link(
    (new Button("Maschine erstellen", BS5_BUTTON_TYPE_SUCCESS, true))->mt_3(),
    "machine?" . EDIT_MACHINE_GET_ID_PARAM
));
$overview->addElement($text);
$overview->addElement($link);

$doc->addElement($overview);


if (isset($_POST[FORM_CREATE_MACHINE_NAME]) && isset($_POST[FORM_CREATE_MACHINE_DATE_FROM]) && isset($_POST[FORM_CREATE_MACHINE_CAPACITY])) {

    $name = $_POST[FORM_CREATE_MACHINE_NAME];
    $from = $_POST[FORM_CREATE_MACHINE_DATE_FROM];
    $to = $_POST[FORM_CREATE_MACHINE_DATE_TO];
    $capacity = $_POST[FORM_CREATE_MACHINE_CAPACITY];

    $id = isset($_POST[FORM_EDIT_MACHINE_ID]) ? intval($_POST[FORM_EDIT_MACHINE_ID]) : -1;

    $machine = new Machine($id);
    $machine->setName($name);
    $machine->setAvailableFromTimestamp(strtotime($from));

    if (strtotime($to) !== false) {
        $machine->setAvailableToTimestamp(strtotime($to));
    }

    $machine->setCapacityPerDay($capacity);

    if ($machine->save()) {
        $overview->addElement(new SuccessAlert("Die Maschine konnte erfolgreich gesichert werden."));
    }
    else $overview->addElement(new ErrorAlert("Es ist ein Fehler beim Sichern aufgetreten. Versuchen Sie es später erneut."));
}

if (isset($_GET[DELETE_MACHINE_GET_ID_PARAM]) && $_GET[DELETE_MACHINE_GET_ID_PARAM] != null) {
    $machine = new Machine($_GET[DELETE_MACHINE_GET_ID_PARAM]);

    if ($machine->exists()) {
        if ($machine->delete()) {
            $overview->addElement(new SuccessAlert("Die Maschine konnte erfolgreich entfernt werden."));
        }
        else $overview->addElement(new ErrorAlert("Es ist ein Fehler beim Entfernen aufgetreten. Versuchen Sie es später erneut."));
    }
    else
        $overview->addElement(new ErrorAlert("Die Maschine existiert nicht und kann somit auch nicht entfernt werden."));
}



#
#
#
#
#       erstellen
#
#
#
#


if (isset($_GET[EDIT_MACHINE_GET_ID_PARAM])) {
    $machine = $_GET[EDIT_MACHINE_GET_ID_PARAM] != null ? new Machine(intval($_GET[EDIT_MACHINE_GET_ID_PARAM])) : null;
    $name = $machine != null ? $machine->getName() : null;
    $from = $machine != null ? date("Y-m-d", $machine->getAvailableFromTimestamp()) : null;
    $to = $machine != null && $machine->getAvailableToTimestamp() != null ? date("Y-m-d", $machine->getAvailableToTimestamp()) : null;
    $capacity = $machine != null ? $machine->getCapacityPerDay() : null;

    $title = $machine != null ? "bearbeiten" : "anlegen";

    $create_section = new Section("Maschine $title", "h2");
    $create_section->container_xl()->mb_5();

    $form = new Form("machine");

    if ($machine != null)
        $form->addElement(new HiddenInput(FORM_EDIT_MACHINE_ID, $machine->getId()));

    $form_name_item = new FormDivItem();

    $form_name_item->addElement(new FormLabel("Name", FORM_CREATE_MACHINE_NAME));
    $form_name_item->addElement((new TextInput(FORM_CREATE_MACHINE_NAME, FORM_CREATE_MACHINE_NAME, "Name"))->required()->max_length(16)->value($name));
    $form_name_item->addElement(new FormText("Geben Sie den Namen der Maschinen an. Der Name ist auf maximal 16 Zeichen begrenzt."));

    $form->addElement($form_name_item);




    $form_date_from_item = new FormDivItem();
    $form_date_from_item->row();

    $form_date_from_item_label = new FormLabel("Verfügbar seit", FORM_CREATE_MACHINE_DATE_FROM);
    $form_date_from_item_label->col_lg(2)->col_form_label();
    $form_date_from_item->addElement($form_date_from_item_label);

    $form_date_from_item_input_div = new Div();
    $form_date_from_item_input_div->col();
    $form_date_from_item_input_div->addElement((new Input(FORM_CREATE_MACHINE_DATE_FROM, FORM_CREATE_MACHINE_DATE_FROM, "", "date"))->required()->value($from)->placeholder("Verfügbar ab"));
    $form_date_from_item->addElement($form_date_from_item_input_div);
    $form_date_from_item->addElement(new FormText("Geben Sie ein, ab wann die Maschine verwendet werden kann."));

    $form->addElement($form_date_from_item);




    $form_date_to_item = new FormDivItem();
    $form_date_to_item->row();

    $form_date_to_item_label = new FormLabel("Verfügbar bis", FORM_CREATE_MACHINE_DATE_TO);
    $form_date_to_item_label->col_lg(2)->col_form_label();
    $form_date_to_item->addElement($form_date_to_item_label);

    $form_date_to_item_input_div = new Div();
    $form_date_to_item_input_div->col();
    $form_date_to_item_input_div->addElement((new Input(FORM_CREATE_MACHINE_DATE_TO, FORM_CREATE_MACHINE_DATE_TO, "", "date"))->value($to)->placeholder("Verfügbar bis"));
    $form_date_to_item->addElement($form_date_to_item_input_div);
    $form_date_to_item->addElement(new FormText("<i>Optional</i><br/>Geben Sie ein, bis wann die Maschine verwendet werden kann."));


    $form->addElement($form_date_to_item);


    $form_cap_item = new FormDivItem();
    $form_cap_item->addElement(new FormLabel("Kapazität (pro Tag)", FORM_CREATE_MACHINE_CAPACITY));
    $form_cap_item->addElement((new NumberInput(FORM_CREATE_MACHINE_CAPACITY, 1, null, 0.5, FORM_CREATE_MACHINE_CAPACITY, "Kapazität"))->required()->value($capacity));
    $form_cap_item->addElement(new FormText("Geben Sie die Kapazität pro Tag <b>in Stunden</b> an."));


    $form->addElement($form_cap_item);
    $form->addElement(new SubmitButton("Sichern"));

    $create_section->addElement($form);



    $doc->addElement($create_section);
}

$doc->setUrl("machine");












#
#
#   Anzeige
#
#






$table_section = new Section("Verfügbare Maschinen", "h2");
$table_section->container_xl();
$table_section->pb_0();

$table_section->addElement((new WarningAlert("Beachten Sie, dass Kapazitätsänderungen nur in zukünftigen Arbeitsaufträgen wirksam sind."))->mt_1()->mb_2());

$table_div = new Div("", array("table-responsive"));
$table_div->container_fluid()->bg_body()->p_0()->border()->rounded();
$table = new Table();
$table->mb_0();
$thead = new TableHead();
$thead_row = new TableRow();

$thead_row->addTH("#");
$thead_row->addTH("Name");
$thead_row->addTH("Verfügbar seit");
$thead_row->addTH("Verfügbar bis");
$thead_row->addTH("Kapazität (pro Tag)");
$thead_row->addTH("Aktion");

$tbody = new TableBody();

$machine_arr = Machine::getMachines(0,500);

if (empty($machine_arr)) {
    $row = new TableRow();
    $td = new TableBodyItem("Es existieren noch keine Maschinen");
    $td->setAttribute("colspan", 6);
    $td->text_center()->text_danger();
    $row->addElement($td);
    $tbody->addElement($row);
}

foreach ($machine_arr as $machine) {
    $row = new TableRow();

    $id = $machine->getId();
    $name = $machine->getName();
    $av_from = $machine->getAvailableFromTimestamp();
    $av_to = ($machine->getAvailableToTimestamp() != null) ? $machine->getAvailableToTimestamp() : "---";
    $cap_per_day = $machine->getCapacityPerDay() . "h";

    $row->addTD($id);
    $row->addTD($name);
    $row->addTD(displayDate($av_from));
    $row->addTD(displayDate($av_to));
    $row->addTD($cap_per_day);

    $action_td = new TableBodyItem();
    $edit = new Link(new Button("Bearbeiten", BS5_BUTTON_TYPE_PRIMARY, true), "machine?" . EDIT_MACHINE_GET_ID_PARAM . "=$id");
    $delete = new Link(new Button("Löschen", BS5_BUTTON_TYPE_DANGER, true), "machine?" . DELETE_MACHINE_GET_ID_PARAM . "=$id");
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

$table_section->addElement($table_div);
$doc->addElement($table_section);























$doc->printHTMLText();


?>

