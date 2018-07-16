"""
# get_hist_data and save JSON file
# python3 web/modules/custom/python/FlexJsonClass.py

"""

#

import urllib.request


# define a class
class FlexJsonClass:

  # 读取Json格式文件
  def readJsonDecode(self, urlPath):
    urlPath = ("http://localhost:8888/agu/web/views/json/debug-term-code-table?_format=json")
    with urllib.request.urlopen(urlPath) as url:
      termCodeData = json.loads(url.read().decode())
