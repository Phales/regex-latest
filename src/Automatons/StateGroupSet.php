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


class StateGroupSet extends Set
{

	/** @var StateGroupMap */
	private $stateMap = NULL;



	/** @param  mixed $items */
	function __construct($items = NULL)
	{
		parent::__construct('Autto\StateGroup', $items);
	}



	/** @return void */
	function beforeLock()
	{
		parent::beforeLock();

		$this->stateMap = new StateGroupMap;
		foreach ($this as $group) {
			foreach ($group->getStates() as $state) {
				$this->stateMap[$state] = $group;
			}
		}
	}



	/**
	 * @param  State $state
	 * @return StateGroup
	 */
	function getStateGroup(State $state)
	{
		return $this->stateMap[$state];
	}

}
