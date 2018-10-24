"""

python3 web/modules/custom/python/datareader/runstockrank.py

"""

from getstockdata import GetPriceBasic

pricesDf = GetPriceBasic().getHistPrice()
print(pricesDf.info())
