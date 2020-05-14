<?php

/**
 * This file is part of the Autto library
 *
 * Copyright (c) 2013 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Autto
 */

namespace Autto\Components\Alphabet;

use Autto\Set;
use Autto\Exceptions;


class Alphabet extends Set
{

	/** @var array */
	private $values = array();

	/** @var bool */
	private $hasEpsilon = FALSE;



	/** @param  mixed $items */
	function __construct($items = NULL)
	{
		parent::__construct('Autto\Components\Alphabet\Symbol', $items);
	}



	/**
	 * @param  Symbol $symbol
	 * @return Alphabet
	 */
	function add($symbol)
	{
		parent::add($symbol);
		$this->values[$symbol->getValue()] = TRUE;

		if (!$this->hasEpsilon && $symbol->isEpsilon()) {
			$this->hasEpsilon = TRUE;
		}

		return $this;
	}



	/**
	 * @param  Symbol $symbol
	 * @return void
	 */
	function beforeAdd($symbol)
	{
		parent::beforeAdd($symbol);

		if (isset($this->values[$symbol->getValue()])) {
			throw new Exceptions\DuplicateItemException;
		}
	}



	/**
	 * @param  Symbol $symbol
	 * @return bool
	 */
	function has($symbol)
	{
		return isset($this->values[$symbol->getValue()]) || parent::has($symbol);
	}



	/** @return bool */
	function hasEpsilon()
	{
		return $this->hasEpsilon;
	}

}
