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

$tempalte = '%%id%%, Student Class=%%class%%, Name => %%name%% ,Student group=>%%id%%'

$obj1 = new Objectron($arr, 'id', $template);
$result1 = $obj1->toObject();

print_r($result1);

/*
(
    [1] => stdClass Object
        (
            [0] => 1
            [Student Class] => 10
            [Name] => john
            [Student group] => 1
        )

    [5] => stdClass Object
        (
            [0] => 5
            [Student Class] => 4
            [Name] => clam
            [Student group] => 5
        )

    [8] => stdClass Object
        (
            [0] => 8
            [Student Class] => 1
            [Name] => robot
            [Student group] => 8
        )

)
*/
```


_For more examples and usage, please refer to the [Wiki][wiki]._

## Development

This project was made as Proof of Work for some certain Person who thinks that it's useless and undoable and if it was done it would be totally slow.

this was made in 1 day during my free time so it might need lots of modifications and testing,
i'll be using it in a project so that would be good testing ground for it.



## Release History


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
