<?php

/**
 * This file is part of the Autto library
 *
 * Copyright (c) 2013 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Autto
 */

namespace Autto\Exceptions;


class StateNotFoundException extends \Exception
{

	/**
	 * @param  State $state
	 * @param  int $code
	 * @param  Exception $previous
	 */
	function __construct(State $state, $code = NULL, $previous = NULL)
	{
		parent::__construct("State '{$state->getName()}' not found.", $code, $previous);
	}

}
