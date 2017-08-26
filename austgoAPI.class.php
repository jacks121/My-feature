<?php

/**
 *+------------------------------------------------------------+
 * 澳购接口封装
 *+------------------------------------------------------------+
 * Created by PhpStorm.
 * User: joe
 * Date: 2017/8/23
 * Time: 上午9:35
 */
class austgoAPI
{

    // region -- attribute
<<<<<<< HEAD
    /**
     * 接口ID
     */
    const APP_ID = '2017082309300576585';

=======
    
>>>>>>> joe
    /**
     * 接口密码
     */
    const APP_TOKEN = '55C9470468044900873E65F046E3C5AC';

    /**
     * 接口基础url
     */
    const BASE_API_URL = 'http://test.austgo.com';
<<<<<<< HEAD
=======
	
    /**
     * 接口ID
     */
    const APP_ID = '2017082309300576585';
>>>>>>> joe

    // endregion

    // region -- 各个接口的url

    /**
     * 扫描商品
     */
    const GOODS_SCAN = '/api/open/goods/scan';

    /**
     * 仓库列表
     */
    const WARE_HOUSE = '/api/open/warehouse';

    /**
<<<<<<< HEAD
     * 商品详情
     */
    const GOODS = '/api/open/goods';

    /**
     * 批量查询SKU状态
     */
    const SKU_STATUS = '/api/open/sku/status';
=======
     * 批量查询SKU状态
     */
    const SKU_STATUS = '/api/open/sku/status';

    /**
     * 商品详情
     */
    const GOODS = '/api/open/goods';
>>>>>>> joe

    /**
     * 查询SKU信息
     */
    const SKU = '/api/open/sku';
    // endregion

    // region -- Base

    /**
     * 接口url映射
     * @param $api
     * @return mixed
     */
    private function apiMap($api)
    {
        $map = [
            'goods_scan' => self::GOODS_SCAN,
            'warehouse' => self::WARE_HOUSE,
            'goods' => self::GOODS,
            'sku' => self::SKU,
            'sku_status' => self::SKU_STATUS,
        ];
        return $map[$api];
    }

    /**
     * 创建接口需要的json数据
     * @param $data
     * @return array|string
     */
    public function createJson($data = '')
    {
        $seq = $this->createSeq();
        $json = [
            'appId' => self::APP_ID,
            'seq' => $seq,
            'request' => $data,
            'sign' => $this->createSign($seq, $data)
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
        $tmp = '';
        foreach ($data as $item) {
            if (is_bool($item)) {
                $tmp .= $item ? 'True' : 'False';
            } elseif (is_array($item)) {
                $tmp .= implode('',$item);
            } else {
                $tmp .= $item;
            }
        }
        return strtoupper(md5($seq . $tmp . self::APP_TOKEN));
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
        $url = self::BASE_API_URL . $this->apiMap($api);
        $post = $this->createJson($data);
        $res = $this->postCurl($url, $post);
        return json_decode($res, true);
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
     * @return mixed
     */
    public function goodsScan($start)
    {
        $data = ['fromId' => $start];
        return $this->_request($data, 'goods_scan');
    }

    /**
     * 获取商品详情
     * @param array $ids 如果是查询单个商品 直接传int数字就可以了
     * @param bool|false $includeContent
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
        if(!is_array($ids)){
            $data['ids'] = [$ids];
        }else{
            $data['ids'] = $ids;
        }
        return $this->_request($data,'sku');
    }

    /**
     * 批量查询sku
     * @return mixed
     */
    public function skuStatus()
    {
        return $this->_request('','sku_status');
    }

    // endregion
}
