<?php
namespace OCA\Move\Controller;
//phpinfo();
require '/var/www/html/apps/move/vendor/autoload.php';

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\Files_External\Lib\Storage;
use Aws;
use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use OCA\Move\Db\Note;
use OCA\Move\Db\NoteMapper;

class PageController extends Controller {
	private $userId;
	private NoteMapper $mapper;
	private string $bucket;
	private string $endpoint;
	private string $s3Key;
	private string $s3Secret;
	private string $s3Hostname;
	private string $s3PathStyle;
	private string $s3UseSsl;
	private string $s3Region;
	private string $s3Port;

	public function __construct($AppName, IRequest $request, $UserId, NoteMapper $mapper){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->mapper = $mapper;
	}
	public function findAllMount(string $mountId): array {
		return $this->mapper->findAllMount($mountId);
	}
	public function findAll(string $userId): array {
		return $this->mapper->findAll($userId);
	}

	function moveFiles($from, $to, $person)
	{
		$s3Client = $this->createClient();
		$bucket_name = 'parc-nextcloud';

		$source = $from;
		$destination = $to;
		
		//List contents
		try {
			$prefix = $from."/";
			$fromContents = $s3Client->listObjects([
				'Bucket' => $bucket_name,
				'Prefix' => $prefix
			]);
			echo "The contents of your bucket are: \n";
			foreach ($fromContents['Contents'] as $content) {
				echo $content['Key'] . "\n";
			}
		} catch (Exception $exception) {
			echo "Failed to list objects in $bucket_name with error: " . $exception->getMessage();
			exit("Please fix error with listing objects before continuing.");
		}

		//Copy everything
		try {
			foreach ($fromContents['Contents'] as $content) {
				$parts = explode("/", $content['Key']);
				$filename = end($parts);
				if ($filename == "")
				{
					// This will be the directory - create it
					$s3Client->putObject([
						'Bucket' => $bucket_name,
						'Key' => $destination."/",
						'Body'   => 'Hello, world!'
					]);
				}
				else 
				{
					$cs = $bucket_name."/".$content['Key'];
					$k = $destination."/".$filename;

					$s3Client->copyObject([
						'Bucket' => $bucket_name,
						'Key' => $k,
						'CopySource' => $cs,
					]);
				}
			}
		} catch (Exception $exception) {
			//echo "Failed to copy $file_name with error: " . $exception->getMessage();
			exit("Please fix error with object copying before continuing.");
		}

		//Remove objects from old folder
		try {
			$prefix = $from."/";
			$files_to_remove = $s3Client->listObjects([
				'Bucket' => $bucket_name,
				'Prefix' => $prefix
			]);
			foreach ($files_to_remove['Contents'] as $content) {
				$parts = explode("/", $content['Key']);
				$filename = end($parts);
				if ($filename == "")
				{
					// Don't delete folder
				}
				else
				{
					$result = $s3Client->deleteObject([
						'Bucket' => $bucket_name,
						'Key' => $content['Key'],
					]);	
				}
			}

		} catch (Exception $exception) {
			echo "Failed to delete $file_name from $bucket_name with error: " . $exception->getMessage();
			exit("Please fix error with object deletion before continuing.");
		}
		$s3Client->putObject([
			'Bucket' => $bucket_name,
			'Key' => $from."/Moved.txt",
			'Body'   => 'Folder Contents moved to: '.$destination
		]);
	}

	function createClient()
	{		// Create an S3Client


		$configuration = $this->findAllMount('1');
		//$configuration = $this->findAll('admin');
		foreach($configuration as $note)
		{
			$key =  $note->key;
			if ($key == 'endpoint') $endpoint = $note->value; 
			if ($key == 'key') $s3Key = $note->value; 
			if ($key == 'secret') $s3Secret = $note->value; 
			if ($key == 'hostname') $s3Hostname = $note->value; 
			if ($key == 'port') $s3port = $note->value; 
			if ($key == 'region') $s3Region = $note->value; 
			if ($key == 'use_ssl') $s3UseSsl = ($note->value)=='1'; 
			if ($key == 'use_path_style') $s3PathStyle = ($note->value)=='1'; 
		}
		
		// Create an S3Client
		$sharedConfig = [
			'version' => 'latest',
			'region' => '',
			'endpoint' => 'https://'.$s3Hostname,
			'use_path_style_endpoint' => $s3PathStyle,
			'use_ssl' => $s3UseSsl,
			'credentials' => array(
				'key' => $s3Key,
				'secret'  => $s3Secret,
			)
		];

		$sdk = new Aws\Sdk($sharedConfig);
		// Create an Amazon S3 client using the shared configuration data.
		$s3Client = $sdk->createS3();
		return $s3Client;
	}
	function createFolders()
	{
		$s3Client = $this->createClient();
		 $bucket_name = 'ag-pssg-csb-judi-dev-bkt';
		//		$bucket_name = 'parc-nextcloud';
		$regionCodes = array(1071,1171,1211,1041,1061,1121,1191,1141,1145,1151,1181,2051,2011,2008,2009,2011,2021,2027,2025,2045,3521,3587,4721,4731,4741,4771,4951,4691,4701,4851,5751,4871,4751,4861,4921,4941,4911,5721,5955,5751,5801,5821,5895,5891,5761,5781,5845,5831,5901,5841,5921,5931,5691,5701,5861,5791,5951,5741,5811,5805,5851,5941,5871,5681,5959);
		$roomCodes1 = array("1031-001","1051-001","1051-125","1091-101","1091-102","1207-REG","1201-001","1201-002","1201-003","1201-004","1201-100","1201-301","1201-302","1201-303","1201-401","1201-402","1201-403","1201-533","1201-CHB","1201-REG","2048-001","2048-002","2048-003");
		$roomCodes2 = array(
		"6011-001","6011-002","6011-200","6011-204","6011-220","6011-REG","2040-001","2040-002","2040-003","2040-004","2040-005","2040-105","2040-12A","2040-200","2040-300","2040-900","2040-REG","2040-TAC","2007-001","2007-002","2007-003","2007-004","2007-005","2007-006","2007-007","2007-008","2007-009","2007-REG","2031-001");
		$roomCodes3 = array("3561-201","3561-202","3561-203","3581-411","3531-001","3531-002","3531-003","3531-004","3531-005","3531-006","3531-007","3531-008","3531-009","3531-010","3531-011","3531-012","3531-REG","3585-001","3585-002","3585-100","3585-101","3585-103","3585-311","3585-JCM","3585-REG",);
		$roomCodes41 = array("4711-002","4781-001","4781-002","4821-1","4821-10","4821-11","4821-12","4821-2","4821-3","4821-4","4821-5","4821-6","4821-7","4821-8","4821-9",
		"4801-001","4801-002","4801-003","4801-004","4801-005","4801-006","4801-007","4801-008","4801-009","4801-010","4801-011","4801-012","4801-013","4801-014","4801-015","4801-016","4801-017","4801-018","4801-019","4801-021","4801-022","4801-023","4801-024","4801-025","4801-026","4801-027","4801-028","4801-029","4801-031","4801-032","4801-033","4801-034","4801-035","4801-036","4801-037","4801-038","4801-039","4801-041","4801-042","4801-043","4801-044","4801-045","4801-046","4801-047","4801-048","4801-049","4801-051","4801-052","4801-053");
		$roomCodes42 = array("4971-TEST","5731-001","5771-218","5961-100","5961-101","5961-102","5961-103","5961-104","5961-105","5961-200","5961-201","5961-202","5961-300","5971-001","5971-VR1","5971-VR2");
		$roomCodes5 = array("5731-001","5771-218","5961-100","5961-101","5961-102","5961-103","5961-104","5961-105","5961-200","5961-201","5961-202","5961-300","5971-001","5971-VR1","5971-VR2");
		$roomCodesAll = array("1031-001","1051-001","1051-125","1091-101","1091-102","1207-REG","1201-001","1201-002","1201-003","1201-004","1201-100","1201-301","1201-302","1201-303","1201-401","1201-402","1201-403","1201-533","1201-CHB","1201-REG","2048-001","2048-002","2048-003",
		"6011-001","6011-002","6011-200","6011-204","6011-220","6011-REG","2040-001","2040-002","2040-003","2040-004","2040-005","2040-105","2040-12A","2040-200","2040-300","2040-900","2040-REG","2040-TAC","2007-001","2007-002","2007-003","2007-004","2007-005","2007-006","2007-007","2007-008","2007-009","2007-REG","2031-001",
		"3561-201","3561-202","3561-203","3581-411","3531-001","3531-002","3531-003","3531-004","3531-005","3531-006","3531-007","3531-008","3531-009","3531-010","3531-011","3531-012","3531-REG","3585-001","3585-002","3585-100","3585-101","3585-103","3585-311","3585-JCM","3585-REG",
		"4711-002","4781-001","4781-002","4821-1","4821-10","4821-11","4821-12","4821-2","4821-3","4821-4","4821-5","4821-6","4821-7","4821-8","4821-9",
		"4801-001","4801-002","4801-003","4801-004","4801-005","4801-006","4801-007","4801-008","4801-009","4801-010","4801-011","4801-012","4801-013","4801-014","4801-015","4801-016","4801-017","4801-018","4801-019","4801-021","4801-022","4801-023","4801-024","4801-025","4801-026","4801-027","4801-028","4801-029","4801-031","4801-032","4801-033","4801-034","4801-035","4801-036","4801-037","4801-038","4801-039","4801-041","4801-042","4801-043","4801-044","4801-045","4801-046","4801-047","4801-048","4801-049","4801-051","4801-052","4801-053",
		"4801-054","4801-055","4801-101","4801-BLAH","4801-JCM","4801-JUNK","4801-OTH","4801-REG","4801-TEST","4801-TMP","4801-VR1","4681-001","4891-001","4891-009",
		"4901-001","4971-001","4971-002","4971-003","4971-004","4971-005","4971-006","4971-007","4971-008","4971-009","4971-010","4971-101","4971-120","4971-300","4971-45534","4971-500","4971-766","4971-BLAH","4971-JUNK","4971-RED","4971-REG",
		"4971-TEST","5731-001","5771-218","5961-100","5961-101","5961-102","5961-103","5961-104","5961-105","5961-200","5961-201","5961-202","5961-300","5971-001","5971-VR1","5971-VR2");
		// foreach ($regionCodes as $court) 
		// {
		// 	$this->createSingleDateFolders($s3Client, $court);
		// }
		// foreach ($roomCodes1 as $court) 
		// {
		// 	$this->createSingleDateFolders($s3Client, $court);
		// }		
		// foreach ($roomCodes2 as $court) 
		// {
		// 	$this->createSingleDateFolders($s3Client, $court);
		// }		
		// foreach ($roomCodes3 as $court) 
		// {
		// 	$this->createSingleDateFolders($s3Client, $court);
		// }		
		// foreach ($roomCodes41 as $court) 
		// {
		// 	$this->createSingleDateFolders($s3Client, $court);
		// }		
		// foreach ($roomCodes42 as $court) 
		// {
		// 	$this->createSingleDateFolders($s3Client, $court);
		// }
		// foreach ($roomCodes5 as $court) 
		// {
		// 	$this->createSingleDateFolders($s3Client, $court);
		// }
		foreach ($roomCodesAll as $court) 
		{
			$this->createSingleDateFolders($s3Client, $court);
		}

		// $regions = array("Island", "Vancouver", "Fraser", "Interior", "North");
		
		// $Island = array("Campbell River","Gold River","Tahsis","Colwood","Western Communities","Courtenay","Duncan","Ganges","Nanaimo","Port Alberni","Ucluelet","Port Hardy","Powell River","Sidney","Tofino","Victoria");
		// $Vancouver = array("Vancouver","222 Main","Bella Bella","Bella Coola","Klemtu","Judicial Justice Centre","Downtown Community Court","North Vancouver","Pemberton","Richmond","Robson Square","Sechelt");
		// $Fraser = array("Abbotsford","Chilliwack","New Westminster","Port Coquitlam","Surrey");
		// $Interior = array("Cranbrook","Creston","Fernie","Golden","Invermere","Sparwood","Kamloops","Chase","Clearwater","Lillooet","Merritt","Kelowna","Nelson","Castlegar","Grand Forks","Nakusp","Rossland","Penticton","Princeton","Salmon Arm","Revelstoke","Vernon");
		// $North = array("Dawson Creek","Chetwynd","Tumbler Ridge","Fort Nelson","Fort St. John","Good Hope Lake","Hudson's Hope","Lower Post","Prince George","Fort St. James","Fraser Lake","McBride","McKenzie","Mackenzie","Valemont","Prince Rupert","Masset","Queen Charlotte City","Quesnel","Smithers","Atlin","Burns Lake","Hazelton","Houston","Terrace","Dease Lake","Kitimat","Kwadacha","Tsay Keh Dene","New Aiyansh","Stewart","Vanderhoof","Williams Lake","100 Mile House","Alexis Creek","Anahim Lake","Valemount");
		// $this->createDateFolders($s3Client, "Island", $Island);
		// $this->createDateFolders($s3Client, "Vancouver", $Vancouver);
		// $this->createDateFolders($s3Client, "Fraser", $Fraser);
		// $this->createDateFolders($s3Client, "Interior", $Interior);
		// $this->createDateFolders($s3Client, "North", $North);
	}

	function lookupLocation($code)
	{
		$locations = array(
			"1051"=>"Duncan Law Courts",
			"1061"=>"Ganges Provincial Court",
			"1071"=>"Gold River Provincial Court",
			"1111"=>"Parksville Provincial Court",
			"1121"=>"Port Alberni Law Courts",
			"1141"=>"Port Hardy Law Courts",
			"1145"=>"Powell River Law Courts",
			"1151"=>"Sidney Provincial Court",
			"1171"=>"Tahsis Provincial Court",
			"1181"=>"Tofino Provincial Court",
			"1191"=>"Ucluelet Provincial Court",
			"1207"=>"Victoria Family and Youth Court",
			"1211"=>"Western Communities Provincial Court",
			"2007"=>"Bella Bella Law Courts",
			"2008"=>"Bella Coola Law Courts",
			"2021"=>"Pemberton Provincial Court",
			"2027"=>"Richmond Court Small Claims and Family Court",
			"2031"=>"Sechelt Provincial Court",
			"2035"=>"Squamish Provincial Court",
			"2045"=>"Robson Square Provincial Court",
			"2048"=>"Vancouver Traffic Court",
			"2051"=>"West Vancouver Provincial Court",
			"3541"=>"Hope Provincial Court",
			"3545"=>"Langley Provincial Court",
			"3571"=>"Mission Provincial Court",
			"4951"=>"Sparwood Provincial Court",
			"5775"=>"Fort Ware Provincial Court",
			"5805"=>"Tsay Keh Dene Court",
			"LECR"=>"Leech Town Court Registry",
			"5895"=>"Prince George Supreme Court",
			"5911"=>"Daajing Giids Provincial Crt",
			"5941"=>"Stewart Provincial Court",
			"5955"=>"Tumbler Ridge Provincial Court",
			"5681"=>"Anahim Lake Provincial Court",
			"5691"=>"Atlin Provincial Court",
			"5701"=>"Burns Lake Court",
			"5711"=>"Cassiar Court",
			"5721"=>"Chetwynd Provincial Court",
			"7999"=>"Leech Town Court House",
			"5731"=>"Dawson Creek Law Courts",
			"5741"=>"Dease Lake Provincial Court",
			"5751"=>"Fort Nelson Law Courts",
			"5761"=>"Fort St. James Provincial Court",
			"5771"=>"Fort St. John Law Courts",
			"5781"=>"Fraser Lake Provincial Court",
			"5791"=>"Houston Provincial Court",
			"5801"=>"Hudson's Hope Provincial Court",
			"5811"=>"Kitimat Law Courts",
			"5821"=>"Lower Post Provincial Court",
			"5831"=>"MacKenzie Provincial Court",
			"5841"=>"Masset Provincial Court",
			"5845"=>"McBride Provincial Court",
			"5851"=>"New Aiyansh Provincial Court",
			"5861"=>"New Hazelton Provincial Court",
			"4831"=>"Lytton Provincial Court",
			"ADJU"=>"Adjudicator Listing",
			"SHER"=>"Sherbrooke Courthouse",
			"NA01"=>"Nanaimo Law Courts NAO1",
			"NA02"=>"Nanaimo Law Courts NAO2",
			"NA03"=>"Nanaimo Law Courts NAO3",
			"NA04"=>"Nanaimo Law Courts NAO4",
			"NA05"=>"Nanaimo Law Courts NAO5",
			"NA06"=>"Nanaimo Law Courts NAO6",
			"NA07"=>"Nanaimo Law Courts NAO7",
			"NA08"=>"Nanaimo Law Courts NAO8",
			"NA09"=>"Nanaimo Law Courts NAO9",
			"NA10"=>"Nanaimo Law Courts NA10",
			"COA"=>"B.C. Court of Appeal",
			"5871"=>"100 Mile House Law Courts",
			"6011"=>"Vancouver Law Courts",
			"2041"=>"Justice Centre (Judicial)",
			"2009"=>"Klemtu Provincial Court",
			"SRES"=>"Shared Resource",
			"CAVA"=>"Court of Appeal of BC - Vancouver",
			"CAKA"=>"Court of Appeal of BC - Kamloops",
			"CAKE"=>"Court of Appeal of BC - Kelowna",
			"CAVI"=>"Court of Appeal of BC - Victoria",
			"2049"=>"Violation Ticket Centre",
			"KSS"=>"Kitsilano Secondary School",
			"KPU"=>"Kwantlen Polytechnic University",
			"4671"=>"Ashcroft Provincial Court",
			"4681"=>"Castlegar Provincial Court",
			"4691"=>"Chase Provincial Court",
			"4701"=>"Clearwater Provincial Court",
			"4711"=>"Cranbrook Law Courts",
			"4721"=>"Creston Law Courts",
			"4731"=>"Fernie Law Courts",
			"4741"=>"Golden Law Court",
			"4751"=>"Grand Forks Law Courts",
			"4771"=>"Invermere Law Courts",
			"4781"=>"Kamloops Court",
			"4801"=>"Kelowna Law Courts",
			"4811"=>"Kimberley Provincial Court",
			"4821"=>"Lillooet Law Courts",
			"4851"=>"Merritt Law Court",
			"4861"=>"Nakusp Provincial Court",
			"1091"=>"Nanaimo Law Courts",
			"1201"=>"Victoria Law Courts",
			"2040"=>"Vancouver Provincial Court",
			"3511"=>"Burnaby Court",
			"3521"=>"Chilliwack Law Courts",
			"3531"=>"Port Coquitlam Court",
			"3551"=>"Maple Ridge Provincial Court",
			"3581"=>"New Westminster Law Courts",
			"3585"=>"Surrey Provincial Court",
			"3587"=>"Surrey Family Court",
			"5891"=>"Prince George Law Courts",
			"4871"=>"Nelson Law Courts",
			"4881"=>"Oliver Law Courts",
			"2025"=>"Richmond Provincial Court",
			"2011"=>"North Vancouver Court",
			"2010"=>"Delta Provincial Court",
			"1031"=>"Campbell River Court",
			"1041"=>"Courtenay Law Courts",
			"5971"=>"Williams Lake Law Courts",
			"5961"=>"Vanderhoof Law Courts",
			"5951"=>"Terrace Law Courts",
			"5931"=>"Smithers Law Courts",
			"5921"=>"Quesnel Law Courts",
			"5901"=>"Prince Rupert Law Courts",
			"4891"=>"Penticton Law Courts",
			"5959"=>"Valemount Provincial Court",
			"4901"=>"Princeton Law Courts",
			"4911"=>"Revelstoke Law Courts",
			"3561"=>"Abbotsford Law Courts",
			"4921"=>"Rossland Law Courts",
			"4941"=>"Salmon Arm Law Courts",
			"4971"=>"Vernon Law Courts"
		);
		$region1 = array(1031,1071,1171,1211,1041,1051,1061,1091,1121,1191,1141,1145,1151,1181,1207,1201);
		$region2 = array(2048,2051,6011,2040,2011,2007,2008,2009,2011,2021,2027,2025,2045,2031);
		$region3 = array(3561,3521,3581,3531,3585,3587);
		$region4 = array(4711,4721,4731,4741,4771,4951,4781,4691,4701,4821,4851,4801,5751,4871,4681,4751,4861,4921,4891,4901,4941,4911,4971);
		$region5 = array(5731,5721,5955,5751,5771,5801,5821,5895,5891,5761,5781,5845,5831,5901,5841,5921,5931,5691,5701,5861,5791,5951,5741,5811,5805,5851,5941,5961,5971,5871,5681,5959);
	
		$parts = explode("-", $code);
		$name = "";
		if (in_array($parts[0], $region1)) $name .= "Island/";
		if (in_array($parts[0], $region2)) $name .= "Vancouver/";
		if (in_array($parts[0], $region3)) $name .= "Fraser/";
		if (in_array($parts[0], $region4)) $name .= "Interior/";
		if (in_array($parts[0], $region5)) $name .= "North/";

		$name .= $locations[$parts[0]];
		$name .="/";
		if (count($parts) > 1)
		{
			// Have Location and room in the code
			$name .= end($parts);
			$name .="/";
		}
		return $name;

	}
	function createSingleDateFolders($s3Client,$singleLocation)
	{
//		$bucket_name = 'parc-nextcloud';
		$bucket_name = 'ag-pssg-csb-judi-dev-bkt';
		$folderName = $this->lookupLocation($singleLocation);
		$months = array("01-Jan","02-Feb","03-Mar","04-Apr","05-May","06-Jun","07-Jul","08-Aug","09-Sep","10-Oct","11-Nov","12-Dec");
		try {			
			foreach ($months as $month) {
				$result = $s3Client->putObject([
					'Bucket' => $bucket_name,
					'Key' => $folderName.$month."/",
					'Body' => 'data.txt',
				]);
				for ($day = 1; $day<=31; $day++) {
					$daysResult = $s3Client->putObject([
						'Bucket' => $bucket_name,
						'Key' => $folderName.$month."/".$day."/",
						'Body' => 'data.txt',
					]);
					//Put REPORTS folder into every day
					$daysResult = $s3Client->putObject([
						'Bucket' => $bucket_name,
						'Key' => $folderName.$month."/".$day."/REPORTS/",
						'Body' => 'data.txt',
					]);
					}
				}
		} catch (S3Exception $e) {
			echo $e->getMessage() . "\n";
		}
	}


	function createBucket($s3Client, $bucketName)
	{
		try {
			$result = $s3Client->createBucket([
				'Bucket' => $bucketName,
			]);
			return 'The bucket\'s location is: ' .
				$result['Location'] . '. ' .
				'The bucket\'s effective URI is: ' . 
				$result['@metadata']['effectiveUri'];
		} catch (AwsException $e) {
			return 'Error: ' . $e->getAwsErrorMessage();
		}
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

	public function index() {

		$s3Client = $this->createClient();

		$this->createFolders();
		return new TemplateResponse('move', 'index');  // templates/index.php
	}

	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *	 
	 * @CORS
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 */
	public function move($from, $to, $person) {
		$s3Client = $this->createClient();

		$bucket_name = 'parc-nextcloud';
		try 
		{
			$this->moveFiles($from, $to, $person);
		} 
		catch (Exception $exception) {
		exit("Please fix error with listing objects before continuing.");
		}
	}
	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *	 
	 * @CORS
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 */
	public function submit($id) {
		$s3Client = $this->createClient();

		$bucket_name = 'parc-nextcloud';
		try {

			if ($_SERVER["REQUEST_METHOD"] == "GET") {
				$fromregion = $_GET["fromregion"];
				$fromplace = $_GET["fromplace"];
				$frommonth = $_GET["frommonth"];
				$fromday = $_GET["fromday"];
				$fromcourt = $_GET["fromcourt"];
				$fromroom = $_GET["fromroom"];
				$person = $_GET["person"];

				$toregion = $_GET["toregion"];
				$toplace = $_GET["toplace"];
				$tomonth = $_GET["tomonth"];
				$today = $_GET["today"];
				$tocourt = $_GET["tocourt"];
				$toroom = $_GET["toroom"];

				$from = $fromregion.'/'.$fromplace.'/'.$fromcourt.'/'.$fromroom.'/'.$frommonth.'/'.$fromday.'/'.$person;
				$to = $toregion.'/'.$toplace.'/'.$tocourt.'/'.$toroom.'/'.$tomonth.'/'.$today.'/'.$person;

				$this->moveFiles($from, $to, $person);
			}
			
		} catch (Exception $exception) {
			// echo "Failed to list objects in $bucket_name with error: " . $exception->getMessage();
			// exit("Please fix error with listing objects before continuing.");
		}
		$url = '/index.php/apps/move/';
		header('Location: '.$url);
		die();
		return new TemplateResponse('move', 'index');  // templates/index.php
	}
		/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @CORS
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 */

	public function folders() {

		// $s3Client = $this->createClient();
		// $this->createFolders();

		// $url = '/index.php/apps/move/';
		// header('Location: '.$url);
		// die();
		return new TemplateResponse('move', 'folders');  // templates/folders.php
	}

}
