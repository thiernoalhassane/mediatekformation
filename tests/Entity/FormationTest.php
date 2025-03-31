
<?php

use App\Entity\Formation;
use DateTime;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{
    public function testGetPublishedAtStringReturnsFormattedDateWhenPublishedAtIsSet(): void
    {
        $formation = new Formation();
        $date = new DateTime('2023-03-30');
        $formation->setPublishedAt($date);
        $expected = $date->format('d/m/Y');
        $this->assertSame($expected, $formation->getPublishedAtString());
    }
}
