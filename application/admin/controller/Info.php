<?php


namespace app\admin\controller;


class Info extends Base
{
    public function info()
    {
        return $this->fetch('index/info');
    }
}