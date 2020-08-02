# coding=utf-8

import http.client
import hashlib
import urllib
import random
import json

import opencc

import sys, getopt



opts, args = getopt.getopt(sys.argv[1:], "f:o:s:d:huv")

input_file=""

output_file=""

for op, value in opts:

    if op == "-i":

        input_file = value

    elif op == "-o":

        output_file = value

    elif op == "-h":

        usage()

        sys.exit()


xg = 's2hk.json'
tw = 's2twp.json'

h=open(filehk,"w+")
converter = opencc.OpenCC(xg)
with open(file1) as f:
    while True:
        line=f.readline()
        print(line)
        if not line:
            break;
        s = converter.convert(line)
        h.write(s)

h.close()

appid = ''  # 填写你的appid
secretKey = ''  # 填写你的密钥

httpClient = None
myurl = '/api/trans/vip/translate'

fromLang = 'auto'   #原文语种
toLang = 'zh'   #译文语种
salt = random.randint(32768, 65536)
q= 'apple'
sign = appid + q + str(salt) + secretKey
sign = hashlib.md5(sign.encode()).hexdigest()
myurl = myurl + '?appid=' + appid + '&q=' + urllib.parse.quote(q) + '&from=' + fromLang + '&to=' + toLang + '&salt=' + str(
salt) + '&sign=' + sign

try:
    httpClient = http.client.HTTPConnection('api.fanyi.baidu.com')
    httpClient.request('GET', myurl)

    # response是HTTPResponse对象
    response = httpClient.getresponse()
    result_all = response.read().decode("utf-8")
    result = json.loads(result_all)

    print (result)

except Exception as e:
    print (e)
finally:
    if httpClient:
        httpClient.close()