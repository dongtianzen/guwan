"""
# get_hist_data and save JSON file
python3 web/modules/custom/python/FlexTushareClass.py

"""

import pandas as pd

import tushare as ts


# define a class
class FlexTushareBasic:

  # @param codeList is list like ['600290', '600291']
  # @param startDate is string like "2018-06-23"
  def downloadHistDataByCode(self, codeList, startDate, endDate):

    allHistoryDataFrames = [];
    for code in codeList:

      # histData's type is <class 'pandas.core.frame.DataFrame'> or "NoneType"
      histData = ts.get_hist_data(code = code, ktype = 'D', start = startDate, end = endDate)

      # should check is empty or not

      # check "histData" is not "NoneType":
      if histData is not None:
        if not histData.empty:
          if len(histData) > 0:
            print(code)

            for row in histData.index.values:
              print(row)

              histDataCache = histData.rename(index={row: (code + '_' + row)})
              histData = histDataCache

          allHistoryDataFrames.append(histData)

    # Concatenate multiple array to pandas objects
    allHistoryData = pd.concat(allHistoryDataFrames)

    return allHistoryData

  def getTodayAll(self):

    todayData = ts.get_today_all()
    print(todayData)

    return

