# get_hist_data and save JSON file

import tushare as ts

from pathlib import Path


# define a class
class GetFeatureClass:

  # 生成Json格式的文件
  def generateHistoryDataToJson(self, allHistoryData):

    filePath = self.getJsonFilePath()

    allHistoryData.to_json(filePath, orient='index')

    print ('JSON generate success')
    return


  # get Day data
  def getHistoryData(self, code, startDay):
    histData = ts.get_hist_data(code, ktype = 'D', start = startDay)
    return histData


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
