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

    private $name;
    private $min_start;

    private function setDefaults($id = -1) {
        $this->setId($id);
        $this->name = null;
        $this->min_start = null;
    }

    public function load()
    {
        if ($this->getId() > 0) {
            $stm = new Statement("select * from `order` where `id_order` = ?");
            $r = $stm->execute($this->getId());

            if (is_array($r) && !empty($r)) {
                $this->setId($r[0]);
                $this->name = strval($r[1]);
                $this->min_start = strtotime($r[2]);
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
}