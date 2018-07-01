# get_hist_data and save JSON file

import tushare as ts
from datetime import date, timedelta

# todayDate is like '2017-12-26'
todayDate = str(date.today())
yesterday = str(date.today() - timedelta(3))

# define a class
class GetFeatureClass:
  # 生成Json格式的文件
  def generateHistoryDataToJson(self, allHistoryData):
    allHistoryData.to_json('historyDataDat.json', orient='index')

    print ('JSON generate success')
    return

  # get Day data
  def getHistoryData(self, code):
    histData = ts.get_hist_data(code, ktype = 'D', start = yesterday)
    return histData
