#------------------------------------------------------
# This code using GDAL library to get band information of 
# PEI.tif; This code is possbile to migrate to other bigger applications
# Reference: GDAL/OGR cookbook, OGR band-class API
#-------------------------------------------------------
from osgeo import gdal
import sys
gdal.UseExceptions()

#printting out following message when error occurs 
def Usage():
    print("""
    $ getrasterband.py [ band_num ] raster
    """)
    sys.exit(1)



# Band number and input file specified below
band_num = 1;
input_file = "../../temp/data.tif";

# Using GDAL bulit-in function to open file and get band information, exit when error occur 
src_ds = gdal.Open( input_file )
if src_ds is None:
    print ('Unable to open %s' % input_file)
    sys.exit(1)

try:
    srcband = src_ds.GetRasterBand(band_num)
except RuntimeError as e:
    print ('No band %i found' % band_num)
    print (e)
    sys.exit(1)

# Getting all the band infomations
print ("[ NO DATA VALUE ] = ", srcband.GetNoDataValue())
print ("[ MIN ] = ", srcband.GetMinimum())
print ("[ MAX ] = ", srcband.GetMaximum())
print ("[ SCALE ] = ", srcband.GetScale())
print ("[ UNIT TYPE ] = ", srcband.GetUnitType())
ctable = srcband.GetColorTable()

# some exceptions 

if ctable is None:
    print ('No ColorTable found')
    sys.exit(1)

print ("[ COLOR TABLE COUNT ] = ", ctable.GetCount())
for i in range( 0, ctable.GetCount() ):
    entry = ctable.GetColorEntry( i )
    if not entry:
        continue
    print ("[ COLOR ENTRY RGB ] = ", ctable.GetColorEntryAsRGB( i, entry ))


# throw errors when input parameters are invalid
if __name__ == '__main__':

    if len( sys.argv ) < 3:
        print ("""
        [ ERROR ] you must supply at least two arguments:
        1) the band number to retrieve and 2) input raster
        """)
        Usage()

    main( int(sys.argv[1]), sys.argv[2] )