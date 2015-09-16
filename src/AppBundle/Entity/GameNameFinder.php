<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

class GameNameFinder{

	protected $gameNames = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois',
		'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana',
		'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania',
		'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'
	];
	
	protected $namesInUse;
	
	public function __construct(array $namesInUse){
		$this->namesInUse = $namesInUse;
	}
	/**
	 * Get Unique Name
	 *
	 * @param array $namesInUse
	 * @return string
	 */
	public function getUniqueName()
	{
		$uniqueName = '';
		$availableNames = $this->getAvailableNames();
		if(count($availableNames) > 0){
			// get random available name
			$uniqueName = $availableNames[array_rand($availableNames )];
		}else{
			// all games are in use, create a new unique name
			$randomInUseName = $this->namesInUse[array_rand($this->namesInUse)];
			$uniqueName =  $randomInUseName  . ' x2';
		}
		return $uniqueName;
		
	}
	

	/**
	 * Get Available Names 
	 *
	 * @param array $namesInUse
	 * @return array
	 */
	public function getAvailableNames()
	{
		$availableNames = [];
		foreach($this->gameNames as $name){
			if(!in_array($name, $this->namesInUse)){
				$availableNames[] = $name; 
			}
		}
		return $availableNames;
	}
	

}
