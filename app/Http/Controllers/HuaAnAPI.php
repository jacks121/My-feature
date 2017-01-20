<?php
/**
 * Created by PhpStorm.
 * User: Joe
 * Date: 17/1/12
 * Time: 下午4:21
 */

class HuaAnAPI{

    //请求华安接口地址
    const CAR_SERVICE = 'http://agenttest.sinosafe.com.cn/carservice';

    /**
     * post请求公共方法
     * @param $xmlData xml  发送数据
     * @return xml
     */
    private function basePostRequest($xmlData)
    {
        $header[] = "Content-type: text/xml";//定义content-type为xml
        $ch = curl_init ();
        curl_setopt($ch, CURLOPT_URL, CAR_SERVICE);//定义表单提交地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);//定义自动输出返回内容
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//定义请求类型
        curl_setopt($ch, CURLOPT_POST, 1);//定义提交类型
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);//定义提交的数据
        curl_exec($ch);
        if(curl_errno($ch))
            print curl_error($ch);
        curl_close($ch);
    }

    /**
     * 如果我没理解错的话，我们请求所有数据都是通过同一个API。
     * 对方会根据我们传递的数据来判断我们需要请求的数据。
     * 所以我在上面写了一个公共的post请求方法。
     * 之后的方法只需要传递不同的数据。
     */
    public function test()
    {
        $xmlData = <<<XML
<?xml version='1.0' encoding='utf-8'?>
<HEAD>
    <TRANSTYPE></TRANSTYPE>
    <SYSCODE></SYSCODE>
    <TANSCODE></TANSCODE>
    <CONTENTTYEP></CONTENTTYEP>
    <VERIFYTYPE></VERIFYTYPE>
    <USER></USER>
    <PASSWORD></PASSWORD>
    <SVCSEQNO></SVCSEQNO>
</HEAD>
XML;
        $this->basePostRequest($xmlData);
    }

    public function test1()
    {
        //组装数据然后调用basePostRequest方法就会输出相应的数据
    }

    public function test2()
    {
        //组装数据然后调用basePostRequest方法就会输出相应的数据
    }

}