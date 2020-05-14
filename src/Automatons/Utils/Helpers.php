<?php

namespace Autto\Utils;

use Autto;
use Autto\Automaton;
use Autto\StateGroupSet;
use Autto\Components\States\State;
use Autto\Components\States\StateSet;
use Autto\Components\Transitions\Transition;
use Autto\Components\Transitions\TransitionSet;
use Autto\Components\Alphabet\Alphabet;
use Autto\Components\Alphabet\Symbol;


class Helpers
{

	const S_STATE_SEP = ',';



	/**
	 * @param  Autto\Automaton $automaton
	 * @param  State $state
	 * @return StateSet
	 */
	static function epsilonClosure(Autto\Automaton $automaton, State $state)
	{
		$closure = new StateSet(array($state));
		foreach ($closure as $s) {
			foreach ($automaton->getTransitions()->filterByState($s)->filterByEpsilon() as $transition) {
				foreach ($transition->getTo() as $target) {
					!$closure->has($target) && $closure->add($target);
				}
			}
		}

		$closure->lock();
		return $closure;
	}



	/**
	 * @param  StateSet $set
	 * @return State
	 */
	static function joinStates(StateSet $set)
	{
		
		$names = array();
		$i=0;
		foreach ($set as $state) {
			$str_splitee = preg_split("/(,|{|})+/", trim($state->getName()));
			foreach($str_splitee as $str){
				if(strcmp("", $str)!=0 && !in_array($str,$names)){
					$names[]=$str;
				}
			}
			$i++;
		}
		sort($names);
		return new State('{' . implode(static::S_STATE_SEP, $names) . '}');
	}



	/**
	 * @param  StateSet $set
	 * @param  State $new
	 * @return void
	 */
	static function memorySaveAdd(StateSet $set, State & $new)
	{
		if ($set->has($new, TRUE)) {
			$new = $set->getByName($new->getName());
		} else {
			//!$set->has($new, TRUE, TRUE);
			$set->add($new);
		}
	}



	/**
	 * @param  Autto\Automaton $a
	 * @param  StateGroupSet $current
	 * @return StateGroupSet
	 */
	static function buildGroupTable(Autto\Automaton $a, StateGroupSet & $current = NULL)
	{
		if ($current === NULL) { // first iteration -> build the final and non-final states group table
			$current = static::createInitialGroupState($a);
		}

		$naming = 0;
		$metaGroups = array();

		foreach ($current as $group) {
			$created = array(); // stack of new group transition targets
			foreach ($group->getStates() as $state) {
				$targetSet = $group->getTransitions()->filterByState($state)->toTargetSet();
				$key = array_search($targetSet, $created); // intentionally non-strict searching

				if ($key === FALSE) { // create new one
					$naming++;
					$metaGroups[$naming] = array($state);
					$created[$naming] = $targetSet;

				} else {
					$metaGroups[$key][] = $state;
				}
			}
		}

		$groups = new StateGroupSet;
		foreach ($metaGroups as $name => $states) {
			$groups->add(new Autto\StateGroup($name, new StateSet($states)));
		}

		static::factoryGroupTransitions($a, $groups);
		return $groups;
	}



	/**
	 * @param  Autto\Automaton
	 * @return StateGroupSet
	 */
	static function createInitialGroupState(Autto\Automaton $a)
	{
		$nonfinals = new StateSet;
		$finals = new StateSet;

		foreach ($a->getStates() as $state) {
			if ($a->getFinalStates()->has($state)) {
				$finals->add($state);

			} else {
				$nonfinals->add($state);
			}
		}

		$g1 = new Autto\StateGroup('1', $finals);
		$g2 = new Autto\StateGroup('2', $nonfinals);

		$groups = new Autto\StateGroupSet(array($g1, $g2));
		static::factoryGroupTransitions($a, $groups);

		return new StateGroupSet($groups);
	}



	/**
	 * @param  Autto\Automaton $a
	 * @param  Autto\StateGroupSet $groups
	 * @return void
	 */
	static function factoryGroupTransitions(Autto\Automaton $a, Autto\StateGroupSet $groups)
	{
		$groups->lock();

		foreach ($groups as $group) {
			$transitions = new TransitionSet;
			foreach ($group->getStates() as $state) {
				foreach ($a->getTransitions()->filterByState($state) as $transition) {
					foreach ($transition->getTo() as $target) {
						$transitions->add(new Transition(
							$transition->getFrom(),
							new StateSet(array($groups->getStateGroup($target)->toState())),
							$transition->getOn()
						));
					}
				}
			}

			$group->setTransitions($transitions);
		}
	}

	/**
	 * @param State
	 * @param State
	 * @return State
	 */
	static function assembleUnique(State $state1, State $state2){
		$str_splitee_1 = preg_split("/(,|{|})+/", trim($state1->getName()));
		$str_splitee_2 = preg_split("/(,|{|})+/", trim($state2->getName()));
		$str = [];
		foreach($str_splitee_2 as $str2){
			if(!in_array($str2,$str)){
				$str[]=$str2;
			}
		}
		foreach($str_splitee_1 as $str1){
			if(!in_array($str1,$str)){
				$str[]=$str1;
			}
		}
		return new State('{'.implode(static::S_STATE_SEP, $str).'}');
	}

	/**
	 * @param Array
	 * @param string
	 * @param bool
	 * @return Array
	 */
	static function replaceStrInArray(&$array, $str, $replace=FALSE){
		foreach($array as &$a){
			$a = str_replace($str, $replace, $a);
		}
		return $array;
	}

	/**
	 * @param  String[] // Positive sample
	 * @return Automaton
	 */
	static function mca($e_positif){
		//var_dump($e_positif);
		$sigma = new Alphabet;
		$initials = new StateSet;
		$finals = new StateSet;
		$transitions = new TransitionSet;
		$states = new StateSet;
		$start = $states->newState();
		!$initials->has($start, TRUE, TRUE);
		
		foreach($e_positif as $elm){
			$str = '';
			$q1 = $start;
			for($i=0;$i<strlen($elm);$i++){
				$q2 = $states->newState();				
				$symbol = new Symbol($elm[$i]);
				!$sigma->has($symbol) && $sigma->add($symbol);
				//var_dump($q2);
				$trans = new Transition($q1, new StateSet(array($q2)), $symbol);
				//var_dump($trans->getTo());
				while($transitions->has($trans)){
					$q2 = $states->newState();
					$trans = new Transition($q1, new StateSet(array($q2)), $symbol);
				}
				$transitions->add($trans);
				//!$transitions->has($trans, TRUE, TRUE);
				!$states->has($q2, TRUE, TRUE);
				if(strcmp($str.$elm[$i], $elm)==0){
					!$finals->has($q2, TRUE, TRUE);
				}
				$str = $str.$elm[$i];
				$q1 = $q2;
			}
		}
		$automaton = new Automaton($states, $sigma, $transitions, $initials, $finals);
		$automaton->mergeTransitions();
		return $automaton;
	}

	/**
	 * @param StateSet[]
	 * @param StateSet
	 * @param bool
	 * @return bool
	 */
	static function isInPartition(&$partition, StateSet $set, $replace_items=FALSE){
		$compteur = 0;
		foreach($partition as &$stateset){
			foreach($set as $s){
				if($stateset->has($s, $replace_items)){
					$compteur++;
				}
			}
			
		}
		if($compteur!=0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	/**
	 * @param StateSet[]
	 * @return StateSet[]
	 */
	static function removeDoubles(&$partition){
		$part = [];
		foreach($partition as $set){
			if(count($set)!=0){
				if(!Helpers::isInPartition($part, $set)){
					$part[] = $set;
				}
			}
		}
		$partition = $part;
		return $partition;
	}

	/**
	 * @param String[] //I+
	 * @param String[] //I-
	 * @return Automaton //Le plus petit AFD
	 */
	static function rpni($e_positif, $e_negatif){
		$mca = Helpers::mca($e_positif);
		$pta = $mca->derive($mca->getPartitionPositive());
		$partition = [];
		foreach($pta->getStates() as $state){
			$partition[] = new StateSet(array($state));
		}
		$items_i = NULL;
		for($i=1;$i<count($partition);$i++){
			$items_j = NULL;
			for($j=0;$j<$i;$j++){
				$bloc_fusionne = new StateSet;

				$items_i = $partition[$i];
				$items_j = $partition[$j];
				
				foreach($items_i as $item){
					!$bloc_fusionne->has($item, TRUE, TRUE);
				}
				foreach($items_j as $item){
					!$bloc_fusionne->has($item, TRUE, TRUE);
				}
				$partition1 = [];
				for($k=0;$k<count($partition);$k++){
					if($k!=$i && $k!=$j){
						$partition1[] = $partition[$k];
					}
				}
				$partition1[] = $bloc_fusionne;
				
				$tmp1 = clone($pta);
				$pta_derive = $tmp1->derive($partition1);

				$partition2 = $pta_derive->fusion_determ();

				/*echo "Partition 2";
				foreach($partition2 as $p2){
					foreach($p2 as $s){
						var_dump($s);
					}
				}*/

				$tmp2 = clone($pta);
				$pta_derive_2 = $tmp2->derive($partition2);

				/*echo "Derive 2";
				foreach($pta_derive_2->getTransitions() as $p2){
					var_dump("From : ", $p2->getFrom());
					foreach($p2->getTo() as $to){
						var_dump("To : ", $to);
					}
					var_dump("On : ", $p2->getOn());
				}*/

				if($pta_derive_2->compatibleToSample($e_negatif)){
					$pta =  $pta_derive_2;
					$partition = $partition2;
					break;
				}
			}
		}
		//$pta->determinize();
		return $pta;
	}

}
