"""
pandas-datareader
pip3 install pandas-datareader

https://www.jianshu.com/p/799027dd979a

DataReader函数中第二个参数代表数据来源
  Yahoo! Finance//雅虎金融
  Google Finance//谷歌金融
  Enigma//Enigma是一个公共数据搜索的提供商
  St.Louis FED (FRED)//圣路易斯联邦储备银行
  Kenneth French’s data library//肯尼斯弗兰奇资料库
  World Bank//世界银行
  OECD//经合组织
  Eurostat//欧盟统计局
  Thrift Savings Plan//美国联邦政府管理离退休的组织
  Oanda currency historical rate //外汇经纪商
  Nasdaq Trader symbol definitions //纳斯达克


python3 web/modules/custom/python/datareader/getHistData.py

"""

import datetime
import pandas as pd
import pandas_datareader.data as web

#%%
# define a class
class GetPriceBasic:

  # @return DataFrame
  def getHistData(self):
    start = datetime.datetime(2018, 10, 18) # or start = '1/1/2018'
    end   = datetime.date.today()

    stockCode = '601628'
    stockCode = stockCode + '.SS'

    # get DataFrame "pricesDf",
    # DataReader函数中第二个参数代表数据来源，DataReader支持包括雅虎、谷歌在内的十数种数据来源，本篇笔记只关注来源为雅虎财经的数据。
    # 通常情况下我们只关注最后一列Adjusted Closing Price 并使用它计算收益率。Adj Close的好处是已将所有的权重、分割和股利分发等因素考虑在了价格中进行调整。
    # 值得一提的是，如果在给定日期内，该证券并没有操作活动，DataReader函数将返回一个空的DataFrame，既没有index，也没有列名。
    pricesDf = web.DataReader(stockCode, 'yahoo', start, end)

    return pricesDf


pricesDf = GetPriceBasic().getHistData()
print(pricesDf.info())
# print(pricesDf.head())
# print(pricesDf.describe())

  # 1 获取股价数据
