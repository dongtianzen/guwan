"""


python3 web/modules/custom/python/datareader/checkcondition.py

"""
import pandas as pd

#%%
# define a class
class CheckCondition:

  #
  def getSumPrice(self, pricesDf, key, num = 5):
    output = 0

    return output


  #
  def getAveragePrice(self, pricesDf, endRow, num = 5):
    startRow = endRow - num

    # closeDaysSeries = pricesDf['Close'][-6:-1]
    closeDaysSeries = pricesDf['Close'][startRow:endRow]

    output = pd.Series(closeDaysSeries).mean()

    return output


  # compare ma5 ma10 on the min < (ma5/ma10) < max
  # @return Boolean, true or false
  def comparePriceRatio(self, pricesDf, endRow, min = 0.90, max = 1.10):
    output = False

    priceMa5  = self.getAveragePrice(pricesDf, endRow, num = 5);
    priceMa10 = self.getAveragePrice(pricesDf, endRow, num = 10);

    percentage = priceMa5 / priceMa10
    print(percentage)

    if ((percentage > min) and (percentage < max)):
      output = True

    return output



