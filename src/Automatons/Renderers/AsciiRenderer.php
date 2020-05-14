<?php

/**
 * This file is part of the Autto library
 *
 * Copyright (c) 2013 Petr Kessler (http://kesspess.1991.cz)
 *
 * @license  MIT
 * @link     https://github.com/uestla/Autto
 */

namespace Autto\Renderers;

use Autto;
use Autto\Components\Alphabet\Symbol;
use Autto\Components\States\StateSet;
use Autto\Components\Transitions\TransitionSet;


class AsciiRenderer implements IRenderer
{

	/** @var Autto\Automaton */
	private $automaton;

	/** @var array */
	private $widths = array(
		'firstColumn' => 0,
		'bodyColumns' => array(),
	);

	const C_PADDING = 2;

	const S_INITIAL = '>';
	const S_FINAL = '<';
	const S_EPSILON = '\eps';
	const S_STATE_SEP = ', ';
	const S_EMPTY_TARGET = '-';

	const ALIGN_CENTER = 0;
	const ALIGN_RIGHT = 1;



	/**
	 * @param  Autto\Automaton $a
	 * @return void
	 */
	function render(Autto\Automaton $a)
	{
		$this->automaton = $a;

		$this->calculateWidths();
		$this->renderHeader();
		$this->renderBody();
		$this->renderFooter();
	}



	/** @return void */
	private function calculateWidths()
	{
		$this->widths['firstColumn'] = static::getMaxStateNameLen($this->automaton->getStates())
				+ strlen(static::S_INITIAL) + strlen(static::S_FINAL) + 1 + static::C_PADDING;

		foreach ($this->automaton->getAlphabet() as $key => $symbol) {
			$this->widths['bodyColumns'][$key] = static::getMaxBodyCellWidth($symbol,
					$this->automaton->getTransitions()->filterBySymbol($symbol)) + static::C_PADDING;
		}
	}



	/** @return void */
	private function renderHeader()
	{
		$this->renderLine();

		echo '|', static::fillCell('', $this->widths['firstColumn']), "|";

		foreach ($this->automaton->getAlphabet() as $key => $symbol) {
			echo static::fillCell(
				$symbol->isEpsilon() ? static::S_EPSILON : $symbol->getValue(),
				$this->widths['bodyColumns'][$key]

			), '|';
		}

		echo "\n";

		$this->renderLine();
	}



	/** @return void */
	private function renderBody()
	{
		foreach ($this->automaton->getStates() as $state) {
			$s = '';
			$padLen = strlen($s) + strlen(static::S_INITIAL) + strlen(static::S_FINAL) + 1;

			$isInitial = $this->automaton->getInitialStates()->has($state);
			if ($isInitial) {
				$s .= static::S_INITIAL;
			}

			if ($this->automaton->getFinalStates()->has($state)) {
				$s .= ($isInitial ? '' : ' ') . static::S_FINAL;
			}

			$s .= ($s === '' ? '' : ' ') . $state->getName();
			$s = str_pad($s, $padLen, ' ', STR_PAD_LEFT);

			echo '|', static::fillCell($s, $this->widths['firstColumn'], static::ALIGN_RIGHT), "|";

			$transitions = $this->automaton->getTransitions()->filterByState($state);
			foreach ($this->automaton->getAlphabet() as $key => $symbol) {
				$names = array();
				foreach ($transitions->filterBySymbol($symbol) as $transition) {
					foreach ($transition->getTo() as $target) {
						$names[] = $target->getName();
					}
				}

				echo static::fillCell(
					count($names) ? implode(static::S_STATE_SEP, $names) : static::S_EMPTY_TARGET,
					$this->widths['bodyColumns'][$key]

				), '|';
			}

			echo "\n";
		}
	}



	/** @return void */
	private function renderFooter()
	{
		$this->renderLine();
		echo "\n";
	}



	/** @return void */
	private function renderLine()
	{
		$line = '+' . str_repeat('-', $this->widths['firstColumn']) . '+';
		foreach ($this->automaton->getAlphabet() as $key => $symbol) {
			$line .= str_repeat('-', $this->widths['bodyColumns'][$key]) . '+';
		}

		echo $line, "\n";
	}



	/**
	 * @param  StateSet $states
	 * @return int
	 */
	static function getMaxStateNameLen(StateSet $states)
	{
		$max = 0;
		foreach ($states as $state) {
			$len = strlen($state->getName());
			if ($len > $max) {
				$max = $len;
			}
		}

		return $max;
	}



	/**
	 * @param  Symbol $symbol
	 * @param  TransitionSet $transitions
	 * @return int
	 */
	static function getMaxBodyCellWidth(Symbol $symbol, TransitionSet $transitions)
	{
		$max = strlen($symbol->isEpsilon() ? static::S_EPSILON : $symbol->getValue());

		foreach ($transitions as $transition) {
			$tmp = 0;
			foreach ($transition->getTo() as $state) {
				$tmp += strlen($state->getName()) + strlen(static::S_STATE_SEP);
			}

			$tmp -= strlen(static::S_STATE_SEP);
			if ($tmp > $max) {
				$max = $tmp;
			}
		}

		return $max;
	}



	/**
	 * @param  string $s
	 * @param  int $width
	 * @param  int $align
	 * @return string
	 */
	static function fillCell($s, $width, $align = self::ALIGN_CENTER)
	{
		$len = strlen($s);
		if ($align === static::ALIGN_RIGHT) {
			$left = $width - $len - 1;
			$right = 1;

		} else {
			$tmp = ($width - $len) / 2;
			$left = floor($tmp);
			$right = ceil($tmp);
		}

		return str_repeat(' ', $left) . $s . str_repeat(' ', $right);
	}

}
