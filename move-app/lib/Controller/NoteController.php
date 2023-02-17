<?php
namespace OCA\Move\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\Move\Service\NoteService;

class NoteController extends Controller {

	private NoteService $service;
	private ?string $userId;

	use Errors;

	public function __construct(string $AppName, IRequest $request,
								NoteService $service, ?string $UserId = null) {
		parent::__construct($AppName, $request);
		$this->service = $service;
		$this->userId = $UserId;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		return new DataResponse($this->service->findAll($this->userId));
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param int $id
	 */
	public function show(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->find($id, $this->userId);
		});
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param string $title
	 * @param string $content
	 */
	public function create(string $title, string $content) {
		return $this->service->create($title, $content, $this->userId);
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param int $id
	 * @param string $title
	 * @param string $content
	 */
	public function update(int $id, string $title, string $content): DataResponse {
		return $this->handleNotFound(function () use ($id, $title, $content): Note {
			return $this->service->update($id, $title, $content, $this->userId);
		});
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param int $id
	 */
	public function destroy(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id): Note {
			return $this->service->delete($id, $this->userId);
		});
	}

}
