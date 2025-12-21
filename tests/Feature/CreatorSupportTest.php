<?php

namespace Tests\Feature;

use Tests\TestCase;

class CreatorSupportTest extends TestCase
{
	public function test_legacy_creator_support_flow_is_deprecated(): void
	{
		$this->markTestSkipped('Legacy creator support (User→User) was replaced by creator pages (fanpages).');
	}
}
