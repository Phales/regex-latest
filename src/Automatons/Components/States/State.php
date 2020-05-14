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

use Autto\Exceptions;
use Autto\Utils\Helpers;


class State
{

	/** @var string */
	private $name;



	/** @param  string $name */
	function __construct($name)
	{
		if (preg_match('#\s#', $name)) {
			throw new Exceptions\InvalidStateNameException('State name must not containt white characters.');
		}

		$this->name = (string) $name;
	}



	/** @return string */
	function getName()
	{
		return $this->name;
	}

	/**
	 * @param State
	 * @param bool
	 * @param bool
	 * @return bool
	 */
	function isEqualTo(State $state, $replace=FALSE){
		if(is_null($state)){
			return FALSE;
		}
		$str_splitee_1 = preg_split("/(,|{|})+/", trim($this->name));
		$str_splitee_1 = array_filter($str_splitee_1, function($x) { return !empty($x); });
		$str_splitee_2 = preg_split("/(,|{|})+/", trim($state->getName()));
		$str_splitee_2 = array_filter($str_splitee_2, function($x) { return !empty($x); });
		$compteur=0;
		foreach($str_splitee_1 as $str1){
			foreach($str_splitee_2 as $str2){
				if(strcmp($str1, $str2)==0){
					$compteur++;
				}
			}
		}
		if($compteur!=0){
			if($replace){
				if(count($str_splitee_2)>=count($str_splitee_1)){
					$q = Helpers::assembleUnique($this, $state);
					$this->name = $q->getName();
					return TRUE;
				}else{
					return TRUE;
				}
			}else{
				return TRUE;
			}
		}else{
			if(count($str_splitee_1)==0 && count($str_splitee_2)==0){
				return TRUE;
			}else{
				return FALSE;
			}
		}
	}

	/**
	 * @param State
	 * @return bool
	 */
	function isIdenticalTo(State $state){
		if(strcmp($this->name, $state->getName())==0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	/**
	 * @return bool
	 */
	function isNameEmpty(){
		if(strcmp($this->name, "{}")==0 || strcmp($this->name, "")==0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

}
