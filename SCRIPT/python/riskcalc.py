# ---------------------------------------------------------------------------------------------------------
# This script is developed as a sample for Geoprocess using GDAL library 
# #Essential library: GDAL, numpy
# #Author: Jiaqi Guo(Max)
# #Data of Last Modified: 2017-03-20
# ---------------------------------------------------------------------------------------------------------
import sys, json
from osgeo import gdal
import ogr, os, osr
from osgeo.gdalnumeric import *
from osgeo.gdalconst import *
import numpy as np


# try:
#     data = json.loads(sys.argv[1])
# except:
#     print ("ERROR")
#     sys.exit(1)

# CropDensity = int(data)

file1 = "/var/www/html/index/temp/data-download.tif"

#Open dataset
bandNum = 1
dis1 = gdal.Open(file1)

band1 = dis1.GetRasterBand(bandNum)





#Read data into numpy array
data1 = BandReadAsArray(band1)
x = data1.shape[0]
y = data1.shape[1]
print (x)
print (y)
data2 = np.empty((x,y)) # numpy.zeros initialize the data, slower

data2.fill(10)

print (data1.shape)
print (data2.shape)
print (data1)
print (data2)

print (dis1.RasterXSize)

# Write to the out file
driver = gdal.GetDriverByName("GTiff")
disOut = driver.Create("/var/www/html/index/temp/CropDensity.tif", dis1.RasterXSize, dis1.RasterYSize, 1, band1.DataType)
disOut.SetGeoTransform(dis1.GetGeoTransform())
disOut.SetProjection(dis1.GetProjection())                #set up the cell size and projection
CopyDatasetInfo = (dis1, disOut)   
bandOut = disOut.GetRasterBand(1) 
BandWriteArray(bandOut, data2)

#Close the datasets
band1 = None
dis1 = None
bandOut= None
disOut = None


