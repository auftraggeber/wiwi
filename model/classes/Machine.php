<?php

namespace de\langner_dev\wiwi\model;

$path = (defined("PATH")) ? PATH : "";
require_once $path . 'model/classes/utils/Entity.php';
require_once $path . 'model/classes/utils/sql.php';

use de\langner_dev\wiwi\model\utils\Entity;
use de\langner_dev\wiwi\model\utils\SQL;
use de\langner_dev\wiwi\model\utils\Statement;

define("MACHINE_CREATE_UPDATE_SUCCESS", 0);

class Machine extends Entity
{
    /**
     * @param int $start
     * @param int $length
     * @return Machine[]
     */
    public static function getMachines(int $start = 0, int $length = 30): array {
        $arr = array();
        $statement = new Statement("select `id_machine` from `machine` limit ?,?");

        $ids = $statement->execute($start, $length);

        foreach ($ids as $id) {
            array_push($arr, new Machine($id));
        }

        return $arr;
    }

    private $name;
    private $available_from_timestamp;
    private $available_to_timestamp;
    private $capacity_per_day;

    public function __construct($id = -1)
    {
        parent::__construct($id);
    }

    private function setDefaults($id = -1) {
        $this->setId($id);
        $this->name = null;
        $this->available_from_timestamp = null;
        $this->available_to_timestamp = null;
        $this->capacity_per_day = 0;
    }

    public function load() {
        $this->setDefaults($this->getId());

        if (!$this->exists())
            return;

        $sql = new SQL();
        $data = $sql->query("select * from `machine` where `id_machine` = " . $this->getId());


        if (is_array($data) && !empty($data)) {
            $data = $data[0];
            $this->name = strval($data[1]);
            $this->available_from_timestamp = strtotime($data[2]);
            $this->available_to_timestamp = ($data[3] == null) ? null : strtotime($data[3]);
            $this->capacity_per_day = intval($data[4]);
        }
        else $this->setDefaults();
    }

    protected function create(): bool {
        $insert = new Statement("insert into `machine` (`name`, `available_from`, `available_to`, `capacity_day`) values (?,?,?,?)");
        $ret = $insert->execute($this->name, SQL::date($this->available_from_timestamp), SQL::date($this->available_to_timestamp), $this->capacity_per_day);

        if (is_array($ret) && !empty($ret)) {
            $this->setId($ret[0]);
            $this->load();
        }

        return $this->exists();
    }

    protected function update(): bool {
        $update = new Statement("update `machine` set `name` = ?, `available_from` = ?, `available_to` = ?, `capacity_day` = ? where `id_machine` = ?");
        $update->execute($this->name, SQL::date($this->available_from_timestamp), SQL::date($this->available_to_timestamp), $this->capacity_per_day, $this->getId());

        $name = $this->name;
        $av_from = $this->available_from_timestamp;
        $av_to = $this->available_to_timestamp;
        $cap = $this->capacity_per_day;

        $this->load();

        return ($name == $this->name && $av_from == $this->available_from_timestamp && $av_to == $this->available_to_timestamp && $cap == $this->capacity_per_day);
    }

    public function delete(): bool {
        if ($this->exists()) {
            $delete = new Statement("delete from `machine` where `id_machine` = ?");
            $delete->execute($this->getId());

            $this->load();

            return !$this->exists();
        }

        return false;
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAvailableFromTimestamp()
    {
        return $this->available_from_timestamp;
    }

    /**
     * @param mixed $available_from_timestamp
     */
    public function setAvailableFromTimestamp($available_from_timestamp)
    {
        $this->available_from_timestamp = $available_from_timestamp;
    }

    /**
     * @return mixed
     */
    public function getAvailableToTimestamp()
    {
        return $this->available_to_timestamp;
    }

    /**
     * @param mixed $available_to_timestamp
     */
    public function setAvailableToTimestamp($available_to_timestamp)
    {
        $this->available_to_timestamp = $available_to_timestamp;
    }

    /**
     * @return mixed
     */
    public function getCapacityPerDay()
    {
        return $this->capacity_per_day;
    }

    /**
     * @param mixed $capacity_per_day
     */
    public function setCapacityPerDay($capacity_per_day)
    {
        $this->capacity_per_day = $capacity_per_day;
    }

}