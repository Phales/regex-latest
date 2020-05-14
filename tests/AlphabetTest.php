<?php

use Autto\Components\Alphabet\Symbol;
use Autto\Components\Alphabet\Alphabet;


class AlphabetTest extends PHPUnit_Framework_TestCase
{

	function testDuplication()
	{
		$a = new Symbol('a');
		$b = new Symbol('a');

		$alphabet = new Alphabet;

		try {
			$alphabet->add($a);
			$alphabet->add($a);
			$this->fail();

		} catch (Autto\Exceptions\DuplicateItemException $e) {}


		try {
			$alphabet->add($a);
			$alphabet->add($b);
			$this->fail();

		} catch (Autto\Exceptions\DuplicateItemException $e) {}
	}

}
