<?php

namespace OCA\Move\Controller;

use OCA\Move\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\Util;

$fromplace = $frommonth = $fromday = $fromtime = $toplace = $tomonth = $today = $totime = "";

class ResultController extends Controller {
	private $userId;

	public function __construct($AppName, IRequest $request, $UserId){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
	}

	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
	 *
	 * Render default template
	 */
	public function index_two() {
		return new TemplateResponse('move', 'test1');  // templates/test1.php
	}
	public function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
		}
	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *	 
     * @NoAdminRequired
     * @NoCSRFRequired
     */
	public function submit() {
		if ($_SERVER["REQUEST_METHOD"] == "GET") {
			$fromplace = $_GET["fromplace"];
			// $frommonth = test_input($_GET["frommonth"]);
			// $fromday = test_input($_GET["fromday"]);
			// $fromtime = test_input($_GET["fromtime"]);
			// $toplace = test_input($_GET["toplace"]);
			// $tomonth = test_input($_GET["tomonth"]);
			// $today = test_input($_GET["today"]);
			// $totime = test_input($_GET["totime"]);
		} 
		return new TemplateResponse('move', 'submit'); 
	}


}
