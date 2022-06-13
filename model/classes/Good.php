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

    /**
     * @return Good[]
     */
    public static function getGoods(int $start = 0, int $length = 500): array {
        $ret = (new Statement("select `id_good` from `good` limit ?,?"))->execute($start, $length);

        $arr = array();

        if (is_array($ret) && !empty($ret)) {
            foreach ($ret as $id) {
                array_push($arr, new Good($id));
            }
        }

        return $arr;
    }

    private $name;
    private $sub_goods;
    private $amount;
    private $main_good_id;

    public function __construct($id = -1)
    {
        $this->setDefaults($id);
        $this->load();
    }

    private function setDefaults($id = -1){
        $this->setId($id);
        $this->name = null;
        $this->sub_goods = array();
        $this->amount = 0;
        $this->main_good_id = null;
    }

    public function load()
    {
        if ($this->getId() > 0) {
            $ret = (new Statement("select * from `good` where `id_good` = ?"))->execute($this->getId());

            if (is_array($ret) && !empty($ret)) {
                $ret = $ret[0];
                $this->setId($ret[0]);
                $this->name = $ret[1];
                $this->main_good_id = intval($ret[2]);
                $this->amount = intval($ret[3]);

                return;
            }
        }

        $this->setDefaults();
    }

    protected function create(): bool
    {
        $s = new Statement("insert into `good`(`name`, `main_good_id`, `amount_for_main_good`) values (?,?,?)");

        $ret = $s->execute($this->name, $this->main_good_id, $this->amount);

        if (is_array($ret) && !empty($ret)) {
            $this->setId($ret[0]);
            $this->load();
        }

        return $this->exists();
    }

    protected function update(): bool
    {
        $s = new Statement("update `good` set `name` = ?, `main_good_id` = ?, `amount_for_main_good` = ? where `id_good` = ?");

        $name = $this->name;
        $main_id = $this->main_good_id;
        $amount = $this->amount;

        $s->execute($name, $main_id, $amount, $this->getId());

        $this->load();

        return ($name == $this->name && $main_id == $this->main_good_id && $amount == $this->amount);
    }

    public function delete(): bool
    {
        $s = new Statement("delete from `good` where `id_good` = ?");

        if ($this->exists()) {
            $s->execute($this->getId());

            $this->load();

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


    /**
     * @return Good[]
     */
    public function getSubGoods(): array
    {
        if (empty($this->sub_goods) && $this->exists()) {
            $ret = (new Statement("select `id_good` from `good` where `main_good_id` = ?"))->execute($this->getId());

            if (is_array($ret) && !empty($ret)) {

                foreach ($ret as $id) {
                    array_push($this->sub_goods, new Good($id));
                }
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