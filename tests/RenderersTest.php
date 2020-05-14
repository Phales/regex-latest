<?php

use Autto\Automaton;
use Autto\Components\States\State;
use Autto\Components\Alphabet\Symbol;
use Autto\Components\States\StateSet;
use Autto\Components\Alphabet\Alphabet;
use Autto\Components\Transitions\Transition;
use Autto\Components\Transitions\TransitionSet;


class RenderersTest extends PHPUnit_Framework_TestCase
{

	function testAscii()
	{
		$a = new Symbol('a');
		$b = new Symbol('b');
		$c = new Symbol('c');
		$eps = new Symbol(Symbol::EPSILON);

		$alphabet = new Alphabet;
		$alphabet->add($a)->add($b)->add($c)->add($eps);

		$q0 = new State('q0');
		$q1 = new State('q1');
		$q2 = new State('q2');
		$q3 = new State('q3');

		$states = new StateSet;
		$states->add($q0)->add($q1)->add($q2)->add($q3);

		$initials = new StateSet;
		$initials->add($q0);

		$finals = new StateSet;
		$finals->add($q3);

		$trg1 = new StateSet;
		$trg1->add($q1);

		$trg2 = new StateSet;
		$trg2->add($q1);
		$trg2->add($q2);

		$trg3 = new StateSet;
		$trg3->add($q3);

		$transitions = new TransitionSet;
		$transitions->add(new Transition($q0, $trg1, $a));
		$transitions->add(new Transition($q1, $trg2, $b));
		$transitions->add(new Transition($q2, $trg3, $c));
		$transitions->add(new Transition($q0, $trg1, $eps));

		$automaton = new Automaton($states, $alphabet, $transitions, $initials, $finals);

		echo "\n";

		$renderer = new Autto\Renderers\AsciiRenderer;
		$renderer->render($automaton);
	}

}
