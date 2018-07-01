# get_hist_data and save JSON file

# define a class
# class getFeature:

# 生成Json格式的文件
def generateHistoryDataToJson(allHistoryData):
  allHistoryData.to_json('historyDataDat.json', orient='index')

  print ('JSON generate success')
  return

# get Day data
def getHistoryData(code):
  histData = ts.get_hist_data(code, ktype = 'D', start = yesterday)
  return histData
