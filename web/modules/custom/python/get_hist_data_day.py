"""
# get_hist_data and save JSON file
# python3 web/modules/custom/python/get_hist_data_day.py

# alternative ts.get_k_data
# cc = ts.get_k_data('399300', index=True, start='2016-10-01', end='2017-01-31')
# print (cc)

"""

import pandas as pd
import time

from datetime import date, timedelta

from FlexJsonClass import FlexJsonBasic
from FlexTushareClass import FlexTushareBasic

# for print execution time start
start_time = time.time()


# define a class
class RunGetHistData:

  def specifyCodeList(self, startDate, endDate = None):
    # codeList = ['600006', '600007', '600008', '600009', '600010']
    codeList = ['600290', '600291']
    codeList = ['600291', '000515']
    codeList = ['sh', 'sz', '000515']

    allHistoryData = FlexTushareBasic().downloadHistDataByCode(codeList, startDate, endDate)

    fileName = 'historyDataByCodeList.json'
    jsonFilePath = FlexJsonBasic().getGenerateJsonFilePath(fileName)
    FlexJsonBasic().generateJsonFromData(jsonFilePath, allHistoryData)

  def specifyCodeListWithPageNum(self, pageNum, startDate, endDate):
    codeList = FlexJsonBasic().convertViewsJsonToTermCodeList(pageNum)

    allHistoryData = FlexTushareBasic().downloadHistDataByCode(codeList, startDate, endDate)

    fileName = 'historyDataByCodeListPiece_' + str(pageNum) + '.json'
    jsonFilePath = FlexJsonBasic().getGenerateJsonFilePath(fileName)
    FlexJsonBasic().generateJsonFromData(jsonFilePath, allHistoryData)

  # -->



# startDate is today('2018-06-23') 减去 想开始的日期个数

startDay = 50
startDate = str(date.today() - timedelta(startDay))
endDate   = str(date.today() - timedelta(startDay - 10))
# endDate   = None

for pageNum in range(90):
  print(pageNum)
  RunGetHistData().specifyCodeListWithPageNum(pageNum, startDate, endDate)


# for print execution time end
print("--- %s seconds ---" % (time.time() - start_time))
exit()

RunGetHistData().specifyCodeList(startDate)
FlexTushareBasic().getTodayAll()


