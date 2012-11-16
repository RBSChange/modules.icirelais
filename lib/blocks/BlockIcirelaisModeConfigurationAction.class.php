<?php
/**
 * icirelais_BlockIcirelaisModeConfigurationAction
 * @package modules.icirelais.lib.blocks
 */
class icirelais_BlockIcirelaisModeConfigurationAction extends shipping_BlockRelayModeConfigurationAction
{
	
	protected function buildRelayList()
	{
		$relays = array();
		
		$dateFrom = date_Calendar::getInstance();
		$dateFormString = date_Formatter::format($dateFrom, 'd/m/Y');
		$requestId = uniqid($dateFrom->getTimestamp());
		
		$webserviceUrl = Framework::getConfigurationValue('modules/icirelais/webserviceUrl');;
		
		$url = $webserviceUrl . '/GetPudoList?address=' . urlencode($this->param['address']) . '&zipCode=' . $this->param['zipcode'] . '&city=' . $this->param['city'] . '&request_id=' . $requestId . '&date_from=' . $dateFormString;
		
		$httpClient = HTTPClientService::getInstance()->getNewHTTPClient();
		$xml = $httpClient->get($url);
		
		$doc = f_util_DOMUtils::fromString($xml);
		$items = $doc->documentElement->lastChild->childNodes;
		
		for ($i = 0; $i < $items->length; $i++)
		{
			$relay = icirelais_IcirelaismodeService::getInstance()->getRelayFromXml($items->item($i));
			$relay->setCountryCode($this->param['countryCode']);
			$relays[] = $relay;
		}
		
		return $relays;
	}

}