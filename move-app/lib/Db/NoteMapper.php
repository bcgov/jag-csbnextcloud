<?php
namespace OCA\Move\Db;

use OCP\IDBConnection;
use OCP\AppFramework\Db\QBMapper;

/**
 * @extends QBMapper<Note>
 */
class NoteMapper extends QBMapper {

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'external_config', Note::class);
	}

	public function find(int $id, string $userId) {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id)))
			->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));

		return $this->findEntity($qb);
	}

	public function findAll(string $userId) {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));

		return $this->findEntities($qb);
	}
	public function findAllMount(string $mountId) {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('mount_id', $qb->createNamedParameter($mountId)));

		return $this->findEntities($qb);
	}
}
