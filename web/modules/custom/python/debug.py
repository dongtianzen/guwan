"""
# get_hist_data and save JSON file
python3 web/modules/custom/python/debug.py

"""

import pandas as pd

import tushare as ts


histData = ts.get_hist_data(code = 'hs300', start = '2017-07-20')

print('sz')
print(type(histData))
print(histData)

exit()

#
histData = ts.get_hist_data(code = 'sh', start = '2018-07-03')
print('sh')
print(histData)

#
histData = ts.get_hist_data(code = '399300', ktype = 'D', start = '2018-07-10', end = '2018-07-27')
print(histData)

#
cc = ts.get_k_data('399300', index=True, start='2018-07-10', end='2018-07-17')
print(cc )
exit()
