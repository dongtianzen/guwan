"""
# get_hist_data and save JSON file
# python3 web/modules/custom/python/get_hist_data_day.py

# alternative ts.get_k_data
# cc = ts.get_k_data('399300', index=True, start='2016-10-01', end='2017-01-31')
# print (cc)

"""

#
from datetime import date, timedelta
import pandas as pd
import time

from featureClass import GetFeatureClass

import tushare as ts

# print (ts.cap_tops())
# exit()

# for print execution time start
start_time = time.time()

codeList = ['600006', '600007', '600008', '600009', '600010']
codeList = ['600006', '600007']
allHistoryDataFrames = [];

# todayDate is like '2017-12-26'
todayDate = str(date.today())
startDay = str(date.today() - timedelta(2))

#
for code in codeList:
  histData = GetFeatureClass().getHistoryData(code, startDay)

  for row in histData.index.values:
    histDataCache = histData.rename(index={row: (code + '_' + row)})
    histData = histDataCache

  allHistoryDataFrames.append(histData)

allHistoryData = pd.concat(allHistoryDataFrames)

# debug
# print (allHistoryData)
# exit()

GetFeatureClass().generateHistoryDataToJson(allHistoryData)

# for print execution time end
print("--- %s seconds ---" % (time.time() - start_time))
