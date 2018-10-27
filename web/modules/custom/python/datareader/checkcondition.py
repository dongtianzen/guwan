"""


python3 web/modules/custom/python/datareader/checkcondition.py

"""
import pandas as pd

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

  # @return Boolean, true or false
  def compareVolumeRatio(self, pricesDf, endRow, min = 0.97, max = 1.10):
    output = False

    ma5  = self.getAverageVolume(pricesDf, endRow, num = 5);
    ma10 = self.getAverageVolume(pricesDf, endRow, num = 10);

    percentage = ma5 / ma10

    if ((percentage > min) and (percentage < max)):
      output = True

    return output



