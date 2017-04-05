# ---------------------------------------------------------------------------------------------------------
# This is Canola Plant Diesease risk prediction , using the RISK TABLE by CCC (Canada Canola Council)
# Needed to use EnvCan file to scraping NRT weather data from Environment Canada  
# Author: Jiaqi Guo(Max)
# Data of Last Modified: 2017-03-07
# ---------------------------------------------------------------------------------------------------------


import EnvCan
import sys, json

try:
    data = json.loads(sys.argv[1])
except:
    print ("ERROR")
    sys.exit(1)

numYears = int(data[0])
DiseaseIncid = int(data[1])
CropDst = data[2]
Rain = int(data[3])
stationCode = data[4]
RegionRisk = data[5]




if __name__ == '__main__':
	Humidity = EnvCan.scraping(stationCode)
	Humidity = int(Humidity)

def riskAnalysis(numYears,DiseaseIncid,CropDst,Rain,Humidity,RegionRisk):
	risk = []

	if numYears > 6:
		risk.append(0)
	elif numYears <= 6 and numYears >= 3:
		risk.append(5) 
	else:
		risk.append(10)

	if DiseaseIncid >= 31:
		risk.append(15)
	elif DiseaseIncid <= 30 and DiseaseIncid >= 11:
		risk.append(10)
	else:
		risk.append(5)
		
	if CropDst == 'low':
		risk.append(0)
	elif CropDst =='normal':
		risk.append(5)
	else: 
		risk.append(10)

	if Rain > 30:
		risk.append(10)
	elif Rain <= 30 and Rain >= 10:
		risk.append(5)
	else:
		risk.append(0)

	if Humidity >= 67:
		risk.append(15)
	elif Humidity < 67 and Humidity >= 34:
		risk.append(10)
	else:
		risk.append(5)

	if RegionRisk == 'none':
		risk.append(0)
	elif RegionRisk =='low':
		risk.append(10)
	else: 
		risk.append(15)

	return (sum(risk))

AOIrisk = riskAnalysis(numYears,DiseaseIncid,CropDst,Rain,Humidity,RegionRisk)

print (str(AOIrisk))

