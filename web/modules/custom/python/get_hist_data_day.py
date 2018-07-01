# get_hist_data and save JSON file

# cd web/modules/custom/python
# python3 get_hist_data_day.py

# alternative ts.get_k_data
# cc = ts.get_k_data('399300', index=True, start='2016-10-01', end='2017-01-31')
# print (cc)

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

#
import tushare as ts
from datetime import date, timedelta
import pandas as pd
import time

start_time = time.time()

# todayDate is like '2017-12-26'
todayDate = str(date.today())
yesterday = str(date.today() - timedelta(3))

codeList = ['600006', '600007', '600008', '600009', '600010']
allHistoryDataFrames = [];

getFeature = GetFeatureClass();

#
for code in codeList:
  histData = getFeature.getHistoryData(code)

  for row in histData.index.values:
    histDataCache = histData.rename(index={row: (code + '_' + row)})
    histData = histDataCache

  allHistoryDataFrames.append(histData)

allHistoryData = pd.concat(allHistoryDataFrames)

# debug
# print (allHistoryData)
# exit()

getFeature.generateHistoryDataToJson(allHistoryData)
print("--- %s seconds ---" % (time.time() - start_time))
