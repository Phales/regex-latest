<?php

/**
 * This file is part of the Autto library
 *
 * Copyright (c) 2013 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Autto
 */

namespace Autto;

use Autto\Components\States\State;
use Autto\Components\States\StateSet;
use Autto\Components\Alphabet\Alphabet;
use Autto\Components\Alphabet\Symbol;
use Autto\Components\Transitions\Transition;
use Autto\Components\Transitions\TransitionSet;
use Autto\Utils\Helpers;


class Automaton
{

	/** @var StateSet */
	protected $states;

	/** @var Alphabet */
	protected $alphabet;

	/** @var TransitionSet */
	protected $transitions;

	/** @var StateSet */
	protected $initials;

	/** @var StateSet */
	protected $finals;

	/** @var bool */
	protected $deterministic = NULL;



	/**
	 * @param  StateSet $states
	 * @param  Alphabet $alphabet
	 * @param  TransitionSet $transitions
	 * @param  StateSet $initials
	 * @param  StateSet $finals
	 */
	function __construct(StateSet $states, Alphabet $alphabet, TransitionSet $transitions,
			StateSet $initials, StateSet $finals)
	{
		$this->construct($states, $alphabet, $transitions, $initials, $finals);
	}



	/** @return Automaton */
	function removeEpsilon()
	{
		if ($this->alphabet->hasEpsilon()) {
			$alphabet = new Alphabet;
			$transitions = new TransitionSet;
			$finals = new StateSet;

			foreach ($this->states as $state) {
				$closure = Utils\Helpers::epsilonClosure($this, $state);

				foreach ($this->alphabet as $symbol) {
					if (!$symbol->isEpsilon()) {
						!$alphabet->has($symbol) && $alphabet->add($symbol);

						$to = new StateSet;
						foreach ($closure as $s) {
							$this->finals->has($s) && !$finals->has($s) && $finals->add($s);

							foreach ($this->transitions->filterByState($s)->filterBySymbol($symbol) as $t) {
								foreach ($t->getTo() as $target) {
									!$to->has($target) && $to->add($target);
								}
							}
						}

						$transitions->add(new Transition($state, $to, $symbol));
					}
				}
			}

			$this->construct($this->states, $alphabet, $transitions, $this->initials, $finals);
		}

		return $this;
	}



	/** @return Automaton */
	function determinize()
	{

		if(!$this->isDeterministic()){

			$this->removeEpsilon();

			$states = new StateSet;
			$transitions = new TransitionSet;
			$initials = new StateSet;
			$finals = new StateSet;

			$queue = new StateSetSet(array($this->initials));

			foreach($queue as $set){

				$new = Utils\Helpers::joinStates($set);
				Utils\Helpers::memorySaveAdd($states, $new);

				!count($initials) && !$initials->has($new, TRUE, TRUE);

				foreach($this->alphabet as $symbol){
					$tos = $this->getTosBySet($set, $symbol);

					if(!count($tos)) continue;

					$target = Utils\Helpers::joinStates($tos);
					Utils\Helpers::memorySaveAdd($states, $target);

					var_dump($target);
					$trans = new Transition($new, new StateSet(array($target)), $symbol);
					$this->transitions->has($trans) && !$transitions->has($trans,TRUE, TRUE);

					!$queue->has($tos) && $queue->add($tos);

				}

			}

			foreach($states as $state){
				$this->finals->has($state) && !$finals->has($state, TRUE, TRUE);// && $finals->add($new);
			}

			$this->construct($states, $this->alphabet, $transitions, $initials, $finals);
			//$this->removeFreeStates();
			//$this->removeIllegalTransitions();
			$this->mergeTransitions();
			$this->deterministic = TRUE;
		}
		return $this;
	}



	/** @return Automaton */
	function minimize()
	{
		$this->determinize();

		$current = NULL;
		while (TRUE) {
			$new = Utils\Helpers::buildGroupTable($this, $current);
			if (count($new) === count($current)) { // no new groups
				break;
			}

			$current = $new;
		}

		$states = new StateSet;
		$initials = new StateSet;
		$finals = new StateSet;
		$transitions = new TransitionSet;

		foreach ($current as $group) {
			$state = $group->toState();
			$states->add($state);

			foreach ($group->getStates() as $s) {
				!$initials->has($state) && $this->initials->has($s) && $initials->add($state);
				!$finals->has($state) && $this->finals->has($s) && $finals->add($state);
			}

			foreach ($group->getStates() as $s) {
				foreach ($group->getTransitions()->filterByState($s) as $transition) {
					$transitions->add(new Transition(
							$state,
							$transition->getTo(),
							$transition->getOn()
					));
				}

				break; // intentionally break - only first transition needed (rest is the same)
			}
		}

		$this->construct($states, $this->alphabet, $transitions, $initials, $finals);
		return $this;
	}



	/**
	 * @param  StateSet $states
	 * @param  Alphabet $alphabet
	 * @param  TransitionSet $transitions
	 * @param  StateSet $initials
	 * @param  StateSet $finals
	 * @return void
	 */
	private function construct(StateSet $states, Alphabet $alphabet, TransitionSet $transitions,
			StateSet $initials, StateSet $finals)
	{
		$states->lock();
		$alphabet->lock();
		$transitions->lock();
		$initials->lock();
		$finals->lock();

		$this->states = $states;
		$this->alphabet = $alphabet;
		$this->transitions = $transitions;
		$this->initials = $initials;
		$this->finals = $finals;

		$this->validate();
		$this->deterministic = NULL;
	}



	/**
	 * @return void
	 * @throws Exceptions\EmptySetException
	 * @throws Exceptions\InvalidSetException
	 * @throws Exceptions\StateNotFoundException
	 */
	private function validate()
	{
		if (!count($this->alphabet) || !count($this->transitions)
				|| !count($this->states) || !count($this->initials)) {
			throw new Exceptions\EmptySetException;
		}

		if (!$this->initials->isSubsetOf($this->states)
				|| !$this->finals->isSubsetOf($this->states)) {
			throw new Exceptions\InvalidSetException;
		}

		foreach ($this->transitions as $transition) {
			if (!$this->states->has($transition->getFrom())) {
				throw new Exceptions\StateNotFoundException($transition->getFrom());
			}

			if (!$transition->getTo()->isSubsetOf($this->states)) {
				throw new Exceptions\InvalidSetException;
			}

			if (!$this->alphabet->has($transition->getOn())) {
				throw new Exceptions\SymbolNotFoundException($transition->getOn());
			}
		}
	}



	/** @return bool */
	private function discoverDeterminism()
	{
		if ($this->alphabet->hasEpsilon() || count($this->initials) > 1) {
			return FALSE;
		}

		$reachable = new StateSet;
		foreach ($this->states as $state) {
			foreach ($this->alphabet as $symbol) {
				foreach ($this->transitions->filterByState($state)->filterBySymbol($symbol) as $transition) {
					$this->initials->has($transition->getFrom())
							&& !$reachable->has($transition->getFrom())
							&& $reachable->add($transition->getFrom());

					if (count($transition->getTo()) !== 1) {
						return FALSE;
					}

					foreach ($transition->getTo() as $target) {
						!$reachable->has($target) && $reachable->add($target);
					}
				}
			}
		}

		return count($reachable) === count($this->states);
	}



	/** @return StateSet */
	function getStates()
	{
		return $this->states;
	}



	/** @return Alphabet */
	function getAlphabet()
	{
		return $this->alphabet;
	}



	/** @return TransitionSet */
	function getTransitions()
	{
		return $this->transitions;
	}



	/** @return StateSet */
	function getInitialStates()
	{
		return $this->initials;
	}



	/** @return StateSet */
	function getFinalStates()
	{
		return $this->finals;
	}



	/** @return bool */
	function isDeterministic()
	{
		if ($this->deterministic === NULL) {
			$this->deterministic = $this->discoverDeterminism();
		}

		return $this->deterministic;
	}

	/**
	 * @return Automaton //$this
	 */
	function removeFreeStates(){
		$new_states = new StateSet;
		$new_initials = new StateSet;
		$new_finals = new StateSet;
		$transitions = $this->getTransitions();
		foreach($this->states as $state){
			$by_tos = $transitions->filterByTo($state);
			$by_froms = $transitions->filterByState($state);
			if(count($by_tos) || count($by_froms)){
				$new_states->add($state);
			}
		}
		foreach($this->initials as $state){
			$by_tos = $transitions->filterByTo($state);
			$by_froms = $transitions->filterByState($state);
			if(!is_null($by_tos) || !is_null($by_froms)){
				$new_initials->add($state);
			}
		}
		foreach($this->finals as $state){
			$by_tos = $transitions->filterByTo($state);
			$by_froms = $transitions->filterByState($state);
			if(!is_null($by_tos) || !is_null($by_froms)){
				$new_finals->add($state);
			}
		}
		$this->states = $new_states;
		$this->initials = $new_initials;
		$this->finals = $new_finals;
		return $this;
	}

	/**
	 * @return Automaton
	 */
	function removeIllegalTransitions(){
		$this->mergeTransitions();
		$transitions = new TransitionSet;
		foreach($this->states as $state){
			foreach($this->alphabet as $symbol){
				$trans = $this->transitions->filterByState($state)->filterBySymbol($symbol);
				if(count($trans)){
					$new_set = new StateSet;
					foreach($trans as $t){
						foreach($t->getTo() as $to){
							if($this->states->hasIdentical($to)){
								!$new_set->has($to, TRUE, TRUE);
							}
						}
					}
					if(count($new_set)){
						if($this->states->hasIdentical($state)){
							$new = new Transition($state, $new_set, $symbol);
							!$transitions->has($new, TRUE, TRUE);
						}
					}
				}
			}
		}
		
		$this->construct($this->states, $this->alphabet, $transitions, $this->initials, $this->finals);
		//$this->removeFreeStates();
		return $this;
	}

	/**
	 * @return Automaton //$this
	 */
	function mergeTransitions(){
		$new_transitions = new TransitionSet;
		foreach($this->states as $state){
			foreach($this->alphabet as $symbol){
				$transitions = $this->transitions->filterByState($state)->filterBySymbol($symbol);
				if(count($transitions)){
					$set = new StateSet;
					foreach($transitions as $trans){
						foreach($trans->getTo() as $to){
							!$set->has($to, TRUE, TRUE);
						}
					}
					$new_tmp = new Transition($state, $set, $symbol);
					!$new_transitions->has($new_tmp, TRUE, TRUE);
				}
			}
		}
		$this->transitions = $new_transitions;
		return $this;
	}

	/**
	 * @param  Set[]
	 * @return Automaton //$this
	 */
	function derive($partition){
		$states = new StateSet;
		$initials = new StateSet;
		$finals = new StateSet;
		$transitions = new TransitionSet;
		$this->mergeTransitions();

		foreach($partition as $set){
			$joined_state = Helpers::joinStates($set);
			$this->states->has($joined_state) && !$states->has($joined_state, TRUE, TRUE);
			$this->initials->has($joined_state) && !$initials->has($joined_state, TRUE, TRUE);
			$this->finals->has($joined_state) && !$finals->has($joined_state, TRUE, TRUE);
		}

		$set_state = clone($states);

		foreach($set_state as $new_state){
			foreach($this->states as $state){
				$set = new StateSet(array($state));
				if($set->has($new_state)){
					foreach($this->alphabet as $symbol){
						$tmp_trans = $this->transitions->filterByState($state)->filterBySymbol($symbol);
						if(count($tmp_trans)){
							foreach($tmp_trans as $tmp){
								$joined_state = Helpers::joinStates($tmp->getTo());
								$trans = new Transition($new_state, new StateSet(array($joined_state)), $tmp->getOn());
								$set_state->has($new_state) && $set_state->has($joined_state) && !$transitions->has($trans, TRUE, TRUE);
							}
						}
					}
				}
			}
		}
		if(!count($this->alphabet)){
			echo "Oh lala";
		}
		$this->construct($states, $this->alphabet, $transitions, $initials, $finals);

		//$this->removeIllegalTransitions();

		$this->simplifyBySplit();

		//$this->removeIllegalTransitions();

		return $this;
	}

	/**
	 * @return Automaton
	 */
	function simplifyBySplit(){
		$states = new StateSet;
		$transitions = new TransitionSet;
		$initials = new StateSet;
		$finals = new StateSet;

		foreach($this->states as $state){
			$str = str_replace("'", "", $state->getName());
			$q = new State($str);
			$q = Helpers::assembleUnique($q, $q);
			!$states->has($q, TRUE, TRUE);
		}

		foreach($this->initials as $state){
			$str = str_replace("'", "", $state->getName());
			$q = new State($str);
			$q = Helpers::assembleUnique($q, $q);
			!$initials->has($q, TRUE, TRUE);
		}

		foreach($this->finals as $state){
			$str = str_replace("'", "", $state->getName());
			$q = new State($str);
			$q = Helpers::assembleUnique($q, $q);
			!$finals->has($q, TRUE, TRUE);
		}
		
		foreach($this->transitions as $transition){
			$str = str_replace("'", "", $transition->getFrom()->getName());
			$from = new State($str);
			$from = Helpers::assembleUnique($from, $from);
			$to = new StateSet;
			foreach($transition->getTo() as $tmp){
				$str_to = str_replace("'", "", $tmp->getName());
				$to_tmp = new State($str_to);
				$to_tmp = Helpers::assembleUnique($to_tmp, $to_tmp);
				!$to->has($to_tmp, TRUE, TRUE);
			}
			$trans = new Transition($from, $to, $transition->getOn());
			!$transitions->has($trans, TRUE, TRUE);
		}
		
		$this->construct($states, $this->alphabet, $transitions, $initials, $finals);

		return $this;
	}

	/**
	 * @return StateSet[]
	 */
	public function getPartitionPositive(){//PiI+ Pour obtenir MCA
		$automaton = clone($this);
		
		$partition = [];
		//$automaton->mergeTransitions();
		$initials = $this->initials;

		if(!Helpers::isInPartition($partition, $initials)){
			$partition[] = $initials;
		}

		$transitions = $automaton->getTransitions();
		foreach($transitions as $trans){
			if(!Helpers::isInPartition($partition, $trans->getTo())){
				$partition[] = $trans->getTo();
				$joined_state = Helpers::joinStates($trans->getTo());
				foreach($this->alphabet as $symbol){
					$new_tos = new StateSet;
					foreach($trans->getTo() as $state){
						$tmp_trans = $this->transitions->filterByState($state)->filterBySymbol($symbol);
						if(count($tmp_trans)){
							$items = $tmp_trans->getItems();
							foreach($items[0]->getTo() as $t){
								!$new_tos->has($t, TRUE, TRUE);
							}
						}
					}
					if(count($new_tos)){
						$new_trans = new Transition($joined_state, $new_tos, $symbol);
						!$transitions->has($new_trans, TRUE, TRUE);
					}
				}
			}
		}
		return $partition;
	}

	/**
	 * @return StateSet[]
	 */
	function fusion_determ(){
		$this->determinize();
		return $this->getPartitionPositive();
	}

	/**
	 * @param string
	 * @return bool
	 */
	function compatible($word){
		$current = $this->initials;
		$error = FALSE;
		for ($i=0; $i<strlen($word); $i++) {
			$next = new StateSet; 
			$c = $word[$i];
			foreach($current as $s){
				$trans = $this->transitions->filterByState($s)->filterBySymbol(new Symbol($c));
				if(count($trans)){
					foreach($trans as $t){
						foreach($t->getTo() as $s_tmp){
							!$next->has($s_tmp, TRUE, TRUE);
						}
					}
				}
			}
			if(!count($next)){
				$error = TRUE;
				break;
			}
			$current = $next;
		}
		if(!$error){
			foreach($current as $state){
				if($this->finals->has($state)){
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	/**
	 * @param string[]
	 * @return bool
	 */
	function compatibleToSample($e_negatif){
		$compteur=0;
		foreach($e_negatif as $e){
			if($this->compatible($e)){
				$compteur++;
			}
		}
		if($compteur==count($e_negatif)){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	/**
	 * @param string[]
	 * @return bool
	 */
	function rejectSample($e_negatif){
		$compteur=0;
		foreach($e_negatif as $e){
			if(!$this->compatible($e)){
				$compteur++;
			}
		}
		if($compteur==count($e_negatif)){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	/**
	 * @param StateSet
	 * @param Symbol
	 */
	function getTosBySet(StateSet $set, Symbol $s) {
		$tos = new StateSet;
		foreach($set as $state){
			$trans = $this->transitions->filterByState($state)->filterBySymbol($s);
			if(count($trans)){
				foreach($trans as $tmp){
					foreach($tmp->getTo() as $to){
						!$tos->has($to, TRUE, TRUE);
					}
				}
			}
		}
		return $tos;
	}

}
