<?php
/**
 * icirelais_IcirelaismodeService
 * @package modules.icirelais
 */
class icirelais_IcirelaismodeService extends shipping_RelayModeService
{
	/**
	 * @var icirelais_IcirelaismodeService
	 */
	private static $instance;
	
	/**
	 * @return icirelais_IcirelaismodeService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}
	
	/**
	 * @return icirelais_persistentdocument_icirelaismode
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_icirelais/icirelaismode');
	}
	
	/**
	 * Create a query based on 'modules_icirelais/icirelaismode' model.
	 * Return document that are instance of modules_icirelais/icirelaismode,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_icirelais/icirelaismode');
	}
	
	/**
	 * Create a query based on 'modules_icirelais/icirelaismode' model.
	 * Only documents that are strictly instance of modules_icirelais/icirelaismode
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_icirelais/icirelaismode', false);
	}
	
	/**
	 * @param icirelais_persistentdocument_icirelaismode $mode
	 * @param order_CartInfo $cart
	 * @return string[]|false
	 */
	public function getConfigurationBlockForCart($mode, $cart)
	{
		return array('icirelais', 'IcirelaisModeConfiguration');
	}
	
	protected function getDetailExpeditionPageTagName()
	{
		return 'contextual_website_website_modules_icirelais_icirelaisexpedition';
	}
	
	/**
	 * @param order_persistentdocument_expeditionline $expeditionLine
	 * @param shipping_persistentdocument_mode $mode
	 * @param order_persistentdocument_expedition $expedition
	 */
	public function completeExpeditionLineForDisplay($expeditionLine, $shippmentMode, $expedition)
	{
		$trackingUrl = $expeditionLine->getTrackingURL();
		
		if ($trackingUrl === null)
		{
			$trackingUrl = $expedition->getOriginalTrackingURL();
		}
		
		$trackingNumber = $expeditionLine->getTrackingNumber();
		$trackingNumber1 = substr($trackingNumber, 0, 3);
		$trackingNumber2 = substr($trackingNumber, 3, 3);
		$trackingNumber3 = substr($trackingNumber, 6);
		
		$trackingUrl = str_replace('{1}', $trackingNumber1, $trackingUrl);
		$trackingUrl = str_replace('{2}', $trackingNumber2, $trackingUrl);
		$trackingUrl = str_replace('{3}', $trackingNumber3, $trackingUrl);
		
		$expeditionLine->setTrackingURL($trackingUrl);
	}
	
	/**
	 * 
	 * @param DOMNode $item
	 * @return shipping_Relay 
	 */
	public function getRelayFromXml($item)
	{
		
		$relay = new shipping_Relay();
		
		$childList = $item->childNodes;
		
		for ($i = 0; $i < $childList->length; $i++)
		{
			
			$child = $childList->item($i);
			$nodeName = strtolower($child->nodeName);
			switch ($nodeName)
			{
				case 'pudo_id' :
					$relay->setRef($child->nodeValue);
					break;
				case 'distance' :
					$relay->setDistance($child->nodeValue);
					break;
				case 'name' :
					$relay->setName($child->nodeValue);
					break;
				case 'address1' :
					$relay->setAddressLine1($child->nodeValue);
					break;
				case 'address2' :
					$value = trim($child->nodeValue);
					if ($value != '')
					{
						$relay->setAddressLine2($value);
					}
					break;
				case 'address3' :
					$value = trim($child->nodeValue);
					if ($value != '')
					{
						$relay->setAddressLine3($value);
					}
					break;
				case 'local_hint' :
					$value = trim($child->nodeValue);
					if ($value != '')
					{
						$relay->setLocationHint($value);
					}
					break;
				case 'zipcode' :
					$value = trim($child->nodeValue);
					if ($value != '')
					{
						$relay->setZipCode($value);
					}
					break;
				case 'city' :
					$value = trim($child->nodeValue);
					if ($value != '')
					{
						$relay->setCity($value);
					}
					break;
				case 'longitude' :
					$value = trim($child->nodeValue);
					if ($value != '')
					{
						$relay->setLongitude(floatval(str_replace(',', '.', $value)));
					}
					break;
				case 'latitude' :
					$value = trim($child->nodeValue);
					if ($value != '')
					{
						$relay->setLatitude(floatval(str_replace(',', '.', $value)));
					}
					break;
				case 'map_url' :
					$value = trim($child->nodeValue);
					if ($value != '')
					{
						$relay->setMapUrl($value);
					}
					break;
				case 'opening_hours_items' :
					$relay->setOpeningHours($this->extractOpeningHour($child));
					break;
			}
		
		}
		
		return $relay;
	
	}
	
	/**
	 * Extract opening hours from raw hours data
	 * @param DOMNode hoursNodes
	 * @return string
	 */
	protected function extractOpeningHour($hoursNode)
	{
		$ls = LocaleService::getInstance();
		
		$hoursByDay = array();
		
		$openingHoursItemList = $hoursNode->childNodes;
		
		for ($i = 0; $i < $openingHoursItemList->length; $i++)
		{
			$item = $openingHoursItemList->item($i);
			
			$infosList = $item->childNodes;
			$start = '';
			$end = '';
			$day = '';
			for ($j = 0; $j < $infosList->length; $j++)
			{
				$info = $infosList->item($j);
				$nodeName = strtolower($info->nodeName);
				if ($nodeName == 'start_tm')
				{
					$start = $info->nodeValue;
				}
				if ($nodeName == 'end_tm')
				{
					$end = $info->nodeValue;
				}
				if ($nodeName == 'day_id')
				{
					$day = $info->nodeValue;
				}
			}
			
			if (!isset($hoursByDay[$day]))
			{
				$hoursByDay[$day] = $ls->transFO('m.shipping.general.opening-hours', array('ucf'), array('hour1' => $start, 'hour2' => $end));
			}
			else
			{
				$timeSpan = ' ' . $ls->transFO('m.shipping.general.and') . ' ' . $ls->transFO('m.shipping.general.opening-hours', array(), array(
					'hour1' => $start, 'hour2' => $end));
				$hoursByDay[$day] = $hoursByDay[$day] . $timeSpan;
			}
		
		}
		
		for ($i = 0; $i < 7; $i++)
		{
			if (!isset($hoursByDay[$i]))
			{
				$hoursByDay[$i] = $ls->transFO('m.shipping.general.closed');
			}
		}
		
		return $hoursByDay;
	}

}