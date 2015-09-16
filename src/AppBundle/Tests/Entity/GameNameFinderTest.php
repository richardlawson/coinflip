<?php
namespace AppBundle\Tests\Entity;

use AppBundle\Entity\GameNameFinder;

class GameNameFinderTest extends \PHPUnit_Framework_TestCase{
	
	public function setUp(){
	}

	public function testGetAvailableNamesWhenAllGamesAreInUseExceptForStatesStartingWithA(){
		$namesInUse = ['California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois',
		'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana',
		'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania',
		'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'];
		$gameNameFinder = new GameNameFinder($namesInUse);
		$availableNames = $gameNameFinder->getAvailableNames();
		$this->assertEquals(4, count($availableNames));
	}
	
	public function testGetAvailableNamesReturns50WhenNoGamesAreInUse(){
		$namesInUse = [];
		$gameNameFinder = new GameNameFinder($namesInUse);
		$availableNames = $gameNameFinder->getAvailableNames();
		$this->assertEquals(50, count($availableNames));
	}
	
	public function testGetAvailableNamesReturnsZeroWhenAllGamesAreInUse(){
		$nameNotInUse = 'California';
		$namesInUse = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois',
				'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana',
				'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania',
				'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'];
		$gameNameFinder = new GameNameFinder($namesInUse);
		$availableNames = $gameNameFinder->getAvailableNames();
		$this->assertEquals(0, count($availableNames));
	}
	
	public function testGetUniqueNamesReturnsCaliforniaWhenAllGamesAreInUseExceptForCalifonrnia(){
		$nameNotInUse = 'California';
		$namesInUse = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois',
				'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana',
				'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania',
				'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'];
		$gameNameFinder = new GameNameFinder($namesInUse);
		$this->assertEquals($nameNotInUse, $gameNameFinder->getUniqueName());
	}
	
	/*
	 * It's tough to test this method since it returns random results
	 * This test gets random names for twenty method calls when arizona and arkansas are not in $nameInUse array
	 * The chances of not getting at least one arizona or arkansas are very slim, so as long as the method works we should get both names in our results
	 */
	public function testGetUniqueNameReturnsRandomResults(){
		$namesInUse = [ 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois',
				'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana',
				'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania',
				'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'];
		$gameNameFinder = new GameNameFinder($namesInUse);
		$names = [];
		for($i = 0; $i < 20; $i++){
			$names[] = $gameNameFinder->getUniqueName();
		}
		// make sure method returns arizona sometimes
		$this->assertTrue(in_array('Arizona', $names));
		// make sure method returns Arkansas sometimes;
		$this->assertTrue(in_array('Arkansas', $names));
	}
	
	public function testGetUniqueNamesReturnsNewUniqueNameWhenAllGamesAreInUse(){
		$namesInUse = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois',
				'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana',
				'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania',
				'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming'];
		$gameNameFinder = new GameNameFinder($namesInUse);
		$uniqueName = $gameNameFinder->getUniqueName();
		$this->assertTrue(strpos($uniqueName, ' x2') > 0);
	}
		
}
