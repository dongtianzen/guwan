"""


python3 web/modules/custom/python/datareader/checkcondition.py

"""

#%%
# define a class
class checkCondition:

  #
  def getAveragePrice(self, pricesDf, key, num = 5):


  # compare ma5 ma10 on the min < (ma5/ma10) < max
  # @return Boolean, true or false
  def comparePriceRatio(self):
    priceMa5  = self.getAveragePrice(pricesDf, key, num = 5);

    priceMa10 = self.getAveragePrice(pricesDf, key, num = 10);

    if ($price_ma10) {
      $price_ratio = \Drupal::getContainer()
        ->get('flexinfo.calc.service')
        ->getPercentage($price_ma5, $price_ma10);

      if ($min < $price_ratio && $price_ratio < $max) {
        $output = TRUE;
      }
      else {
      }
    }
    start = datetime.datetime(2018, 10, 18)
