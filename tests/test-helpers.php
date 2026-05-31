<?php
/**
 * Tests for helper functions.
 *
 * @package IntranetDashboardBase
 */

class Test_Helpers extends WP_UnitTestCase {

	public function test_reading_time_label_returns_string() {
		$post_id = $this->factory->post->create(array(
			'post_content' => 'word one two three four five six seven eight nine ten',
		));

		$label = intranet_dashboard_base_get_reading_time_label($post_id);
		$this->assertIsString($label);
		$this->assertStringContainsString('min', $label);
	}

	public function test_reading_time_label_default_when_no_post() {
		$label = intranet_dashboard_base_get_reading_time_label(0);
		$this->assertIsString($label);
	}

	public function test_sanitize_checkbox() {
		$this->assertFalse(intranet_dashboard_base_sanitize_checkbox(false));
		$this->assertFalse(intranet_dashboard_base_sanitize_checkbox(0));
		$this->assertFalse(intranet_dashboard_base_sanitize_checkbox(''));
		$this->assertTrue(intranet_dashboard_base_sanitize_checkbox(true));
		$this->assertTrue(intranet_dashboard_base_sanitize_checkbox(1));
		$this->assertTrue(intranet_dashboard_base_sanitize_checkbox('on'));
	}

	public function test_search_url() {
		$this->assertStringContainsString('busca-interna', intranet_dashboard_base_search_url());
	}

	public function test_profile_edit_url() {
		$this->assertStringContainsString('meu-perfil', intranet_dashboard_base_profile_edit_url());
	}

	public function test_weekday_short_label() {
		$label = intranet_dashboard_base_get_weekday_short_label('2026-05-31');
		$this->assertIsString($label);
		$this->assertNotEmpty($label);
	}

	public function test_search_post_types_returns_array() {
		$types = intranet_dashboard_base_search_post_types();
		$this->assertIsArray($types);
		$this->assertContains('post', $types);
		$this->assertContains('page', $types);
		$this->assertContains('comunicado', $types);
	}
}
