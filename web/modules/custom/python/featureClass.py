# get_hist_data and save JSON file

import tushare as ts

# define a class
class GetFeatureClass:
  # 生成Json格式的文件
  def generateHistoryDataToJson(self, allHistoryData):
    allHistoryData.to_json('historyDataDat.json', orient='index')

    print ('JSON generate success')
    return

  # get Day data
  def getHistoryData(self, code, startDay):
    histData = ts.get_hist_data(code, ktype = 'D', start = startDay)
    return histData
