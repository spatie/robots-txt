<?php

namespace Spatie\Robots\Tests;

use Spatie\Robots\RobotsTxt;

class RobotsTxtTest extends TestCase
{
    /** @test */
    public function it_can_parse_content()
    {
        $robots = RobotsTxt::create(file_get_contents(__DIR__.'/data/robots.txt'));

        $this->assertInstanceOf(RobotsTxt::class, $robots);
    }

    /** @test */
    public function it_can_parse_content_from_a_source()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertInstanceOf(RobotsTxt::class, $robots);
    }

    /** @test */
    public function test_allowed_link_for_default_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/'));
    }

    /** @test */
    public function test_disallow_keyword_in_url_is_correctly_disallowed()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->allows('/es/admin-disallow/', '*'));
    }

    /** @test */
    public function test_disallowed_link_for_default_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->allows('/en/admin/'));
    }

    /** @test */
    public function test_allowed_link_for_custom_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->allows('/test', 'google'));

        $this->assertTrue($robots
            ->exactMatchesOnly()
            ->allows('/no-agents', 'Mozilla/5.0 (compatible; UserAgent007/1.1)')
        );
        $this->assertFalse($robots
            ->withPartialMatches()
            ->allows('/no-agents', 'Mozilla/5.0 (compatible; UserAgent007/1.1)')
        );

        $this->assertTrue($robots
            ->ignoreGlobalGroup()
            ->withPartialMatches()
            ->allows('/nl/admin/', 'Mozilla/5.0 (compatible; UserAgent007/1.1)')
        );
        $this->assertFalse($robots
            ->includeGlobalGroup()
            ->withPartialMatches()
            ->allows('/nl/admin/', 'Mozilla/5.0 (compatible; UserAgent007/1.1)')
        );
    }

    /** @test */
    public function test_disallowed_link_for_custom_user_agent()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->allows('/', 'google'));
    }

    /** @test */
    public function it_can_handle_an_invalid_robots_txt()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/invalid-robots.txt');

        $this->assertTrue($robots->allows('/'));
    }

    /** @test */
    public function it_can_handle_an_empty_robots_txt()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/empty-robots.txt');

        $this->assertTrue($robots->allows('/'));
    }

    /** @test */
    public function it_can_handle_star_in_pattern()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/en/admin'));
        $this->assertFalse($robots->allows('/en/admin/'));
        $this->assertFalse($robots->allows('/en/admin/users'));
    }

    /** @test */
    public function it_can_handle_dollar_in_pattern()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/fr/ad'));
        $this->assertFalse($robots->allows('/fr/admin'));
        $this->assertTrue($robots->allows('/fr/admin/'));
        $this->assertTrue($robots->allows('/fr/admin?'));
        $this->assertTrue($robots->allows('/fr/admin?test'));
    }

    /** @test */
    public function it_can_handle_query_strings()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/en/admin'));
        $this->assertTrue($robots->allows('/en/admin?id=123'));
        $this->assertFalse($robots->allows('/en/admin?print'));
        $this->assertFalse($robots->allows('/en/admin?print=true'));
    }

    /** @test */
    public function the_allows_user_agent_check_is_case_insensitive()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/', 'UserAgent007'));
        $this->assertTrue($robots->allows('/', strtolower('UserAgent007')));
    }

    /** @test */
    public function the_disallows_user_agent_check_is_case_insensitive()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->allows('/no-agents', 'UserAgent007'));
        $this->assertFalse($robots->allows('/no-agents', strtolower('UserAgent007')));
    }

    /** @test */
    public function the_disallows_uri_check_is_case_sensitive()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->allows('/Case-Sensitive/Disallow'));
        $this->assertTrue($robots->allows(strtolower('/Case-Sensitive/Disallow')));
    }

    /** @test */
    public function it_can_handle_multiple_user_agent_query_strings()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertFalse($robots->allows('/en/admin?print=true', 'UserAgent010'));
        $this->assertFalse($robots->allows('/en/admin?print=true', 'UserAgent011'));
        $this->assertTrue($robots->allows('/en/admin?print=true', 'UserAgent012'));
        $this->assertTrue($robots->allows('/en/admin?print=true', 'UserAgent013'));
    }

    /** @test */
    public function it_can_handle_multiple_user_agent_root_path()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/', 'UserAgent010'));
        $this->assertTrue($robots->allows('/', 'UserAgent011'));
        $this->assertTrue($robots->allows('/', 'UserAgent012'));
        $this->assertTrue($robots->allows('/', 'UserAgent013'));
    }

    /** @test */
    public function it_can_handle_multiple_user_agent_first_in_list()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/fr/ad', 'UserAgent010'));
        $this->assertFalse($robots->allows('/fr/admin', 'UserAgent010'));
        $this->assertTrue($robots->allows('/fr/admin/', 'UserAgent010'));
        $this->assertTrue($robots->allows('/fr/admin?', 'UserAgent010'));
        $this->assertTrue($robots->allows('/fr/admin?test', 'UserAgent010'));
    }

    /** @test */
    public function it_can_handle_multiple_user_agent_last_in_list()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/fr/ad', 'UserAgent011'));
        $this->assertFalse($robots->allows('/fr/admin', 'UserAgent011'));
        $this->assertTrue($robots->allows('/fr/admin/', 'UserAgent011'));
        $this->assertTrue($robots->allows('/fr/admin?', 'UserAgent011'));
        $this->assertTrue($robots->allows('/fr/admin?test', 'UserAgent011'));
    }

    /** @test */
    public function it_can_handle_multiple_user_agent_first_in_list_with_empty_and_comment_lines()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/fr/ad', 'UserAgent012'));
        $this->assertTrue($robots->allows('/fr/admin', 'UserAgent012'));
        $this->assertTrue($robots->allows('/fr/admin/', 'UserAgent012'));
        $this->assertTrue($robots->allows('/fr/admin?', 'UserAgent012'));
        $this->assertTrue($robots->allows('/fr/admin?test', 'UserAgent012'));
        $this->assertFalse($robots->allows('/es/admin-disallow/', 'UserAgent013'));
    }

    /** @test */
    public function it_can_handle_multiple_user_agent_last_in_list_with_empty_and_comment_line()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');

        $this->assertTrue($robots->allows('/fr/ad', 'UserAgent013'));
        $this->assertTrue($robots->allows('/fr/admin', 'UserAgent013'));
        $this->assertTrue($robots->allows('/fr/admin/', 'UserAgent013'));
        $this->assertTrue($robots->allows('/fr/admin?', 'UserAgent013'));
        $this->assertTrue($robots->allows('/fr/admin?test', 'UserAgent013'));
        $this->assertFalse($robots->allows('/es/admin-disallow/', 'UserAgent013'));
    }

    /** @test */
    public function it_can_handle_explicit_multiple_allows_after_generic_deny()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');
        $this->assertTrue($robots->allows('/abc/1234/explicit.html'));
        $this->assertFalse($robots->allows('/abc/1234'));
        $this->assertFalse($robots->allows('/abc/1234/not_mentioned.html'));
        $this->assertFalse($robots->allows('/abc/1234/folder/not_mentioned.html'));
        $this->assertFalse($robots->allows('/abc/1235/folder/not_mentioned.html'));
    }

    /** @test */
    /** @test */
    public function it_can_handle_explicit_multiple_allows_after_generic_deny_for_only_me()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots.txt');
        $this->assertTrue($robots->allows('/xyz/1234/explicit.html', 'only-me'));
        $this->assertFalse($robots->allows('/xyz/1234', 'only-me'));
        $this->assertFalse($robots->allows('/xyz/1234/not_mentioned.html', 'only-me'));
        $this->assertFalse($robots->allows('/xyz/1234/folder/not_mentioned.html', 'only-me'));
        $this->assertFalse($robots->allows('/xyz/1235/folder/not_mentioned.html', 'only-me'));
    }

    /** @test */
    public function it_can_handle_weighted_allow()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots_weight.txt');
        $this->assertTrue($robots->allows('/nb/reindrift/', 'only-me'));
        $this->assertTrue($robots->allows('/sitemap.xml', 'only-me'));
        $this->assertFalse($robots->allows('/some_random/sub/site', 'only-me'));
    }

    /** @test */
    public function it_can_parse_common_crawl_delay()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots_crawl_delay.txt');
        $crawlDelay = $robots->crawlDelay('only-me');
        $this->assertEquals('10', $crawlDelay);
    }

    /** @test */
    public function it_can_parse_individual_user_agent_crawl_delay()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots_crawl_delay.txt');
        $crawlDelay = $robots->crawlDelay('google');
        $this->assertEquals('5', $crawlDelay);
    }

    /** @test */
    public function it_can_parse_fractional_crawl_delay()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots_crawl_delay.txt');
        $crawlDelay = $robots->crawlDelay('bing');
        $this->assertEquals('1.5', $crawlDelay);
    }

    /** @test */
    public function it_can_apply_crawl_delay_to_multiple_user_agents()
    {
        $robots = RobotsTxt::readFrom(__DIR__.'/data/robots_crawl_delay.txt');
        $this->assertEquals('1.5', $robots->crawlDelay('bing'));
        $this->assertEquals('1.5', $robots->crawlDelay('yandex'));
    }

    /** @test */
    public function it_parses_crawl_delay_directive_case_insensitive()
    {
        $robots = new RobotsTxt('
            User-agent: *
            CrAwL-dElAy: 2
        ');
        $this->assertEquals(2, $robots->crawlDelay('bing'));
    }

    /** @test */
    public function it_has_crawl_delay_for_default_user_agent_if_it_is_defined()
    {
        $robots = new RobotsTxt('
            User-agent: *
            CrAwL-dElAy: 2
        ');
        $this->assertEquals(2, $robots->crawlDelay('*'));
    }

    /** @test */
    public function it_has_null_crawl_delay_for_default_user_agent_if_it_is_not_defined()
    {
        $robots = new RobotsTxt('
            User-agent: bing
            CrAwL-dElAy: 2
        ');
        $this->assertNull($robots->crawlDelay('*'));
    }
}
