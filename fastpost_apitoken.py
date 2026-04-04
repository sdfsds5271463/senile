# 快速測試 POST
import requests

#取得 api-token
with open("api-token.txt", "r", encoding="utf-8") as f:
    apitoken = f.read()
    
#準備請求
url = "http://127.0.0.1/api/userInfo"
headers = {'Authorization': 'Bearer '+apitoken,}
post_data = {}

# 使用 data= 參數 表單發送
response = requests.post(url, data=post_data, headers=headers)  
print("表單發送: ", response.json())



