"""

python3 web/modules/custom/python/datareader/runstockrank.py

"""

from getstockdata import GetPriceBasic

pricesDf = GetPriceBasic().getHistPrice()
print(pricesDf.info())

# print(pricesDf)

# 选取DataFrame最后一行，返回的是DataFrame
print(pricesDf.iloc[-1:])

#选取最后一行, 第5列，用于已知行、列位置的选
# Date        High   Low    Open   Close  Volume   Adj Close
# 2018-10-24  23.07  22.62  22.65  22.79  3593873  22.790001
print(pricesDf.iat[-1, 3])
# print(pricesDf['2018-10-24':])
# print(pricesDf.loc['2018-10-24',['Close']])
# print(pricesDf['2018-10-24', 'Close'])
