# ---------------------------------------------------------------------------------------------------------
# This script is developed AAFC-CanSIS to abstract weather data from Environment Canada with given time interval.
# #Essential library: URLLIB, NUMPY, TWISTED, BEAUTIFULSOUP
# #Author: Jiaqi Guo(Max)
# #Data of Last Modified: 2017-02-28
# ---------------------------------------------------------------------------------------------------------


#import system and library
import os
import pprint
import sys
import urllib 
import csv
import re
import twisted 
import numpy as np
from twisted.internet import task
from twisted.internet import reactor 
from datetime import datetime 
from bs4 import BeautifulSoup  
from pprint import pprint




#input the weather sataion information

stationCode = "yyc"

# Scraping the web by parsing the html
def scraping(): 
	#print (datetime.now())
	#print ("------------------------------------------------------------")
	#spefify url, query the weather info for the past 24 hours and return
	quote_page = 'https://weather.gc.ca/past_conditions/index_e.html?station=' + stationCode
	page = urllib.request.urlopen(quote_page)

	#parse the html using beautiful soup
	soup = BeautifulSoup(page, 'html.parser')

	#Using prettify to have a better understanding of the page info.
	#print (soup.prettify())

	location = soup.find('main').find('h1', id='wb-cont')
	location = location.text.strip()
	location = str(location)


	#grabbing the information from the page
	date_box = soup.find('th', attrs={'class': 'wxo-th-bkg table-date'})
	date = date_box.text.strip()
	#print ("This is the weather data on: " + date)
	#print ("Location: " + location)
	#print ("------------------------------------------------------------")
	

	# Extracting the NRT weather information from the past 24 hours table
	
	table = soup.find('main').find('div', id="table-container").find('table', id="past24Table")


	#--------------------------Try to write table data into a list------------------------------------
	
	def makelist(table):
		result = []
		allrow = table.findAll('tr')
		for row in allrow:
			result.append([])
			allcols = row.findAll('td')
			for col in allcols:
				thestrings = [u"".join(s).strip() for s in col.findAll(text=True)] # generate unicode data
				thetext = ''.join(thestrings)
				result[-1].append(thetext)

		return result


	#----------------------------------------------------------------------------------------

	# Getting the data value
	weatherdata = makelist(table)
	myarray = np.asarray(weatherdata)

	TimeVal = myarray[3][0]
	TemperatureVal = myarray[3][list_Temperature]
	TemperatureVal = re.sub(' ','',TemperatureVal)
	WindspeedVal = myarray[3][list_Windspeed]
	HumidityVal = myarray[3][list_humidity]
	PressureVal = myarray[3][list_pressure] 


	return HumidityVal

if __name__ == '__main__':
	scraping()


