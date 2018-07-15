"""
# get_hist_data and save JSON file
# python3 web/modules/custom/python/get_hist_data_day.py

# alternative ts.get_k_data
# cc = ts.get_k_data('399300', index=True, start='2016-10-01', end='2017-01-31')
# print (cc)

"""

#
from datetime import date, timedelta

import json
import pandas as pd
import time

import tushare as ts

from featureClass import GetFeatureClass

import urllib.request



# for print execution time start
start_time = time.time()

urlPath = ("http://localhost:8888/agu/web/views/json/debug-term-code-table?_format=json")
with urllib.request.urlopen(urlPath) as url:
  termCodeData = json.loads(url.read().decode())

fullCodeList = []
for termCodeRow in termCodeData:
  fullCodeList.append(termCodeRow['name'][0]['value'])

# fullCodeList = ['600006', '600007', '600008', '600009', '600010']
fullCodeList = ['600290', '600291']

# startDate is today('2018-06-23') 减去 想开始的日期个数
startDate = str(date.today() - timedelta(3))

#
allHistoryDataFrames = [];
for code in fullCodeList:
  histData = GetFeatureClass().getHistoryData(code, startDate)

  for row in histData.index.values:
    histDataCache = histData.rename(index={row: (code + '_' + row)})
    histData = histDataCache

  allHistoryDataFrames.append(histData)

allHistoryData = pd.concat(allHistoryDataFrames)

#
GetFeatureClass().generateHistoryDataToJson(allHistoryData)

# for print execution time end
print("--- %s seconds ---" % (time.time() - start_time))

print("555")
exit()
