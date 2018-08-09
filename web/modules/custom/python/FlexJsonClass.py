"""
# get_hist_data and save JSON file
# python3 web/modules/custom/python/FlexJsonClass.py

"""
import json
import urllib.request

from pathlib import Path

#%%
# define a class
class FlexJsonBasic:

  # @return output type is list
  def readJsonDecode(self, urlPath):
    with urllib.request.urlopen(urlPath) as url:
      termCodeData = json.loads(url.read().decode())

    return termCodeData

  #
  def convertViewsJsonToTermCodeList(self, pageNum = 1):
    urlPath = self.getTermCodeListFromViewsJsonUrlPath(pageNum)

    termCodeData = self.readJsonDecode(urlPath)

    fullCodeList = []
    for termCodeRow in termCodeData:
      fullCodeList.append(termCodeRow['name'][0]['value'])

    return fullCodeList


  #
  def getTermCodeListFromViewsJsonUrlPath(self, pageNum = 1):
    urlPath = ("http://localhost:8888/agu/web/views/json/debug-term-code-table?page=" + str(pageNum) + "&_format=json")
    urlPath = ("54.183.85.173/agu/web/views/json/debug-term-code-table?page=" + str(pageNum) + "&_format=json")

    return urlPath


  # use pandas.DataFrame.to_json 生成Json格式的文件
  # @param jsonData is require as <class 'pandas.core.frame.DataFrame'>
  # orient = 'columns' or orient = 'index' is 不同转换数组List排序方法
  def generateJsonFromData(self, filePath, jsonData):
    if jsonData is None:
      return
    else:
      jsonData.to_json(filePath, orient='index')
      print ('JSON generate success')

    return

  #
  def getGenerateJsonFilePath(self, fileName):
    # 运行文件从server or local command line, 在当前Repository下
    pathDir  = 'web/sites/default/files/json/tushare/'
    pathDirObject = Path(pathDir)

    if pathDirObject.is_dir():
      print('path is run from command line')
      filePath = pathDir + fileName
      return filePath

    # 运行文件从Drupal file or Devel or PHP , 要使用当前系统下的完全路径
    pathDir = '/Applications/MAMP/htdocs/agu/web/sites/default/files/json/tushare/'
    pathDirObject = Path(pathDir)

    if pathDirObject.is_dir():
      print('path is run from PHP')
      filePath = pathDir + fileName
      return filePath

    # 运行文件从Server Cron command，所以要服务器上的绝对路径
    pathDir = '/var/www/html/agu/web/sites/default/files/json/tushare/'
    pathDirObject = Path(pathDir)

    if pathDirObject.is_dir():
      print('is exist from Ubuntu Server')
      filePath = pathDir + fileName
      return filePath

    return

  #%%
