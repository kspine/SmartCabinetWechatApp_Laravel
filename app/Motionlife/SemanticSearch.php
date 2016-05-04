<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 1/30/2016
 * Time: 6:36 AM
 */

namespace App\Motionlife;

use App\Smartkey;

class SemanticSearch
{
    protected $content;
    protected $threshold;

    public function __construct($content, $threshold = 8)
    {
        $this->content = $content;
        $this->threshold = $threshold;
    }

    public function composeResponse()
    {
        $keys = $this->getSnFromContext($this->content, $this->threshold);
        if (!$keys) return "对不起，没有找到您要的结果，您是说“{$this->content}”？。获得更精确的搜索结果，请点击查看<a href='http://iot.sg-z.com/about#search'>搜索规则</a>，或者到钥匙总览中查询。";
        $rsp = "为您搜索到的钥匙如下:";
        $i = 0;
        foreach ($keys as $key) {
            $fn = sprintf('#%03d', $key->sn);
            $state = $key->missing ? '丢失' : ($key->state ? '借出' : '在箱');
            $rsp = $rsp . "\n\n钥匙编号:{$fn}   状态:{$state}\n钥匙名称：<a href='http://iot.sg-z.com/smartkey/{$key->sn}?from=query'>{$key->door}</a>";
            if ($i++ > 10) {
                $rsp = $rsp . "\n\n更多请<a href='http://iot.sg-z.com/smartkey'>查看</a>";
                break;
            }
        }
        return $rsp . "\n\n点击可查看详情并操作";
    }

    public function getSnFromContext($content, $threshold = 8)
    {
        //first if user explicitly search for key by sn we just do as they say
        if(!empty($keys = $this->getTheNumKey($content))) return $keys;

        //if they explicitly want all out-keys or in-keys or missing keys
        if(!empty($keys = $this->getTheStateKeys($content, $threshold))) return $keys;

        //if the user search from door name we should process their input text
        return $this->getMostlikelySns($content);
    }

    public function getTheNumKey($content, $threshold=1)
    {
        $keys = [];
        //if the first char is not # or 零一二三...+'号' return false otherwize return the number
        if (substr($content, 0, 1) == '#') {
            $validsn = intval(substr($content, 1));
        } elseif (is_numeric($content)) {
            $validsn = $content;
        } elseif ($po = strpos($content, '号') !== false) {
            //todo need an implementation of CHINESE DIGIT TO ARABIC DIGIT
            $content = str_replace(['号','钥匙'],'',$content);
            $tempkeys = Smartkey::select('sn', 'door', 'state', 'missing')->get();
            foreach($tempkeys as $k)
            {
                if(levenshtein($content,new ChineseInteger($k->sn))<$threshold)
                    array_push($keys, $k);
            }
        }
        if (isset($validsn)) {
            $key = Smartkey::select('sn', 'door', 'state', 'missing')->where('sn', $validsn)->first();
            if($key) array_push($keys, $key);
        }
        return $keys;
    }

    public function getTheStateKeys($content, $threshold = 8)
    {
        if ((strpos($content, '所有') === false)&&(strpos($content,'全部')===false)) return null;
        $content = str_replace(['所有','全部'], '', $content);
        $texts = ['可借用的钥匙', '在箱的钥匙',
            '还没未还的钥匙', '仍未归还的钥匙','借出去的钥匙',
            '丢遗失的钥匙', '搞掉了的钥匙'];
        for ($i = 0; $i < sizeof($texts); $i++) {
            $texts[$i] = levenshtein($texts[$i], $content);
        }
        $min = min($texts);
        if ($min >= $threshold) return null;
        switch ($index = array_keys($texts, $min)[0]) {
            case 0:
            case 1:
                $query = 'state =0 and missing!=1';
                break;
            case 2:
            case 3:
            case 4:
                $query = 'state =1 and missing!=1';
                break;
            case 5:
            case 6:
                $query = 'missing = 1';
        }
        if (isset($query)){
            $keys = Smartkey::select('sn', 'door', 'state', 'missing')->whereRaw($query)->get();
            if(!$keys->isEmpty()) return $keys;
        }
        return false;
    }

    public function getMostlikelySns($content, $threshold=8)
    {
        $keys = [];
        $smks = Smartkey::select('sn', 'door', 'state', 'missing')->get();
        foreach ($smks as $key) {
            if (($key->id = levenshtein(str_replace(['开闭所','配电箱'], '', $key->door), str_replace(['开闭所','配电箱'], '', $content))) <= $threshold)
                array_push($keys, $key);
        }
        usort($keys,function($a,$b){
              return $a->id - $b->id;
        });
        return array_slice($keys,0,3);
    }

}