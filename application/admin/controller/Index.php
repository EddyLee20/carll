<?php


namespace app\admin\controller;


use think\Request;

class Index extends Base
{
    public function index()
    {
        return $this->fetch('index');
    }
    public function frame()
    {
//        var_dump(Request::instance()->param());exit;
        switch ($this->action){
            case 'info':
                return (new Info())->info();
                break;
            case 'user_list':
                return (new User())->lists();
                break;
            default:
                break;
        }
    }

}