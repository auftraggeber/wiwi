<?php

namespace de\langner_dev\wiwi\model\utils;

abstract class Entity
{

    private $id = -1;

    public function __construct($id = -1)
    {
        $this->setId($id);

        $this->load();
    }

    public abstract function load();
    protected abstract function create(): bool;
    protected abstract function update(): bool;
    public abstract function delete(): bool;

    public function save(): bool {
        if (!$this->exists())
            return $this->create();
        else
            return $this->update();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    protected function setId($id) {
        $this->id = intval($id);
    }

    public function exists(): bool {
        return $this->getId() > 0;
    }


}