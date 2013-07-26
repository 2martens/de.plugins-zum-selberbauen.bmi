<?php
//wcf imports
require_once(WCF_DIR.'lib/system/event/EventListener.class.php');

/**
 * Handles the BMI.
 *
 * @author		Jim Martens
 * @copyright	2013 Jim Martens
 * @license		http://www.gnu.org/licenses/lgpl-3.0 GNU Lesser General Public License, version 3
 * @package		de.plugins-zum-selberbauen.bmi
 * @subpackage	system.event.listener
 * @category	Community Framework
*/
class BMIListener implements EventListener {
	/**
	 * @see EventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if ($className != 'RegisterForm' && $className != 'UserProfileEditForm') return;
		if ($className == 'UserProfileEditForm' && $eventObj->activeCategory != 'profile') return;
		$this->updateBMI($eventObj);
	}

	/**
	 * Updates the BMI.
	 * 
	 * @param	RegisterForm|UserProfileEditForm	$eventObj
	 */
	protected function updateBMI($eventObj) {
		$height = $eventObj->activeOptions['height']['optionValue'];
		$weight = $eventObj->activeOptions['weight']['optionValue'];
		$bmi = $this->calculateBMI($height, $weight);
		
		try {
			$this->validateBMI($bmi);
			$eventObj->activeOptions['bmi'] =& $eventObj->cachedOptions['bmi'];
			$eventObj->activeOptions['bmi']['optionValue'] = $bmi;
		}
		catch (UserInputException $uie) {
			$errorTypes = array(
				'height' => 'bmiTooLow', 
				'weight' => 'bmiTooLow'
			);
			throw new UserInputException('options', $errorTypes);
		}
	}
	
	protected function validateBMI($bmi) {
		$isValidated = $bmi >= 25;
		if (!$isValidated) {
			throw new UserInputException('height', 'validationFailed');
		}
	}
	
	/**
	 * Calculates the BMI.
	 * 
	 * @param	integer	$height
	 * @param	integer	$weight
	 * @return	float
	 */
	protected function calculateBMI($height, $weight) {
		$heightToMeter = ((float) $height) / 100;
		$bmi = $weight / ($heightToMeter * $heightToMeter);
		$bmi = round($bmi, 2);
		return $bmi;
	}
}
