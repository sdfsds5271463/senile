# 快速測試 POST
import requests

#準備請求
url = "http://127.0.0.1/api/tests"
headers = {'User-Agent': 'Mozilla/5.0',}
post_data = {
    'name': 'allen',
    'phone': '0912345678',
    'num': 123,
}

# 使用 data= 參數 表單發送
response = requests.post(url, data=post_data, headers=headers)  
print("表單發送: ", response.json())



