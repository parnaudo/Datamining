<?php 

class Zephyr_Helper_Address_City extends Zephyr_Helper_Address_Helper
{
	protected $_filterMatchIndex = 2;
	protected $_extractMatchIndex = 2;
	
	protected $_cities = array('New York', 'Los Angeles', 'Chicago', 'Houston', 'Philadelphia', 'Phoenix', 'San Antonio', 'San Diego', 'Dallas', 'San Jose', 'Jacksonville', 'Indianapolis', 'San Francisco', 'Austin', 'Columbus', 'Fort Worth', 'Charlotte', 'Detroit', 'El Paso', 'Memphis', 'Baltimore', 'Boston', 'Seattle', 'Washington', 'Nashville', 'Denver', 'Louisville', 'Milwaukee', 'Portland', 'Las Vegas', 'Oklahoma US', 'Albuquerque', 'Tucson', 'Fresno', 'Sacramento', 'Long Beach', 'Kansas US', 'Mesa', 'Virginia Beach', 'Atlanta', 'Colorado Springs', 'Omaha', 'Raleigh', 'Miami', 'Cleveland', 'Tulsa', 'Oakland', 'Minneapolis', 'Wichita', 'Arlington', 'Bakersfield', 'New Orleans', 'Honolulu', 'Anaheim', 'Tampa', 'Aurora', 'Santa Ana', 'Saint Louis', 'Pittsburgh', 'Corpus Christi', 'Riverside', 'Cincinnati', 'Lexington', 'Anchorage', 'Stockton', 'Toledo', 'Saint Paul', 'Newark', 'Greensboro', 'Buffalo', 'Plano', 'Lincoln', 'Henderson', 'Fort Wayne', 'Jersey US', 'Saint Petersburg', 'Chula Vista', 'Norfolk', 'Orlando', 'Chandler', 'Laredo', 'Madison', 'Winston-Salem', 'Lubbock', 'Baton Rouge', 'Durham', 'Garland', 'Glendale', 'Reno', 'Hialeah', 'Chesapeake', 'Scottsdale', 'North Las Vegas', 'Irving', 'Fremont', 'Irvine', 'Birmingham', 'Rochester', 'San Bernardino', 'Spokane', 'Gilbert', 'Arlington', 'Montgomery', 'Boise', 'Richmond', 'Des Moines', 'Modesto', 'Fayetteville', 'Shreveport', 'Akron', 'Tacoma', 'Aurora', 'Oxnard', 'Fontana', 'Yonkers', 'Augusta', 'Mobile', 'Little Rock', 'Moreno Valley', 'Glendale', 'Amarillo', 'Huntington Beach', 'Columbus', 'Grand Rapids', 'Salt Lake US', 'Tallahassee', 'Worcester', 'Newport News', 'Huntsville', 'Knoxville', 'Providence', 'Santa Clarita', 'Grand Prairie', 'Brownsville', 'Jackson', 'Overland Park', 'Garden Grove', 'Santa Rosa', 'Chattanooga', 'Oceanside', 'Fort Lauderdale', 'Rancho Cucamonga', 'Port Saint Lucie', 'Ontario', 'Vancouver', 'Tempe', 'Springfield', 'Lancaster', 'Eugene', 'Pembroke Pines', 'Salem', 'Cape Coral', 'Peoria', 'Sioux Falls', 'Springfield', 'Elk Grove', 'Rockford', 'Palmdale', 'Corona', 'Salinas', 'Pomona', 'Pasadena', 'Joliet', 'Paterson', 'Kansas US', 'Torrance', 'Syracuse', 'Bridgeport', 'Hayward', 'Fort Collins', 'Escondido', 'Lakewood', 'Naperville', 'Dayton', 'Hollywood', 'Sunnyvale', 'Alexandria', 'Mesquite', 'Hampton', 'Pasadena', 'Orange', 'Savannah', 'Cary', 'Fullerton', 'Warren', 'Clarksville', 'McKinney', 'McAllen', 'New Haven', 'Sterling Heights', 'West Valley US', 'Columbia', 'Killeen', 'Topeka', 'Thousand Oaks', 'Cedar Rapids', 'Olathe', 'Elizabeth', 'Waco', 'Hartford', 'Visalia', 'Gainesville', 'Simi Valley', 'Stamford', 'Bellevue', 'Concord', 'Miramar', 'Coral Springs', 'Lafayette', 'Charleston', 'Carrollton', 'Roseville', 'Thornton', 'Beaumont', 'Allentown', 'Surprise', 'Evansville', 'Abilene', 'Frisco', 'Independence', 'Santa Clara', 'Springfield', 'Vallejo', 'Victorville', 'Athens', 'Peoria', 'Lansing', 'Ann Arbor', 'El Monte', 'Denton', 'Berkeley', 'Provo', 'Downey', 'Midland', 'Norman', 'Waterbury', 'Costa Mesa', 'Inglewood', 'Manchester', 'Murfreesboro', 'Columbia', 'Elgin', 'Clearwater', 'Miami Gardens', 'Rochester', 'Pueblo', 'Lowell', 'Wilmington', 'Arvada', 'San Buenaventura', 'Ventura', 'Westminster', 'West Covina', 'Gresham', 'Fargo', 'Norwalk', 'Carlsbad', 'Fairfield', 'Cambridge', 'Wichita Falls', 'High Point', 'Billings', 'Green Bay', 'West Jordan', 'Richmond', 'Murrieta', 'Burbank', 'Palm Bay', 'Everett', 'Flint', 'Antioch', 'Erie', 'South Bend', 'Daly US', 'Centennial', 'Temecula');

	protected function _getRegex()
	{
		$citiesRegex = implode('|', $this->_cities);
		$regex = array();
		$regex[] = "~(.+,|^)\s*($citiesRegex)$~i";
		$regex[] = '~()(([A-Z][a-z`\']+\.?[-\s]?){1,4})$~';
		$regex[] = '~()(([A-Z][a-z`\']+\.?[-\s]?){1,3})$~';
		$regex[] = '~()(([A-Z][a-z`\']+\.?[-\s]?){1,2})$~';
		$regex[] = '~()(([A-Z][a-z`\']+\.?[-\s]?){1,1})$~';
		return $regex;
	}
	
	protected function _getReplacement()
	{
		return '$1';
	}
	
	protected function _validate($text)
	{
		$citiesDb = new Zephyr_Tool_CreateCitiesDb();
		return $citiesDb->isCity($text);
	}
}