<?php
/**
 * icirelais_BlockExpeditionDetailAction
 * @package modules.icirelais.lib.blocks
 */
class icirelais_BlockExpeditionDetailAction extends shipping_BlockExpeditionDetailAction
{
	/**
	 * Initialize $this->param
	 */
	protected function init()
	{
		$shippingAdress = $this->expedition->getAddress();
		$shippingMode = $this->expedition->getShippingMode();
		
		$this->param['relayCode'] = $shippingAdress->getLabel();
		$this->param['countryCode'] = $shippingAdress->getCountryCode();
		$this->param['lang'] = strtoupper($this->getContext()->getLang());
		
		$this->param['webserviceUrl'] = Framework::getConfigurationValue('modules/icirelais/webserviceUrl');
		$this->param['trackingUrl'] = Framework::getConfigurationValue('modules/icirelais/trackingUrl');
	
	}
	
	/**
	 * @return shipping_Relay
	 */
	protected function getRelayDetail()
	{
		$relay = null;
		
		$url = $this->param['webserviceUrl'] . '/GetPudoDetails?pudo_id=' . $this->param['relayCode'];
		
		$httpClient = HTTPClientService::getInstance()->getNewHTTPClient();
		$xml = $httpClient->get($url);
		
		try
		{
			$doc = f_util_DOMUtils::fromString($xml);
			$items = $doc->documentElement->lastChild->childNodes;
			
			if ($items->length > 0)
			{
				$relay = icirelais_IcirelaismodeService::getInstance()->getRelayFromXml($items->item(0));
				$relay->setCountryCode($this->param['countryCode']);
			}
		}
		catch (Exception $e)
		{
			Framework::exception($e);
		}
		
		return $relay;
	}
	
	/**
	 * @param string $trackingNumber
	 * @return array
	 */
	protected function getTrackingDetail($trackingNumber)
	{
		return array();
	}
}