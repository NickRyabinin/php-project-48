<?php

namespace Differ\tests\GenDiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {
        $fixture1 = $this->getPathToFixture('file3.json');
        $fixture2 = $this->getPathToFixture('file4.json');
        $actual = genDiff($fixture1, $fixture2, 'stylish');
        $expected = file_get_contents($this->getPathToFixture('expectedStylish'));
        $this->assertEquals($expected, $actual);

        $fixture1 = $this->getPathToFixture('file3.yaml');
        $fixture2 = $this->getPathToFixture('file4.yaml');
        $actual = genDiff($fixture1, $fixture2, 'stylish');
        $expected = file_get_contents($this->getPathToFixture('expectedStylish'));
        $this->assertEquals($expected, $actual);

        $fixture1 = $this->getPathToFixture('file3.json');
        $fixture2 = $this->getPathToFixture('file4.json');
        $actual = genDiff($fixture1, $fixture2, 'plain');
        $expected = file_get_contents($this->getPathToFixture('expectedPlain'));
        $this->assertEquals($expected, $actual);

        $fixture1 = $this->getPathToFixture('file3.yaml');
        $fixture2 = $this->getPathToFixture('file4.yaml');
        $actual = genDiff($fixture1, $fixture2, 'plain');
        $expected = file_get_contents($this->getPathToFixture('expectedPlain'));
        $this->assertEquals($expected, $actual);

        $fixture1 = $this->getPathToFixture('file3.json');
        $fixture2 = $this->getPathToFixture('file4.json');
        $actual = genDiff($fixture1, $fixture2, 'json');
        $expected = file_get_contents($this->getPathToFixture('expectedJson'));
        $this->assertEquals($expected, $actual);

        $fixture1 = $this->getPathToFixture('file3.yaml');
        $fixture2 = $this->getPathToFixture('file4.yaml');
        $actual = genDiff($fixture1, $fixture2, 'json');
        $expected = file_get_contents($this->getPathToFixture('expectedJson'));
        $this->assertEquals($expected, $actual);
    }

    private function getPathToFixture($fixtureName)
    {
        return __DIR__ . "/fixtures/" . $fixtureName;
    }
}
