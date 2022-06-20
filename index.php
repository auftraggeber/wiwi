<?php

use de\langner_dev\ui\utils\document\Div;
use de\langner_dev\ui\utils\document\Section;
use de\langner_dev\ui\utils\document\StackedGroupBarChart;
use de\langner_dev\wiwi\model\Machine;
use de\langner_dev\wiwi\model\Order;

require_once 'head.php';
require_once 'model/classes/Machine.php';
require_once 'model/classes/Order.php';

$doc = buildHTMLDocument("index");

$overview = new Section("WIWI - Kapazitätsplanung");
$overview->container_xl();
$overview->addElement(new Div(
    "<p>Hier sehen Sie die verplante Fertigungskapazität. Es stehen Maschinen mit einem festen Kapazitätsangebot und die verschiedenen Teile zur Verfügung.<br/>"
    ."Die verschiedenen Teile können durch Aufträge, welche Arbeitspläne beinhalten, die Kapazität der jeweiligen Maschinen beanspruchen.</p>".
    "Im Diagramm unten sehen Sie, wie die sich die Aufträge auf das Kapazitätsangebot <i>(relativ)</i> auswirken.<br/>"
));

$doc->addElement($overview);

$chart_section = new Section("Plan", "h2");
$chart_section->container_xl();

$dataset = array();

$min = strtotime("2022-01-01") / (3600 * 24);
$max = strtotime("2022-12-31") / (3600 * 24);

$orders = Order::getOrders();
$times = Order::getScheduleMinMaxTimes();

$chart = new StackedGroupBarChart("orders-chart");
$chart->setTitle("Plan");

for ($i = $min; $i <= $max; $i++) {
    $time = $i * 3600 * 24;

    $chart->addLabel(displayDate($time));
}
foreach ($orders as $order) {
    $schedule =  $order->getScheduleOfDates();
    foreach (array_keys($schedule) as $date) {

        foreach (array_keys($schedule[$date]) as $machine_id) {
            $machine = new Machine($machine_id);

            $chart->addDataset($order, $machine, $date, $schedule[$date][$machine_id]);
        }
    }
}

$chart_section->addElement($chart);
$doc->addElement($chart_section);

$doc->printHTMLText();