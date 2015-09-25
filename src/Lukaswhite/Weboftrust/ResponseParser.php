<?php namespace Lukaswhite\Weboftrust;

use Lukaswhite\Weboftrust\Models\Website;

/**
 * @class Lukaswhite\Weboftrust\ResponseParser
 *
 * Responsible for parsing the results from the Web Of Trust API, and creating a bunch of Website model objects to
 * encapsulate and "make sense of" the returned data.
 */
class ResponseParser {

	/**
	 * The fromCommand method is used by Guzzle to create an instance of this class. It "injects"
	 * the response data, which is then parsed to populate this and the associated models.
	 *
	 * @param Guzzle\Service\Command\OperationCommand
	 * @return array
	 */
	public static function fromCommand(\Guzzle\Service\Command\OperationCommand $command)
	{
    // Grab the results, which will be in JSON format
    //krumo($command->getResponse());
    $result = $command->getResponse()->json();

    // Start building an array, since a call to the API can include several different sites
    $websites = array();
    
    // Now iterate through the results
    foreach ($result as $key => $row) {

    	// "Target" is a stripped-down version of the URL provided
    	$url = $row['target'];

    	// Create a new Website model
    	$website = new Website($url);
    	
    	// The 0 axis holds information about trustworthiness
    	if (isset($row[0])) {
    		$website->setTrustworthiness($row[0][0], $row[0][1]);
    	}

    	// The 4 axis contins information pertaining to child satefy
    	if (isset($row[4])) {
    		$website->setChildSafety($row[4][0], $row[4][1]);
    	}

    	// NOTE: the axes in indexes 1 and 2 are deprecated.

    	// If any categories are specified, set them
    	if (isset($row['categories'])) {
    		$website->setCategories($row['categories']);
    	}

    	// If provided, set the blacklist information; we'll discard the timestamp information
    	if (isset($row['blacklists'])) {
    		$website->setBlacklists(array_keys($row['blacklists']));
    	}
    
    	// Add the website
    	$websites[$key] = $website;	
    }

    return $websites;
	}

}
