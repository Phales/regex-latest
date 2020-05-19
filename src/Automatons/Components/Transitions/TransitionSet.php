<?php

/**
 * This file is part of the Autto library
 *
 * Copyright (c) 2013 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Autto
 */

namespace Autto\Components\Transitions;

use Autto\Set;
use Autto\Components\States\State;
use Autto\Components\Alphabet\Symbol;
use Autto\Components\States\StateSet;
use Autto\Utils\Helpers;


class TransitionSet extends Set
{

	/** @var TransitionSet[] */
	private static $filters = array();



	/** @param  mixed $items */
	function __construct($items = NULL)
	{
		parent::__construct('Autto\Components\Transitions\Transition', $items);
	}



	/**
	 * @param  Transition $item
	 * @return TransitionSet
	 */
	function add($item)
	{
		parent::add($item);
		if($item->getTo() instanceof StateSet){
			$item->getTo()->lock();
		}
		return $this;
	}



	/** @return StateSet[] */
	function toTargetSet()
	{
		$targets = array();
		foreach ($this as $transition) {
			$targets[] = $transition->getTo();
		}

		return $targets;
	}



	/** @return TransitionSet */
	function filterByEpsilon()
	{
		return $this->filter(function (Transition $transition) {
			return $transition->getOn()->getValue() === Symbol::EPSILON;
		});
	}



	/**
	 * @param  \Closure $filter
	 * @return TransitionSet
	 */
	private function filter(\Closure $filter)
	{
		$self = clone $this;
		$set = new TransitionSet;
		foreach ($self as $transition) {
			if ($filter($transition)) {
				$set->add($transition);
			}
		}

		$set->lock();
		return $set;
	}



	/**
	 * @param  TransitionSet $set
	 * @param  State|Symbol $arg
	 * @param  \Closure $filter
	 * @return TransitionSet
	 */
	private static function loadFilter(TransitionSet $set, $arg, \Closure $filter)
	{
		$key = md5(spl_object_hash($set) . spl_object_hash($arg));
		if (!$set->isLocked() || !isset(self::$filters[$key])) {
			self::$filters[$key] = $set->filter($filter);
		}

		return self::$filters[$key];
	}

	/**
	 * @param  Transition $transition
	 * @param bool
	 * @param bool
	 * @return bool
	 */
	function has($transition, $replace=FALSE, $add=FALSE)
	{
		if(is_null($transition)){
			return FALSE;
		}
		$compteur = 0;
		$count = 0;
		$items = $this->filterByState($transition->getFrom())->filterBySymbol($transition->getOn());
		$tmp_trans = NULL;
		
		foreach($items as $trans){
			if($trans->getTo() instanceof StateSet){
				if($transition->getTo() instanceof StateSet){
					$tmp = Helpers::joinStates($transition->getTo());
					$to = $trans->getTo()->getByName($tmp->getName());
					if(!is_null($to)){
						if($trans->getTo()->has($to, $replace)){
							$compteur++;
						}
					}
				}
				if($transition->getTo() instanceof State){
					if($trans->getTo()->has($transition->getTo(), $replace)){
						$compteur++;
					}
				}
			}
			if($trans->getTo() instanceof State){
				if($transition->getTo() instanceof StateSet){
					$set = new StateSet;
					$set->add($trans->getTo());
					$count = 0;
					foreach($transition->getTo() as $state){
						if($set->has($state, $replace)){
							$count++;
						}
					}
					if($count!=0){
						$trans->replaceTo($set);
						$compteur++;
					}
				}
				if($transition->getTo() instanceof State){
					$set = new StateSet;
					$set->add($trans->getTo());
					if($set->has($transition->getTo(), $replace)){
						$trans->replaceTo($set);
						$compteur++;
					}
				}
			}
		}
		if($compteur!=0){
			return TRUE;
		}else{
			if($add){
				$this->setLock(FALSE);
				$this->add($transition);
				$this->setLock(TRUE);
			}
			return FALSE;
		}
		//return isset($this->names[$transition->getName()]) || parent::has($transition);
	}

	/**
	 * @param Transition
	 * @return bool
	 */
	function hasIdentical(Transition $transition){
		foreach($this->getItems() as $trans){
			if($trans->isIdenticalTo($transition)){
				return TRUE;
			}
		}
		return TRUE;
	}

	/**
	 * @param  State $state
	 * @return TransitionSet
	 */
	function filterByState(State $state)
	{
		$transitions = new TransitionSet;
		foreach($this->getItems() as $transition){
			$transition->getFrom()->isEqualTo($state) && !$transitions->has($transition) && $transitions->add($transition); 
		}
		return $transitions;
		/*return self::loadFilter($this, $state, function (Transition $transition) use ($state) {
			return $transition->getFrom() === $state;
		});*/
	}

	/**
	 * @param  State $state
	 * @return TransitionSet
	 */
	function filterByTo(State $state)
	{
		$transitions = new TransitionSet;
		foreach($this->getItems() as $transition){
			$transition->getTo()->has($state) && !$transitions->has($transition) && $transitions->add($transition); 
		}
		return $transitions;
		/*return self::loadFilter($this, $state, function (Transition $transition) use ($state) {
			return $transition->getFrom() === $state;
		});*/
	}

	/**
	 * @param  Symbol $symbol
	 * @return TransitionSet
	 */
	function filterBySymbol(Symbol $symbol)
	{
		$transitions = new TransitionSet;
		foreach($this->getItems() as $transition){
			$transition->getOn()->isEqualTo($symbol) && !$transitions->has($transition) && $transitions->add($transition); 
		}
		return $transitions;
		/*return self::loadFilter($this, $symbol, function (Transition $transition) use ($symbol) {
			return $transition->getOn() === $symbol;
		});*/
	}

}
