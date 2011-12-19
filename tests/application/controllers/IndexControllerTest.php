<?php
/**
 * 
 * @package zfCustomError
 * @author Denis Uraganov
 *
 */
class IndexControllerTest extends ControllerTestCase
{
    public function testIndexPage(){
        $this->dispatch("/");
        $this->assertController("index");
        $this->assertAction("index");
        $this->assertResponseCode(200);
    }
    
    public function testInvalidControllerPage(){
        $this->dispatch("/invalid");
        $this->assertController("error");
        $this->assertAction("error");
        $this->assertResponseCode(404);
        $this->assertXpathContentContains("//h1", 'An error occurred');
    }
    
    public function testInvalidActionPage(){
        $this->dispatch("/index/invalid");
        $this->assertController("error");
        $this->assertAction("error");
        $this->assertResponseCode(404);
        $this->assertXpathContentContains("//h1", 'An error occurred');
    }
    
    public function testMaintenancePage(){
        $this->dispatch("/index/maintenance");
        $this->assertController("error");
        $this->assertAction("maintenance");
        $this->assertResponseCode(200);
        $this->assertXpathContentContains("//h1", 'Maintenance Mode');
        $this->assertNotXpathContentContains("//h2", 'Maintenance custom msg');
    }    

    public function testMaintenanceCustomPage(){
        $this->dispatch("/index/maintenance-custom");
        $this->assertController("error");
        $this->assertAction("maintenance");
        $this->assertResponseCode(200);
        $this->assertXpathContentContains("//h1", 'Maintenance Mode');
        $this->assertXpathContentContains("//h2", 'Maintenance custom msg');
    }
    
    public function testNotFoundPage(){
    
        $this->dispatch("/index/notfound");
        $this->assertController("error");
        $this->assertAction("item");
        $this->assertResponseCode(404);
        $this->assertXpathContentContains("//h1", 'No Item Found');
        $this->assertNotXpathContentContains("//h2", 'Not found custom msg');
    }
    
    public function testNotFoundCustomPage(){
    
        $this->dispatch("/index/notfound-custom");
        $this->assertController("error");
        $this->assertAction("item");
        $this->assertResponseCode(404);
        $this->assertXpathContentContains("//h1", 'No Item Found');
        $this->assertXpathContentContains("//h2", 'Not found custom msg');
    }
    
    public function testNoaccessPage(){
        $this->dispatch("/index/noaccess");
        $this->assertController("error");
        $this->assertAction("noaccess");
        $this->assertResponseCode(401);
        $this->assertXpathContentContains("//h1", 'You have no access to this resource');
        $this->assertNotXpathContentContains("//h2", 'No access custom msg');
    }
    
    public function testNoaccessCustomPage(){
        $this->dispatch("/index/noaccess-custom");
        $this->assertController("error");
        $this->assertAction("noaccess");
        $this->assertResponseCode(401);
        $this->assertXpathContentContains("//h1", 'You have no access to this resource');
        $this->assertXpathContentContains("//h2", 'No access custom msg');
    }
}

?>