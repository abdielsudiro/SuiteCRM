<?php /** @noinspection PhpUnhandledExceptionInspection */
/**
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2018 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */

/**
 * Created by PhpStorm.
 * User: viocolano
 * Date: 02/07/18
 * Time: 09:44
 */

use SuiteCRM\Search\ElasticSearch\ElasticSearchClientBuilder;
use SuiteCRM\Search\SearchTestAbstract;
use SuiteCRM\StateSaver;

class ElasticSearchClientBuilderTest extends SearchTestAbstract
{

    public function testGetClient()
    {
        $client = ElasticSearchClientBuilder::getClient();

        self::assertInstanceOf(\Elasticsearch\Client::class, $client);
    }

    public function testLoadConfig()
    {
        $builder = new ElasticSearchClientBuilder();
        $config = self::invokeMethod($builder, 'loadConfig', [__DIR__ . '/TestData/ElasticsearchServerConfig.json']);
        $expected = [
            [
                "host" => "foo.com",
                "port" => "9200",
                "scheme" => "https",
                "user" => "username",
                "pass" => "password!#$?*abc"
            ],
            ["host" => "localhost"]
        ];

        self::assertEquals($expected, $config);
    }

    // Tests if the default configs are returned when the config file is not found
    public function testLoadConfigFileNotThere()
    {
        $builder = new ElasticSearchClientBuilder();
        $config = self::invokeMethod($builder, 'loadConfig', [__DIR__ . '/TestData/NopeNotHere.json']);
        $expected = ["127.0.0.1"];

        self::assertEquals($expected, $config);
    }

    public function testLoadSugarConfig()
    {
        global $sugar_config;

        $stateSave = new StateSaver();
        $stateSave->pushGlobals();

        $sugar_config['search']['ElasticSearch']['host'] = '127.0.0.1';
        $sugar_config['search']['ElasticSearch']['user'] = 'foo';
        $sugar_config['search']['ElasticSearch']['pass'] = 'bar';

        $actual = $this->loadFromSugarConfig();
        $expected = [
            [
                'host' => '127.0.0.1',
                'user' => 'foo',
                'pass' => 'bar'
            ]
        ];

        self::assertEquals($expected, $actual);

        $stateSave->popGlobals();
    }

    public function testLoadSugarConfig2()
    {
        global $sugar_config;

        $stateSave = new StateSaver();
        $stateSave->pushGlobals();

        $sugar_config['search']['ElasticSearch']['host'] = 'localhost';
        $sugar_config['search']['ElasticSearch']['user'] = 'bar';
        $sugar_config['search']['ElasticSearch']['pass'] = '';

        $actual = $this->loadFromSugarConfig();
        $expected = [
            [
                'host' => 'localhost',
                'user' => 'bar',
                'pass' => ''
            ]
        ];

        self::assertEquals($expected, $actual);

        $stateSave->popGlobals();
    }

    public function testLoadSugarConfig3()
    {
        global $sugar_config;

        $stateSave = new StateSaver();
        $stateSave->pushGlobals();

        $sugar_config['search']['ElasticSearch']['host'] = 'www.example.com';
        $sugar_config['search']['ElasticSearch']['user'] = '';
        $sugar_config['search']['ElasticSearch']['pass'] = '';

        $actual = $this->loadFromSugarConfig();

        $expected = ['www.example.com'];

        self::assertEquals($expected, $actual);

        $stateSave->popGlobals();
    }

    private function loadFromSugarConfig()
    {
        $builder = new ElasticSearchClientBuilder();
        return self::invokeMethod($builder, 'loadFromSugarConfig');
    }
}
