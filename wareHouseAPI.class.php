<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 2017/8/24
 * Time: 下午3:11
 */
abstract class wareHouseAPI{

    /**
     * 接口提供给我们的账号
     */
    protected $id;

    /**
     * 接口提供给我们的密码或者令牌
     */
    protected $key;

    /**
     * 接口的域名
     */
    protected $base_url;

    /**
     * 接口的方法
     */
    protected $extend_url;

    /**
     * wareHouseAPI constructor.
     */
    public function __construct($connection)
    {
        $this->id = $connection['id'];
        $this->key = $connection['key'];
        $this->base_url = $connection['base_url'];
        $this->extend_url = $connection['extend_url'];
    }

    /**
     * 获取第三方的商品
     * @param $start
     * @param int $limit
     * @return mixed
     */
    abstract public function getGoods($start, $limit = 100);

    /**
     * 数据导入
     * @param $data
     * @return mixed
     */
    public function importData($data){
        M('goods')->insert($data['field'],$data['value'],'mall_goods');
    }
}