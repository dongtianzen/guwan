"""


python3 web/modules/custom/python/datareader/checkcondition.py

"""
import pandas as pd
import talib


#%%
# define a class
class CheckCondition:

  ###
  def getSumPrice(self, pricesDf, key, num = 5):
    output = 0

    return output


  ###
  def getAveragePrice(self, pricesDf, endRow, num = 5):
    startRow = endRow - num

    # valueSeries = pricesDf['Close'][-6:-1]
    valueSeries = pricesDf['Close'][startRow:endRow]

    output = pd.Series(valueSeries).mean()

    return output

  ###
  def getAverageVolume(self, pricesDf, endRow, num = 5):
    startRow = endRow - num

    valueSeries = pricesDf['Volume'][startRow:endRow]

    output = pd.Series(valueSeries).mean()

    return output

  ###
  # compare ma5 ma10 on the min < (ma5/ma10) < max
  # @return Boolean, true or false
  def comparePriceRatio(self, pricesDf, endRow, min = 0.97, max = 1.10):
    output = False

    ma5  = self.getAveragePrice(pricesDf, endRow, num = 5);
    ma10 = self.getAveragePrice(pricesDf, endRow, num = 10);

    percentage = ma5 / ma10

    if ((percentage > min) and (percentage < max)):
      output = True

    return output

  ###
  # @return Boolean, true or false
  def compareVolumeRatio(self, pricesDf, endRow, min = 0.97, max = 1.10):
    output = False

    ma5  = self.getAverageVolume(pricesDf, endRow, num = 5);
    ma10 = self.getAverageVolume(pricesDf, endRow, num = 10);

    percentage = ma5 / ma10

    if ((percentage > min) and (percentage < max)):
      output = True

    return output

  ###
  # @return Boolean, true or false
  def checkMacd(self, pricesDf, endRow, min = -0.2, max = 0.1):
    output = False

    MACD = self.getMacd(pricesDf)

    if ((MACD[endRow] > min) and (MACD[endRow] < max)):
      output = True

    return output

  ###
  def getMacd(self, pricesDf):

    ## 使用talib计算MACD的参数
    short_day = 12    # 短期EMA平滑天数
    long_day  = 26    # 长期EMA平滑天数
    macd_day  = 9    # DEA线平滑天数

    # talib计算MACD
    macd_tmp = talib.MACD(pricesDf['Close'], fastperiod = short_day, slowperiod = long_day, signalperiod = macd_day)
    DIF  = macd_tmp[0]
    DEA  = macd_tmp[1]
    MACD = macd_tmp[2]

    return MACD



