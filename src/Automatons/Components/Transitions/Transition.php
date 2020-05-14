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

use Autto\Components\States\State;
use Autto\Components\States\StateSet;
use Autto\Components\Alphabet\Symbol;


class Transition
{

	/** @var State */
	private $from;

	/** @var StateSet */
	private $to;

	/** @var Symbol */
	private $on;



	/**
	 * @param  State $from
	 * @param  StateSet $to
	 * @param  Symbol $on
	 */
	function __construct(State $from, StateSet $to, Symbol $on)
	{
		$this->from = $from;
		$this->to = $to;
		$this->on = $on;
	}



	/** @return State */
	function getFrom()
	{
		return $this->from;
	}



	/** @return StateSet */
	function getTo()
	{
		return $this->to;
	}



	/** @return Symbol */
	function getOn()
	{
		return $this->on;
	}

	/**
	 * @param Transition
	 * @return void
	 */
	function replace(Transition $transition){
		$this->from = $transition->getFrom();
		$this->to = $transition->getTo();
		$this->on = $transition->getOn();
	}

	/**
	 * @param State
	 * @return void
	 */
	function replaceFrom(State $state){
		$this->from = $state;
	}

	/**
	 * @param StateSet
	 * @return void
	 */
	function replaceTo(StateSet $state){
		$this->to = $state;
	}

	/**
	 * @param Transition
	 * @return bool
	 */
	function isIdenticalTo(Transition $transition){
		$compteur=0;
		foreach($transition->getTo() as $to){
			if($this->to->hasIdentical($to)){
				$compteur++;
			}
		}
		if($compteur==count($transition->getTo())){
			if($this->from->isIdentical($transition->getFrom())){
				return TRUE;
			}
		}
		return FALSE;
	}

}
