<?php

class SimplePriorityQueue
{
	public $values = [];
	private $index = 0;

	public function top() {
		return end($this->values);
	}

	public function pop() {
		$this->index--;
		return array_pop($this->values);
	}

	public function push($data) {
		$this->values[$this->index] = $data;
		$this->index++;
		usort($this->values, function ($a, $b) {
			return $a->freq < $b->freq;
		});
	}

	public function size() {
		return count($this->values);
	}
}
