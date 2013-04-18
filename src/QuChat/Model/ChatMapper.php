<?php

namespace QuChat\Model;

use QuAdmin\Model\AbstractMapper;
use QuAdmin\Model\Interfaces\WebMapperInterface;

class ChatMapper extends AbstractMapper implements  WebMapperInterface
{

    public function getRow($array = array(),$order = null)
    {
        $this->Order($order);
        $this->where($array);
        return $this->row();
    }

    public function getAll($array = array(),$order = null)
    {
        $this->Order($order);
        $this->where($array);
        $this->toArray();
        return $this->all();
    }
}