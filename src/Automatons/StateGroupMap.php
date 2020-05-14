<?php

namespace Autto;

use Autto\Components\States\State;


class StateGroupMap implements \ArrayAccess
{

	/** @var array */
	private $map = array();



	/**
	 * @param  State $state
	 * @return bool
	 */
	function offsetExists($state)
	{
		return isset($this->map[spl_object_hash($state)]);
	}



	/**
	 * @param  State $state
	 * @return StateGroup
	 */
	function offsetGet($state)
	{
		return $this->map[spl_object_hash($state)];
	}



	/**
	 * @param  State $state
	 * @param  StateGroup $group
	 * @return void
	 */
	function offsetSet($state, $group)
	{
		$key = spl_object_hash($state);
		if (isset($this->map[$key])) {
			throw new Exceptions\DuplicateItemException;
		}

		$this->map[$key] = $group;
	}



	/** @throws Exceptions\NotSupportedException */
	function offsetUnset($offset)
	{
		throw new Exceptions\NotSupportedException;
	}

}
