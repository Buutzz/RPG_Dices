<?php

/**
 *  Game dice rolling including Ohet mechanism 
 *  Emulates probability as closely as possible and interpretate result for Ohet
 *  
 * @author Mateusz Wojdala
*/

class Dices{


    /**
 	 *
 	 * Main function for dice rolls
 	 * @param str choosen dice
 	 * @param int number of rolls to perform
 	 * @param str difficulty roll - only for Ohet
 	 * @return JSON array including results of dice rolls
 	 *
 	*/
    public function makeRoll(string $dice, int $rolls, ?string $difficulty){

		if(!is_int($rolls))
            return false;
            
		switch($dice){
            case 'ohet':
                $result = $this->OhetRolls($rolls, $difficulty);
                break;
            default:
                $result = $this->regularDice($dice, $rolls);
                break;
        }   
       
		return json_encode($result);

    }

    /**
 	 *
 	 * Function for Ohet dice rolls to perform
 	 * @param int number of rolls to perform
 	 * @param str difficulty of rolls
 	 * @return array with interpretation of all rolls
 	 *
 	*/
    private function ohetRolls(int $rolls, string $difficulty){
        //Set roll difficulty as easy if not set
        if(is_null($difficulty))
            $roll_difficulty = "easy";
        else
            $roll_difficulty = $difficulty;

        $results = [];
        
        for($i = 1; $i <= $rolls; $i++){
            switch($roll_difficulty){
                case 'easy':
                    //Choose max result from two dice roll on d6
                    $results[] =  $this->rollInterpretationforOhet(max([ $this->diceRoll(1,6), $this->diceRoll(1,6) ]));
                    break;
                case 'mid':
                    $results[] = $this->diceRoll(1,6);
                    break;
                case 'hard':
                    //Choose min result from two dice roll on d6
                    $results[] =  $this->rollInterpretationforOhet(min([ $this->diceRoll(1,6), $this->diceRoll(1,6) ]));
                    break;
            } 
        }

        return $results;
    }

    /**
 	 *
 	 * Function for normal dice rolls to perform
 	 * @param string choosen dice
 	 * @param int number of rolls to perform
 	 * @return array with of all rolls results
 	 *
 	*/
    private function regularDice(string $dice, int $rolls){
		$bottom_limit = 1;
        $upper_limit = (int) str_replace('d','',$dice);
        
        $result = [];

        for($i = 1; $i <= $rolls; $i++){
            $result[] = $this->diceRoll($bottom_limit,$upper_limit);
        }
		
		return $result;
    }

    /**
 	 *
 	 * Function for dice roll - emulating the probability
 	 * @param int lowest possible result
 	 * @param int highest posible result
 	 * @return int result
 	 *
 	*/
    private function diceRoll(int $bottom, int $up){
        return mt_rand($bottom,$up);
    }


    /**
 	 *
 	 * Function for interpretating dice rolls in Ohet
 	 * @param int result of rolls
 	 * @return string interpretation of the rolls
 	 *
 	*/
    private function rollInterpretationforOhet(int $result){
        $interpretation = '';
        switch($result){
            case 1:
                $interpretation = 'Test failed, additional negative effects included.';
                break;
            case 2:
                $interpretation = 'Test failed.';
                break;
            case 3:
                $interpretation = 'Test failed, but additional positive effects included.';
                break;
            case 4:
                $interpretation = 'Test successed, additional negative effects included.';
                break;
            case 5:
                $interpretation = 'Test successed.';
                break;
            case 6:
                $interpretation = 'Test failed, additional positive effects included.';
                break;
        }

        return $interpretation;
    }
}


$dice = new Dices;

var_dump(
    'd3 - 3 rolls',
    $dice->makeRoll('d3',3,null)
);

var_dump(
    'd20 - 2 rolls',
    $dice->makeRoll('d20',2,null)
);

var_dump(
    'Ohet - 3 rolls, hard',
    $dice->makeRoll('ohet',3,'hard')
);