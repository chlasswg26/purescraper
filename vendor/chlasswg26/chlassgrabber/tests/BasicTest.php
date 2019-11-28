<?php

use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function testShouldSuccessWhenAssertTrue(): void
    {
        $this->assertEquals(5, 5);
    }
}