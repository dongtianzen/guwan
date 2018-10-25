"""

python3 web/modules/custom/python/datareader/runstockrank.py

"""
import pandas as pd

from getstockdata import GetPriceBasic
from checkcondition import CheckCondition

pricesDf = GetPriceBasic().getHistPrice()
# print(pricesDf.head())

# print(pricesDf['Close'][-1])

# CheckCondition().comparePriceRatio(pricesDf, -1)


# 选取DataFrame最后一行，返回的是DataFrame
# print(pricesDf.iloc[-1:])

#选取最后一行, 第5列，用于已知行、列位置的选
# Date        High   Low    Open   Close  Volume   Adj Close
# 2018-10-24  23.07  22.62  22.65  22.79  3593873  22.790001
# 2018-10-25  23.11  21.62  22.80  22.79  2591853  21.790001

# print(pricesDf.iat[-1, 3])
# print(pricesDf.loc['2018-10-24',['Close']])
