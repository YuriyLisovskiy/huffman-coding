<?php

include_once('simple_priority_queue.php');

class MinHeapNode
{
	public $data;
	public $freq;
	public $left;
	public $right;

	function __construct($char, $freq, $left, $right)
	{
		$this->data = $char;
		$this->freq = $freq;
		$this->left = $left;
		$this->right = $right;
	}
}

class HuffmanCode
{
	private $codes = [];
	private $freq = [];
	private $min_heap;

	function __construct()
	{
		$this->min_heap = new SimplePriorityQueue();
	}

	private function store_codes(&$root, string $str) {
		if ($root == null) {
			return;
		}
		if ($root->data != '$') {
			$this->codes[$root->data] = $str;
		}
		$this->store_codes($root->left, $str.'0');
		$this->store_codes($root->right, $str.'1');
	}

	private function calc_freq(string $str) {
		$spl_string = str_split($str);
		foreach ($spl_string as $char) {
			if (!array_key_exists($char, $this->freq)) {
				$this->freq[$char] = 0;
			}
			$this->freq[$char]++;
		}
	}

	public function encode(string $str) {
		$this->calc_freq($str);
		$left = null;
		$right = null;
		$top = null;
		foreach ($this->freq as $key => $value) {
			$this->min_heap->push(new MinHeapNode($key, $value, null, null));
		}
		if ($this->min_heap->size() == 1) {
			throw new Exception('there is no point to compress data, input and output sizes will be the same');
		}
		while ($this->min_heap->size() != 1) {
			$left = $this->min_heap->pop();
			$right = $this->min_heap->pop();
			$top = new MinHeapNode('$', $left->freq + $right->freq, $left, $right);
			$this->min_heap->push($top);
		}
		$root = $this->min_heap->top();
		$this->store_codes($root, '');
		$enc_str = "";
		$spl_string = str_split($str);
		foreach ($spl_string as $char) {
			$enc_str.= strval($this->codes[$char]);
		}
		return $enc_str;
	}

	public function decode(string $str) {
		$ans = '';
		$curr = $this->min_heap->top();
		$spl_string = str_split($str);
		foreach ($spl_string as $char) {
			switch ($char) {
				case '0':
					$curr = $curr->left;
					break;
				case '1':
					$curr = $curr->right;
					break;
				default:
					throw new Exception("not a byte $char");
			}
			if ($curr->left == null && $curr->right == null) {
				$ans.= strval($curr->data);
				$curr = $this->min_heap->top();
			}
		}
		return $ans;
	}

	public function size_of_hex($hex) {
		return ceil((mb_strlen($this->base_convert_arbitrary($hex, 2, 16), '8bit')) / 2);
	}

	public function base_convert_arbitrary($number, $from_base, $to_base) {
		$digits = '0123456789abcdefghijklmnopqrstuvwxyz';
		$length = strlen($number);
		$result = '';
		$nibbles = array();
		for ($i = 0; $i < $length; ++$i) {
			$nibbles[$i] = strpos($digits, $number[$i]);
		}
		do {
			$value = 0;
			$new_len = 0;
			for ($i = 0; $i < $length; ++$i) {
				$value = $value * $from_base + $nibbles[$i];
				if ($value >= $to_base) {
					$nibbles[$new_len++] = (int)($value / $to_base);
					$value %= $to_base;
				}
				else if ($new_len > 0) {
					$nibbles[$new_len++] = 0;
				}
			}
			$length = $new_len;
			$result = $digits[$value].$result;
		}
		while ($new_len != 0);
		return $result;
	}

	public function size_of_string($str) {
		return mb_strlen($str, '8bit');
	}
}
