# get_hist_data and save JSON file

# cd web/modules/custom/python
# python3 get_hist_data_day.py

import tushare as ts
from datetime import date, timedelta

# alternative ts.get_k_data
# cc = ts.get_k_data('399300', index=True, start='2016-10-01', end='2017-01-31')
# print (cc)

# todayDate is like '2017-12-26'
todayDate = str(date.today())
yesterday = str(date.today() - timedelta(10))

print (yesterday)

#
def getHistoryData():
  # get Day data
  code = '000875'
  df = ts.get_hist_data(code, ktype = 'D', start = yesterday)

  # 生成Json格式的文件
  df.to_json('000875_60m.json', orient='index')
