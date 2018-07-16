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

codeList = FlexJsonBasic().convertViewsJsonToTermCodeList()

# codeList = ['600006', '600007', '600008', '600009', '600010']
# codeList = ['600290', '600291']

# startDate is today('2018-06-23') 减去 想开始的日期个数
startDate = str(date.today() - timedelta(2))

allHistoryData = FlexTushareBasic().downloadHistDataByCode(codeList, startDate)

jsonFilePath = FlexJsonBasic().getGenerateJsonFilePath()
FlexJsonBasic().generateJsonFromData(jsonFilePath, allHistoryData)

# for print execution time end
print("--- %s seconds ---" % (time.time() - start_time))
exit()
