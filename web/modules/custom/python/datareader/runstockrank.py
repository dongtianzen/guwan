"""

python3 web/modules/custom/python/datareader/runstockrank.py

"""
import pandas as pd

from getstockdata import GetPriceBasic
from checkcondition import CheckCondition

from FlexJsonClass import FlexJsonBasic

pricesDf = GetPriceBasic().getHistPrice('601628')
# print(pricesDf.head())



for pageNum in range(0, 90):
  print(pageNum)
  codeList = FlexJsonBasic().convertViewsJsonToTermCodeList(pageNum)
  print(codeList)

ma5 = CheckCondition().comparePriceRatio(pricesDf, -1)
print(ma5)
ma5 = CheckCondition().compareVolumeRatio(pricesDf, -1)
print(ma5)
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
