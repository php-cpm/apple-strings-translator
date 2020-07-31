<?php
define("CURL_TIMEOUT", 10);
define("URL", "http://api.fanyi.baidu.com/api/trans/vip/translate");
define("APP_ID", getenv('BAIDU_APP_ID')); //替换为您的APPID
define("SEC_KEY", getenv('BAIDU_SEC_KEY'));//替换为您的密钥

$opts = getopt('f:o:s:d:h::u::v::', array());

if (empty($opts) || isset($opts['h'])) {

    echo <<<EOT
poinit 1.0 - a tool to quick translate wordpress po files writen by php.

https://api.fanyi.baidu.com/doc/21

usage:
  -u : if determined will only translate empty item
  -f : from po file path
  -o : save to po file path
  -s : from lang default: zh
  -d : translate lang default: en 
  -v : verbose mode 
  
setup env like below:
export BAIDU_APP_ID=xxx
export BAIDU_SEC_KEY=xxx
EOT;
    exit;
}
require 'vendor/autoload.php';

if(!APP_ID || !SEC_KEY) {
    exit( "you should setup BAIDU_APP_ID and BAIDU_SEC_KEY in system env \n");
}
$debug = isset($opts['v']) ? true : false;
if ($debug) {
    print_r($opts);
}
$from_lang = $opts['s'] ?? 'zh';
$to_lang = $opts['d'] ?? 'en';
$from_file = $opts['f'] ?? 'zh.po';
$from_file = realpath($from_file);
$save_file = $opts['o'] ?? 'en.po';
$is_update = isset($opts['u']) ? true : false;

if (! file_exists($from_file)) {
    exit("no file exists : " . $from_file);
}
if (file_exists($save_file)) {
    echo "warning ! saving to " . $save_file . "\n";
}
$saveHandler = new \Sepia\PoParser\SourceHandler\FileSystem($save_file);
$compiler = new \Sepia\PoParser\PoCompiler();

echo "traslation start...\n";
echo "source from $from_file\n";
echo $from_lang . '=>' . $to_lang . " " . "\n";
// Parse a po file
$fileHandler = new Sepia\PoParser\SourceHandler\FileSystem($from_file);

$poParser = new Sepia\PoParser\Parser($fileHandler);
$catalog = $poParser->parse();
$checkList = $catalog->getEntries();
$count = count($checkList);
$c = 0;
foreach ($checkList as $entry) {
    $c++;
    if ($debug) {
        echo $c . '/' . $count . "\n";
    }
    $start = microtime(true);
    $from = $entry->getMsgId();
    $now = $entry->getMsgStr();
    if ($is_update && ! empty($now)) {
        continue;
    }
    $to = translate($from, $from_lang, $to_lang);
    $result = $to['trans_result'][0]['dst'] ?? '';
    if (isset($to['error_code']) && $to['error_code'] == 54003) {
        sleep(1);
        $to = translate($from, $from_lang, $to_lang);
        $result = $to['trans_result'][0]['dst'] ?? '';
    }
    if (! empty($result)) {
        $entry->setMsgStr($result);

    } else {

        print_r($to);
        continue;
    }

    $time = (microtime(true) - $start);
    if ($debug) {
        echo $time . ' ' . $from . ' ' . $result . "\r\n";
    }
    usleep(1000 * 1000 * ((1.1 - $time) > 0 ? (1.1 - $time) : ($time - 1.1)));
}

if ($debug) {
    echo 'translation finished!' . "\n";
}
if ($debug) {
    echo 'file save start...' . "\n";
}
$saveHandler->save($compiler->compile($catalog));
if ($debug) {
    echo 'file save finished!' . "\n";
}


//翻译入口
function translate($query, $from, $to)
{
    $args = array(
        'q' => $query,
        'appid' => APP_ID,
        'salt' => rand(10000, 99999),
        'from' => $from,
        'to' => $to,

    );
    $args['sign'] = buildSign($query, APP_ID, $args['salt'], SEC_KEY);
    $ret = call(URL, $args);
    $ret = json_decode($ret, true);
    return $ret;
}

//加密
function buildSign($query, $appID, $salt, $secKey)
{/*{{{*/
    $str = $appID . $query . $salt . $secKey;
    $ret = md5($str);
    return $ret;
}/*}}}*/

//发起网络请求
function call($url, $args = null, $method = "post", $testflag = 0, $timeout = CURL_TIMEOUT, $headers = array())
{/*{{{*/
    $ret = false;
    $i = 0;
    while ($ret === false) {
        if ($i > 1) {
            break;
        }
        if ($i > 0) {
            sleep(1);
        }
        $ret = callOnce($url, $args, $method, false, $timeout, $headers);
        $i++;
    }
    return $ret;
}/*}}}*/

function callOnce($url, $args = null, $method = "post", $withCookie = false, $timeout = CURL_TIMEOUT, $headers = array())
{/*{{{*/
    $ch = curl_init();
    if ($method == "post") {
        $data = convert($args);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1);
    } else {
        $data = convert($args);
        if ($data) {
            if (stripos($url, "?") > 0) {
                $url .= "&$data";
            } else {
                $url .= "?$data";
            }
        }
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (! empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if ($withCookie) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
    }
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}/*}}}*/

function convert(&$args)
{/*{{{*/
    $data = '';
    if (is_array($args)) {
        foreach ($args as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    $data .= $key . '[' . $k . ']=' . rawurlencode($v) . '&';
                }
            } else {
                $data .= "$key=" . rawurlencode($val) . "&";
            }
        }
        return trim($data, "&");
    }
    return $args;
}/*}}}*/
