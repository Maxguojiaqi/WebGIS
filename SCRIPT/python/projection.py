# import sys, json
# from osgeo import gdal
# import ogr, os, osr
# from osgeo.gdalnumeric import *
# from osgeo.gdalconst import *
# import numpy as np





# file1 = "/var/www/html/index/temp/riskmap1.tif"
# file2 = "/var/www/html/index/temp/cropping_history.tif"
# #Open dataset
# bandNum = 1
# disIn1 = gdal.Open(file1)
# disIn2 = gdal.Open(file2)
# band = disIn1.GetRasterBand(bandNum)
# data = BandReadAsArray(band)

# print (data)

# driver = gdal.GetDriverByName("GTiff")
# disOut = driver.Create("/var/www/html/index/temp/resample_risk.tif", disIn1.RasterXSize, disIn1.RasterYSize, 1, band.DataType)
# disOut.SetGeoTransform(disIn2.GetGeoTransform())
# disOut.SetProjection(disIn2.GetProjection())                #set up the cell size and projection
# CopyDatasetInfo = (disIn1, disOut)   
# bandOut = disOut.GetRasterBand(1) 
# BandWriteArray(bandOut, data)


# disIn1 = None
# disIn2 = None
# and1 = None
# bandOut= None
# disOut = None


#!/usr/bin/env python

from osgeo import gdal, gdalconst

# Source
src_filename = '/var/www/html/index/temp/riskmap1.tif'
src = gdal.Open(src_filename, gdalconst.GA_ReadOnly)
src_proj = src.GetProjection()
src_geotrans = src.GetGeoTransform()

# We want a section of source that matches this:
match_filename = '/var/www/html/index/temp/cropping_history.tif'
match_ds = gdal.Open(match_filename, gdalconst.GA_ReadOnly)
match_proj = match_ds.GetProjection()
match_geotrans = match_ds.GetGeoTransform()
wide = match_ds.RasterXSize
high = match_ds.RasterYSize

# Output / destination
dst_filename = '/var/www/html/index/temp/resample_risk.tif'
dst = gdal.GetDriverByName('GTiff').Create(dst_filename, wide, high, 1, gdalconst.GDT_Float32)
dst.SetGeoTransform( match_geotrans )
dst.SetProjection( match_proj)

# Do the work
gdal.ReprojectImage(src, dst, src_proj, match_proj, gdalconst.GRA_Bilinear)

del dst # Flush