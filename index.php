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
    "TODO"
));

$doc->addElement($overview);

$dataset = array();

$min = strtotime("2022-06-01") / (3600 * 24);
$max = strtotime("2022-06-31") / (3600 * 24);

$orders = Order::getOrders();

$chart = new StackedGroupBarChart("orders-chart");

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

$doc->addElement($chart);

$doc->printHTMLText();