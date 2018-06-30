# get_hist_data and save JSON file

# cd web/modules/custom/python
# python3 get_hist_data_day.py

import tushare as ts

# alternative ts.get_k_data
# cc = ts.get_k_data('399300', index=True,start='2016-10-01', end='2017-01-31')
# print (cc)

# get Day data
code = '000875'
df = ts.get_hist_data(code, ktype='D')

# get 60 m data
# df = ts.get_hist_data(code, ktype='60')

# 生成Json格式的文件
df.to_json('000875_60m.json', orient='index')



