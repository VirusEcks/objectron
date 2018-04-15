<?php
/**
 * Created by PhpStorm.
 * User: Ahmed Salah
 * Date: 4/14/2018
 * Time: 1:16 PM
 */




/**
 * Class Objectron
 * @package VirusEcks\Objectron
 */
final class Objectron
{

    private $_patterns = array();
    private $_length = 0;
    private $_tokens = array();
    private $_tokens_count = 0;
    private $_delimiter = '';
    private $_errors = [];

    private $data = [];
    private $varid = null;
    private $format = null;

    private $_rinput = null;
    private $_structure = null;
    private $_required_vars = [];

    private $result = null;
    private $done = false;


    /**
     * Objectron constructor.
     * @param Mixed $data the array or object to read data from
     * @param string|null $varid the id of each row in the object returned (without the %%)
     * @param string|null $format the format to be applied
     * @example Objectron($array, 'id', 'Student Class=%%class%%, Student Name => %%name%% ,Student group=>[Student Class=%%class%%]');
     */
    public function __construct($data, string $varid = null, string $format = null)
    {
        $this->_delimiter = '/';

        $this->result = new \stdClass();

        $this->data = $data;
        $this->varid = $varid;
        $this->format = $format;
        $this->_rinput = $format;

        $this->initTokenizer();

    }


    /**
     * convert the input to object
     * @return \stdClass
     */
    public function toObject()
    {
        if ($this->done) {
            return $this->result;
        }

        if (!$this->data) {
            $this->done = true;
            return $this->result;
        }

        if (!$this->format && !$this->varid) {
            foreach ($this->data as $datum => $value) {
                $this->result->$datum = $value;
            }
            $this->done = true;
            return $this->result;
        }

        if ($this->varid && !$this->format) {
            foreach ($this->data as $datum => $value) {
                $id = $value[$this->varid] ?? $datum;
                $this->result->$id = $value;
            }
            $this->done = true;
            return $this->result;
        }

        $this->tokenize();
        $this->make_structure();

        if ($this->_tokens_count == 1) {

            foreach ($this->data as $datum => $value) {
                $id = $value[$this->varid] ?? $datum;
                $this->result->$id = $this->_structure;
                foreach ($this->_required_vars as $myvar => $var) {
                    if (is_array($value) && isset($value[$myvar])) {
                        $var($value[$myvar], $this->result->$id);
                    } elseif (is_object($value) && isset($value->$myvar)) {
                        $var($value->$myvar, $this->result->$id);
                    }
                }
            }

        } else {

            foreach ($this->data as $datum => $value) {
                $id = $value[$this->varid] ?? $datum;
                $this->result->$id = clone($this->_structure);
                foreach ($value as $valuen => $valuev) {
                    if (isset($this->_required_vars[$valuen])) {
                        foreach ($this->_required_vars[$valuen] as $var) {
                            $var($valuev, $this->result->$id);
                        }
                    }
                }
            }

        }

        $this->done = true;
        return $this->result;

    }


    private function tokenize()
    {
        $is_match = true;
        $matches = null;

        while ($is_match) {

            $is_match = false;

            if ($this->_rinput !== '') {

                for ($i = 0; $i < $this->_length; $i++) {
                    if ($lasterror = @preg_match($this->_patterns[$i]['regex'], $this->_rinput, $matches)) {
                        ++$this->_tokens_count;
                        $matches[0] = trim($matches[0]);
                        $this->_tokens[] = array('name' => $this->_patterns[$i]['name'], 'token' => $matches[0]);

                        //remove last found token from the $input string
                        //we use preg_quote to escape any regular expression characters in the matched input
                        $this->_rinput = trim(preg_replace($this->_delimiter . "^" . preg_quote($matches[0], $this->_delimiter) . $this->_delimiter, "", $this->_rinput));
                        $is_match = true;

                        continue;
                    } elseif ($lasterror === false) {
                        $this->_errors[] = 'Tokenizing error @pattern:' . $this->_patterns[$i]['name'] . ' string: ' . $this->_rinput;

                        continue;
                    }
                }

            }

        }

    }

    private function make_structure()
    {
        $expecting_value = false; // false is varname, true is variable

        $lasttoken   = '';
        $lastvarname = '';
        $lastindex   = 0;

        $this->_required_vars = [];
        if ($this->_tokens_count == 1) {
            $this->_structure     = '';
        } else {
            $this->_structure     = new \stdClass();
        }

        for ($i = 0; $i < $this->_tokens_count; $i++) {
            switch ($this->_tokens[$i]['name']) {
                case 'NAME':
                    if ($lasttoken != 'NAME') {
                        if ($expecting_value === false) {

                            if (!isset($this->_tokens[$i+1]['name']) || $this->_tokens[$i+1]['name'] == 'DELIMITER') {

                                if ($this->_tokens_count == 1) {
                                    $this->_structure = $this->_tokens[$i]['token'];
                                } else {
                                    $this->_structure->{$lastindex} = $this->_tokens[$i]['token'];
                                }
                                ++$lastindex;

                            } else {

                                $this->_structure->{$this->_tokens[$i]['token']} = null;
                                $lastvarname = $this->_tokens[$i]['token'];

                            }

                        } else {

                            $this->_structure->{$lastvarname} = $this->_tokens[$i]['token'];
                            $lastvarname = '';
                        }
                    } else {
                        $this->_errors[] = 'parse error @NAME : 2 consecutive names ! : ' . $this->_tokens[$i - 1]['token'] . ' | ' . $this->_tokens[$i]['token'];
                    }
                    $lasttoken = 'NAME';
                    break;

                case 'DELIMITER':
                    $expecting_value = false;
                    $lasttoken = 'DELIMITER';
                    break;

                case 'EQUALS':
                    $expecting_value = true;
                    $lasttoken = 'EQUALS';
                    break;

                case 'VARIABLE':
                    if ($lasttoken != 'VARIABLE') {

                        $var = trim($this->_tokens[$i]['token'], "%");

                        if (!isset($this->_required_vars[$var])) {
                            if ($this->_tokens_count !== 1) {
                                $this->_required_vars[$var] = [];
                            }
                        }

                        if ($expecting_value === false) {
                            // look ahead for the next delimiter or var
                            if (!isset($this->_tokens[$i+1]['name']) || $this->_tokens[$i+1]['name'] == 'DELIMITER') {

                                if ($this->_tokens_count == 1) {

                                    $this->_structure = $this->_tokens[$i]['token'];

                                    $this->_required_vars[$var] = function ($value, &$struct) {
                                        $struct = $value;
                                    };

                                } else {

                                    $this->_structure->{$lastindex} = $this->_tokens[$i]['token'];

                                    $this->_required_vars[$var][] = function ($value, &$struct) use ($lastindex) {
                                        $struct->{$lastindex} = $value;
                                    };

                                }

                                ++$lastindex;

                            } else {

                                $this->_structure->{$this->_tokens[$i]['token']} = null;
                                $lastvarname = $this->_tokens[$i]['token'];

                                $this->_required_vars[$var][] = function ($value, &$struct) {
                                    $struct->{$value} = null;
                                };

                            }
                        } else {
                            $this->_structure->{$lastvarname} = $this->_tokens[$i]['token'];

                            $this->_required_vars[$var][] = function ($value, &$struct) use ($lastvarname) {
                                $struct->{$lastvarname} = $value;
                            };

                            $lastvarname = '';
                        }
                    } else {
                        $this->_errors[] = 'parse error @VARIABLE : 2 consecutive variables ! : ' . $this->_tokens[$i - 1]['token'] . ' | ' . $this->_tokens[$i]['token'];
                    }
                    $lasttoken = 'VARIABLE';
                    break;
            }
        }
    }


    /**
     * Add a regular expression to the Tokenizer
     *
     * @param string $name name of the token
     * @param string $pattern the regular expression to match
     */
    private function add($name, $pattern)
    {
        $this->_patterns[$this->_length]['name'] = $name;
        $this->_patterns[$this->_length]['regex'] = $pattern;
        $this->_length++;
    }


    private function initTokenizer()
    {
        $this->add("NAME",
<<<'NAME'
/^[a-zA-Z0-9\s\\]+/
NAME
        );

        $this->add("EQUALS",
<<<'EQUALS'
/^=>?/
EQUALS
        );

        $this->add("VARIABLE",
<<<'VARIABLE'
/^%%[a-zA-Z0-9\s\\]+%%/
VARIABLE
        );

        $this->add("DELIMITER",
<<<'DELIMTER'
/^\s?,\s?/
DELIMTER
        );

    }

    /**
     * @return array
     */
    public function getTokens(): array
    {
        return $this->_tokens;
    }

    /**
     * @return null|\stdClass
     */
    public function getResult(): \stdClass
    {
        return $this->result;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->_errors;
    }


}

