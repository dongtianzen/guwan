"""

python3 web/modules/custom/python/datareader/runstockrank.py

"""
import pandas as pd

from getstockdata import GetPriceBasic
from checkcondition import CheckCondition

from FlexJsonClass import FlexJsonBasic

# pricesDf = GetPriceBasic().getHistPrice('000014')
# print(pricesDf.head())


codeList = FlexJsonBasic().getAllStockCodeList()
for codeNum in codeList:
  print(codeNum)
  pricesDf = GetPriceBasic().getHistPrice(codeNum)

  if not(pricesDf.empty):

    for endRow in range(-1, -6, -1):
      comparePrice = CheckCondition().comparePriceRatio(pricesDf, endRow)
      print('comparePrice ' + str(endRow))

      if comparePrice:
        compareVolum = CheckCondition().compareVolumeRatio(pricesDf, endRow)
        print('compareVolum ' + str(endRow))
        if compareVolum:
          pass
        else:
          break

      else:
        break

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
