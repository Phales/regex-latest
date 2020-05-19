<?php

/**
 * This file is part of the Autto library
 *
 * Copyright (c) 2020 Mbola Raharison (https://mbolaraharison.me)
 *
 * Polytech Angers
 * 
 */

namespace Autto\Components\Transitions;

use Autto\Components\States\State;
use Autto\Components\Alphabet\Symbol;


class SingleTransition extends Transition
{
	/**
	 * @param  State $from
	 * @param  State $to
	 * @param  Symbol $on
	 */
	function __construct(State $from, State $to, Symbol $on)
	{
        parent::__construct($from, $to, $on);
		/*$this->from = $from;
		$this->to = $to;
		$this->on = $on;*/
	}

}
