<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

// Adopted from https://github.com/mpetroff/kindle-weather-display
// Add following cronjob to crontab
// 0,15,30,45 * * * * /usr/bin/php /path/to/kindle-weather-display/weather-script.php >/dev/null 2>&1
// Server updates 5 mintues ahead of kindle
// For Debian, packages required are "librsvg2-bin", "pngcrush" and "fonts-dejavu-extra"

//Set working directory
chdir ("/path/to/kindle-weather-display");

//Weather Icon relationships
$IconArray = array(
	'chanceflurries' => 'blizzard', 'chancerain' => 'ra', 'chancesleet' => 'rasn', 'chancesnow' => 'sn', 'chancetstorms' => 'tsra',
	'clear' => 'skc', 'cloudy' => 'ovc', 'flurries' => 'blizzard', 'fog' => 'fg', 'hazy' => 'mist',
	'mostlycloudy' => 'bkn', 'mostlysunny' => 'sct', 'partlycloudy' => 'bkn', 'partlysunny' => 'sct', 'rain' => 'ra',
	'sleet' => 'rasn', 'snow' => 'sn', 'sunny' => 'skc', 'tstorms' => 'tsra',
	'nt_chanceflurries' => 'blizzard', 'nt_chancerain' => 'ra', 'nt_chancesleet' => 'rasn', 'nt_chancesnow' => 'sn', 'nt_chancetstorms' => 'tsra',
	'nt_clear' => 'skc', 'nt_cloudy' => 'ovc', 'nt_flurries' => 'blizzard', 'nt_fog' => 'fg', 'nt_hazy' => 'mist',
	'nt_mostlycloudy' => 'bkn', 'nt_mostlysunny' => 'sct', 'nt_partlycloudy' => 'bkn', 'nt_partlysunny' => 'sct', 'nt_rain' => 'ra',
	'nt_sleet' => 'rasn', 'nt_snow' => 'sn', 'nt_sunny' => 'skc', 'nt_tstorms' => 'tsra'
);

$APIKey = "0000000000000000";
$Coordinates = "-37.881294,145.049675"; //Latitude, comma, longitude. No space!
//Weather Underground API entrance
$BaseURL = "http://api.wunderground.com/api/$APIKey/VAR_FUNCTION/q/$Coordinates.json";
//Functions required by weather display
$FunctionArray = array('geolookup', 'hourly', 'forecast10day');

//Get data from API
$DataArray = array();
$ValidFlag = true;
foreach ($FunctionArray as $function) {
	$currentContent = file_get_contents(str_replace("VAR_FUNCTION", $function, $BaseURL));
	if ($currentContent) {
		$decodedData = json_decode($currentContent);
		if (is_null($decodedData)) $ValidFlag = false;
		else $DataArray[$function] = $decodedData;
	}
}

//Ocean of variables
$WeatherData = array();
$WeatherData['VAR_LOCATION']			= $DataArray['geolookup']->{'location'}->{'city'};
$WeatherData['VAR_UPDATE_HOUR']			= date('g');
$WeatherData['VAR_UPDATE_MINUTE']		= date('i');
$WeatherData['VAR_UPDATE_AMPM']			= date('A');
$VAR_TODAY = $DataArray['forecast10day']->{'forecast'}->{'simpleforecast'}->{'forecastday'}[0];
$WeatherData['VAR_TODAY_ICON']			= $IconArray[$VAR_TODAY->{'icon'}];
$WeatherData['VAR_TODAY_HIGH']			= $VAR_TODAY->{'high'}->{'celsius'};
$WeatherData['VAR_TODAY_LOW']			= $VAR_TODAY->{'low'}->{'celsius'};
$VAR_TOM = $DataArray['forecast10day']->{'forecast'}->{'simpleforecast'}->{'forecastday'}[1];
$WeatherData['VAR_DAILY_TOM_ICON']		= $IconArray[$VAR_TOM->{'icon'}];
$WeatherData['VAR_DAILY_TOM_DAY']		= $VAR_TOM->{'date'}->{'weekday'};
$WeatherData['VAR_DAILY_TOM_HIGH']		= $VAR_TOM->{'high'}->{'celsius'};
$WeatherData['VAR_DAILY_TOM_LOW']		= $VAR_TOM->{'low'}->{'celsius'};
$WeatherData['VAR_DAILY_TOM_COND']		= $VAR_TOM->{'conditions'};
$WeatherData['VAR_DAILY_TOM_WIND_DIR']	= $VAR_TOM->{'avewind'}->{'dir'};
$WeatherData['VAR_DAILY_TOM_WIND_DEG']	= $VAR_TOM->{'avewind'}->{'degrees'};
$WeatherData['VAR_DAILY_TOM_WIND_LOW']	= $VAR_TOM->{'avewind'}->{'kph'};
$WeatherData['VAR_DAILY_TOM_WIND_HIGH']	= $VAR_TOM->{'maxwind'}->{'kph'};
$VAR_DAILY_1 = $DataArray['forecast10day']->{'forecast'}->{'simpleforecast'}->{'forecastday'}[2];
$WeatherData['VAR_DAILY_1_ICON']		= $IconArray[$VAR_DAILY_1->{'icon'}];
$WeatherData['VAR_DAILY_1_DAY']			= $VAR_DAILY_1->{'date'}->{'weekday'};
$WeatherData['VAR_DAILY_1_HIGH']		= $VAR_DAILY_1->{'high'}->{'celsius'};
$WeatherData['VAR_DAILY_1_LOW']			= $VAR_DAILY_1->{'low'}->{'celsius'};
$VAR_DAILY_2 = $DataArray['forecast10day']->{'forecast'}->{'simpleforecast'}->{'forecastday'}[3];
$WeatherData['VAR_DAILY_2_ICON']		= $IconArray[$VAR_DAILY_2->{'icon'}];
$WeatherData['VAR_DAILY_2_DAY']			= $VAR_DAILY_2->{'date'}->{'weekday'};
$WeatherData['VAR_DAILY_2_HIGH']		= $VAR_DAILY_2->{'high'}->{'celsius'};
$WeatherData['VAR_DAILY_2_LOW']			= $VAR_DAILY_2->{'low'}->{'celsius'};
$VAR_DAILY_3 = $DataArray['forecast10day']->{'forecast'}->{'simpleforecast'}->{'forecastday'}[4];
$WeatherData['VAR_DAILY_3_ICON']		= $IconArray[$VAR_DAILY_3->{'icon'}];
$WeatherData['VAR_DAILY_3_DAY']			= $VAR_DAILY_3->{'date'}->{'weekday'};
$WeatherData['VAR_DAILY_3_HIGH']		= $VAR_DAILY_3->{'high'}->{'celsius'};
$WeatherData['VAR_DAILY_3_LOW']			= $VAR_DAILY_3->{'low'}->{'celsius'};
$VAR_DAILY_4 = $DataArray['forecast10day']->{'forecast'}->{'simpleforecast'}->{'forecastday'}[5];
$WeatherData['VAR_DAILY_4_ICON']		= $IconArray[$VAR_DAILY_4->{'icon'}];
$WeatherData['VAR_DAILY_4_DAY']			= $VAR_DAILY_4->{'date'}->{'weekday'};
$WeatherData['VAR_DAILY_4_HIGH']		= $VAR_DAILY_4->{'high'}->{'celsius'};
$WeatherData['VAR_DAILY_4_LOW']			= $VAR_DAILY_4->{'low'}->{'celsius'};
$VAR_DAILY_5 = $DataArray['forecast10day']->{'forecast'}->{'simpleforecast'}->{'forecastday'}[6];
$WeatherData['VAR_DAILY_5_ICON']		= $IconArray[$VAR_DAILY_5->{'icon'}];
$WeatherData['VAR_DAILY_5_DAY']			= $VAR_DAILY_5->{'date'}->{'weekday'};
$WeatherData['VAR_DAILY_5_HIGH']		= $VAR_DAILY_5->{'high'}->{'celsius'};
$WeatherData['VAR_DAILY_5_LOW']			= $VAR_DAILY_5->{'low'}->{'celsius'};
$VAR_HOURLY_1 = $DataArray['hourly']->{'hourly_forecast'}[0];
$WeatherData['VAR_HOURLY_1_ICON']		= $IconArray[$VAR_HOURLY_1->{'icon'}];
$WeatherData['VAR_HOURLY_1_TIME']		= date("gA", strtotime($VAR_HOURLY_1->{'FCTTIME'}->{'civil'}));;
$WeatherData['VAR_HOURLY_1_TEMP']		= $VAR_HOURLY_1->{'temp'}->{'metric'};
$VAR_HOURLY_2 = $DataArray['hourly']->{'hourly_forecast'}[1];
$WeatherData['VAR_HOURLY_2_ICON']		= $IconArray[$VAR_HOURLY_2->{'icon'}];
$WeatherData['VAR_HOURLY_2_TIME']		= date("gA", strtotime($VAR_HOURLY_2->{'FCTTIME'}->{'civil'}));;
$WeatherData['VAR_HOURLY_2_TEMP']		= $VAR_HOURLY_2->{'temp'}->{'metric'};
$VAR_HOURLY_3 = $DataArray['hourly']->{'hourly_forecast'}[2];
$WeatherData['VAR_HOURLY_3_ICON']		= $IconArray[$VAR_HOURLY_3->{'icon'}];
$WeatherData['VAR_HOURLY_3_TIME']		= date("gA", strtotime($VAR_HOURLY_3->{'FCTTIME'}->{'civil'}));;
$WeatherData['VAR_HOURLY_3_TEMP']		= $VAR_HOURLY_3->{'temp'}->{'metric'};
$VAR_HOURLY_4 = $DataArray['hourly']->{'hourly_forecast'}[3];
$WeatherData['VAR_HOURLY_4_ICON']		= $IconArray[$VAR_HOURLY_4->{'icon'}];
$WeatherData['VAR_HOURLY_4_TIME']		= date("gA", strtotime($VAR_HOURLY_4->{'FCTTIME'}->{'civil'}));;
$WeatherData['VAR_HOURLY_4_TEMP']		= $VAR_HOURLY_4->{'temp'}->{'metric'};
$VAR_HOURLY_5 = $DataArray['hourly']->{'hourly_forecast'}[4];
$WeatherData['VAR_HOURLY_5_ICON']		= $IconArray[$VAR_HOURLY_5->{'icon'}];
$WeatherData['VAR_HOURLY_5_TIME']		= date("gA", strtotime($VAR_HOURLY_5->{'FCTTIME'}->{'civil'}));;
$WeatherData['VAR_HOURLY_5_TEMP']		= $VAR_HOURLY_5->{'temp'}->{'metric'};
$WeatherData['VAR_VERSION']		= "1.0";

$svgTemplate = file_get_contents("weather-script-preprocess.svg");
foreach ($WeatherData as $varKey => $varValue) $svgTemplate = str_replace($varKey, $varValue, $svgTemplate);
$saveResult = file_put_contents('weather-script-output.svg', $svgTemplate);
if ($saveResult == FALSE) $ValidFlag = false;
if ($ValidFlag) {
	exec("rsvg-convert --background-color=white -o weather-script-output.png weather-script-output.svg");
	exec("pngcrush -c 0 -ow weather-script-output.png");
	exec("cp -f weather-script-output.png /var/www/_default/weather-update.png");
} else {
	die(json_encode(array("error" => "Retrieved data is invalid!")));
}
?>