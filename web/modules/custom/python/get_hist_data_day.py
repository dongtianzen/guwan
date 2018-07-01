# get_hist_data and save JSON file

# cd web/modules/custom/python
# python3 get_hist_data_day.py

# alternative ts.get_k_data
# cc = ts.get_k_data('399300', index=True, start='2016-10-01', end='2017-01-31')
# print (cc)

#
from datetime import date, timedelta
import pandas as pd
import time

from featureClass import GetFeatureClass


# for print execution time start
start_time = time.time()

codeList = ['600006', '600007', '600008', '600009', '600010']
allHistoryDataFrames = [];

# todayDate is like '2017-12-26'
todayDate = str(date.today())
yesterday = str(date.today() - timedelta(3))
startDay = yesterday

getFeature = GetFeatureClass();

#
for code in codeList:
  histData = getFeature.getHistoryData(code, startDay)

  for row in histData.index.values:
    histDataCache = histData.rename(index={row: (code + '_' + row)})
    histData = histDataCache

  allHistoryDataFrames.append(histData)

allHistoryData = pd.concat(allHistoryDataFrames)

# debug
# print (allHistoryData)
# exit()

getFeature.generateHistoryDataToJson(allHistoryData)

# for print execution time end
print("--- %s seconds ---" % (time.time() - start_time))
