<?php

use Autto\Components\States\State;
use Autto\Components\States\StateSet;
use Autto\Components\Alphabet\Symbol;
use Autto\Components\Transitions\Transition;
use Autto\Components\Transitions\TransitionSet;


class TransitionSetTest extends PHPUnit_Framework_TestCase
{

	function testFiltering()
	{
		$transitions = new TransitionSet;

		$a = new Symbol('a');
		$b = new Symbol('b');
		$c = new Symbol('c');
		$d = new Symbol('d');

		$q0 = new State('q0');
		$q1 = new State('q1');
		$q2 = new State('q2');
		$q3 = new State('q3');
		$q4 = new State('q4');

		$tg0 = new StateSet;
		$tg0->add($q0)->add($q1);

		$tg1 = new StateSet;
		$tg1->add($q1);

		$tg2 = new StateSet;
		$tg2->add($q2);

		$tg3 = new StateSet;
		$tg3->add($q3);

		$t1 = new Transition($q0, $tg0, $a);
		$t2 = new Transition($q3, $tg1, $b);
		$t3 = new Transition($q2, $tg2, $c);
		$t4 = new Transition($q3, $tg3, $a);

		$transitions->add($t1)->add($t2)->add($t3)->add($t4);

		$this->assertEquals(2, count($transitions->filterByState($q3)));
		$this->assertEquals(1, count($transitions->filterByState($q2)));
		$this->assertEquals(0, count($transitions->filterByState($q1)));

		$this->assertTrue($transitions->filterByState($q3)->has($t4));
		$this->assertTrue($transitions->filterByState($q3)->has($t2));
		$this->assertFalse($transitions->filterByState($q3)->has($t1));

		$this->assertEquals(2, count($transitions->filterBySymbol($a)));
		$this->assertEquals(1, count($transitions->filterBySymbol($b)));
		$this->assertEquals(0, count($transitions->filterBySymbol($d)));

		$this->assertTrue($transitions->filterBySymbol($a)->has($t1));
		$this->assertTrue($transitions->filterBySymbol($a)->has($t4));
		$this->assertFalse($transitions->filterBySymbol($a)->has($t2));
	}

}
