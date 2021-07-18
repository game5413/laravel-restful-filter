<?php declare(strict_types=1);

namespace Tests\RestfulFilter;

use Tests\RestfulFilter\Database\Model\User;

/**
 * @covers \Sorting
 * @uses \RestfulFilter
 */
final class SortingCase extends TestSuite
{
    public function testSortingAsc(): void
    {
        $data = User::sortable('name_asc')->get();

        $this->assertNotEmpty($data->toArray());

        $this->assertEquals('Ben Brown Jr.', $data->first()->name);
    }

    public function testSortingDesc(): void
    {
        $data = User::sortable('name_desc')->get();

        $this->assertNotEmpty($data->toArray());

        $this->assertEquals('Ross Leuschke DDS', $data->first()->name);
    }

    public function testSortingMultiple(): void
    {
        $data = User::sortable('name_desc,email_asc')->toSql();

        $this->assertStringContainsString(
            'order by "name" desc, "email" asc',
            $data
        );
    }
}
