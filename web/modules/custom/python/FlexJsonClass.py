"""
# get_hist_data and save JSON file
# python3 web/modules/custom/python/FlexJsonClass.py

"""
import json
import urllib.request

# define a class
class FlexJsonBasic:

  # @return output type is list
  def readJsonDecode(self, urlPath):
    with urllib.request.urlopen(urlPath) as url:
      termCodeData = json.loads(url.read().decode())

    return termCodeData


  def convertViewsJsonToTermCodeList(self):
    urlPath = ("http://localhost:8888/agu/web/views/json/debug-term-code-table?_format=json")

    termCodeData = self.readJsonDecode(urlPath)

    fullCodeList = []
    for termCodeRow in termCodeData:
      fullCodeList.append(termCodeRow['name'][0]['value'])

    return fullCodeList
