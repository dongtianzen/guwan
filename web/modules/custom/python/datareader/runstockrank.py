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
# codeList = ['600372']
for codeNum in codeList:
  print(codeNum)

  pricesDf = GetPriceBasic().getHistPrice(codeNum)

  if not(pricesDf.empty):

    maxDay = -6
    for endRow in range(-1, maxDay, -1):
      comparePrice = CheckCondition().comparePriceRatio(pricesDf, endRow)

      if comparePrice:
        print('comparePrice ' + str(endRow))

        compareVolume = CheckCondition().compareVolumeRatio(pricesDf, endRow)
        if compareVolume:
          print('compareVolume ' + str(endRow))

          checkMacd = CheckCondition().checkMacd(pricesDf, endRow)
          if checkMacd:
            print('checkMacd ' + str(endRow))

          else:
            break

        else:
          break

      else:
        break

      if (endRow == (maxDay + 1)):
        print ('It pass all condition : ' + codeNum)

# CheckCondition().comparePriceRatio(pricesDf, -1)


# print(ss.mean)
# print(pricesDf['Close'][-8:-2])
# print(pricesDf['Close'])
# print(pricesDf['Close'][-2:])



# 选取DataFrame最后一行，返回的是DataFrame
# cc = pricesDf.iloc[-1:]['Close']
# print(type(cc))
# print(pricesDf.iloc[-1:]['Close'])
# print(pricesDf['Close'][-8:-2])

#选取最后一行, 第5列，用于已知行、列位置的选
# print(pricesDf.iat[-1, 3])
# print(pricesDf.loc['2018-10-24',['Close']])
