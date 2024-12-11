<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FormattingTest extends TestCase
{

    /**
     * A basic unit test example.
     */
    public function test_format_number(): void {
        $number = 1.23456789;
        $this->assertEquals(1, vlx_number_format($number, 0));
        $this->assertEquals(1.2, vlx_number_format($number, 1));
        $this->assertEquals(1.23, vlx_number_format($number, 2));
    }
    /**
     * A basic unit test example.
     */
    public function test_format_route(): void {
        $string = "dashboard.notifications.add";
        $this->assertEquals("Dashboard", vlx_format_route_name($string));
    }
}
