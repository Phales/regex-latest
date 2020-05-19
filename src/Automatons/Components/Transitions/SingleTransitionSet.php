<?php

/**
 * This file is part of the Autto library
 *
 * Copyright (c) 2020 Mbola Raharison (https://mbolaraharison.me)
 *
 * Polytech Angers
 */

namespace Autto\Components\Transitions;

use Autto\Set;
use Autto\Components\States\State;
use Autto\Components\Alphabet\Symbol;
use Autto\Components\States\StateSet;


class SingleTransitionSet extends TransitionSet
{

	/**
	 * @param  SingleTransition $item
	 * @return SingleTransitionSet
	 */
	public function add($item)
	{
		parent::add($item);
		return $this;
	}

}
