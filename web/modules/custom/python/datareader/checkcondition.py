"""


python3 web/modules/custom/python/datareader/checkcondition.py

"""

#%%
# define a class
class checkCondition:

  # compare ma5 ma10 on the min < (ma5/ma10) < max
  # @return Boolean, true or false
  def comparePriceRatio(self):
    $price_ma5 = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValue($entity, 'field_day_ma5');

    $price_ma10 = \Drupal::getContainer()
      ->get('flexinfo.field.service')
      ->getFieldFirstValue($entity, 'field_day_ma10');

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
