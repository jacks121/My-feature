<?php

/**
 * +------------------------------------------------------------+
 * 澳购接口文件
 * +------------------------------------------------------------+
 * Created by PhpStorm.
 * User: joe
 * Date: 2017/8/23
 * Time: 上午9:35
 */
class austgoAPI extends wareHouseAPI
{

    // region -- Base
	//hahaha1
    /**
     * 创建接口需要的json数据
     * @param $data
     * @return array|string
     */
    public function createJson($data = '')
    {
        $seq = $this->createSeq();
        $sign = $this->createSign($seq, $data);
        $json = [
            'appId' => $this->id,
            'seq' => $seq,
            'request' => $data,
            'sign' => $sign
        ];

        $json = json_encode($json);
        return $json;
    }

    /**
     * 创建sign
     * @param $data 根据接口要求发送数组
     * @return string
     */
    public function createSign($seq, $data)
    {
        ksort($data);
        $tmp = arr($data);
        return strtoupper(md5($seq . $tmp . $this->key));
    }

    /**
     * 创建seq = 毫秒级时间戳
     * @return array|string
     */
    public function createSeq()
    {
        $time = explode(" ", microtime());
        $time = $time [1] . ($time [0] * 1000);
        $time2 = explode(".", $time);
        $time = $time2 [0];
        return $time;
    }

    /**
     * curl请求接口
     * @param $url
     * @param $post_data
     * @return mixed
     */
    protected function postCurl($url, $post_data)
    {
        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * 请求接口
     * @param $data request数据
     * @param $api 接口映射名称 参考apiMap方法
     * @return mixed
     */
    public function _request($data, $api)
    {
        $url = $this->base_url . $this->extend_url[$api];
        $post = $this->createJson($data);
        $res = $this->postCurl($url, $post);
        $res = json_decode($res, true);
        return $res['data'];
    }

    // endregion

    // region -- 各个不同接口的封装

    /**
     * 获取仓库列表
     * @return mixed
     */
    public function wareHouse()
    {
        return $this->_request('', 'warehouse');
    }

    /**
     * 获取商品列表
     * @param $start 起始ID 每次最多获取50个
     * @param $limit 查询多少个 默认为100 必须是50的倍数
     * @return mixed
     */
    public function getGoods($start, $limit = 100)
    {
        $data = ['fromId' => $start];
        $goods_data = [];
        $count = 50;
        $sum = 0;
        while ($count == 50 && $sum < $limit) {
            $result = $this->_request($data, 'goods_scan');
            $goods_data = array_merge($goods_data, $result);
            $count = count($result);
            $start = $start + 50;
            $sum = count($goods_data);
        }
        return $goods_data;
    }

    /**
     * 获取商品详情
     * @param array $ids 如果是查询单个商品 直接传int数字就可以了
     * @param bool|false $includeContent 是否需要详情
     * @return mixed
     */
    public function goods($ids, $includeContent = false)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $data = [
            'ids' => $ids,
            'includeContent' => $includeContent
        ];
        return $this->_request($data, 'goods');
    }

    /**
     * 获取sku
     * @param $ids array 如果查询单条 传int
     * @return mixed
     */
    public function sku($ids)
    {
        if (!is_array($ids)) {
            $data['ids'] = [$ids];
        } else {
            $data['ids'] = $ids;
        }
        return $this->_request($data, 'sku');
    }

    /**
     * 批量查询sku
     * @return mixed
     */
    public function skuStatus()
    {
        return $this->_request('', 'sku_status');
    }

    /**
     * 添加购物车
     * @param $data
     * @return mixed
     */
    public function addCart($data)
    {
        return $this->_request($data,'cart');
    }

    /**
     * 创建订单
     * @param $data
     * $data = [
     *    'isPickup' => true, //是否字体
     *    'currency' => 'RMB', //结算货币
     *    'includeInsurance' => false, //是否购买保险 可以不传 默认为False
     *    'items' => [
     *        ['id' => 19876, 'qty' => 3], //skuID 规格ID/货号  ，qty购买数量
     *        ['id' => 19877, 'qty' => 1],
     *        ['id' => 12940, 'qty' => 1],
     *        ['id' => 15, 'qty' => 10],
     *    ],
     *    'autoPay' => false, //是否使用自动支付 我们采用手动支付 传false
     *    'billName' => 'Joe', //收件人名称
     *    'billPhone' => '18629001764', //收件人电话
     *    'billAddress' => '西安市 大寨路', //收件人地址
     *    'idNumber' => '430726198509200012', //收件人身份证号 非必选项
     *    'senderName' => 'jelly', //发件人姓名 非必选项 不传发件人为澳购
     *    'senderPhone' => '15829372775', //发件人电话 非必选项 不传电话为澳购电话
     *    'remark' => '挑个好一点的', //订单备注 仓库打单人会看到
     *    'orderId' => '93142905' //我们平台的订单ID
     * ];
     * @return mixed
     */
    public function createOrder($data)
    {
        return $this->_request($data,'order');
    }

    /**
     * 查看订单详情
     * @param $data string '70000692926'
     * @return mixed
     */
    public function getOrder($data)
    {
        return $this->_request($data,'get_order');
    }
    // endregion


}
