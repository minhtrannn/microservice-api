<?php 

namespace App\Models\Traits;

/**
 * Trait setter
 */
trait Setter{

	public function setOwner($id){
		$this->created_by = $id;
	}

	public function setUpdater($id){
		$this->updated_by = $id;
	}

	public function setDeleter($id){
		$this->deleted_by = $id;
	}
}