"""
# get_hist_data and save JSON file
# python3 web/modules/custom/python/FlexJsonClass.py

"""
import json
import urllib.request

from pathlib import Path


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


  # 生成Json格式的文件
  # @param jsonData is <class 'pandas.core.frame.DataFrame'>
  def generateJsonFromData(self, filePath, jsonData):
    jsonData.to_json(filePath, orient='index')

    print ('JSON generate success')
    return


  def getJsonFilePath(self):
    # 运行文件从command line
    filePath = 'web/sites/default/files/json/tushare/historyDataDat.json';
    my_file = Path(filePath)
    if my_file.is_file():
      print('is exist from command line')
      return filePath

    # 运行文件从Drupal file or Devel or PHP , 要使用当前系统下的完全路径
    filePath = '/Applications/MAMP/htdocs/agu/web/sites/default/files/json/tushare/historyDataDat.json';
    my_file = Path(filePath)
    if my_file.is_file():
      print('is exist from PHP')
      return filePath

    return
