<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Ponder Source Foundation <michiel@pondersource.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\PreJournal\Controller;



if (getenv("LOAD_ENV")) {
	require_once(__DIR__ . '/../prejournal-src/loadenv.php');
}
require_once(__DIR__ . '/../prejournal-src/platform.php');
require_once(__DIR__ . '/../prejournal-src/run-command.php');


use OCA\PreJournal\AppInfo\Application;
use OCA\PreJournal\Service\NoteService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\Files\IRootFolder;

class NoteController extends Controller {
	private NoteService $service;
	private ?string $userId;

	use Errors;

	public function __construct(IRequest $request,
								NoteService $service,
								IRootFolder $rootFolder,
								?string $userId) {
		parent::__construct(Application::APP_ID, $request);
		error_log(var_export("hello!", true));
		$this->service = $service;
		$this->userId = $userId;
		$this->rootFolder = $rootFolder;
		$this->userFolder = $this->rootFolder->getUserFolder($userId);
	}


	/**
	 * @NoAdminRequired
	 */
	public function import($contentType, $content, $file): DataResponse {
		error_log(var_export("import!", true));
		error_log(var_export($contentType, true));
		error_log(var_export($content, true));
		error_log(var_export($file, true));

		\PreJournal\RunCommand\runCommandWithInlineData(["user" => [
			"username" => "admin",
			"id" => 1
			]
		], [
			"import-hours",
			$contentType,
			$content

		]);

		// $list = $this->service->findAll($this->userId);
		// $nodes = $this->userFolder->getDirectoryListing();
		// $list = array_map(function (\OCP\Files\Node $node) {
		// 	$prefixLen = strlen("/" . $this->userId . "/files/");
		// 	return [
		// 		"title" => substr($node->getPath(), $prefixLen),
		// 		"content" => "sdv",
		// 		"userId" => "admin",
		// 		"id" => 123
		// 	];
		// }, $nodes);
		// error_log(var_export($list, true));
		return new DataResponse([]);
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		$list = $this->service->findAll($this->userId);
		$nodes = $this->userFolder->getDirectoryListing();
		$list = array_map(function (\OCP\Files\Node $node) {
			$prefixLen1 = strlen("/" . $this->userId . "/");
			$prefixLen2 = $prefixLen1 + strlen("files/");
			$size = $node->getSize();
			$path = $node->getPath();
			$pathOnStorage = substr($path, $prefixLen1);
			$pathForUser = substr($path, $prefixLen2);
			error_log(var_export($prefixLen1, true));
			error_log(var_export($prefixLen2, true));
			error_log(var_export($size, true));
			error_log(var_export($path, true));
			error_log(var_export($pathOnStorage, true));
			error_log(var_export($pathForUser, true));

			if ($size > 10000) {
				$content = "(preview not available)";
			} else {
				$storage = $node->getStorage();
				$content = $storage->file_get_contents($pathOnStorage);
			}
			return [
				"size" => $size,
				"title" => $pathForUser,
				"content" => $content,
				"userId" => "admin",
				"id" => 123
			];
		}, $nodes);
		error_log(var_export($list, true));
		return new DataResponse($list);
	}

	/**
	 * @NoAdminRequired
	 */
	public function show(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->find($id, $this->userId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(string $title, string $content): DataResponse {
		return new DataResponse($this->service->create($title, $content,
			$this->userId));
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $id, string $title,
						   string $content): DataResponse {
		return $this->handleNotFound(function () use ($id, $title, $content) {
			return $this->service->update($id, $title, $content, $this->userId);
		});
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $id): DataResponse {
		return $this->handleNotFound(function () use ($id) {
			return $this->service->delete($id, $this->userId);
		});
	}
}
