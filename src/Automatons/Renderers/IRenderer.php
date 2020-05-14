<?php

/**
 * This file is part of the Autto library
 *
 * Copyright (c) 2013 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Autto
 */

namespace Autto\Renderers;

use Autto;


interface IRenderer
{

	/**
	 * @param  Autto\Automaton $a
	 * @return void
	 */
	function render(Autto\Automaton $a);

}
