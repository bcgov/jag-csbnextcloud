<?php
namespace OCA\Move\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\ApiController;

use OCA\Move\Service\NoteService;

class NoteApiController extends ApiController {

    private NoteService $service;
    private ?string $userId;

    use Errors;

    public function __construct(string $appName, IRequest $request,
                                NoteService $service, ?string $userId = null) {
        parent::__construct($appName, $request);
        $this->service = $service;
        $this->userId = $userId;
    }

    /**
     * @CORS
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function index() {
        return new DataResponse($this->service->findAll($this->userId));
    }

    /**
     * @CORS
     * @NoCSRFRequired
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function show($id) {
        return $this->handleNotFound(function () use ($id) {
            return $this->service->find($id, $this->userId);
        });
    }

    /**
     * @CORS
     * @NoCSRFRequired
     * @NoAdminRequired
     *
     * @param string $title
     * @param string $content
     */
    public function create($fromlocation,$fromroom,$tolocation,$toroom, $tomonth, $today, $frommonth, $fromday, $lastname, $firstname) {
        $response = $this->service->move($fromlocation,$fromroom,$tolocation,$toroom, $tomonth, $today, $frommonth, $fromday, $lastname, $firstname);
        if ($response == "true")
        {
            return "fromlocation: ".$fromlocation." fromroom: ".$fromroom." tolocation:".$tolocation." toroom:".$toroom." frommonth:".$frommonth." fromday:".$fromday." tomonth:".$tomonth." today:".$today." lastname:".$lastname." firstname:".$firstname;
        }
        else
        {
            return $response;
        }
    }

    /**
     * @CORS
     * @NoCSRFRequired
     * @NoAdminRequired
     *
     * @param int $id
     * @param string $title
     * @param string $content
     */
    public function update($id, $title, $content) {
        return $this->handleNotFound(function () use ($id, $title, $content) {
            return $this->service->update($id, $title, $content, $this->userId);
        });
    }

    /**
     * @CORS
     * @NoCSRFRequired
     * @NoAdminRequired
     *
     * @param int $id
     */
    public function destroy($id) {
        return $this->handleNotFound(function () use ($id) {
            return $this->service->delete($id, $this->userId);
        });
    }

}
