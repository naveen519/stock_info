<?php
/**
 * @file
 * Contains \Drupal\stk_backend\Controller\StkServicesController.
 */

namespace Drupal\stk_backend\Controller;

use Drupal\Core\Controller\ControllerBase;
//use GuzzleHttp\Client;

class StkServicesController extends ControllerBase {
  public function content() {
	$config = [ 
			'base_uri'        => 'https://www.google.com',
            'timeout'         => 0,
            'allow_redirects' => false,
			]; 
/*			
	$client = new Client($config);
	$request = $client->get('finance/info?q=NSE:AIAENG');
	//$request->addHeader('Accept', 'application/hal+json');
	$response = $request->send()->json();
	print '<pre>';
  print_r($response);
  print '</pre>';
  die();*/
  /*
  $client = \Drupal::httpClient();
$request = $client->createRequest('GET', $config);
$request->addHeader('If-Modified-Since', gmdate(DATE_RFC1123, $last_fetched));
*/

 try {
    $response = \Drupal::httpClient()->get('https://www.google.com/finance/info?q=NSE:AIAENG', array('headers' => array('Accept' => 'text/json')));
    $data = (string) ($response->getBody());
	$feeds = $this->parseOpml($data);
	print '<pre>';
  print_r($feeds);
  print '</pre>';
  die();
    if (empty($data)) {
      return FALSE;
    }
  }
  catch (RequestException $e) {
    return FALSE;
  }
    return array(
        '#type' => 'markup',
        '#markup' => $this->t('Hello, World!'),
    );
  }
    protected function parseOpml($opml) {
    $feeds = array();
    $xml_parser = drupal_xml_parser_create($opml);
    if (xml_parse_into_struct($xml_parser, $opml, $values)) {
      foreach ($values as $entry) {
        if ($entry['tag'] == 'OUTLINE' && isset($entry['attributes'])) {
          $item = $entry['attributes'];
          if (!empty($item['XMLURL']) && !empty($item['TEXT'])) {
            $feeds[] = array('title' => $item['TEXT'], 'url' => $item['XMLURL']);
          }
        }
      }
    }
    xml_parser_free($xml_parser);

    return $feeds;
  }
}