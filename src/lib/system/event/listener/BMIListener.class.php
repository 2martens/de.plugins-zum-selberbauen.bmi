<?php
//wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Handles the BMI.
 *
 * @author Jim Martens
 * @copyright 2012 Jim Martens
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 * @package de.plugins-zum-selberbauen.bmi
 * @subpackage system.event.listener
 * @category Community Framework
*/
class BMIListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($className != 'RegisterForm' && $className != 'UserProfileEditForm') return;
		$this->updateBMI($eventObj);
	}

	/**
	 * Updates the BMI.
	 */
	protected function updateBMI($eventObj) {
		$height = $eventObj->activeOptions['height']['optionValue'];
		$weight = $eventObj->activeOptions['weight']['optionValue'];
		$bmi = $this->calculateBMI($height, $weight);
		$eventObj->activeOptions['bmi'] =& $eventObj->cachedOptions['bmi'];
		$eventObj->activeOptions['bmi']['optionValue'] = $bmi;
	}
	
	/**
	 * Calculates the BMI.
	 * 
	 * @param integer	$height
	 * @param integer	$weight
	 * @return float
	 */
	protected function calculateBMI($height, $weight) {
		$heightToMeter = ((float) $height) / 100;
		$bmi = $weight / ($heightToMeter * $heightToMeter);
		$bmi = round($bmi, 2);
		return $bmi;
	}
}
