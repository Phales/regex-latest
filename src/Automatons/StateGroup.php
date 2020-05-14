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
use Autto\Components\Transitions\TransitionSet;


class StateGroup
{

	/** @var string */
	private $name;

	/** @var StateSet */
	private $states;

	/** @var State */
	private $stateMorph;

	/** @var TransitionSet */
	private $transitions = NULL;



	/**
	 * @param  string $name
	 * @param  StateSet $states
	 */
	function __construct($name, StateSet $states)
	{
		$states->lock();

		$this->name = (string) $name;
		$this->states = $states;
		$this->stateMorph = new State($name);
	}



	/** @return string */
	function getName()
	{
		return $this->name;
	}



	/** @return StateSet */
	function getStates()
	{
		return $this->states;
	}



	/**
	 * @param  State $state
	 * @return bool
	 */
	function hasState(State $state)
	{
		return $this->states->has($state);
	}



	/** @return TransitionSet */
	function getTransitions()
	{
		return $this->transitions;
	}



	/**
	 * @param  TransitionSet
	 * @return StateGroup
	 */
	function setTransitions(TransitionSet $transitions)
	{
		if ($this->transitions !== NULL) {
			throw new Exceptions\TransitionsAlreadySetException;
		}

		$transitions->lock();
		$this->transitions = $transitions;
		return $this;
	}



	/** @return State */
	function toState()
	{
		return $this->stateMorph;
	}

}
