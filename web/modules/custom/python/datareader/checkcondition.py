"""


python3 web/modules/custom/python/datareader/checkcondition.py

"""

#%%
# define a class
class CheckCondition:

  #
  def getSumPrice(self, pricesDf, key, num = 5):
    output = 0

    maxNum = (key - num)
    while (key > maxNum):

      # @param 3 is Close price
      print(pricesDf.iat[key, 3])
      output += pricesDf.iat[key, 3]
      key = key - 1

    return output

  #
  def getAveragePrice(self, pricesDf, key, num = 5):
    output = 0

    sumPrice = self.getSumPrice(pricesDf, key, num)
    if (num > 0):
      output = sumPrice / num

    return output

  # compare ma5 ma10 on the min < (ma5/ma10) < max
  # @return Boolean, true or false
  def comparePriceRatio(self, pricesDf, key):
    priceMa5  = self.getAveragePrice(pricesDf, key, num = 5);
    print(priceMa5)

    # priceMa10 = self.getAveragePrice(pricesDf, key, num = 10);
    # print(priceMa5)

