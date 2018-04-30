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

    public function __construct()
    {

    }


    /**
     * @param $that array|object the object to get the property from
     * @param string $property_name
     * @param mixed $default_value default value if the property is not found (default is null)
     * @return mixed|null
     */
    public static function getPropertyValueFrom($that, string $property_name, $default_value = null)
    {
        if (is_array($that)) {
            if (array_key_exists($property_name, $that)) {
                $value = $that[$property_name];
            } else {
                $value = $default_value;
            }
        } elseif (is_object($that)) {
            // TODO: check if the property is accessible or not before using
            if (property_exists($that, $property_name)) {
                $value = $that->{$property_name};
            } elseif (method_exists($that, 'get' . $property_name)) {
                $value = $that->{'get' . $property_name}();
            } elseif (method_exists($that, 'get_' . $property_name)) {
                $value = $that->{'get_' . $property_name}();
            } elseif (method_exists($that, $property_name)) {
                $value = $that->{$property_name}();
            } else {
                $value = $default_value;
            }
        } else {
            $value = $default_value;
        }

        return $value;
    }

    /**
     * @param $data
     * @param string|null $varid
     * @param string|null $format
     * @return stdClass
     */
    public static function toObject($data, string $varid = null, string $format = null)
    {
        $result = new \stdClass();

        if (!$data) {
            return $result;
        }

        if (!$varid && !$format) {

            foreach ($data as $datumid => $datumvalue) {
                $result->{$datumid} = $datumvalue;
            }

        } elseif ($varid && !$format) {

            foreach ($data as $datumid => $datumvalue) {
                $id = self::getPropertyValueFrom($datumvalue, $varid, $datumid);
                $result->{$id} = $datumvalue;
            }

        } else {

            $_tokens = self::Tokenize($format);
            $_required_vars = [];
            $_structure = self::BuildStructure($_tokens, $_required_vars);

            if (count($_tokens) == 1) {

                foreach ($data as $datumid => $datumvalue) {
                    $id = self::getPropertyValueFrom($datumvalue, $varid, $datumid);
                    $result->{$id} = $_structure;
                    foreach ($_required_vars as $varname => $var) {
                        $value = self::getPropertyValueFrom($datumvalue, $varname);
                        $var($value, $result->{$id});
                    }
                }

            } else {

                foreach ($data as $datumid => $datumvalue) {
                    $id = self::getPropertyValueFrom($datumvalue, $varid, $datumid);
                    $result->{$id} = clone($_structure);
                    foreach ($datumvalue as $varname => $val) {
                        if (isset($_required_vars[$varname])) {
                            foreach ($_required_vars[$varname] as $var) {
                                $var($val, $result->{$id});
                            }
                        }
                    }
                }

            }

        }

        return $result;
    }


    /**
     * @param string $data data or format to tokenize
     * @param array|null $_errors reference to an array to put errors into
     * @return array
     */
    public static function Tokenize(string $data, array &$_errors = null)
    {
        $_tokens = [];

        if (!$data) {
            return $_tokens;
        }

        $matches    = null;
        $regex      = '%'. implode('|', self::getTokenizerVars()) .'%i';
        $result     = preg_match_all($regex, $data, $matches, PREG_PATTERN_ORDER);

        if ($result === false) {
            if ($_errors) {
                $_errors[] = 'Tokenizer error: '. $result .', '. array_flip(get_defined_constants(true)['pcre'])[preg_last_error()];
            }
            return $_tokens;
        }

        foreach ($matches as $id => $match) {
            if (!is_int($id)) {
                foreach ($match as $match_id => $match_value) {
                    if ($match_value) {
                        $_tokens[$match_id] = [
                            'name'  => $id,
                            'token' => $match_value
                        ];
                    }
                }
            }
        }

        return $_tokens;
    }

    // TODO: add option for array and class tokens
    private static function BuildStructure(array $_tokens, array &$_required_vars, array &$_errors = null)
    {
        $expecting_value = false; // false is varname, true is variable

        $last_token   = '';
        $last_variable_name = '';
        $last_index   = 0;

        $_tokens_count = count($_tokens);

        if ($_tokens_count == 1) {
            $_structure     = '';
        } else {
            $_structure     = new \stdClass();
        }

        for ($i = 0; $i < $_tokens_count; $i++) {
            switch ($_tokens[$i]['name']) {
                case 'NAME':
                    if ($last_token != 'NAME') {

                        if ($expecting_value === false) {

                            if (!isset($_tokens[$i+1]['name']) || $_tokens[$i+1]['name'] == 'DELIMITER') {
                                if ($_tokens_count == 1) {
                                    $_structure = $_tokens[$i]['token'];
                                } else {
                                    $_structure->{$last_index} = $_tokens[$i]['token'];
                                }
                                ++$last_index;
                            } else {
                                $_structure->{$_tokens[$i]['token']} = null;
                                $last_variable_name = $_tokens[$i]['token'];
                            }

                        } else {

                            $_structure->{$last_variable_name} = $_tokens[$i]['token'];
                            $last_variable_name = '';
                        }

                    } else {

                        if ($_errors) {
                            $_errors[] = 'parse error @NAME : 2 consecutive names ! : ' . $_tokens[$i - 1]['token'] . ' | ' . $_tokens[$i]['token'];
                        }

                    }
                    $last_token = 'NAME';
                    break;

                case 'DELIMITER':
                    $expecting_value = false;
                    $last_token = 'DELIMITER';
                    break;

                case 'EQUALS':
                    $expecting_value = true;
                    $last_token = 'EQUALS';
                    break;

                case 'VARIABLE':
                    if ($last_token != 'VARIABLE') {

                        $var = trim($_tokens[$i]['token'], "%");

                        if (!isset($_required_vars[$var])) {
                            if ($_tokens_count !== 1) {
                                $_required_vars[$var] = [];
                            }
                        }

                        if ($expecting_value === false) {

                            // look ahead for the next delimiter or var
                            if (!isset($_tokens[$i+1]['name']) || $_tokens[$i+1]['name'] == 'DELIMITER') {

                                if ($_tokens_count == 1) {
                                    $_structure = $_tokens[$i]['token'];
                                    $_required_vars[$var] = function ($value, &$struct) {
                                        $struct = $value;
                                    };
                                } else {
                                    $_structure->{$last_index} = $_tokens[$i]['token'];
                                    $_required_vars[$var][] = function ($value, &$struct) use ($last_index) {
                                        $struct->{$last_index} = $value;
                                    };
                                }
                                ++$last_index;

                            } else {

                                $_structure->{$_tokens[$i]['token']} = null;
                                $last_variable_name = $_tokens[$i]['token'];
                                $_required_vars[$var][] = function ($value, &$struct) {
                                    $struct->{$value} = null;
                                };

                            }

                        } else {

                            $_structure->{$last_variable_name} = $_tokens[$i]['token'];
                            $_required_vars[$var][] = function ($value, &$struct) use ($last_variable_name) {
                                $struct->{$last_variable_name} = $value;
                            };

                            $last_variable_name = '';
                        }

                    } else {

                        if ($_errors) {
                            $_errors[] = 'parse error @VARIABLE : 2 consecutive variables ! : ' . $_tokens[$i - 1]['token'] . ' | ' . $_tokens[$i]['token'];
                        }

                    }
                    $last_token = 'VARIABLE';
                    break;
            }
        }

        return $_structure;
    }

    private static function getTokenizerVars()
    {
        $patterns[] =
<<<'REGEX'
(?P<NAME>\b[\w\s]+\b)
REGEX;

        $patterns[] =
<<<'REGEX'
(?P<VARIABLE>\%[\w\s]+\%)
REGEX;

        $patterns[] =
<<<'REGEX'
(?P<EQUALS>\={1}\>?)
REGEX;

        $patterns[] =
<<<'REGEX'
(?P<DELIMITER>,)
REGEX;

        return $patterns;
    }


}

