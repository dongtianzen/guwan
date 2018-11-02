"""

python3 web/modules/custom/python/datareader/runstockrank.py

"""
import pandas as pd
import talib


from getstockdata import GetPriceBasic
from checkcondition import CheckCondition

from FlexJsonClass import FlexJsonBasic


# pricesDf = GetPriceBasic().getHistPrice('601628')
# print(pricesDf.head())
# print(pricesDf['Close'])


codeList = FlexJsonBasic().getAllStockCodeList()
codeList = ['601898']
for codeNum in codeList:
  print(codeNum)

  pricesDf = GetPriceBasic().getHistPrice(codeNum)
  # print(pricesDf)
  # if not(pricesDf.empty):

  #   maxDay = -6
  #   for endRow in range(-1, maxDay, -1):
  #     comparePrice = CheckCondition().comparePriceRatio(pricesDf, endRow)

  #     if comparePrice:
  #       pass
  #       # print('comparePrice ' + str(endRow))

  #       compareVolume = CheckCondition().compareVolumeRatio(pricesDf, endRow)
  #       if compareVolume:
  #         pass
  #         # print('compareVolume ' + str(endRow))

  #         checkMacd = CheckCondition().checkMacd(pricesDf, endRow)
  #         if checkMacd:
  #           pass
  #           # print('checkMacd ' + str(endRow))

  #         else:
  #           break

  #       else:
  #         break

  #     else:
  #       break

  #     if (endRow == (maxDay + 1)):
  #       print ('It pass all condition : ' + codeNum)

CheckCondition().checkMacd(pricesDf, -1)


