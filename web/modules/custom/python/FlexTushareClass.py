"""
# get_hist_data and save JSON file
# python3 web/modules/custom/python/FlexTushareClass.py

"""

import pandas as pd

import tushare as ts

# define a class
class FlexTushareBasic:

  def downloadHistDataByCode(self, fullCodeList, startDate):
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

    return allHistoryData
