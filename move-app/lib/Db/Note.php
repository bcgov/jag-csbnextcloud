<?php
namespace OCA\Move\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Note extends Entity implements JsonSerializable {

	public $title;
	public $content;
	public $userId;
	public $configId;
	public $mountId;
	public $key;
	public $value;

	public function __construct() {
		$this->addType('id','integer');
	}

	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'title' => $this->title,
			'content' => $this->content,
			'configId' => $this->configId,
			'mountId' => $this->mountId,
			'key' => $this->key,
			'value' => $this->value,
		];
	}
}
