# ---------------------------------------------------------------------------------------------------------
# This script is developed as a sample for Geoprocess using GDAL library 
# #Essential library: GDAL, numpy
# #Author: Jiaqi Guo(Max)
# #Data of Last Modified: 2017-03-20
# ---------------------------------------------------------------------------------------------------------


from osgeo import gdal
import ogr, os, osr
from osgeo.gdalnumeric import *
from osgeo.gdalconst import *
import numpy as np


file1 = "/var/www/html/index/temp/soil_sand_clipped.tif"
file2 = "/var/www/html/index/temp/soil_clay_clipped.tif"

#Open dataset
bandNum = 1
dis1 = gdal.Open(file1)
dis2 = gdal.Open(file2)


band1 = dis1.GetRasterBand(bandNum)
band2 = dis2.GetRasterBand(bandNum)




#Read data into numpy array
data1 = BandReadAsArray(band1)
data2 = BandReadAsArray(band2)
x = data1.shape[0]
y = data1.shape[1]
print (x)
print (y)
data3 = np.empty((x,y)) # numpy.zeros initialize the data, slower

data3.fill(100)

print (data1.shape)
print (data2.shape)
print (data3.shape)
print (data1)
print (data2)
print (data3)


# Write to the out file
driver = gdal.GetDriverByName("GTiff")
disOut = driver.Create("/var/www/html/index/temp/AOI_mask.tiff", dis1.RasterXSize, dis1.RasterYSize, 1, band1.DataType)
CopyDatasetInfo = (dis1, disOut)
bandOut = disOut.GetRasterBand(1)
BandWriteArray(bandOut, data3)

#Close the datasets
band1 = None
band2 = None
dis1 = None
dis2 = None
bandOut= None
disOut = None


