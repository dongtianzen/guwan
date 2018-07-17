"""
# get_hist_data and save JSON file
# python3 web/modules/custom/python/FlexTushareClass.py

"""

import pandas as pd

import tushare as ts

# define a class
class FlexTushareBasic:

  # @param codeList is list like ['600290', '600291']
  # @param startDate is string like "2018-06-23"
  def downloadHistDataByCode(self, codeList, startDate):

    allHistoryDataFrames = [];
    for code in codeList:
      histData = histData = ts.get_hist_data(code = code, ktype = 'D', start = startDate)

      ? should check is empty or not
      for row in histData.index.values:
        histDataCache = histData.rename(index={row: (code + '_' + row)})
        histData = histDataCache

      allHistoryDataFrames.append(histData)

    # Concatenate multiple array to pandas objects
    allHistoryData = pd.concat(allHistoryDataFrames)

    return allHistoryData
