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


class Set implements \Iterator, \Countable
{

	/** @var string */
	private $type;

	/** @var array */
	private $items = array();

	/** @var array */
	private $hashes = array();

	/** @var bool */
	private $locked = FALSE;



	/**
	 * @param  string $type
	 * @param  mixed $items
	 */
	function __construct($type, $items = NULL)
	{
		$this->type = $type;

		if ($items !== NULL) {
			foreach ($items as $item) {
				$this->add($item);
			}
		}
	}



	/**
	 * @param  object $item
	 * @return Set
	 * @throws Exceptions\InvalidItemTypeException
	 */
	function add($item)
	{
		$this->beforeAdd($item);
		$this->items[] = $item;
		$this->hashes[spl_object_hash($item)] = TRUE;
		return $this;
	}



	/**
	 * @param  object $item
	 * @return void
	 * @throws Exceptions\DuplicateItemException
	 */
	function beforeAdd($item)
	{
		$this->checkLock();

		if (!is_object($item) || !$item instanceof $this->type) {
			throw new Exceptions\InvalidItemTypeException;
		}

		if ($this->has($item)) {
			throw new Exceptions\DuplicateItemException;
		}
	}



	/**
	 * @param  object $item
	 * @return bool
	 */
	function has($item)
	{
		return isset($this->hashes[spl_object_hash($item)]);
	}



	/**
	 * @param  Set $set
	 * @return bool
	 */
	function isSubsetOf(Set $set)
	{
		$self = clone $this;
		foreach ($self as $item) {
			if (!$set->has($item)) {
				return FALSE;
			}
		}

		return TRUE;
	}


	/** @return void */
	final function lock()
	{
		if (!$this->locked) {
			$this->beforeLock();
			$this->locked = TRUE;
		}
	}



	/** @return void */
	function beforeLock()
	{}



	/** @return bool */
	final function isLocked()
	{
		return $this->locked;
	}



	/**
	 * @return void
	 * @throws Exceptions\UpdatingLockedSetException
	 */
	final function checkLock()
	{
		if ($this->locked) {
			throw new Exceptions\UpdatingLockedSetException;
		}
	}



	/** @return void */
	function __clone()
	{
		$this->locked = FALSE;
	}



	// === \Iterator ====================================

	/** @return void */
	function rewind()
	{
		reset($this->items);
	}



	/** @return int */
	function key()
	{
		return key($this->items);
	}



	/** @return mixed */
	function current()
	{
		return current($this->items);
	}



	/** @return void */
	function next()
	{
		next($this->items);
	}



	/** @return bool */
	function valid()
	{
		return current($this->items) !== FALSE;
	}



	// === \Countable ====================================

	/** @return int */
	function count()
	{
		return count($this->items);
	}

	/**
	 * @param  Set $set
	 * @param bool
	 * @param bool
	 * @return bool
	 */
	function isEqualTo($set)
	{
		$compteur=0;
		if(count($this)==count($set)){
			foreach($set as $s){
				if($this->has($s)){
					$compteur++;
				}
			}
		}
		if($compteur==count($set)){
			return TRUE;
		}else{
			return FALSE;
		}
		//return $this->isSubsetOf($set) && count($this) === count($set);
	}

	/**
	 * @return Set
	 */
	function getItems(){
		return $this->items;
	}

	/**
	 * @param bool
	 * @return Set
	 */
	function setLock($bool){
		$this->locked = $bool;
	}

	/**
	 * @param StateSet
	 * @return void
	 */
	function replace($set){
		$this->setLock(FALSE);
		$this->items = [];

		foreach($set as $item){
			$this->items[] = $item;
		}
		
		$this->setLock(TRUE);
	}

}
