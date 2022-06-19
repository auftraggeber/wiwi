<?php

namespace de\langner_dev\wiwi\model;

$path = (defined("PATH")) ? PATH : "";
require_once $path . 'model/classes/utils/Entity.php';
require_once $path . 'model/classes/utils/sql.php';

use de\langner_dev\wiwi\model\utils\Entity;
use de\langner_dev\wiwi\model\utils\Statement;

class Order extends Entity
{

    /**
     * @return Order[]
     */
    public static function getOrders(int $start = 0, int $length = 500): array
    {
        $ret = (new Statement("select `id_order` from `order` limit ?,?"))->execute($start, $length);

        $arr = array();

        if (is_array($ret) && !empty($ret)) {
            foreach ($ret as $id) {
                array_push($arr, new Order($id));
            }
        }

        return $arr;
    }

    public static function getScheduleMinMaxTimes(): array {
        $ret = (new Statement("select `date` from `schedule` order by `date` asc"))->execute();

        return array(strtotime($ret[0]), strtotime($ret[count($ret) - 1]));
    }

    private $name;
    private $min_start;
    private $goods;

    private function setDefaults($id = -1) {
        $this->setId($id);
        $this->name = null;
        $this->min_start = null;
        $this->goods = array();
    }

    public function load()
    {
        if ($this->getId() > 0) {
            $stm = new Statement("select * from `order` where `id_order` = ?");
            $r = $stm->execute($this->getId());

            if (is_array($r) && !empty($r)) {
                $r = $r[0];
                $this->setId($r[0]);
                $this->name = strval($r[1]);
                $this->min_start = strtotime($r[2]);

                $good_statement = new Statement("select * from `order_contains_good` where `order_id` = ? order by `position` asc");
                $ret = $good_statement->execute($r[0]);

                if (is_array($ret) && !empty($ret)) {
                    $this->goods = array();
                    foreach ($ret as $data) {
                        array_push($this->goods, array(
                            $data[1],
                            $data[2],
                            intval($data[3]),
                            intval($data[4]),
                            intval($data[5])
                        ));
                    }
                }
            }
            else $this->setDefaults();
        }
        else $this->setDefaults();
    }

    protected function create(): bool
    {
        $stm = new Statement("insert into `order`(`name`, `min_start`) values (?,?)");

        $ret = $stm->execute($this->name, date("Y-m-d", $this->min_start));

        if (is_array($ret) && !empty($ret)) {
            $this->setId($ret[0]);
            $this->load();
        }

        return $this->exists();
    }

    protected function update(): bool
    {
        if (!$this->exists())
            return false;

        $stm = new Statement("update `order` set `name` = ?, `min_start` = ? where `id_order` = ?");
        $name = $this->name;
        $timestamp = $this->min_start;

        $stm->execute($name, date("Y-m-d", $timestamp), $this->getId());
        $this->load();

        return $name == $this->name && $timestamp == $this->min_start;
    }

    public function delete(): bool
    {
        $stm = new Statement("delete from `order` where `id_order` = ?");
        $stm->execute($this->getId());

        $this->load();

        return !$this->exists();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getMinStartTimestamp(): ?int
    {
        return $this->min_start;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function setDate(?string $date): void {
        if ($date != null) {
            $this->min_start = strtotime($date);
        }
        else $this->min_start = null;
    }

    public function getGoods(): array {
        return $this->goods != null ? $this->goods : array();
    }

    public function hasGood(Good $good, Machine $machine) : bool
    {
        return !empty($this->getGoodDetails($good, $machine));
    }

    public function addGood(Good $good, Machine $machine, int $position, int $time_in_mins): bool {
        if (!$good->exists() || !$machine->exists()) {
            return false;
        }

        if ($time_in_mins > $machine->getCapacityPerDay() * 60)
            return false;

        $stm = new Statement("insert into `order_contains_good`(`order_id`, `good_id`, `machine_id`, `amount`, `position`, `time`) values (?,?,?,?,?,?)");
        $stm->execute($this->getId(), $good->getId(), $machine->getId(), 1, $position, $time_in_mins);

        $this->load();

        $this->schedule();

        return $this->hasGood($good, $machine);
    }

    public function getGoodDetails(Good $good, Machine $machine):array {
        if (!$good->exists() || !$machine->exists()) {
            return array();
        }

        $ret = (new Statement("select * from `order_contains_good` where `order_id` = ? and `good_id` = ? and `machine_id` = ? limit 1"))->execute($this->getId(), $good->getId(), $machine->getId());

        if (is_array($ret) && !empty($ret)) {
            $ret = $ret[0];
            return array(intval($ret[3]), intval($ret[4]), intval($ret[5]));
        }

        return array();
    }

    public function removeGood(Good $good, Machine $machine) {
        if (!$good->exists() || !$machine->exists()) {
            return false;
        }

        (new Statement("delete from `order_contains_good` where `order_id` = ? and `good_id` = ? and `machine_id` = ?"))->execute($this->getId(), $good->getId(), $machine->getId());

        $this->load();

        $this->schedule();

        return !$this->hasGood($good, $machine);
    }

    private function getCurrentSchedule(Machine $machine, string $min_date): array {
        $cur_sched_ret = (new Statement("select `schedule`.`date`, `schedule`.`position`, `schedule`.`good_id`, `order_contains_good`.`time` from `schedule`, `order_contains_good`" .
            " where `schedule`.`order_id` = ? and `schedule`.`machine_id` = ? and `schedule`.`date` >= ? and `schedule`.`order_id` = `order_contains_good`.`order_id` and `schedule`.`machine_id` = `order_contains_good`.`machine_id` and `schedule`.`good_id` = `order_contains_good`.`good_id`"))->execute($this->getId(), $machine->getId(), $min_date);

        $dates = array();

        if (is_array($cur_sched_ret) && !empty($cur_sched_ret)) {

            foreach ($cur_sched_ret as $data) {
                if (!isset($dates[$data[0]]))
                    $dates[$data[0]] = array();

                array_push($dates[$data[0]], array($data[1], $data[2], $data[4], $data[3]));
            }
        }

        return $dates;
    }

    private function getScheduledTimeInMins(Machine $machine, string $date, ?array $current_schedule = null) {
        if (!is_array($current_schedule))
            $current_schedule = $this->getCurrentSchedule($machine, $date);

        if (!isset($current_schedule[$date]))
            return 0;

        $sum = 0;

        foreach ($current_schedule[$date] as $data) {
            $sum += $data[3];
        }

        return $sum;
    }

    private function isScheduled(Good $good, string $date) {
        $ret = (new Statement("select * from `schedule` where `order_id` = ? and `good_id` = ? and `date` = ?"))->execute($this->getId(), $good->getId(), $date);

        return is_array($ret) && !empty($ret);
    }

    public function getScheduleOfGood(Good $good, Machine $machine): ?int {
        if (!$good->exists() || !$machine->exists()) {
            return null;
        }

        $ret = (new Statement("select `date` from `schedule` where `order_id` = ? and `good_id` = ? and `machine_id` = ?"))->execute($this->getId(), $good->getId(), $machine->getId());

        if (is_array($ret) && !empty($ret)) {
            return strtotime($ret[0]);
        }

        return null;
    }

    public function getEndDate(): ?int {
        $ret = (new Statement("select `date` from `schedule` where `order_id` = ? order by `date` desc limit 1"))->execute($this->getId());

        if (is_array($ret) && !empty($ret))
            return strtotime($ret[0]);

        return null;
    }

    public function schedule() {
        (new Statement("delete from `schedule` where `order_id` = ?"))->execute($this->getId());

        $timestamp = $this->getMinStartTimestamp();
        $pos_before = null;

        $pos = 1;

        foreach ($this->getGoods() as $details) {
            $good = new Good($details[0]);
            $machine = new Machine($details[1]);
            $amount = $details[2];
            $position = $details[3];
            $time = intval($details[4]);


            if ($pos_before != null && $pos_before < $position) {
                $timestamp += 3600 * 24;
            }
            $pos_before = $position;
            $date = date("Y-m-d", $timestamp);

            $current = $this->getCurrentSchedule($machine, $date);


            while ($this->getScheduledTimeInMins($machine, $date, $current) + $time > ($machine->getCapacityPerDay() * 60) || $this->isScheduled($good, $date)) {
                $timestamp += 3600 * 24;
                $date = date("Y-m-d", $timestamp);
            }

            (new Statement("insert into `schedule`(`good_id`, `order_id`, `machine_id`, `position`, `date`) values (?,?,?,?,?)"))->execute($good->getId(), $this->getId(), $machine->getId(), $pos, $date);
            $pos++;
        }
    }

    public function getScheduledMachines(): array {
        $ret = (new Statement("select `schedule`.`machine_id`, `schedule`.`good_id`, `schedule`.`date`, `order_contains_good`.`time` from `schedule`, `order_contains_good` where `schedule`.`order_id` = ? and `schedule`.`order_id` = `order_contains_good`.`order_id` and `schedule`.`good_id` = `order_contains_good`.`good_id` and `schedule`.`machine_id` = `order_contains_good`.`machine_id` order by date asc"))->execute($this->getId());

        if (is_array($ret) && !empty($ret)) {
            $arr = array();

            foreach ($ret as $id) {
                array_push($arr, array(new Machine($id[0]), new Good($id[1]), strtotime($id[2]), $id[3]));
            }

            return $arr;
        }

        return array();
    }

    /**
     * @return array Ein Array als Key ein Datum als Value ein Weiteres Array, welches die Maschinen-IDs und die zugehörige relative Kapazität liefert.
     */
    public function getScheduleOfDates(): array {
        $ret = (new Statement("select `schedule`.`machine_id`, `order_contains_good`.`time`, `schedule`.`date` from `schedule`, `order_contains_good` where `schedule`.`order_id` = ? and `schedule`.`machine_id` = `order_contains_good`.`machine_id` and `schedule`.`order_id` = `order_contains_good`.`order_id` and `schedule`.`good_id` = `order_contains_good`.`good_id` order by `schedule`.`position` asc")
        )->execute($this->getId());

        $arr = array();

        if (is_array($ret) && !empty($ret)) {

            foreach ($ret as $data) {
                $cap_per_day = (new Machine($data[0]))->getCapacityPerDay() * 60;
                $data[2] = date("Y-m-d", strtotime($data[2]));

                $data_arr = isset($arr[$data[2]]) && is_array($arr[$data[2]]) ? $arr[$data[2]] : array();

                $data_arr[$data[0]] = $data[1] / $cap_per_day;

                $arr[$data[2]] = $data_arr;
            }
        }

        return $arr;
    }
}