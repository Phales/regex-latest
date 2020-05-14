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


class SymbolNotFoundException extends \Exception
{

	/**
	 * @param  Symbol $symbol
	 * @param  int $code
	 * @param  Exception $previous
	 */
	function __construct(Symbol $symbol, $code = NULL, $previous = NULL)
	{
		parent::__construct("Symbol '{$symbol->getValue()}' not found.", $code, $previous);
	}

}
