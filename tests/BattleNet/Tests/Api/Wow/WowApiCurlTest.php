<?php
namespace BattleNet\Tests\Api\Wow;

use BattleNet\Api\Wow\WowApi;
use BattleNet\Cache\ArrayCache;

class WowApiCurlTest
    extends \PHPUnit_Framework_TestCase
{
    public function provideRealmData() {
        return array(
                    array('Kilrogg'),
                    array('Doomhammer')
                    );
    }
    
    public function provideCharacterData() {
        return array(
                    array('Kilrogg', 'Khepri'),
                    array('Kilrogg', 'Miguez'),
                    array('Kilrogg', 'Xanetia'),
                    array('Kilrogg', 'Mabs')
                );
    }

    public function provideGuildData() {
        return array(
                    array('Kilrogg', 'Rebel Knights'),
                    array('Kilrogg', 'Anarchy')
                );
    }
    
    public function provideArenaTeamData()
    {
        return array(
                    array('Bonechewer','2v2','Samurai Jack')
               );
    }
       
    public function provideQuestData() {
        return array(
                    array(25)
                );
    }
    
    public function provideItemData() {
        return array(
                    array(71053),
                    array(71054),
                    array(38268)
                );
    }
    
    public function provideRegionAndLocaleData() {
        return array(
                    array('eu', 'en_GB'),
                    array('us', 'en_US'),
                    array('kr', 'ko_KR'),
                    array('tw', 'zh_TW'),
                    array('cn', 'zh_CN')
                );
    }
    
    /**
     * @test
     * @dataProvider provideCharacterData
     */
    public function GetCharacter($realm, $character)
    {
         $client = new WowApi(array('region'=>'eu', 'locale'=>'en_GB', 'httpAdapter' => 'curl'));

         $result = $client->getCharacter($realm, $character,array('guild'));

         $this->assertObjectHasAttribute('name',$result);
         $this->assertEquals($character, $result->name);
         $this->assertObjectHasAttribute('realm',$result);
         $this->assertEquals($realm, $result->realm);
    }
    
    /**
     * @test
     * @dataProvider provideRealmData
     */
    public function GetRealmStatus($realm)
    {
         $client = new WowApi(array('region'=>'eu', 'httpAdapter' => 'curl'));

         $result = $client->getRealmStatus($realm);
         $this->assertObjectHasAttribute('realms',$result);
         $this->assertEquals(count($result->realms), 1);
         $this->assertObjectHasAttribute('name',$result->realms[0]);
         $this->assertEquals($realm, $result->realms[0]->name);
    }
    
    /**
     * @test
     * @dataProvider provideGuildData
     */
    public function GetGuild($realm, $guild)
    {
         $client = new WowApi(array('region'=>'eu', 'httpAdapter' => 'curl'));

         $result = $client->getGuild($realm, $guild,array());
         
         $this->assertObjectHasAttribute('name',$result);
         $this->assertObjectHasAttribute('realm',$result);
         $this->assertObjectHasAttribute('level',$result);
         $this->assertObjectHasAttribute('side',$result);
         $this->assertObjectHasAttribute('achievementPoints',$result);
         
         $this->assertEquals($guild, $result->name);
         $this->assertEquals($realm, $result->realm);
    }   
    
    /**
     * @test
     * @dataProvider provideItemData
     */
    public function GetItem($itemId)
    {
         $client = new WowApi(array('region'=>'us', 'httpAdapter' => 'curl'));

         $result = $client->getItem($itemId);
         
         $this->assertObjectHasAttribute('name',$result);
    }
       
    /**
     * @test
     */
    public function GetRaces()
    {
         $client = new WowApi(array('region'=>'eu', 'httpAdapter' => 'curl'));

         $result = $client->getRaces();
         $this->assertObjectHasAttribute('races',$result);
         $this->assertGreaterThanOrEqual(1,count($result->races));
         $this->assertObjectHasAttribute('id',$result->races[0]);
         $this->assertObjectHasAttribute('mask',$result->races[0]);
         $this->assertObjectHasAttribute('side',$result->races[0]);
         $this->assertObjectHasAttribute('name',$result->races[0]);
    }
       
    /**
     * @test
     */
    public function GetClasses()
    {
         $client = new WowApi(array('region'=>'eu', 'httpAdapter' => 'curl'));

         $result = $client->getClasses();
         $this->assertObjectHasAttribute('classes',$result);
         $this->assertGreaterThanOrEqual(1,count($result->classes));
         $this->assertObjectHasAttribute('id',$result->classes[0]);
         $this->assertObjectHasAttribute('mask',$result->classes[0]);
         $this->assertObjectHasAttribute('powerType',$result->classes[0]);
         $this->assertObjectHasAttribute('name',$result->classes[0]);
    }
       
    /**
     * @test
     */
    public function GetGuildPerks()
    {
         $client = new WowApi(array('region'=>'eu', 'httpAdapter' => 'curl'));

         $result = $client->getGuildPerks();
         $this->assertObjectHasAttribute('perks',$result);
         $this->assertGreaterThanOrEqual(1,count($result->perks));
    }

    /**
     * @test
     */
    public function GetGuildRewards()
    {
         $client = new WowApi(array('region'=>'eu', 'httpAdapter' => 'curl'));

         $result = $client->getGuildRewards();
         $this->assertObjectHasAttribute('rewards',$result);
         $this->assertGreaterThanOrEqual(1,count($result->rewards));
    }    
    
    /**
     * @test
     * @todo assertions
     */
    public function GetClassesWithCache()
    {
        $client = new WowApi(array('region'=>'eu', 'httpAdapter' => 'curl'));
        $cache = new ArrayCache();
        $cache->setNamespace('WowApi');
        $client->setCache($cache);
        
        $result = $client->getClasses();

        $result2 = $client->getClasses();
    }
    
    /**
     * @test
     * @dataProvider provideRegionAndLocaleData
     */
    public function SetRegionAndLocale($region, $locale)
    {
        $client = new WowApi(array('region'=>$region, 'locale'=>$locale, 'httpAdapter' => 'curl'));
    }
    
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Region "be" is not available.
     */
    public function BadRegion()
    {
        $client = new WowApi(array('region'=>'be', 'httpAdapter' => 'curl'));
    }
    
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Locale "nl_BE" is not available for region "eu".
     */
    public function BadLocale()
    {
        $client = new WowApi(array('region'=>'eu','locale'=>'nl_BE', 'httpAdapter' => 'curl'));
    }
    
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Item ID "" invalid for BattleNet\Api\Wow\Call\ItemCall.
     */
    public function GetItemNullItemId()
    {
        $client = new WowApi(array('region'=>'eu','locale'=>'en_GB', 'httpAdapter' => 'curl'));
        
        $client->getItem(null);
    }
    
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Item ID "test" invalid for BattleNet\Api\Wow\Call\ItemCall.
     */
    public function GetItemNonNumericItemId()
    {
        $client = new WowApi(array('region'=>'eu','locale'=>'en_GB', 'httpAdapter' => 'curl'));
        
        $client->getItem('test');
    }
    
    /**
     * @test
     * @expectedException BattleNet\Api\Exception\NotFoundException
     * @expectedExceptionMessage Character not found.
     */
    public function GetNonexistingCharacter()
    {
         $client = new WowApi(array('region'=>'eu', 'locale'=>'en_GB', 'httpAdapter' => 'curl'));

         $result = $client->getCharacter('Kilrogg','DoesNotExists');
    }
    
    /**
     * @test
     */
    public function GetCharacterAchievements()
    {
         $client = new WowApi(array('region'=>'us', 'httpAdapter' => 'curl'));

         $result = $client->getCharacterAchievements();
    }
           
    /**
     * @test
     */
    public function GetGuildAchievements()
    {
         $client = new WowApi(array('region'=>'us', 'httpAdapter' => 'curl'));

         $result = $client->getGuildAchievements();
    }
   
    
    /**
     * @test
     * @dataProvider provideArenaTeamData
     */
    public function GetArenaTeam($realm, $size, $name)
    {
         $client = new WowApi(array('region'=>'us', 'httpAdapter' => 'curl'));

         $result = $client->getArenaTeam($realm, $size, $name);
         
         $this->assertObjectHasAttribute('name',$result);
    }
        
    /**
     * @test
     * @dataProvider provideQuestData
     */
    public function GetQuest($questId)
    {
         $client = new WowApi(array('region'=>'us', 'httpAdapter' => 'curl'));

         $result = $client->getQuest($questId);
         
         $this->assertObjectHasAttribute('title',$result);
    }
}