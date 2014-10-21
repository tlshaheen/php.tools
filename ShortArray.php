<?php
/**
 * From PHP-CS-Fixer
 */
class ShortArray extends FormatterPass {
	const FOUND_ARRAY = 'array';
	const FOUND_PARENTHESES = 'paren';
	public function format($source) {
		$this->tkns = token_get_all($source);
		$this->code = '';
		$found_paren = [];

		while (list($index, $token) = each($this->tkns)) {
			list($id, $text) = $this->get_token($token);
			$this->ptr = $index;
			switch ($id) {
				case T_ARRAY:
					if ($this->is_token([ST_PARENTHESES_OPEN])) {
						$found_paren[] = self::FOUND_ARRAY;
						while (list($index, $token) = each($this->tkns)) {
							list($id, $text) = $this->get_token($token);
							$this->ptr = $index;
							if (ST_PARENTHESES_OPEN == $id) {
								$this->append_code('[', false);
								break;
							}
						}
						break;
					}
				case ST_PARENTHESES_OPEN:
					$found_paren[] = self::FOUND_PARENTHESES;
					$this->append_code($text, false);
					break;

				case ST_PARENTHESES_CLOSE:
					$pop_token = array_pop($found_paren);
					if (self::FOUND_ARRAY == $pop_token) {
						$this->append_code(']', false);
						break;
					}
				default:
					$this->append_code($text, false);
					break;
			}
		}

		return $this->code;
	}
}