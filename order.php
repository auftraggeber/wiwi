<?php

use de\langner_dev\ui\utils\document\Button;
use de\langner_dev\ui\utils\document\DateInput;
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
use de\langner_dev\wiwi\model\Order;

require_once 'head.php';
require_once 'model/classes/Order.php';

define("FORM_EDIT_ORDER_ID", "order-id");
define("FORM_CREATE_ORDER_NAME", "order-name");
define("FORM_CREATE_ORDER_DATE", "order-date");

define("EDIT_ORDER_GET_ID_PARAM", "e");
define("DELETE_ORDER_GET_ID_PARAM", "d");

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
    $order = new order($id);

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

    $action_td = new TableBodyItem();
    $edit = new Link(new Button("Bearbeiten", BS5_BUTTON_TYPE_PRIMARY, true), "order?" . EDIT_ORDER_GET_ID_PARAM . "=" . $order->getId());
    $delete = new Link(new Button("Löschen", BS5_BUTTON_TYPE_DANGER, true), "order?" . DELETE_ORDER_GET_ID_PARAM . "=" . $order->getId());
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