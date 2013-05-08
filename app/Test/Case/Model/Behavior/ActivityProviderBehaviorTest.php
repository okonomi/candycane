<?php

App::uses('Model', 'Model');
App::uses('AppModel', 'Model');

class ActivityProviderBehaviorTest extends CakeTestCase {

	public $Issue = null;

	public $data = null;

	public $autoFixtures = false;

	public $fixtures = array(
		'app.issue', 'app.project', 'app.tracker', 'app.issue_status', 'app.user', 'app.version',
		'app.enumeration', 'app.issue_category', 'app.token', 'app.member', 'app.role', 'app.user_preference',
		'app.enabled_module', 'app.time_entry', 'app.changeset', 'app.changesets_issue', 'app.attachment',
		'app.projects_tracker', 'app.custom_value', 'app.custom_field', 'app.watcher',
		'app.wiki', 'app.wiki_page', 'app.wiki_content', 'app.wiki_content_version', 'app.wiki_redirect','app.workflow'
	);

	public function setUp() {
		$this->loadFixtures(
			'Project', 'Tracker', 'User', 'Version', 'IssueCategory', 'TimeEntry',
			'Issue', 'IssueStatus', 'Enumeration', 'CustomValue', 'CustomField',
			'EnabledModule', 'Role', 'Member', 'Changeset', 'ChangesetsIssue','Watcher',
			'Token','UserPreference'
		);
		$this->Issue = ClassRegistry::init('Issue');
		$this->data = $this->Issue->read(null, 1);
	}

	public function testActivityWithoutSubprojects() {
		$User = ClassRegistry::init('User');
		$user = $User->find_by_id_logged(2);

		$events = $this->Issue->find_events('issues', $user, false, false, array('project' => $this->data['Project']));
		$this->assertNotNull($events);
		$this->assertTrue(in_array(1, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(2, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(3, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(7, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
	}

	public function testActivityWithSubprojects() {
		$User = ClassRegistry::init('User');
		$user = $User->find_by_id_logged(2);

		$events = $this->Issue->find_events('issues', $user, false, false, array('project' => $this->data['Project'], 'with_subprojects' => 1));
		$this->assertNotNull($events);

		$this->assertTrue(in_array(1, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(2, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(3, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(7, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		// subproject issue
		$this->assertTrue(in_array(5, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(6, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
	}

	public function testGlobalActivity() {
		$User = ClassRegistry::init('User');
		$user = $User->find_by_id_logged(2);

		$events = $this->Issue->find_events('issues', $user, false, false, array());

		// Issue of a private project
		$this->assertTrue(in_array(1, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(2, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(3, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(4, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(5, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(6, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
		$this->assertTrue(in_array(7, Hash::flatten(Hash::extract($events, '{n}.{n}.{n}.Issue.id'))));
	}

/*

	def test_user_activity
		user = User.find(2)
		events = Redmine::Activity::Fetcher.new(User.anonymous, :author => user).events(nil, nil, :limit => 10)

		assert(events.size > 0)
		assert(events.size <= 10)
		assert_nil(events.detect {|e| e.event_author != user})
	end

	private

	def find_events(user, options={})
		Redmine::Activity::Fetcher.new(user, options).events(Date.today - 30, Date.today + 1)
	end
*/

}
