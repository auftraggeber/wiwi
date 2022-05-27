<?php

namespace de\langner_dev\wiwi\model;

$path = (defined("PATH")) ? PATH : "";
require_once $path . 'model/classes/utils/Entity.php';
require_once $path . 'model/classes/utils/sql.php';

use de\langner_dev\wiwi\model\utils\Entity;
use de\langner_dev\wiwi\model\utils\Statement;

/**
 * Ein Produktionsgut.
 */
class Good extends Entity
{

    private $name;
    private $position;
    private $sub_goods;
    private $amount;
    private $duration_in_mins;
    private $main_good_id;

    public function __construct($id = -1)
    {
        $this->setDefaults($id);
    }

    private function setDefaults($id = -1){
        $this->setId($id);
        $this->name = null;
        $this->position = -1;
        $this->sub_goods = array();
        $this->amount = 0;
        $this->duration_in_mins = 0;
        $this->main_good_id = null;
    }

    public function load()
    {
        if ($this->getId() > 0) {
            $ret = (new Statement("select * from `good` where `id_good` = ?"))->execute($this->getId());

            if (is_array($ret) && !empty($ret)) {
                $this->setId($ret[0]);
                $this->name = $ret[1];
                $this->position = intval($ret[2]);
                $this->main_good_id = intval($ret[3]);
                $this->amount = intval($ret[4]);
                $this->duration_in_mins = intval($ret[5]);

                return;
            }
        }

        $this->setDefaults();
    }

    protected function create(): bool
    {
        $s = new Statement("insert into `good`(`name`, `position`, `main_good_id`, `amount_for_main_good`, `duration`) values (?,?,?,?,?)");

        $ret = $s->execute($this->name, $this->position, $this->main_good_id, $this->amount, $this->duration_in_mins);

        if (is_array($ret) && !empty($ret)) {
            $this->setId($ret[0]);
            $this->load();
        }

        return $this->exists();
    }

    protected function update(): bool
    {
        $s = new Statement("update `good` set `name` = ?, `position` = ?, `main_good_id` = ?, `amount_for_main_good` = ?, `duration` = ? where `id_good` = ?");

        $name = $this->name;
        $pos = $this->position;
        $main_id = $this->main_good_id;
        $amount = $this->amount;
        $duration = $this->duration_in_mins;

        $s->execute($name, $pos, $main_id, $amount, $duration, $this->getId());

        $this->load();

        return ($name == $this->name && $pos == $this->position && $main_id == $this->main_good_id && $amount == $this->amount && $duration == $this->duration_in_mins);
    }

    public function delete(): bool
    {
        $s = new Statement("delete from `good` where `id_good` = ?");

        if ($this->exists()) {
            $s->execute($this->getId());

            return !$this->exists();
        }

        return false;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition($position): void
    {
        $this->position = $position;
    }

    /**
     * @return Good[]
     */
    public function getSubGoods(): array
    {
        if (empty($this->sub_goods) && $this->exists()) {
            $ret = (new Statement("select `id_good` from `good` where `main_good_id` = ?"))->execute($this->getId());

            if (is_array($ret) && !empty($ret)) {
                array_push($this->sub_goods, new Good($ret[0]));
            }
        }

        return $this->sub_goods;
    }

    public function setSubGoods($sub_goods): void
    {
        $this->sub_goods = $sub_goods;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    public function getDurationInMins(): int
    {
        return $this->duration_in_mins;
    }

    public function setDurationInMins($duration_in_mins): void
    {
        $this->duration_in_mins = $duration_in_mins;
    }

    public function getMainGood(): ?Good
    {
        if ($this->main_good_id != null) {
            return new Good($this->main_good_id);
        }

        return null;
    }

    public function setMainGood(?Good $good): void
    {
        $this->main_good_id = ($good != null) ? $good->getId() : null;

        if ($good != null) {
            if (!in_array($this, $good->getSubGoods())) {
                array_push($good->sub_goods, $this);
            }
        }
    }
}