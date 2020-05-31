<?php


namespace app\api\controller\action;

use app\common\model\Product as ProModel;
use app\common\traits\ApiHelper;
use think\Request;


class Product
{
    public $User,$request;
    public function __construct($base = '')
    {
        if(empty($base)) return ApiHelper::output(20000);
        $this->User = $base;
        $this->request = Request::instance();
    }

    /**
     * 非车险列表
     * @return array
     * @throws \think\exception\DbException
     */
    public function lists()
    {
        $User = $this->User;
        $param = $this->request->param();
        $page = empty($param['page']) ? 1 : $param['page'];
        $page_size = empty($param['page_size']) ? config('paginate.list_rows') : $param['page_size'];
        $field = 'product_id,product_name,product_price,instructions,url';
        $res = ProModel::where(['del_flag'=>0])->field($field)->order('sort desc')->paginate(['paginate'=>$page_size, 'page'=>$page]);
        $info = $res->toArray();
        $pagination = ApiHelper::pagination($info, $page);
        return ApiHelper::output(10000, $info['data'], compact('pagination','User'));
    }
}