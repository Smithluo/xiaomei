<?php

namespace Tale\Test\Jade;

use Tale\Jade\Compiler;
use Tale\Jade\Renderer;

class LoopTest extends \PHPUnit_Framework_TestCase
{

    /** @var \Tale\Jade\Renderer */
    private $renderer;

    public function setUp()
    {

        $this->renderer = new Renderer([
            'adapterOptions' => [
                'path' => __DIR__.'/cache/loops'
            ],
            'pretty' => false,
            'paths' => [__DIR__.'/views/loops']
        ]);
    }

    /**
     * @dataProvider arrayValueProvider
     */
    public function testEach($array, $expected)
    {

        $this->assertEquals($expected, $this->renderer->render('each', ['array' => $array]));
    }

    public function arrayValueProvider()
    {

        return [
            [range('a', 'z'), '<p>1 The value is a</p><p>1 The value is b</p><p>1 The value is c</p><p>1 The value is d</p><p>1 The value is e</p><p>1 The value is f</p><p>1 The value is g</p><p>1 The value is h</p><p>1 The value is i</p><p>1 The value is j</p><p>1 The value is k</p><p>1 The value is l</p><p>1 The value is m</p><p>1 The value is n</p><p>1 The value is o</p><p>1 The value is p</p><p>1 The value is q</p><p>1 The value is r</p><p>1 The value is s</p><p>1 The value is t</p><p>1 The value is u</p><p>1 The value is v</p><p>1 The value is w</p><p>1 The value is x</p><p>1 The value is y</p><p>1 The value is z</p><p>2 The value is a</p><p>2 The value is b</p><p>2 The value is c</p><p>2 The value is d</p><p>2 The value is e</p><p>2 The value is f</p><p>2 The value is g</p><p>2 The value is h</p><p>2 The value is i</p><p>2 The value is j</p><p>2 The value is k</p><p>2 The value is l</p><p>2 The value is m</p><p>2 The value is n</p><p>2 The value is o</p><p>2 The value is p</p><p>2 The value is q</p><p>2 The value is r</p><p>2 The value is s</p><p>2 The value is t</p><p>2 The value is u</p><p>2 The value is v</p><p>2 The value is w</p><p>2 The value is x</p><p>2 The value is y</p><p>2 The value is z</p><p>3 The value is a, the key is 0</p><p>3 The value is b, the key is 1</p><p>3 The value is c, the key is 2</p><p>3 The value is d, the key is 3</p><p>3 The value is e, the key is 4</p><p>3 The value is f, the key is 5</p><p>3 The value is g, the key is 6</p><p>3 The value is h, the key is 7</p><p>3 The value is i, the key is 8</p><p>3 The value is j, the key is 9</p><p>3 The value is k, the key is 10</p><p>3 The value is l, the key is 11</p><p>3 The value is m, the key is 12</p><p>3 The value is n, the key is 13</p><p>3 The value is o, the key is 14</p><p>3 The value is p, the key is 15</p><p>3 The value is q, the key is 16</p><p>3 The value is r, the key is 17</p><p>3 The value is s, the key is 18</p><p>3 The value is t, the key is 19</p><p>3 The value is u, the key is 20</p><p>3 The value is v, the key is 21</p><p>3 The value is w, the key is 22</p><p>3 The value is x, the key is 23</p><p>3 The value is y, the key is 24</p><p>3 The value is z, the key is 25</p><p>4 The value is a, the key is 0</p><p>4 The value is b, the key is 1</p><p>4 The value is c, the key is 2</p><p>4 The value is d, the key is 3</p><p>4 The value is e, the key is 4</p><p>4 The value is f, the key is 5</p><p>4 The value is g, the key is 6</p><p>4 The value is h, the key is 7</p><p>4 The value is i, the key is 8</p><p>4 The value is j, the key is 9</p><p>4 The value is k, the key is 10</p><p>4 The value is l, the key is 11</p><p>4 The value is m, the key is 12</p><p>4 The value is n, the key is 13</p><p>4 The value is o, the key is 14</p><p>4 The value is p, the key is 15</p><p>4 The value is q, the key is 16</p><p>4 The value is r, the key is 17</p><p>4 The value is s, the key is 18</p><p>4 The value is t, the key is 19</p><p>4 The value is u, the key is 20</p><p>4 The value is v, the key is 21</p><p>4 The value is w, the key is 22</p><p>4 The value is x, the key is 23</p><p>4 The value is y, the key is 24</p><p>4 The value is z, the key is 25</p>'],
            [range(0, 25), '<p>1 The value is 0</p><p>1 The value is 1</p><p>1 The value is 2</p><p>1 The value is 3</p><p>1 The value is 4</p><p>1 The value is 5</p><p>1 The value is 6</p><p>1 The value is 7</p><p>1 The value is 8</p><p>1 The value is 9</p><p>1 The value is 10</p><p>1 The value is 11</p><p>1 The value is 12</p><p>1 The value is 13</p><p>1 The value is 14</p><p>1 The value is 15</p><p>1 The value is 16</p><p>1 The value is 17</p><p>1 The value is 18</p><p>1 The value is 19</p><p>1 The value is 20</p><p>1 The value is 21</p><p>1 The value is 22</p><p>1 The value is 23</p><p>1 The value is 24</p><p>1 The value is 25</p><p>2 The value is 0</p><p>2 The value is 1</p><p>2 The value is 2</p><p>2 The value is 3</p><p>2 The value is 4</p><p>2 The value is 5</p><p>2 The value is 6</p><p>2 The value is 7</p><p>2 The value is 8</p><p>2 The value is 9</p><p>2 The value is 10</p><p>2 The value is 11</p><p>2 The value is 12</p><p>2 The value is 13</p><p>2 The value is 14</p><p>2 The value is 15</p><p>2 The value is 16</p><p>2 The value is 17</p><p>2 The value is 18</p><p>2 The value is 19</p><p>2 The value is 20</p><p>2 The value is 21</p><p>2 The value is 22</p><p>2 The value is 23</p><p>2 The value is 24</p><p>2 The value is 25</p><p>3 The value is 0, the key is 0</p><p>3 The value is 1, the key is 1</p><p>3 The value is 2, the key is 2</p><p>3 The value is 3, the key is 3</p><p>3 The value is 4, the key is 4</p><p>3 The value is 5, the key is 5</p><p>3 The value is 6, the key is 6</p><p>3 The value is 7, the key is 7</p><p>3 The value is 8, the key is 8</p><p>3 The value is 9, the key is 9</p><p>3 The value is 10, the key is 10</p><p>3 The value is 11, the key is 11</p><p>3 The value is 12, the key is 12</p><p>3 The value is 13, the key is 13</p><p>3 The value is 14, the key is 14</p><p>3 The value is 15, the key is 15</p><p>3 The value is 16, the key is 16</p><p>3 The value is 17, the key is 17</p><p>3 The value is 18, the key is 18</p><p>3 The value is 19, the key is 19</p><p>3 The value is 20, the key is 20</p><p>3 The value is 21, the key is 21</p><p>3 The value is 22, the key is 22</p><p>3 The value is 23, the key is 23</p><p>3 The value is 24, the key is 24</p><p>3 The value is 25, the key is 25</p><p>4 The value is 0, the key is 0</p><p>4 The value is 1, the key is 1</p><p>4 The value is 2, the key is 2</p><p>4 The value is 3, the key is 3</p><p>4 The value is 4, the key is 4</p><p>4 The value is 5, the key is 5</p><p>4 The value is 6, the key is 6</p><p>4 The value is 7, the key is 7</p><p>4 The value is 8, the key is 8</p><p>4 The value is 9, the key is 9</p><p>4 The value is 10, the key is 10</p><p>4 The value is 11, the key is 11</p><p>4 The value is 12, the key is 12</p><p>4 The value is 13, the key is 13</p><p>4 The value is 14, the key is 14</p><p>4 The value is 15, the key is 15</p><p>4 The value is 16, the key is 16</p><p>4 The value is 17, the key is 17</p><p>4 The value is 18, the key is 18</p><p>4 The value is 19, the key is 19</p><p>4 The value is 20, the key is 20</p><p>4 The value is 21, the key is 21</p><p>4 The value is 22, the key is 22</p><p>4 The value is 23, the key is 23</p><p>4 The value is 24, the key is 24</p><p>4 The value is 25, the key is 25</p>'],
            [array_combine(range('a', 'z'), range(0, 25)), '<p>1 The value is 0</p><p>1 The value is 1</p><p>1 The value is 2</p><p>1 The value is 3</p><p>1 The value is 4</p><p>1 The value is 5</p><p>1 The value is 6</p><p>1 The value is 7</p><p>1 The value is 8</p><p>1 The value is 9</p><p>1 The value is 10</p><p>1 The value is 11</p><p>1 The value is 12</p><p>1 The value is 13</p><p>1 The value is 14</p><p>1 The value is 15</p><p>1 The value is 16</p><p>1 The value is 17</p><p>1 The value is 18</p><p>1 The value is 19</p><p>1 The value is 20</p><p>1 The value is 21</p><p>1 The value is 22</p><p>1 The value is 23</p><p>1 The value is 24</p><p>1 The value is 25</p><p>2 The value is 0</p><p>2 The value is 1</p><p>2 The value is 2</p><p>2 The value is 3</p><p>2 The value is 4</p><p>2 The value is 5</p><p>2 The value is 6</p><p>2 The value is 7</p><p>2 The value is 8</p><p>2 The value is 9</p><p>2 The value is 10</p><p>2 The value is 11</p><p>2 The value is 12</p><p>2 The value is 13</p><p>2 The value is 14</p><p>2 The value is 15</p><p>2 The value is 16</p><p>2 The value is 17</p><p>2 The value is 18</p><p>2 The value is 19</p><p>2 The value is 20</p><p>2 The value is 21</p><p>2 The value is 22</p><p>2 The value is 23</p><p>2 The value is 24</p><p>2 The value is 25</p><p>3 The value is 0, the key is a</p><p>3 The value is 1, the key is b</p><p>3 The value is 2, the key is c</p><p>3 The value is 3, the key is d</p><p>3 The value is 4, the key is e</p><p>3 The value is 5, the key is f</p><p>3 The value is 6, the key is g</p><p>3 The value is 7, the key is h</p><p>3 The value is 8, the key is i</p><p>3 The value is 9, the key is j</p><p>3 The value is 10, the key is k</p><p>3 The value is 11, the key is l</p><p>3 The value is 12, the key is m</p><p>3 The value is 13, the key is n</p><p>3 The value is 14, the key is o</p><p>3 The value is 15, the key is p</p><p>3 The value is 16, the key is q</p><p>3 The value is 17, the key is r</p><p>3 The value is 18, the key is s</p><p>3 The value is 19, the key is t</p><p>3 The value is 20, the key is u</p><p>3 The value is 21, the key is v</p><p>3 The value is 22, the key is w</p><p>3 The value is 23, the key is x</p><p>3 The value is 24, the key is y</p><p>3 The value is 25, the key is z</p><p>4 The value is 0, the key is a</p><p>4 The value is 1, the key is b</p><p>4 The value is 2, the key is c</p><p>4 The value is 3, the key is d</p><p>4 The value is 4, the key is e</p><p>4 The value is 5, the key is f</p><p>4 The value is 6, the key is g</p><p>4 The value is 7, the key is h</p><p>4 The value is 8, the key is i</p><p>4 The value is 9, the key is j</p><p>4 The value is 10, the key is k</p><p>4 The value is 11, the key is l</p><p>4 The value is 12, the key is m</p><p>4 The value is 13, the key is n</p><p>4 The value is 14, the key is o</p><p>4 The value is 15, the key is p</p><p>4 The value is 16, the key is q</p><p>4 The value is 17, the key is r</p><p>4 The value is 18, the key is s</p><p>4 The value is 19, the key is t</p><p>4 The value is 20, the key is u</p><p>4 The value is 21, the key is v</p><p>4 The value is 22, the key is w</p><p>4 The value is 23, the key is x</p><p>4 The value is 24, the key is y</p><p>4 The value is 25, the key is z</p>']
        ];
    }

    public function testWhile()
    {

        $this->assertEquals('<p>1 My $i is 0!</p><p>1 My $i is 1!</p><p>1 My $i is 2!</p><p>1 My $i is 3!</p><p>1 My $i is 4!</p><p>1 My $i is 5!</p><p>1 My $i is 6!</p><p>1 My $i is 7!</p><p>1 My $i is 8!</p><p>1 My $i is 9!</p><p>1 My $i is 10!</p><p>1 My $i is 11!</p><p>1 My $i is 12!</p><p>1 My $i is 13!</p><p>1 My $i is 14!</p><p>1 My $i is 15!</p><p>1 My $i is 16!</p><p>1 My $i is 17!</p><p>1 My $i is 18!</p><p>1 My $i is 19!</p><p>1 My $i is 20!</p><p>1 My $i is 21!</p><p>1 My $i is 22!</p><p>1 My $i is 23!</p><p>1 My $i is 24!</p><p>2 My $i is 0</p><p>2 My $i is 1</p><p>2 My $i is 2</p><p>2 My $i is 3</p><p>2 My $i is 4</p><p>2 My $i is 5</p><p>2 My $i is 6</p><p>2 My $i is 7</p><p>2 My $i is 8</p><p>2 My $i is 9</p><p>2 My $i is 10</p><p>2 My $i is 11</p><p>2 My $i is 12</p><p>2 My $i is 13</p><p>2 My $i is 14</p><p>2 My $i is 15</p><p>2 My $i is 16</p><p>2 My $i is 17</p><p>2 My $i is 18</p><p>2 My $i is 19</p><p>2 My $i is 20</p><p>2 My $i is 21</p><p>2 My $i is 22</p><p>2 My $i is 23</p><p>2 My $i is 24</p>', $this->renderer->render('while'));
    }

    public function testFor()
    {

        $this->assertEquals('<p>1 Character at 0 is a</p><p>1 Character at 1 is b</p><p>1 Character at 2 is c</p><p>1 Character at 3 is d</p><p>1 Character at 4 is e</p><p>1 Character at 5 is f</p><p>1 Character at 6 is g</p><p>1 Character at 7 is h</p><p>1 Character at 8 is i</p><p>1 Character at 9 is j</p><p>1 Character at 10 is k</p><p>1 Character at 11 is l</p><p>1 Character at 12 is m</p><p>1 Character at 13 is n</p><p>1 Character at 14 is o</p><p>1 Character at 15 is p</p><p>1 Character at 16 is q</p><p>1 Character at 17 is r</p><p>1 Character at 18 is s</p><p>1 Character at 19 is t</p><p>1 Character at 20 is u</p><p>1 Character at 21 is v</p><p>1 Character at 22 is w</p><p>1 Character at 23 is x</p><p>1 Character at 24 is y</p><p>1 Character at 25 is z</p>', $this->renderer->render('for'));
    }
}