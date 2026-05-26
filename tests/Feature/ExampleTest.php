<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_admin(): void
    {
        $this->get('/')->assertRedirect('/admin');
    }

    public function test_invalid_magic_link_page_loads(): void
    {
        $this->get('/auth/link-invalido')->assertOk();
    }
}
