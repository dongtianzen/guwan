"""
# get_hist_data and save JSON file
#

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
    codeList = ['sh', '000515']
    codeList = ['601636', '601666', '601633', '601618', '601607', '601601']

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

  #%%



# startDate is today('2018-06-23') 减去 想开始的日期个数

startDay = 0
startDate = str(date.today() - timedelta(startDay))
endDate   = str(date.today() - timedelta(startDay - 50))
# endDate   = None

RunGetHistData().specifyCodeList(startDate)

# for print execution time end
print("--- %s seconds ---" % (time.time() - start_time))
exit()

for pageNum in range(69, 90):
  print(pageNum)
  RunGetHistData().specifyCodeListWithPageNum(pageNum, startDate, endDate)



FlexTushareBasic().getTodayAll()


