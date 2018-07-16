"""
# get_hist_data and save JSON file
# python3 web/modules/custom/python/get_hist_data_day.py

# alternative ts.get_k_data
# cc = ts.get_k_data('399300', index=True, start='2016-10-01', end='2017-01-31')
# print (cc)

"""

from datetime import date, timedelta

import pandas as pd
import time

import tushare as ts

from FlexJsonClass import FlexJsonBasic

# for print execution time start
start_time = time.time()


# fullCodeList = FlexJsonBasic().convertViewsJsonToTermCodeList()
# print(fullCodeList)
# exit()

# fullCodeList = ['600006', '600007', '600008', '600009', '600010']
fullCodeList = ['600290', '600291']

# startDate is today('2018-06-23') 减去 想开始的日期个数
startDate = str(date.today() - timedelta(2))

#
allHistoryDataFrames = [];
for code in fullCodeList:
  histData = histData = ts.get_hist_data(code = code, ktype = 'D', start = startDate)

  for row in histData.index.values:
    histDataCache = histData.rename(index={row: (code + '_' + row)})
    histData = histDataCache

  allHistoryDataFrames.append(histData)

# Concatenate multiple array to pandas objects
allHistoryData = pd.concat(allHistoryDataFrames)
print(allHistoryDataFrames)
print(allHistoryData)
exit()
jsonFilePath = FlexJsonBasic().getJsonFilePath()
FlexJsonBasic().generateJsonFromData(jsonFilePath, allHistoryData)

# for print execution time end
print("--- %s seconds ---" % (time.time() - start_time))
exit()
