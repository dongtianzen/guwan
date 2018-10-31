"""

"""

import datetime

end   = datetime.date.today()

# start = datetime.datetime(2018, 10, 18) # or start = '1/1/2018'

# start = end - datetime.timedelta(days = 200)  # 200天
start = end + datetime.timedelta(205*365/12)  # 6个月

print(start)
print(end)
