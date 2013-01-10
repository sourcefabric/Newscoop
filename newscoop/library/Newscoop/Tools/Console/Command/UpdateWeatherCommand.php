<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Newscoop\Entity\WeatherStat;

ini_set('memory_limit','128M');

/**
 * Update weather command
 */
class UpdateWeatherCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('weather:update')
            ->setDescription('Updates weather information.')
            ->setHelp('Updates weather information.');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = new \Zend_Config_Ini( APPLICATION_PATH . '/configs/meteonews.ini', APPLICATION_ENV);

        $geonamesLists = array(
            'main_regions', 
            'important_regions', 
            'important_winter_regions', 
            'teaser_slopes'
        );

        $mexsLists = array(
            'important_winter_slopes',
        );

        // get data for geonames ids
        foreach ($geonamesLists as $list) {
            foreach ($config->$list as $location) {
                $locationType = 'geonames';
                $data = $this->getApiData('forecasts',$locationType,$location->id);
                $this->saveForecastData($data,
                    $location->id,
                    $location->name,
                    $locationType,
                    $list,
                    $location->region
                );
            }
        }
        
        // get data for mexs ids
        foreach ($mexsLists as $list) {
            foreach ($config->$list as $location) {
                $locationType = 'mexs';

                // get forecast data
                $data = $this->getApiData('forecasts',$locationType,$location->id);
                $this->saveForecastData($data,
                    $location->id,
                    $location->name,
                    'mexs',
                    $list,
                    $location->region
                );

                // get wintersports data
                $data = $this->getApiData('wintersports',$locationType,$location->id);
                $this->saveWintersportsData($data,
                    $location->id,
                    $location->name,
                    $locationType,
                    $list,
                    $location->region
                );
            }
        }
        
        // get data for all slopes lists
        foreach ($config->main_regions as $location) {
            $locationType = 'geonames';
            $slopeData = $this->getApiData('wintersports',$locationType,$location->id);
            $this->saveAllWintersportsData($slopeData,
                'mexs',
                'all_slopes',
                $location->name
            );

            // now load forecast data
            foreach ($slopeData->content as $regions) {
                foreach ($regions as $key => $record) {
                    $locationId = $record["mexs_id"];
                    $locationName = $record["name"];
                        
                    $data = $this->getApiData('forecasts','mexs',$locationId);
                    $this->saveForecastData($data,
                        $locationId,
                        $locationName,
                        'mexs',
                        'all_slopes',
                        $location->name
                    );
                }
            } 
        } 
    }

    protected function getApiData($feed,$locationType,$locationId)
    {
        $url = "http://mmv.feeds.meteonews.net/$feed/$locationType/$locationId.xml";
        $user = 'mmv';
        $pass = 'NZUG$ObvwGx/%!+,';
        $parameters = array( 'cumulation' => '1h', 'lang' => 'de');

        $client = new \Zend_Http_Client($url);
        $client->setMethod(\Zend_Http_Client::GET);
        $client->setAuth($user,$pass, \Zend_Http_Client::AUTH_BASIC);
        $client->setParameterGet($parameters);
        $body = $client->request()->getBody();

        if ($body) {
            return simplexml_load_string($body);
        } else {
            return false;
        }
    }

    protected function saveForecastData($xml,$locationId,$locationName,$locationType,$locationList,$regionName)
    {
        $em = \Zend_Registry::get('container')->getService('em');
        $weatherStatRepository = $em->getRepository('Newscoop\Entity\WeatherStat'); 

        foreach ($xml->content as $timeperiods) {
            foreach ($timeperiods as $key => $record) {
                $time = date_parse($record["end_datetime"]);
                $hour = $time['hour'];

                $searchBy = array('locationId' => $locationId, 'locationList' => $locationList, 'hour' => $hour);

                if ($weatherStatRepository->countBy($searchBy) > 0) {
                    $weatherStat = $weatherStatRepository->findOneBy($searchBy);
                } else { 
                    $weatherStat = new WeatherStat();
                }
                $values = array(
                    'location_id' => $locationId,
                    'location_type' => (string)$locationType,
                    'location_name' => (string)$locationName,
                    'location_list' => (string)$locationList,
                    'region_name' => (string)$regionName,
                    'hour' => (int)$hour,
                    'symbol' => (int)($record->symb) ? $record->symb : 0,
                    'temperature' => (int)($record->temp) ? $record->temp : 0,
                    'temperature_min' => (int)($record->temp_min) ? $record->temp_min : 0,
                    'temperature_max' => (int)($record->temp_max) ? $record->temp_max : 0,
                    'precip' => (int)($record->precip) ? $record->precip : 0,
                    'winddir' => (int)($record->winddir) ? $record->winddir : 0,
                    'windforce' => (int)($record->windforce) ? $record->windforce : 0
                );
                $weatherStatRepository->save($weatherStat, $values);
                $weatherStatRepository->flush();
            }
        }
    }

    protected function saveWintersportsData($xml,$locationId,$locationName,$locationType,$locationList,$regionName)
    {
        $em = \Zend_Registry::get('container')->getService('em');
        $weatherStatRepository = $em->getRepository('Newscoop\Entity\WeatherStat'); 

        foreach ($xml->content as $regions) {
            foreach ($regions as $key => $record) {
                if ($record["mexs_id"] == $locationId) {
                    // save slope data for every hour
                    for ($hour = 0; $hour <= 23; $hour++) {
                        $searchBy = array('locationId' => $locationId, 'hour' => $hour, 'locationList' => $locationList);

                        if ($weatherStatRepository->countBy($searchBy) > 0) {
                            $weatherStat = $weatherStatRepository->findOneBy($searchBy);
                        } else { 
                            $weatherStat = new WeatherStat();
                        }
                        $values = array(
                            'location_id' => $locationId,
                            'location_type' => (string)$locationType,
                            'location_name' => (string)$locationName,
                            'location_list' => (string)$locationList,
                            'region_name' => (string)$regionName,
                            'hour' => $hour,
                            'snow_condition' => $record->ski->snow_condition,
                            'slope_condition' => $record->ski->condition,
                            'total_slopes' => (int)($record->ski->facilities["total"]) ? $record->ski->facilities["total"] : 0,
                            'open_slopes' => (int)($record->ski->facilities) ? $record->ski->facilities : 0
                        );
                        $weatherStatRepository->save($weatherStat, $values);
                        $weatherStatRepository->flush();
                    }
                } else {
                    continue;
                }
            }
        }
    }
    
    protected function saveAllWintersportsData($xml,$locationType,$locationList,$regionName)
    {
        $em = \Zend_Registry::get('container')->getService('em');
        $weatherStatRepository = $em->getRepository('Newscoop\Entity\WeatherStat'); 

        foreach ($xml->content as $regions) {
            foreach ($regions as $key => $record) {
                $locationId = $record["mexs_id"];
                $locationName = $record["name"];

                // save slope data for every hour
                for ($hour = 0; $hour <= 23; $hour++) {
                    $searchBy = array('locationId' => $locationId, 'hour' => $hour, 'locationList' => $locationList);

                    if ($weatherStatRepository->countBy($searchBy) > 0) {
                        $weatherStat = $weatherStatRepository->findOneBy($searchBy);
                    } else { 
                        $weatherStat = new WeatherStat();
                    }
                    $values = array(
                        'location_id' => $locationId,
                        'location_type' => (string)$locationType,
                        'location_name' => (string)$locationName,
                        'location_list' => (string)$locationList,
                        'region_name' => (string)$regionName,
                        'hour' => $hour,
                        'snow_condition' => $record->ski->snow_condition,
                        'slope_condition' => $record->ski->condition,
                        'total_slopes' => (int)($record->ski->facilities["total"]) ? $record->ski->facilities["total"] : 0,
                        'open_slopes' => (int)($record->ski->facilities) ? $record->ski->facilities : 0
                    );
                    $weatherStatRepository->save($weatherStat, $values);
                    $weatherStatRepository->flush();
                }
            }
        }
    }

}
