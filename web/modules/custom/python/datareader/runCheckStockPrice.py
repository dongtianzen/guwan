"""

python3 web/modules/custom/python/datareader/runCheckStockPrice.py

"""

from GetHistData import GetPriceBasicClass

pricesDf = GetPriceBasicClass().getHistData()
print(pricesDf.info())
