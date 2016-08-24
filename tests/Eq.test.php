<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use PHPIntegration\Utils\ObjectHelper;
use PHPIntegration\Utils\ArrayHelper;
use PHPIntegration\Utils\Interfaces\Eq;

class SimpleObject implements Eq
{
    public $simpleFld1;
    public $simpleFld2;
    public $simpleFld3;

    public function __construct()
    {
        $this->simpleFld1 = 540;
        $this->simpleFld2 = "false";
        $this->simpleFld3 = [];
    }

    public static function equal(Eq $a, Eq $b)
    {
        if ($a->simpleFld1 !== $b->simpleFld1) {
            return [ ['simpleFld1'],  "{$a->simpleFld1} != {$b->simpleFld1}" ];
        } elseif ($a->simpleFld2 !== $b->simpleFld2) {
            return [ ['simpleFld2'],  "{$a->simpleFld2} != {$b->simpleFld2}" ];
        }

        return ArrayHelper::equal($a->simpleFld3, $b->simpleFld3);
    }
}

class SimpleObjectT implements Eq
{
    use \PHPIntegration\Utils\Traits\Eq;

    public $simpleFld1;
    public $simpleFld2;
    public $simpleFld3;

    public function __construct()
    {
        $this->simpleFld1 = 540;
        $this->simpleFld2 = "false";
        $this->simpleFld3 = [];
    }
}

class EqTest extends TestCase
{
    public function testObjectEqual()
    {
        $obj1 = (object) [
            "field1" => "a",
            "field2" => 3.14,
            "field3" => [ "arrkey1" => true, "arrkey2" => "str" ],
            "field4" => (object) [ "innerfield1" => false, "innerfield2" => 400 ],
            "field5" => new SimpleObject()
        ];

        $obj2 = (object) [
            "field1" => "a",
            "field2" => 3.14,
            "field3" => [ "arrkey1" => true, "arrkey2" => "str" ],
            "field4" => (object) [ "innerfield1" => "false", "innerfield2" => 400 ],
            "field5" => new SimpleObject()
        ];

        $result = ObjectHelper::equal($obj1, $obj2);
        $this->assertTrue(is_array($result));
        $this->assertEquals($result[0], ['field4', 'innerfield1']);

        $obj2->field4->innerfield1 = false;
        $result = ObjectHelper::equal($obj1, $obj2);
        $this->assertTrue($result);

        $obj2->field5->simpleFld1 = 616;
        $result = ObjectHelper::equal($obj1, $obj2);
        $this->assertTrue(is_array($result));
        $this->assertEquals($result[0], ['field5', 'simpleFld1']);

        $obj2->field3["arrkey1"] = "true";
        $result = ObjectHelper::equal($obj1, $obj2);
        $this->assertTrue(is_array($result));
        $this->assertEquals($result[0], ['field3', '[arrkey1]']);

        $obj2->field3["arrkey1"] = true;
        $obj1->field5 = new SimpleObjectT();
        $obj2->field5 = new SimpleObjectT();
        $result = ObjectHelper::equal($obj1, $obj2);
        $this->assertTrue($result);
    }

    public function testArrayEqual()
    {
        $arr1 = [
            "field1" => "a",
            "field2" => 3.14,
            "field3" => [ "arrkey1" => true, "arrkey2" => "str" ],
            "field4" => (object) [ "innerfield1" => false, "innerfield2" => 400 ],
            "field5" => new SimpleObject()
        ];

        $arr2 = [
            "field1" => "a",
            "field2" => 3.14,
            "field3" => [ "arrkey1" => true, "arrkey2" => "str" ],
            "field4" => (object) [ "innerfield1" => "false", "innerfield2" => 400 ],
            "field5" => new SimpleObject()
        ];

        $result = ArrayHelper::equal($arr1, $arr2);
        $this->assertTrue(is_array($result));
        $this->assertEquals($result[0], ['[field4]', 'innerfield1']);

        $arr2["field4"]->innerfield1 = false;
        $result = ArrayHelper::equal($arr1, $arr2);
        $this->assertTrue($result);

        $arr2["field5"]->simpleFld1 = 616;
        $result = ArrayHelper::equal($arr1, $arr2);
        $this->assertTrue(is_array($result));
        $this->assertEquals($result[0], ['[field5]', 'simpleFld1']);

        $arr2["field3"]["arrkey1"] = "true";
        $result = ArrayHelper::equal($arr1, $arr2);
        $this->assertTrue(is_array($result));
        $this->assertEquals($result[0], ['[field3]', '[arrkey1]']);

        $arr2["field3"]["arrkey1"] = true;
        $arr1["field5"] = new SimpleObjectT();
        $arr2["field5"] = new SimpleObjectT();
        $result = ArrayHelper::equal($arr1, $arr2);
        $this->assertTrue($result);
    }
}
