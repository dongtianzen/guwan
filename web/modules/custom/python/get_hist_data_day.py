# get_hist_data and save JSON file

# cd web/modules/custom/python
# python3 get_hist_data_day.py

# alternative ts.get_k_data
# cc = ts.get_k_data('399300', index=True, start='2016-10-01', end='2017-01-31')
# print (cc)

#
import tushare as ts
from datetime import date, timedelta

# todayDate is like '2017-12-26'
todayDate = str(date.today())
yesterday = str(date.today() - timedelta(5))

code = '000875'

# get Day data
def getHistoryData(code):
  histData = ts.get_hist_data(code, ktype = 'D', start = yesterday)
  return histData

histData = getHistoryData(code)

for row in histData.index.values:
  histDataCache = histData.rename(index={row: (code + '_' + row)})
  histData = histDataCache

# debug
# print (histData)
# exit()

# 生成Json格式的文件
def generateHistoryDataToJson():
  histData.to_json('historyDataDat.json', orient='index')

generateHistoryDataToJson()
