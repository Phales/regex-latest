<?php

use Autto\Automaton;
use Autto\Components\States\State;
use Autto\Components\Alphabet\Symbol;
use Autto\Components\States\StateSet;
use Autto\Components\Alphabet\Alphabet;
use Autto\Exceptions\EmptySetException;
use Autto\Exceptions\InvalidSetException;
use Autto\Components\Transitions\Transition;
use Autto\Components\Transitions\TransitionSet;


class AutomatonTest extends PHPUnit_Framework_TestCase
{

	function testCreationFail()
	{
		try {
			new Automaton(new StateSet, new Alphabet, new TransitionSet, new StateSet, new StateSet);

		} catch (EmptySetException $e) {}


		try {
			$a = new Symbol('a');
			$alphabet = new Alphabet;
			$alphabet->add($a);

			$q0 = new State('q0');
			$states = new StateSet;
			$states->add($q0);

			$initials = new StateSet;
			$initials->add(new State('q1'));

			$target = new StateSet;
			$target->add($q0);

			$t = new Transition($q0, $target, $a);
			$transitions = new TransitionSet;
			$transitions->add($t);

			new Automaton($states, $alphabet, $transitions, $initials, new StateSet);
			$this->fail();

		} catch (InvalidSetException $e) {}
	}



	function testEpsilonClosure()
	{
		$automaton = new TestingAutomaton;
		foreach ($automaton->getStates() as $state) {
			if ($state->getName() === 'B') {
				break;
			}
		}

		$this->assertEquals(2, count(\Autto\Utils\Helpers::epsilonClosure($automaton, $state)));
	}



	function testRemoveEpsilon()
	{
		$renderer = new Autto\Renderers\AsciiRenderer;

		$automaton = new TestingAutomaton;
		$renderer->render($automaton);

		$this->assertTrue($automaton->getAlphabet()->hasEpsilon());

		$automaton->removeEpsilon();
		$renderer->render($automaton);

		$this->assertFalse($automaton->getAlphabet()->hasEpsilon());
	}



	function testDeterminization()
	{
		$renderer = new Autto\Renderers\AsciiRenderer;

		$automaton = new TestingAutomaton;
		$renderer->render($automaton);

		$automaton->determinize();
		$renderer->render($automaton);
	}



	function testMinimization()
	{
		$renderer = new Autto\Renderers\AsciiRenderer;

		$automaton = new TestingAutomaton;
		$renderer->render($automaton);

		$automaton->minimize();
		$renderer->render($automaton);
	}

}
