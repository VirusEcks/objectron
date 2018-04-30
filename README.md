# Objectron
> Objectron uses string template to return a designed class.


instead of using 30 different model to modify data before using it in your project use 1 base model with different string (template) to modify the output


## Installation

Composer:

```sh
VirusEcks/Objectron
```


## Usage example


```sh
$arr[] = ['id'=> 1, 'name'=>'john', 'class'=>10];
$arr[] = ['id'=> 5, 'name'=>'clam', 'class'=>4];
$arr[] = ['id'=> 8, 'name'=>'robot', 'class'=>1];


$result1 = Objectron::toObject($arr, 'id', '%id%, Student Class=%class%, Student Name => %name% ,Student group=>%id%');
$result2 = Objectron::toObject($arr, 'name', '%id%, %class%, %name%');
$result3 = Objectron::toObject($arr, 'id', '%name%');
$result4 = Objectron::toObject($arr, 'id');
$result5 = Objectron::toObject($arr);


print_r($result1);
print_r($result2);
print_r($result3);
print_r($result4);
print_r($result5);


$result1 =
            stdClass Object
            (
                [1] => stdClass Object
                    (
                        [0] => 1
                        [Student Class] => 10
                        [Student Name] => john
                        [Student group] => 1
                    )
            
                [5] => stdClass Object
                    (
                        [0] => 5
                        [Student Class] => 4
                        [Student Name] => clam
                        [Student group] => 5
                    )
            
                [8] => stdClass Object
                    (
                        [0] => 8
                        [Student Class] => 1
                        [Student Name] => robot
                        [Student group] => 8
                    )
            
            )
            
$result2 =
            stdClass Object
            (
                [john] => stdClass Object
                    (
                        [0] => 1
                        [1] => 10
                        [2] => john
                    )
            
                [clam] => stdClass Object
                    (
                        [0] => 5
                        [1] => 4
                        [2] => clam
                    )
            
                [robot] => stdClass Object
                    (
                        [0] => 8
                        [1] => 1
                        [2] => robot
                    )
            
            )
            
$result3 =
            stdClass Object
            (
                [1] => john
                [5] => clam
                [8] => robot
            )
            
$result4 =
            stdClass Object
            (
                [1] => Array
                    (
                        [id] => 1
                        [name] => john
                        [class] => 10
                    )
            
                [5] => Array
                    (
                        [id] => 5
                        [name] => clam
                        [class] => 4
                    )
            
                [8] => Array
                    (
                        [id] => 8
                        [name] => robot
                        [class] => 1
                    )
            
            )
            
$result5 =
            stdClass Object
            (
                [0] => Array
                    (
                        [id] => 1
                        [name] => john
                        [class] => 10
                    )
            
                [1] => Array
                    (
                        [id] => 5
                        [name] => clam
                        [class] => 4
                    )
            
                [2] => Array
                    (
                        [id] => 8
                        [name] => robot
                        [class] => 1
                    )
            
            )

```


_For more examples and usage, please refer to the [Wiki][wiki]._

## Development

This project was made as Proof of Work for some certain Person who thinks that it's useless and undoable and if it was done it would be totally slow.

this was made in 1 day during my free time so it might need lots of modifications and testing,
i'll be using it in a project so that would be good testing ground for it.



## Release History

* 0.0.4-alpha
    + added option to use tokenizer
    * changed the call style to static function
    * FASTER performance -> 260% faster performance on same data and formatting from last version
    + added some generic tests (no unit test yet)
    + added better property value finder (can be used outside of project)
    - removed some of the unneeded code
    * changed the regex to perform better on nonuniform formatting
* 0.0.3-alpha
    * changed the layout and namespace
* 0.0.2-alpha
    * Added some adjustments and fixes
* 0.0.1-alpha
    * Initial release and WIP

## Meta

Ahmed Salah – [@virusecks](https://twitter.com/virusecks) – virus.ecks@gmail.com

Distributed under the MIT license. See ``LICENSE`` for more information.

[https://github.com/virusecks/objectron](https://github.com/virusecks/objectron)

## Contributing

1. Fork it (<https://github.com/virusecks/objectron/fork>)
2. Create your feature branch (`git checkout -b feature/fooBar`)
3. Commit your changes (`git commit -am 'Add some fooBar'`)
4. Push to the branch (`git push origin feature/fooBar`)
5. Create a new Pull Request

<!-- Markdown link & img dfn's -->
[npm-image]: https://img.shields.io/npm/v/datadog-metrics.svg?style=flat-square
[npm-url]: https://npmjs.org/package/datadog-metrics
[npm-downloads]: https://img.shields.io/npm/dm/datadog-metrics.svg?style=flat-square
[travis-image]: https://img.shields.io/travis/dbader/node-datadog-metrics/master.svg?style=flat-square
[travis-url]: https://travis-ci.org/dbader/node-datadog-metrics
[wiki]: https://github.com/virusecks/objectron/wiki
