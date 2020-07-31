# POINT(PO INit Translation)

a tool to quick translate gettext po files writen by php.

since poedit need to pay for Machine Translation, I wrote this in a afternoon.

it also has a phar file to make it simple for run.

## HOW TO USE

this tool just do translation from po file you give at 1 line/s

the backend is baidu AI translation service https://api.fanyi.baidu.com/ which can be use for free in limit condition

you need register a baidu fanyi account and set your env API-KEY

you may save export in your .bashrc file
```
export BAIDU_APP_ID=xxx
export BAIDU_SEC_KEY=xxx

```


## Support Languages

Refer to [https://api.fanyi.baidu.com/doc/21](https://api.fanyi.baidu.com/doc/21)

some at below

| code | lang |
| --- | --- |
| zh | Simple Chinese |
| en | English |
| jp | Japanese |
| kor | Korean |
| fra | Franch |
| spa | Spanish |
| cht | Traditional Chinese |


## Command

```
point -u -s zh -d en -f en.po -o en2.po 
```

## Development

```
cd src && php package.php 
export BAIDU_APP_ID=xxx
export BAIDU_SEC_KEY=xxx
cd .. && php point.phar -xxx
```