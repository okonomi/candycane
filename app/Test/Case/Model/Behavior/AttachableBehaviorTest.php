<?php
App::uses('AttachableBehavior', 'Model');
App::uses('AppModel', 'Model');

class Test extends AppModel {

    public $actsAs = array(
        'Attachable',
    );
}

class AttachableBehaviorTest extends CakeTestCase {

    public function setUp() {
        parent::setUp();
        $this->Test = ClassRegistry::init('Test');
    }

    public function tearDown() {
        unset($this->Test);
        parent::tearDown();
    }

    public function testIsAttachmentsVisible() {
        $user = array();
        $project = array();
        $this->Test->is_attachments_visible($user, $project);
    }
}
