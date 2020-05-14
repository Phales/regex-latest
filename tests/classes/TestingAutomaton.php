<?php

use Autto\Components\States\State;
use Autto\Components\States\StateSet;
use Autto\Components\Alphabet\Symbol;
use Autto\Components\Alphabet\Alphabet;
use Autto\Components\Transitions\Transition;
use Autto\Components\Transitions\TransitionSet;


class TestingAutomaton extends Autto\Automaton
{

	function __construct()
	{
		$a = new Symbol('a');
		$b = new Symbol('b');
		$c = new Symbol('c');
		$eps = new Symbol(Symbol::EPSILON);

		$alphabet = new Alphabet;
		$alphabet->add($a)->add($b)->add($c)->add($eps);


		$A = new State('A');
		$B = new State('B');
		$C = new State('C');

		$states = new StateSet;
		$states->add($A)->add($B)->add($C);


		$dAaS = new StateSet;
		$dAaS->add($B);

		$dAcS = new StateSet;
		$dAcS->add($A)->add($C);

		$dBaS = new StateSet;
		$dBaS->add($A);

		$dBbS = new StateSet;
		$dBbS->add($A);

		$dBcS = new StateSet;
		$dBcS->add($A)->add($B);

		$dBepsS = new StateSet;
		$dBepsS->add($A);

		$dCaS = new StateSet;
		$dCaS->add($C);

		$dCbS = new StateSet;
		$dCbS->add($C);

		$dCcS = new StateSet;
		$dCcS->add($C);


		$transitions = new TransitionSet;

		$transitions->add(new Transition($A, $dAaS, $a));
		$transitions->add(new Transition($A, $dAcS, $c));

		$transitions->add(new Transition($B, $dBaS, $a));
		$transitions->add(new Transition($B, $dBbS, $b));
		$transitions->add(new Transition($B, $dBcS, $c));
		$transitions->add(new Transition($B, $dBepsS, $eps));

		$transitions->add(new Transition($C, $dCaS, $a));
		$transitions->add(new Transition($C, $dCbS, $b));
		$transitions->add(new Transition($C, $dCcS, $c));


		$initials = new StateSet;
		$initials->add($A);

		$finals = new StateSet;
		$finals->add($A)->add($C);

		parent::__construct($states, $alphabet, $transitions, $initials, $finals);
	}

}
