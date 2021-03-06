<?php

/**
 * This file is part of the Autto library
 *
 * Copyright (c) 2013 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Autto
 */

namespace Autto\Components\States;

use Autto\Set;
use Autto\Exceptions;
use Autto\Components\States\State;
use Autto\Utils\Helpers;


class StateSet extends Set
{

	/** @var array */
	private $names = array();

	/** @var int */
	private $num_state = 0;

	/** @param  mixed $items */
	function __construct($items = NULL)
	{
		parent::__construct('Autto\Components\States\State', $items);
	}



	/**
	 * @param  State $item
	 * @return StateSet
	 */
	function add($item)
	{
		parent::add($item);
		$this->names[$item->getName()] = TRUE;
		return $this;
	}



	/**
	 * @param  State $state
	 * @return void
	 * @throws Exceptions\DuplicateItemException
	 */
	function beforeAdd($state)
	{
		parent::beforeAdd($state);

		if (isset($this->names[$state->getName()])) {
			throw new Exceptions\DuplicateItemException;
		}
	}



	/**
	 * @param  string $name
	 * @return State|NULL
	 */
	function getByName($name)
	{
		$tmp_state = new State($name);
		foreach ($this as $state) {
			$new_state = Helpers::assembleUnique($tmp_state, $state);

			if ($tmp_state->isEqualTo($state)) {
				//var_dump("Hello");
				return $state;
			}
		}
		return NULL;
	}

	/**
	 * @param  string $name
	 * @return State|NULL
	 */
	function getByRealName($name)
	{
		$tmp_state = new State($name);
		foreach ($this as $state) {
			//$new_state = Helpers::assembleUnique($state, $tmp_state);
			$set = new StateSet(array($tmp_state));
			if ($set->has($state)){
				var_dump($tmp_state);
				return $state;
			}
		}
		return NULL;
	}

	/**
	 * @param string
	 * @param string
	 * @return StateSet //$this
	 */
	function replaceName($str, $replace){
		$str_splitee = preg_split("/(,|{|})+/", trim($str));
		$replace_splitee = preg_split("/(,|{|})+/", trim($replace));
		if(count(array_filter($replace_splitee, function($x) { return !empty($x); }))>=count(array_filter($str_splitee, function($x) { return !empty($x); }))){
			//$this->setLock(FALSE);
			unset($this->names[$str]);
			$this->names[$replace] = TRUE;
			//$this->setLock(TRUE);
		}
		return $this;
	}

	/**
	 * @param  State $state
	 * @param bool
	 * @param bool
	 * @return bool
	 */
	function has($state, $replace=FALSE, $add=FALSE)
	{
		if(!is_null($state)){
			$items = $this->getItems();
			$compteur = 0;
			foreach($items as $s){
				$str = $s->getName();
				if($s->isEqualTo($state, $replace)){
					if($replace){
						$tmp = Helpers::assembleUnique($s, $state);
						$this->replaceName($str, $tmp->getName());
					} 
					$compteur++;
				}
			}
			if($compteur!=0){
				return TRUE;
			}else{
				if($add){
					$this->setLock(FALSE);
					$this->add($state);
					$this->setLock(TRUE);
					return TRUE;
				}else{
					return FALSE;
				}
			}
		}
		return FALSE;
		//return isset($this->names[$state->getName()]) || parent::has($state);
	}

	/**
	 * @param State
	 */
	function hasIdentical(State $state){
		foreach($this->getItems() as $s){
			if($s->isIdenticalTo($state)){
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * @return State
	 */
	function newState(){
		$q = new State("q".$this->num_state);
		$this->num_state++;
		while($this->has($q)){
			$q = new State("q".$this->num_state);
			$this->num_state++;
		}
		//$this->setLock(FALSE);
		$this->add($q);
		//$this->setLock(TRUE);
		return $q;
	}

	/**
	 * @return int
	 */
	function lastNumState(){
		return $this->num_state;
	}

	/**
	 * @param  StateSet $set
	 * @param bool
	 * @param bool
	 * @return bool
	 */
	function isEqualTo($set, $replace_items=FALSE)
	{
		$compteur=0;
		if(count($this)==count($set)){
			foreach($set as $s){
				if($this->has($s, $replace_items)){
					$compteur++;
				}
			}
		}
		if($compteur!=0){
			return TRUE;
		}else{
			return FALSE;
		}
		//return $this->isSubsetOf($set) && count($this) === count($set);
	}

}
