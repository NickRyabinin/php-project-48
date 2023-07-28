<?php

namespace Differ\tests\GenDiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        $fixture1 = $this->getPathToFixture('file1.json');
        $fixture2 = $this->getPathToFixture('file2.json');
        $actual = genDiff($fixture1, $fixture2, 'plain');
        $expected = file_get_contents($this->getPathToFixture('expectedPlainJson'));
        $this->assertEquals($expected, $actual);
    }

    private function getPathToFixture($fixtureName)
    {
        return __DIR__ . "/fixtures/" . $fixtureName;
    }
}
