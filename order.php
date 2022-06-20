<?php

use de\langner_dev\ui\utils\document\Button;
use de\langner_dev\ui\utils\document\Chart;
use de\langner_dev\ui\utils\document\DateInput;
use de\langner_dev\ui\utils\document\Div;
use de\langner_dev\ui\utils\document\ErrorAlert;
use de\langner_dev\ui\utils\document\Form;
use de\langner_dev\ui\utils\document\FormDivItem;
use de\langner_dev\ui\utils\document\FormLabel;
use de\langner_dev\ui\utils\document\FormText;
use de\langner_dev\ui\utils\document\HiddenInput;
use de\langner_dev\ui\utils\document\HorizontalScheduleBarChart;
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
use de\langner_dev\wiwi\model\Machine;
use de\langner_dev\wiwi\model\Order;

require_once 'head.php';
require_once 'model/classes/Order.php';
require_once 'model/classes/Good.php';
require_once 'model/classes/Machine.php';

define("SECS_PER_DAY", 60 * 60 * 24);

define("FORM_EDIT_ORDER_ID", "order-id");
define("FORM_CREATE_ORDER_NAME", "order-name");
define("FORM_CREATE_ORDER_DATE", "order-date");

define("EDIT_ORDER_GET_ID_PARAM", "e");
define("DELETE_ORDER_GET_ID_PARAM", "d");

define("SEE_DETAILS_GET_ORDER_ID", "id");
define("ADD_GOOD_GET_PARAM_GOOD_ID", "ag");
define("ADD_GOOD_GET_PARAM_MACHINE_ID", "am");
define("REMOVE_GOOD_GET_PARAM_GOOD_ID", "dg");
define("REMOVE_GOOD_GET_PARAM_MACHINE_ID", "dm");

define("FORM_ADD_GOOD_POST_OLD_GOOD_ID", "old-good-id");
define("FORM_ADD_GOOD_POST_OLD_MACHINE_ID", "old-machine-id");
define("FORM_ADD_GOOD_POST_NEW_GOOD_ID", "new-good-id");
define("FORM_ADD_GOOD_POST_NEW_MACHINE_ID", "new-machine-id");
define("FORM_ADD_GOOD_POST_AMOUNT", "good-amount");
define("FORM_ADD_GOOD_POST_POSITION", "good-position");
define("FORM_ADD_GOOD_POST_TIME", "good-time");

function buildListId(Order $order): string {
    return "order-list-element-" . $order->getId();
}

$doc = buildHTMLDocument("Aufträge");

$overview = new Section("Aufträge");
$overview->container_xl()->mb_3();

$overview->addElement(
    new Div(
        "<p>Aufträge dienen zur Produktion. Durch sie können Produktionen von <a href='good'>Teilen</a> an bestimmten <a href='machine'>Maschinen</a> geplant und durchgeführt werden.</p>Hier sehen Sie alle Aufträge. Über die Schaltflächen lassen sich diese bearbeiten oder entfernrn.<br/>Zudem können Sie hier neue anlegen."
    )
);

$details_order = isset($_GET[SEE_DETAILS_GET_ORDER_ID]) ? new Order($_GET[SEE_DETAILS_GET_ORDER_ID]) : null;

if ($details_order == null || !$details_order->exists()) {

    $overview->addElement(
        new Link(
            (new Button("Auftrag erstellen",BS5_BUTTON_TYPE_SUCCESS, true))->mt_3(), "order?" . EDIT_ORDER_GET_ID_PARAM
        )
    );

    $doc->addElement($overview);

    if (isset($_POST[FORM_CREATE_ORDER_NAME]) && isset($_POST[FORM_CREATE_ORDER_DATE])) {
        $name = $_POST[FORM_CREATE_ORDER_NAME];
        $date = $_POST[FORM_CREATE_ORDER_DATE];

        $id = $_POST[FORM_EDIT_ORDER_ID] ?? -1;

        $order = new Order($id);
        $order->setName($name);
        $order->setDate($date);


        if ($order->save()) {
            $overview->addElement(new SuccessAlert("Der Auftrag konnte erfolgreich gesichert werden."));
        }
        else {
            $overview->addElement(new ErrorAlert("Der Auftrag konnte nicht gesichert werden. Versuchen Sie es bitte später erneut."));
        }
    }

    if (isset($_GET[DELETE_ORDER_GET_ID_PARAM]) && $_GET[DELETE_ORDER_GET_ID_PARAM] != null) {
        $order = new Order($_GET[DELETE_ORDER_GET_ID_PARAM]);

        if ($order->delete()) {
            $overview->addElement(new SuccessAlert("Der Auftrag wurde erfolgreich entfernt."));
        }
        else $overview->addElement(new ErrorAlert("Der Auftrag konnte nicht entfernt werden. Versuchen Sie es später erneut."));
    }

#
#
#   erstellen / bearbeiten
#
#
#
    $orders = Order::getOrders();

    if (isset($_GET[EDIT_ORDER_GET_ID_PARAM])) {
        $id = ($_GET[EDIT_ORDER_GET_ID_PARAM] != null) ? intval($_GET[EDIT_ORDER_GET_ID_PARAM]) : -1;
        $order = new Order($id);

        $name = ($order->exists()) ? $order->getName() : null;
        $date = ($order->exists()) ? date("Y-m-d", $order->getMinStartTimestamp()) : null;

        $create_section = new Section("Auftrag anlegen", "h2");
        $create_section->container_xl()->mb_5();

        $form = new Form("order");

        if ($id > 0)
            $form->addElement(new HiddenInput(FORM_EDIT_ORDER_ID, $id));

        $form_name_item = new FormDivItem();
        $form_name_item->addElement(new FormLabel("Name", FORM_CREATE_ORDER_NAME));
        $form_name_item->addElement((new TextInput(FORM_CREATE_ORDER_NAME, FORM_CREATE_ORDER_NAME))->required()->max_length(16)->placeholder("Name")->value($name));
        $form_name_item->addElement(new FormText("Geben Sie den Namen des Auftrags an. Der Name ist auf maximal 16 Zeichen begrenzt."));


        $form_date_item_div = new Div();
        $form_date_item_div->col();
        $form_date_item_div->addElement((new DateInput(FORM_CREATE_ORDER_DATE, FORM_CREATE_ORDER_DATE))->required()->value($date));
        $form_date_item_label = new FormLabel("Datum", FORM_CREATE_ORDER_DATE);
        $form_date_item_label->col_lg(1);
        $form_date_item = new FormDivItem();
        $form_date_item->row();

        $form_date_item->addElement($form_date_item_label);
        $form_date_item->addElement($form_date_item_div);
        $form_date_item->addElement(new FormText("Geben Sie das gewünschte Startdatum der Produktion an. Je nach Auslastung kann sich dies jedoch verschieben."));

        $form->addElement($form_name_item);
        $form->addElement($form_date_item);
        $form->addElement(new SubmitButton("Auftrag sichern", BS5_BUTTON_TYPE_PRIMARY));

        $create_section->addElement($form);

        $doc->addElement($create_section);
    }

    $doc->setUrl("order");



#
#
#
# anzeige
#
#




    $section = new Section("Alle Aufträge", "h2");
    $section->container_xl();

    $table_div = new Div("", array("table-responsive"));
    $table_div->container_fluid()->bg_body()->p_0()->border()->rounded();
    $table = new Table();
    $table->mb_0();
    $thead = new TableHead();
    $thead_row = new TableRow();

    $thead_row->addElement(new TableHeadItem("#"));
    $thead_row->addElement(new TableHeadItem("Name"));
    $thead_row->addElement(new TableHeadItem("Gewünschtes Startdatum"));
    $thead_row->addElement(new TableHeadItem("Vorraussichtliches Enddatum"));
    $thead_row->addElement(new TableHeadItem("Aktion"));

    $tbody = new TableBody();

    if (empty($orders)) {
        $row = new TableRow();
        $td = new TableBodyItem("Es existieren noch keine Aufträge.");
        $td->setAttribute("colspan", 4);
        $td->text_danger()->text_center();
        $row->addElement($td);

        $tbody->addElement($row);
    }

    foreach ($orders as $order) {
        $row = new TableRow();

        $link = new Link($order->getId());
        $link->setId(buildListId($order));

        $row->addElement(new TableBodyItem($link));
        $row->addElement(new TableBodyItem($order->getName()));
        $row->addElement(new TableBodyItem(displayDate($order->getMinStartTimestamp())));
        $row->addElement(new TableBodyItem(displayDate($order->getEndDate())));

        $action_td = new TableBodyItem();
        $details = new Link(new Button("Details", BS5_BUTTON_TYPE_SUCCESS, true), "order?" . SEE_DETAILS_GET_ORDER_ID . "=" . $order->getId());
        $edit = new Link(new Button("Bearbeiten", BS5_BUTTON_TYPE_PRIMARY, true), "order?" . EDIT_ORDER_GET_ID_PARAM . "=" . $order->getId());
        $delete = new Link(new Button("Löschen", BS5_BUTTON_TYPE_DANGER, true), "order?" . DELETE_ORDER_GET_ID_PARAM . "=" . $order->getId());
        $delete->ms_2();
        $edit->ms_2();
        $action_td->addElement($details);
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
}
else {
    $doc->addElement($overview);

    $section = new Section($details_order->getName(), "h2");
    $section->container_xl();

    if (isset($_POST[FORM_ADD_GOOD_POST_NEW_GOOD_ID]) &&
        isset($_POST[FORM_ADD_GOOD_POST_NEW_MACHINE_ID]) &&
       // isset($_POST[FORM_ADD_GOOD_POST_AMOUNT]) &&
        isset($_POST[FORM_ADD_GOOD_POST_POSITION]) &&
        isset($_POST[FORM_ADD_GOOD_POST_TIME])) {

        $good = new Good($_POST[FORM_ADD_GOOD_POST_NEW_GOOD_ID]);
        $machine = new Machine($_POST[FORM_ADD_GOOD_POST_NEW_MACHINE_ID]);
        $amount = intval($_POST[FORM_ADD_GOOD_POST_AMOUNT]);
        $position = intval($_POST[FORM_ADD_GOOD_POST_POSITION]);
        $time = intval($_POST[FORM_ADD_GOOD_POST_TIME]);

        if (isset($_POST[FORM_ADD_GOOD_POST_OLD_GOOD_ID]) && isset($_POST[FORM_ADD_GOOD_POST_OLD_MACHINE_ID])) {
            $details_order->removeGood(new Good($_POST[FORM_ADD_GOOD_POST_OLD_GOOD_ID]), new Machine($_POST[FORM_ADD_GOOD_POST_OLD_MACHINE_ID]));
        }

        if ($details_order->addGood($good, $machine, $position, $time)) {
            $section->addElement(new SuccessAlert("Das Teil konnte erfolgreich hinzugefügt werden."));
        }
        else {
            $section->addElement(new ErrorAlert("Das Teil konnte nicht hinzugefügt werden. Versuchen Sie es bitte später erneut."));
        }
    }

    if (isset($_GET[REMOVE_GOOD_GET_PARAM_GOOD_ID]) && isset($_GET[REMOVE_GOOD_GET_PARAM_MACHINE_ID])) {

        if ($details_order->removeGood(new Good($_GET[REMOVE_GOOD_GET_PARAM_GOOD_ID]), new Machine($_GET[REMOVE_GOOD_GET_PARAM_MACHINE_ID]))) {
            $section->addElement(new SuccessAlert("Das Teil konnte erfolgreich entfernt werden."));
        }
        else {
            $section->addElement(new ErrorAlert("Das Teil konnte nicht entfernt werden. Versuchen Sie es bitte später erneut."));
        }
    }

    if (isset($_GET[ADD_GOOD_GET_PARAM_GOOD_ID])) {
        $good_id = ($_GET[ADD_GOOD_GET_PARAM_GOOD_ID] != null) ? intval($_GET[ADD_GOOD_GET_PARAM_GOOD_ID]) : -1;
        $machine_id = (isset($_GET[ADD_GOOD_GET_PARAM_MACHINE_ID]) && $_GET[ADD_GOOD_GET_PARAM_MACHINE_ID] != null) ? intval($_GET[ADD_GOOD_GET_PARAM_MACHINE_ID]) : -1;
        $good = new Good($good_id);
        $machine = new Machine($machine_id);
        $good_details = $details_order->getGoodDetails($good, $machine);
        $amount = (!empty($good_details)) ? $good_details[0] : null;
        $position = (!empty($good_details)) ? $good_details[1] : null;
        $time = (!empty($good_details)) ? $good_details[2] : null;

        $create_section = new Section("Teil hinzufügen", "h2");
        $create_section->container_xl()->mb_5();

        $form = new Form("order?" . SEE_DETAILS_GET_ORDER_ID . "=" . $details_order->getId());

        if ($good_id >= 1 && $machine_id >= 1) {
            $form->addElement(new HiddenInput(FORM_ADD_GOOD_POST_OLD_GOOD_ID, $good_id));
            $form->addElement(new HiddenInput(FORM_ADD_GOOD_POST_OLD_MACHINE_ID, $machine_id));
        }

        $select_good_values = array();
        $select_machine_values = array();

        foreach (Good::getGoods() as $good) {
            $select_good_values[$good->getId()] = $good->getName();
        }

        foreach (Machine::getMachines() as $machine) {
            $select_machine_values[$machine->getId()] = $machine->getName();
        }

        $select_good = new Select($select_good_values);
        $select_good->setSelectedKey($good_id);
        $select_good->setAttribute("name", FORM_ADD_GOOD_POST_NEW_GOOD_ID);
        $select_good->setAttribute("required", true);

        $select_machine = new Select($select_machine_values);
        $select_machine->setSelectedKey(null);
        $select_machine->setAttribute("name", FORM_ADD_GOOD_POST_NEW_MACHINE_ID);
        $select_machine->setAttribute("required", true);

        $form_good = new FormDivItem();
        $form_good->addElement(new FormLabel("Teil", FORM_ADD_GOOD_POST_NEW_GOOD_ID));
        $form_good->addElement($select_good);
        $form_good->addElement(new FormText("Wählen Sie das Teil aus."));

        $form_machine = new FormDivItem();
        $form_machine->addElement(new FormLabel("Machine", FORM_ADD_GOOD_POST_NEW_MACHINE_ID));
        $form_machine->addElement($select_machine);
        $form_machine->addElement(new FormText("Wählen Sie die Maschine aus."));

       /* $form_amount = new FormDivItem();
        $form_amount->addElement(new FormLabel("Anzahl", FORM_ADD_GOOD_POST_AMOUNT));
        $form_amount->addElement((new NumberInput(FORM_ADD_GOOD_POST_AMOUNT, 1, null, 1, FORM_ADD_GOOD_POST_AMOUNT))->required()->placeholder("Anzahl")->value($amount));
        $form_amount->addElement(new FormText("Geben Sie an, wie viele Teile von diesem Typ mit diesm Auftrag bearbeitet werden sollen."));
       */

        $form_position = new FormDivItem();
        $form_position->addElement(new FormLabel("Arbeitsgang", FORM_ADD_GOOD_POST_POSITION));
        $form_position->addElement((new NumberInput(FORM_ADD_GOOD_POST_POSITION, 1, null, 1, FORM_ADD_GOOD_POST_POSITION))->required()->placeholder("Arbeitsgang")->value($position));
        $form_position->addElement(new FormText("Geben Sie an, an welchem Schritt dieses Teil bearbeitet werden kann. Teile mit dem gleichen Arbeitsgang können zeitgleich bearbeitet werden."));

        $form_time = new FormDivItem();
        $form_time->addElement(new FormLabel("Bearbeitungszeit (min) pro Stück", FORM_ADD_GOOD_POST_TIME));
        $form_time->addElement((new NumberInput(FORM_ADD_GOOD_POST_TIME, 1, null, 1, FORM_ADD_GOOD_POST_TIME))->required()->placeholder("Bearbeitungszeit in Minuten")->value($time));
        $form_time->addElement(new FormText("Geben Sie an, wie lange die Bearbeitung (für ein Stück) dauert."));

        $form->addElement($form_good);
        $form->addElement($form_machine);
        //$form->addElement($form_amount);
        $form->addElement($form_position);
        $form->addElement($form_time);
        $form->addElement(new SubmitButton("Teil hinzufügen", BS5_BUTTON_TYPE_PRIMARY));

        $create_section->addElement($form);

        $doc->addElement($create_section);
    }

    $doc->setUrl("order?" . SEE_DETAILS_GET_ORDER_ID . "=" . $details_order->getId());

    $add_good = new Link(new Button("Teil hinzufügen", BS5_BUTTON_TYPE_SUCCESS, true), "order?" . SEE_DETAILS_GET_ORDER_ID . "=" . $details_order->getId() . "&" . ADD_GOOD_GET_PARAM_GOOD_ID);
    $edit = new Link(new Button("Bearbeiten", BS5_BUTTON_TYPE_PRIMARY, true), "order?" . EDIT_ORDER_GET_ID_PARAM . "=" . $details_order->getId());
    $delete = new Link(new Button("Entfernen", BS5_BUTTON_TYPE_DANGER, true), "order?" . DELETE_ORDER_GET_ID_PARAM . "=" . $details_order->getId());
    $delete->ms_2();
    $edit->ms_2();
    $section->addElement($add_good);
    $section->addElement($edit);
    $section->addElement($delete);

    $table_div = new Div("", array("table-responsive"));
    $table_div->container_fluid()->bg_body()->p_0()->mt_2()->border()->rounded();
    $table = new Table();
    $table->mb_0();
    $thead = new TableHead();
    $thead_row = new TableRow();

    $thead_row->addElement(new TableHeadItem("Teil"));
    $thead_row->addElement(new TableHeadItem("Maschine"));
    //$thead_row->addElement(new TableHeadItem("Anzahl"));
    $thead_row->addElement(new TableHeadItem("Arbeitsgang"));
    $thead_row->addElement(new TableHeadItem("Bearbeitungszeit (min)"));
    $thead_row->addElement(new TableHeadItem("Geplante Bearbeitung"));
    $thead_row->addElement(new TableHeadItem("Aktionen"));

    $tbody = new TableBody();

    if (empty($details_order->getGoods())) {
        $row = new TableRow();
        $td = new TableBodyItem("Es wurden noch keine Bearbeitungsanweisungen hinzugefügt.");
        $td->setAttribute("colspan", 7);
        $td->text_danger()->text_center();
        $row->addElement($td);

        $tbody->addElement($row);
    }

    foreach ($details_order->getGoods() as $entry) {
        $row = new TableRow();

        $good = new Good($entry[0]);
        $machine = new Machine($entry[1]);
        $amount = $entry[2];
        $position = $entry[3];
        $time = $entry[4];

        $row->addElement(new TableBodyItem($good->getName()));
        $row->addElement(new TableBodyItem($machine->getName()));
        //$row->addElement(new TableBodyItem($amount));
        $row->addElement(new TableBodyItem($position));
        $row->addElement(new TableBodyItem($time));
        $row->addElement(new TableBodyItem(displayDate($details_order->getScheduleOfGood($good, $machine))));

        $action_td = new TableBodyItem();
        $edit = new Link(new Button("Bearbeiten", BS5_BUTTON_TYPE_PRIMARY, true), "order?" . SEE_DETAILS_GET_ORDER_ID . "=" . $details_order->getId() . "&" . ADD_GOOD_GET_PARAM_GOOD_ID . "=" . $good->getId() . "&" . ADD_GOOD_GET_PARAM_MACHINE_ID . "=" . $machine->getId());
        $delete = new Link(new Button("Löschen", BS5_BUTTON_TYPE_DANGER, true), "order?" . SEE_DETAILS_GET_ORDER_ID . "=" . $details_order->getId() . "&" . REMOVE_GOOD_GET_PARAM_GOOD_ID . "=" . $good->getId() . "&" . REMOVE_GOOD_GET_PARAM_MACHINE_ID . "=" . $machine->getId());
        $delete->ms_2();
        $edit->ms_2();
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

    $time_section = new Section("Ablauf", "h3");
    $time_section->container_xl();

    $chart = new HorizontalScheduleBarChart("orderTimeChart");
    $chart->setTitle("Zeitplan");

    $prev_end_time = array();


    foreach ($details_order->getScheduledMachines() as $data) {
        $machine = $data[0];
        $good = $data[1];
        $date = date("Y-m-d", $data[2]);
        $dataset_name = $good->getName();

        $chart->addLabel($machine->getName());

        $abs_start_time = $details_order->getMinStartTimestamp();

        $prev_end_time_value = $prev_end_time[$machine->getId()][$data[2]] ?? 0;

        $start_time = (($data[2] - $abs_start_time) / SECS_PER_DAY) + $prev_end_time_value;
        $end_time = $start_time + ($data[3] / (60 * 24));

        $prev_end_time[$machine->getId()][$data[2]] = $end_time - $start_time;

        $chart->addDataset($dataset_name, array(array($start_time,$end_time)), $chart->getIndexOfLabel($machine->getName()));
    }

    for ($i = 0; $i <= (($details_order->getEndDate() - $details_order->getMinStartTimestamp()) / SECS_PER_DAY); $i++) {
        $timestamp = $details_order->getMinStartTimestamp() + ($i * SECS_PER_DAY);
        $chart->addTick(displayDate($timestamp));
    }

    $time_section->addElement($chart);



    $doc->addElement($time_section);
}

$doc->printHTMLText();